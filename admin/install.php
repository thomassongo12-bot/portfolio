<?php
/**
 * Script d'installation automatique – Hostinger / Hébergeur mutualisé
 * URL : https://votre-site.com/admin/install.php
 * SUPPRIMER immédiatement après exécution
 */

// Afficher toutes les erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';

$steps  = [];
$errors = [];

// ─── 1. Créer data/auth.json ──────────────────────────────
$auth_file = DATA_PATH . '/auth.json';
if (!file_exists($auth_file)) {
    $hash = password_hash('admin2024', PASSWORD_BCRYPT, ['cost' => 12]);
    $ok   = file_put_contents($auth_file, json_encode([
        'username' => 'rodrigue',
        'hash'     => $hash,
    ], JSON_PRETTY_PRINT), LOCK_EX);
    if ($ok) {
        $steps[] = ['ok', 'data/auth.json créé (mot de passe : admin2024)'];
    } else {
        $errors[] = 'Impossible de créer data/auth.json — vérifiez les permissions';
    }
} else {
    $steps[] = ['ok', 'data/auth.json existe déjà'];
}

// ─── 2. Créer data/settings.json ─────────────────────────
$settings_file = DATA_PATH . '/settings.json';
if (!file_exists($settings_file)) {
    $default = [
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
        'site_url'      => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'rodrigue-ekoko.com'),
        'cv_filename'   => 'CV-Rodrigue-Ekoko.pdf',
        'profile_photo' => '',
        'about_fr'      => "Je suis Rodrigue Ekoko, développeur Web & Logiciel passionné par la conception de solutions informatiques.",
        'about_en'      => "I am Rodrigue Ekoko, a Web & Software Developer passionate about designing IT solutions.",
    ];
    $ok = file_put_contents($settings_file, json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    if ($ok) {
        $steps[] = ['ok', 'data/settings.json créé'];
    } else {
        $errors[] = 'Impossible de créer data/settings.json';
    }
} else {
    $steps[] = ['ok', 'data/settings.json existe déjà'];
}

// ─── 3. Créer data/messages.json ─────────────────────────
$messages_file = DATA_PATH . '/messages.json';
if (!file_exists($messages_file)) {
    $ok = file_put_contents($messages_file, '[]', LOCK_EX);
    if ($ok !== false) {
        $steps[] = ['ok', 'data/messages.json créé'];
    } else {
        $errors[] = 'Impossible de créer data/messages.json';
    }
} else {
    $steps[] = ['ok', 'data/messages.json existe déjà'];
}

// ─── 4. Créer data/projects.json si absent ────────────────
$projects_file = DATA_PATH . '/projects.json';
if (!file_exists($projects_file)) {
    $ok = file_put_contents($projects_file, '[]', LOCK_EX);
    $steps[] = $ok !== false ? ['ok', 'data/projects.json créé'] : ['err', 'Impossible de créer projects.json'];
} else {
    $steps[] = ['ok', 'data/projects.json existe déjà (' . count(json_decode(file_get_contents($projects_file), true) ?: []) . ' projets)'];
}

// ─── 5. Créer assets/files/ ───────────────────────────────
$files_dir = ROOT_PATH . '/assets/files';
if (!is_dir($files_dir)) {
    $ok = mkdir($files_dir, 0755, true);
    $steps[] = $ok ? ['ok', 'assets/files/ créé'] : ['err', 'Impossible de créer assets/files/'];
} else {
    $steps[] = ['ok', 'assets/files/ existe déjà'];
}

// ─── 6. Créer assets/images/projects/ si absent ──────────
$proj_img_dir = ROOT_PATH . '/assets/images/projects';
if (!is_dir($proj_img_dir)) {
    $ok = mkdir($proj_img_dir, 0755, true);
    $steps[] = $ok ? ['ok', 'assets/images/projects/ créé'] : ['warn', 'assets/images/projects/ non créé'];
} else {
    $steps[] = ['ok', 'assets/images/projects/ existe'];
}

