<?php
/**
 * Admin – Configuration & Sécurité
 * Rodrigue Ekoko Portfolio
 */

ob_start();

define('ADMIN_VERSION', '1.0.0');

// ─── Chemins absolus ──────────────────────────────────────
define('ROOT_PATH',    dirname(__DIR__));
define('DATA_PATH',    ROOT_PATH . '/data');
define('UPLOADS_PATH', ROOT_PATH . '/assets/images');
define('FILES_PATH',   ROOT_PATH . '/assets/files');

define('SETTINGS_FILE', DATA_PATH . '/settings.json');
define('PROJECTS_FILE', DATA_PATH . '/projects.json');
define('MESSAGES_FILE', DATA_PATH . '/messages.json');
define('AUTH_FILE',     DATA_PATH . '/auth.json');
define('LOGS_FILE',     DATA_PATH . '/security.log');

// ─── Upload ──────────────────────────────────────────────
define('MAX_UPLOAD_SIZE',    5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg','image/png','image/webp','image/gif']);
define('ALLOWED_CV_TYPES',    ['application/pdf']);

// ─── Session ─────────────────────────────────────────────
define('SESSION_NAME',    'sid_' . substr(md5(ROOT_PATH), 0, 8));
define('SESSION_TIMEOUT', 7200); // 2h

// ─── Identifiants ────────────────────────────────────────
// Lire depuis auth.json (créé par password.php)
// Fallback : admin / Admin2024! (hash BCrypt)
function get_admin_credentials(): array {
    if (file_exists(AUTH_FILE)) {
        $d = json_decode(file_get_contents(AUTH_FILE), true);
        if (!empty($d['username']) && !empty($d['hash'])) {
            return ['username' => $d['username'], 'hash' => $d['hash']];
        }
    }
    // Hash par défaut — sera écrasé dès le 1er changement de mot de passe
    return [
        'username' => 'rodrigue',
        'hash'     => '$2y$12$Zl6UXgR7q1N9XiKqG3eKxOJ/3v6Z5N8r7F2wR9T4mK1bQ8sP0YvCu',
        // mot de passe par défaut : Admin2024!
    ];
}

// ─── Démarrage de session sécurisé ───────────────────────
function admin_session_start(): void {
    if (session_status() !== PHP_SESSION_NONE) return;

    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
             || ($_SERVER['SERVER_PORT'] ?? 80) == 443;

    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $is_https,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// ─── Auth ─────────────────────────────────────────────────
function is_logged_in(): bool {
    admin_session_start();
    if (empty($_SESSION['admin_ok']))          return false;
    if (empty($_SESSION['admin_ts']))          return false;
    if (empty($_SESSION['admin_ip']))          return false;
    if (empty($_SESSION['admin_ua']))          return false;

    // Timeout
    if ((time() - $_SESSION['admin_ts']) > SESSION_TIMEOUT) {
        security_log('session_expired', $_SESSION['admin_user'] ?? '?');
        session_unset(); session_destroy();
        return false;
    }
    // IP binding (protection session hijacking)
    if ($_SESSION['admin_ip'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
        security_log('ip_mismatch', $_SESSION['admin_user'] ?? '?');
        session_unset(); session_destroy();
        return false;
    }
    // User-Agent binding
    $ua_hash = md5($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($_SESSION['admin_ua'] !== $ua_hash) {
        security_log('ua_mismatch', $_SESSION['admin_user'] ?? '?');
        session_unset(); session_destroy();
        return false;
    }

    $_SESSION['admin_ts'] = time();
    return true;
}

function login_user(string $username): void {
    admin_session_start();
    session_regenerate_id(true); // fixation protection
    $_SESSION['admin_ok']   = true;
    $_SESSION['admin_user'] = $username;
    $_SESSION['admin_ts']   = time();
    $_SESSION['admin_ip']   = $_SERVER['REMOTE_ADDR'] ?? '';
    $_SESSION['admin_ua']   = md5($_SERVER['HTTP_USER_AGENT'] ?? '');
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . admin_url('login.php'));
        exit;
    }
}

// ─── URL admin (auto-détectée) ────────────────────────────
function admin_url(string $page = ''): string {
    $script = $_SERVER['SCRIPT_NAME'] ?? '/admin/index.php';
    // Trouver la position de /admin/ dans le chemin
    $pos  = strpos($script, '/admin/');
    $base = $pos !== false ? substr($script, 0, $pos) . '/admin/' : '/admin/';
    return $page ? $base . $page : $base;
}

// ─── CSRF ─────────────────────────────────────────────────
function csrf_token(): string {
    admin_session_start();
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_ts'])
        || (time() - $_SESSION['csrf_ts']) > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_ts']    = time();
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): void {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!$token || !hash_equals(csrf_token(), $token)) {
        security_log('csrf_fail', 'POST ' . ($_SERVER['REQUEST_URI'] ?? ''));
        http_response_code(403);
        die(json_encode(['error' => 'CSRF token invalide.']));
    }
}

// ─── Brute-force protection ───────────────────────────────
function check_brute_force(string $ip): bool {
    $file  = DATA_PATH . '/bruteforce.json';
    $data  = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    $now   = time();
    $key   = md5($ip);

    // Nettoyer les entrées > 15 minutes
    if (!empty($data[$key])) {
        $data[$key] = array_values(array_filter($data[$key], fn($t) => ($now - $t) < 900));
    }

    $attempts = count($data[$key] ?? []);
    return $attempts >= 5; // bloqué après 5 tentatives / 15 min
}

function record_failed_login(string $ip): void {
    $file  = DATA_PATH . '/bruteforce.json';
    $data  = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    $key   = md5($ip);
    $now   = time();

    if (!isset($data[$key])) $data[$key] = [];
    $data[$key][] = $now;
    // Garder seulement les 15 dernières minutes
    $data[$key]   = array_values(array_filter($data[$key], fn($t) => ($now - $t) < 900));

    file_put_contents($file, json_encode($data), LOCK_EX);
}

function clear_failed_logins(string $ip): void {
    $file = DATA_PATH . '/bruteforce.json';
    $data = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    unset($data[md5($ip)]);
    file_put_contents($file, json_encode($data), LOCK_EX);
}

// ─── Log de sécurité ──────────────────────────────────────
function security_log(string $event, string $detail = ''): void {
    $ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua      = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 80);
    $uri     = $_SERVER['REQUEST_URI'] ?? '';
    $line    = date('Y-m-d H:i:s') . " | {$event} | IP:{$ip} | {$detail} | UA:{$ua} | {$uri}\n";
    @file_put_contents(LOGS_FILE, $line, FILE_APPEND | LOCK_EX);
}

