<?php
/**
 * API Contact – reçoit le formulaire, sauvegarde en JSON, envoie un e-mail
 */

// Pas de output avant les headers
ob_start();

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

// Accepter seulement POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// ─── Chemins absolus ──────────────────────────────────────
$root_path     = dirname(__DIR__);
$data_path     = $root_path . DIRECTORY_SEPARATOR . 'data';
$messages_file = $data_path . DIRECTORY_SEPARATOR . 'messages.json';
$settings_file = $data_path . DIRECTORY_SEPARATOR . 'settings.json';

// ─── Helpers ──────────────────────────────────────────────
function api_read_json(string $file): array {
    if (!file_exists($file)) return [];
    $raw = file_get_contents($file);
    if ($raw === false) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function api_write_json(string $file, array $data): bool {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

function api_sanitize(string $v): string {
    return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
}

// ─── Rate limit simple ────────────────────────────────────
$ip        = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rate_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'porto_' . md5($ip) . '.json';
$now       = time();

$rate_data = [];
if (file_exists($rate_file)) {
    $rate_raw  = file_get_contents($rate_file);
    $rate_data = json_decode($rate_raw ?: '[]', true) ?: [];
    $rate_data = array_values(array_filter($rate_data, fn($t) => ($now - $t) < 3600));
}

if (count($rate_data) >= 10) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Trop de messages. Réessayez dans une heure.']);
    exit;
}

// ─── Lire les données POST ────────────────────────────────
// Support JSON et form-urlencoded
$raw  = file_get_contents('php://input');
$body = [];
if (!empty($raw)) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $body = $decoded;
    }
}
// Fallback form POST
if (empty($body)) {
    $body = $_POST;
}

$name    = api_sanitize((string)($body['name']    ?? ''));
$email   = api_sanitize((string)($body['email']   ?? ''));
$subject = api_sanitize((string)($body['subject'] ?? ''));
$message = api_sanitize((string)($body['message'] ?? ''));

// ─── Validation ───────────────────────────────────────────
$errors = [];

if (mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 100) {
    $errors[] = 'Nom invalide (2–100 caractères).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Adresse e-mail invalide.';
}
if (mb_strlen($subject, 'UTF-8') < 2 || mb_strlen($subject, 'UTF-8') > 200) {
    $errors[] = 'Sujet invalide (2–200 caractères).';
}
if (mb_strlen($message, 'UTF-8') < 5 || mb_strlen($message, 'UTF-8') > 5000) {
    $errors[] = 'Message trop court (min. 5 caractères) ou trop long.';
}
// Anti-spam basique
if (preg_match('/(http[s]?:\/\/|<a\s|onclick|javascript:|<script)/i', $name . $subject . $message)) {
    $errors[] = 'Contenu non autorisé.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ─── Sauvegarder le message ───────────────────────────────
$messages = api_read_json($messages_file);
$new_id   = empty($messages) ? 1 : (max(array_column($messages, 'id')) + 1);

$new_message = [
    'id'      => $new_id,
    'name'    => $name,
    'email'   => $email,
    'subject' => $subject,
    'message' => $message,
    'date'    => date('Y-m-d H:i:s'),
    'ip'      => $ip,
    'read'    => false,
];

$messages[] = $new_message;

if (!api_write_json($messages_file, $messages)) {
    // Écriture échouée – log silencieux, on retourne quand même succès
    // pour ne pas bloquer l'UX (le message est perdu mais l'e-mail partira)
    error_log('[Portfolio Contact] Impossible d\'écrire dans ' . $messages_file);
}

// ─── Envoi e-mail (optionnel, ne bloque pas si échec) ─────
$settings = api_read_json($settings_file);
$to       = !empty($settings['email']) ? $settings['email'] : '';

if ($to && filter_var($to, FILTER_VALIDATE_EMAIL)) {
    $mail_subject = '=?UTF-8?B?' . base64_encode('[Portfolio] ' . $subject) . '?=';
    $mail_body    = "Nouveau message reçu via le formulaire de contact du portfolio.\n\n"
                  . "Nom       : {$name}\n"
                  . "E-mail    : {$email}\n"
                  . "Sujet     : {$subject}\n\n"
                  . "Message :\n" . str_repeat('-', 40) . "\n"
                  . $message . "\n"
                  . str_repeat('-', 40) . "\n\n"
                  . "Date : " . date('d/m/Y à H:i') . "\n"
                  . "IP   : {$ip}\n";

    $mail_headers  = "MIME-Version: 1.0\r\n";
    $mail_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $mail_headers .= "Content-Transfer-Encoding: base64\r\n";
    $mail_headers .= "From: Portfolio Rodrigue <noreply@rodrigue-ekoko.com>\r\n";
    $mail_headers .= "Reply-To: {$name} <{$email}>\r\n";
    $mail_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    @mail($to, $mail_subject, base64_encode($mail_body), $mail_headers);
}

// ─── Rate limit update ────────────────────────────────────
$rate_data[] = $now;
@file_put_contents($rate_file, json_encode(array_values($rate_data)));

// ─── Réponse succès ───────────────────────────────────────
ob_end_clean();
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Message envoyé avec succès.',
    'id'      => $new_id,
]);
