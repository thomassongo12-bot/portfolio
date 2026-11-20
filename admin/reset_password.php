<?php
/**
 * Reset du mot de passe admin
 * Accès : https://votre-site.com/admin/reset_password.php
 * SUPPRIMER ce fichier immédiatement après utilisation !
 */

// Clé de sécurité unique – empêche l'accès non autorisé
// Changez cette valeur AVANT d'uploader ce fichier
define('RESET_KEY', 'rodrigue2024portfolio');

$message = '';
$done    = false;

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key      = $_POST['key']      ?? '';
    $username = trim($_POST['username'] ?? 'rodrigue');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    if (!hash_equals(RESET_KEY, $key)) {
        $message = 'Clé de sécurité incorrecte.';
    } elseif (strlen($password) < 6) {
        $message = 'Mot de passe trop court (min. 6 caractères).';
    } elseif ($password !== $confirm) {
        $message = 'Les mots de passe ne correspondent pas.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $ok   = write_json(AUTH_FILE, [
            'username' => $username,
            'hash'     => $hash,
        ]);
        if ($ok) {
            $done    = true;
            $message = "Mot de passe réinitialisé pour l'utilisateur \"$username\". Supprimez ce fichier maintenant !";
        } else {
            $message = 'Erreur écriture fichier. Vérifiez les permissions du dossier data/.';
        }
    }
}

// Test rapide de l'état actuel
$auth_exists = file_exists(AUTH_FILE);
$auth_ok     = false;
$current_user = '';
if ($auth_exists) {
    $d = json_decode(file_get_contents(AUTH_FILE), true) ?: [];
    $auth_ok      = !empty($d['hash']);
    $current_user = $d['username'] ?? '?';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Mot de passe Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:system-ui,sans-serif;background:#0F172A;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:1rem}
    .card{background:#fff;border-radius:16px;padding:2rem;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.4)}
    h1{font-size:1.3rem;color:#0F172A;margin-bottom:.3rem}
    .sub{font-size:.85rem;color:#64748B;margin-bottom:1.5rem}
    .status{padding:10px 14px;border-radius:8px;font-size:.84rem;margin-bottom:1.25rem}
    .ok  {background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0}
    .err {background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
    .info{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe}
    label{display:block;font-size:.82rem;font-weight:600;color:#0F172A;margin-bottom:.3rem}
    input{width:100%;padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:.9rem;margin-bottom:1rem;outline:none}
    input:focus{border-color:#2563EB;box-shadow:0 0 0 3px rgba(37,99,235,.1)}
    button{width:100%;padding:11px;background:#2563EB;color:#fff;border:none;border-radius:8px;font-size:.92rem;font-weight:700;cursor:pointer}
    button:hover{background:#1d4ed8}
    .warn{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;padding:10px 14px;border-radius:8px;font-size:.82rem;margin-top:1rem;line-height:1.5}
  </style>
</head>
<body>
<div class="card">
  <h1>🔑 Réinitialisation mot de passe</h1>
  <p class="sub">Admin – Portfolio Rodrigue Ekoko</p>

  <!-- Statut actuel -->
  <div class="status <?= $auth_exists && $auth_ok ? 'ok' : 'err' ?>">
    <?php if ($auth_exists && $auth_ok): ?>
      auth.json présent · Utilisateur actuel : <strong><?= htmlspecialchars($current_user) ?></strong>
    <?php else: ?>
      auth.json absent ou invalide → sera créé
    <?php endif; ?>
  </div>

  <?php if ($message): ?>
    <div class="status <?= $done ? 'ok' : 'err' ?>"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if (!$done): ?>
  <form method="POST">
    <label>Clé de sécurité</label>
    <input type="text" name="key" placeholder="rodrigue2024portfolio" required autocomplete="off">

    <label>Nom d'utilisateur</label>
    <input type="text" name="username" value="rodrigue" required autocomplete="off">

    <label>Nouveau mot de passe</label>
    <input type="password" name="password" placeholder="Min. 6 caractères" required>

    <label>Confirmer le mot de passe</label>
    <input type="password" name="confirm" placeholder="Répétez le mot de passe" required>

    <button type="submit">Réinitialiser le mot de passe</button>
  </form>
  <?php else: ?>
    <a href="login.php" style="display:block;text-align:center;padding:11px;background:#2563EB;color:#fff;border-radius:8px;font-weight:700;text-decoration:none;">→ Se connecter</a>
  <?php endif; ?>

  <div class="warn">
    ⚠️ <strong>Supprimez ce fichier immédiatement</strong> après avoir réinitialisé votre mot de passe.<br>
    Chemin : <code>admin/reset_password.php</code>
  </div>
</div>
</body>
</html>