// ─── Flash messages ───────────────────────────────────────
function flash(string $type, string $msg): void {
    admin_session_start();
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array {
    admin_session_start();
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// ─── JSON helpers ─────────────────────────────────────────
function read_json(string $file, array $default = []): array {
    if (!file_exists($file)) return $default;
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : $default;
}

function write_json(string $file, array $data): bool {
    $dir = dirname($file);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

// ─── Paramètres ───────────────────────────────────────────
function default_settings(): array {
    return [
        'name'          => 'Rodrigue Ekoko',
        'profession_fr' => 'Développeur Web & Logiciel',
        'profession_en' => 'Web & Software Developer',
        'email'         => 'votre.email@example.com',
        'phone'         => '+237 6XX XXX XXX',
        'whatsapp'      => '237600000000',
        'location_fr'   => 'Cameroun',
        'location_en'   => 'Cameroon',
        'linkedin'      => 'https://www.linkedin.com/in/votre-profil',
        'github'        => 'https://github.com/votre-username',
        'site_url'      => 'https://rodrigue-ekoko.com',
        'cv_filename'   => 'CV-Rodrigue-Ekoko.pdf',
        'profile_photo' => '',
        'about_fr'      => "Je suis Rodrigue Ekoko, développeur Web & Logiciel passionné par la conception de solutions informatiques. Mon objectif est de mettre la technologie au service des besoins réels des entreprises et des particuliers.\n\nJ'interviens dans la conception d'interfaces web modernes, le développement d'applications métier, la gestion de bases de données et la création de plateformes digitales.\n\nJ'accorde une importance particulière à la qualité du code, à l'expérience utilisateur, à la performance et à l'évolutivité des applications.",
        'about_en'      => "I am Rodrigue Ekoko, a Web & Software Developer passionate about designing IT solutions. My goal is to put technology at the service of real needs of businesses and individuals.\n\nI work on designing modern web interfaces, developing business applications, database management and creating digital platforms.\n\nI place particular importance on code quality, user experience, performance and application scalability.",
    ];
}

function get_settings(): array {
    return array_merge(default_settings(), read_json(SETTINGS_FILE));
}
