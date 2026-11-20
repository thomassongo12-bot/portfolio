<?php
/**
 * Script d'initialisation – À exécuter UNE SEULE FOIS après déploiement
 * Puis SUPPRIMER ce fichier ou le protéger via .htaccess
 *
 * URL : https://votre-site.com/setup.php
 */

// Protection : désactiver après initialisation
$lock_file = __DIR__ . '/data/.setup_done';
if (file_exists($lock_file)) {
    http_response_code(403);
    die('<h2>Setup déjà effectué. Supprimez ce fichier.</h2>');
}

$data_dir = __DIR__ . '/data';
$errors   = [];
$done     = [];

// Créer le dossier data/ si absent
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
    $done[] = 'Dossier data/ créé';
}

// Créer messages.json vide
$msg_file = $data_dir . '/messages.json';
if (!file_exists($msg_file)) {
    file_put_contents($msg_file, '[]');
    $done[] = 'messages.json créé';
} else {
    $done[] = 'messages.json existe déjà';
}

// Créer settings.json depuis l'exemple
$set_file = $data_dir . '/settings.json';
$set_ex   = $data_dir . '/settings.json.example';
if (!file_exists($set_file)) {
    if (file_exists($set_ex)) {
        copy($set_ex, $set_file);
        $done[] = 'settings.json créé depuis l\'exemple';
    } else {
        file_put_contents($set_file, json_encode([
            'name' => 'Rodrigue Ekoko',
            'email' => 'votre.email@example.com',
        ], JSON_PRETTY_PRINT));
        $done[] = 'settings.json créé (minimal)';
    }
} else {
    $done[] = 'settings.json existe déjà';
}

// Créer projects.json depuis l'exemple
$proj_file = $data_dir . '/projects.json';
$proj_ex   = $data_dir . '/projects.json'; // déjà présent dans le repo
if (!file_exists($proj_file)) {
    file_put_contents($proj_file, '[]');
    $done[] = 'projects.json créé (vide)';
} else {
    $done[] = 'projects.json existe déjà (' . count(json_decode(file_get_contents($proj_file), true) ?: []) . ' projet(s))';
}

// Créer auth.json avec mot de passe par défaut
$auth_file = $data_dir . '/auth.json';
if (!file_exists($auth_file)) {
    // Mot de passe par défaut : Admin2024!  — À CHANGER immédiatement dans l'admin
    $hash = password_hash('Admin2024!', PASSWORD_BCRYPT, ['cost' => 12]);
    file_put_contents($auth_file, json_encode([
        'username' => 'rodrigue',
        'hash'     => $hash,
    ], JSON_PRETTY_PRINT));
    $done[] = 'auth.json créé (mot de passe : Admin2024!)';
} else {
    $done[] = 'auth.json existe déjà';
}

// Créer dossiers assets
foreach (['assets/images/projects', 'assets/files'] as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        $done[] = "Dossier $dir/ créé";
    }
}

// Vérifier les permissions
$checks = [
    $data_dir          => 'data/',
    $data_dir . '/..'  . '/assets/images' => 'assets/images/',
    $data_dir . '/..'  . '/assets/files'  => 'assets/files/',
];
foreach ($checks as $path => $label) {
    if (is_writable($path)) {
        $done[] = "$label : accessible en écriture ✓";
    } else {
        $errors[] = "$label : NON accessible en écriture – chmod 755";
    }
}

// Verrouiller le setup
file_put_contents($lock_file, date('Y-m-d H:i:s'));

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup – Portfolio Rodrigue Ekoko</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, sans-serif; background: #F1F5F9; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
    .card { background: #fff; border-radius: 12px; padding: 2rem; max-width: 560px; width: 100%; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    h1 { font-size: 1.4rem; color: #0F172A; margin-bottom: .3rem; }
    .sub { color: #64748B; font-size: .88rem; margin-bottom: 1.5rem; }
    ul { list-style: none; display: flex; flex-direction: column; gap: .4rem; margin-bottom: 1.5rem; }
    li { padding: 8px 12px; border-radius: 8px; font-size: .86rem; display: flex; align-items: center; gap: 8px; }
    .ok  { background: #f0fdf4; color: #15803d; }
    .err { background: #fef2f2; color: #b91c1c; }
    .warn-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; font-size: .86rem; color: #9a3412; }
    .warn-box strong { display: block; margin-bottom: .4rem; }
    .cta { display: flex; gap: .75rem; flex-wrap: wrap; }
    a.btn { padding: 10px 20px; border-radius: 8px; font-size: .88rem; font-weight: 600; text-decoration: none; }
    .btn-p { background: #2563EB; color: #fff; }
    .btn-o { background: #fff; color: #2563EB; border: 2px solid #2563EB; }
  </style>
</head>
<body>
<div class="card">
  <h1>⚙️ Initialisation du portfolio</h1>
  <p class="sub">Vérification et création des fichiers nécessaires.</p>

  <ul>
    <?php foreach ($done as $msg): ?>
      <li class="ok">✓ <?= htmlspecialchars($msg) ?></li>
    <?php endforeach; ?>
    <?php foreach ($errors as $err): ?>
      <li class="err">✗ <?= htmlspecialchars($err) ?></li>
    <?php endforeach; ?>
  </ul>

  <?php if (empty($errors)): ?>
    <div class="warn-box">
      <strong>⚠️ Actions obligatoires :</strong>
      1. Connectez-vous à l'admin et <strong>changez immédiatement le mot de passe</strong> (Admin2024! → le vôtre).<br>
      2. Remplissez vos paramètres (email, téléphone, réseaux sociaux).<br>
      3. <strong>Supprimez ce fichier setup.php</strong> de votre hébergeur après configuration.
    </div>
  <?php endif; ?>

  <div class="cta">
    <a href="admin/login.php" class="btn btn-p">→ Aller à l'administration</a>
    <a href="index.php" class="btn btn-o">Voir le portfolio</a>
  </div>
</div>
</body>
</html>