// ─── 7. Vérifier la connexion admin ──────────────────────
if (file_exists($auth_file)) {
    $d = json_decode(file_get_contents($auth_file), true);
    $ok_login = !empty($d['hash']) && password_verify('admin2024', $d['hash']);
    $steps[] = $ok_login
        ? ['ok', 'Connexion admin vérifiée : rodrigue / admin2024']
        : ['err', 'Hash invalide dans auth.json'];
}

$all_ok = empty($errors);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Installation – Portfolio</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:system-ui,sans-serif;background:#0F172A;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
    .card{background:#fff;border-radius:16px;padding:2rem;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.4)}
    h1{font-size:1.4rem;font-weight:800;color:#0F172A;margin-bottom:.3rem}
    .sub{font-size:.85rem;color:#64748B;margin-bottom:1.5rem}
    .steps{display:flex;flex-direction:column;gap:.5rem;margin-bottom:1.5rem}
    .step{display:flex;align-items:center;gap:.75rem;padding:10px 14px;border-radius:8px;font-size:.86rem}
    .step.ok  {background:#f0fdf4;color:#15803d}
    .step.err {background:#fef2f2;color:#b91c1c}
    .step.warn{background:#fefce8;color:#854d0e}
    .icon{font-size:1rem;flex-shrink:0}
    .cta{display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.25rem}
    a.btn{display:inline-flex;align-items:center;gap:6px;padding:11px 22px;border-radius:8px;font-size:.9rem;font-weight:700;text-decoration:none}
    .btn-p{background:#2563EB;color:#fff}
    .btn-p:hover{background:#1d4ed8}
    .btn-o{background:#fff;color:#2563EB;border:2px solid #2563EB}
    .warn-box{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;padding:12px 16px;border-radius:8px;font-size:.84rem;line-height:1.6}
    .warn-box strong{display:block;margin-bottom:.3rem;font-size:.9rem}
    .creds{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;font-family:monospace;font-size:.88rem;color:#0F172A;margin-bottom:1.25rem}
    .creds div{margin-bottom:.3rem}
    .creds span{color:#2563EB;font-weight:700}
  </style>
</head>
<body>
<div class="card">
  <h1><?= $all_ok ? '✅' : '⚠️' ?> Installation <?= $all_ok ? 'réussie' : 'incomplète' ?></h1>
  <p class="sub">Portfolio Rodrigue Ekoko – Hostinger</p>

  <div class="steps">
    <?php foreach ($steps as [$type, $msg]): ?>
      <div class="step <?= $type ?>">
        <span class="icon"><?= $type === 'ok' ? '✓' : ($type === 'err' ? '✗' : '⚠') ?></span>
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endforeach; ?>
    <?php foreach ($errors as $err): ?>
      <div class="step err"><span class="icon">✗</span><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>
  </div>

  <?php if ($all_ok): ?>
    <div class="creds">
      <div>URL admin : <span><?= htmlspecialchars('https://' . ($_SERVER['HTTP_HOST'] ?? '') . '/admin/') ?></span></div>
      <div>Login     : <span>rodrigue</span></div>
      <div>Mot de passe : <span>admin2024</span></div>
    </div>

    <div class="cta">
      <a href="login.php" class="btn btn-p">→ Se connecter à l'admin</a>
      <a href="../index.php" class="btn btn-o">Voir le portfolio</a>
    </div>
  <?php endif; ?>

  <div class="warn-box">
    <strong>⚠️ Sécurité obligatoire</strong>
    1. Connectez-vous et <strong>changez immédiatement le mot de passe</strong> dans Admin → Mot de passe<br>
    2. <strong>Supprimez ce fichier</strong> <code>admin/install.php</code> via le gestionnaire de fichiers Hostinger<br>
    3. Supprimez aussi <code>admin/diag.php</code> si présent
  </div>
</div>
</body>
</html>
