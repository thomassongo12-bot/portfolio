<?php
require_once __DIR__ . '/config.php';
admin_session_start();

if (is_logged_in()) {
    header('Location: ' . admin_url('index.php'));
    exit;
}

$error      = '';
$blocked    = false;
$ip         = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$logged_out = !empty($_GET['logged_out']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals(csrf_token(), $token)) {
        security_log('csrf_fail_login', 'login form');
        $error = 'Requête invalide. Veuillez réessayer.';
    }
    // Brute-force check
    elseif (check_brute_force($ip)) {
        security_log('brute_force_blocked', $ip);
        $blocked = true;
        $error   = 'Trop de tentatives. Réessayez dans 15 minutes.';
    }
    else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $creds    = get_admin_credentials();

        // Vérification en temps constant (anti-timing attack)
        $user_ok = hash_equals($creds['username'], $username);
        $pass_ok = password_verify($password, $creds['hash']);

        if ($user_ok && $pass_ok) {
            clear_failed_logins($ip);
            login_user($username);
            security_log('login_success', $username);

            $redirect = $_GET['redirect'] ?? '';
            // Valider le redirect pour éviter open redirect
            if ($redirect && strpos($redirect, admin_url()) === 0) {
                header('Location: ' . $redirect);
            } else {
                header('Location: ' . admin_url('index.php'));
            }
            exit;
        } else {
            record_failed_login($ip);
            security_log('login_failed', $username);
            // Délai anti-brute-force (500ms)
            usleep(500000);
            $error = 'Identifiant ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion – Administration</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="login-page">

<div class="login-container">
  <div class="login-card">

    <div class="login-logo">
      <div class="login-logo-icon">R</div>
      <div>
        <div class="login-logo-title">RODRIGUE<span>.</span></div>
        <div class="login-logo-sub">Espace Administration</div>
      </div>
    </div>

    <?php if ($logged_out): ?>
      <div class="alert alert-success">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Vous avez été déconnecté avec succès.
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if (!$blocked): ?>
    <form method="POST" action="" autocomplete="on">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="form-group">
        <label for="username">Identifiant</label>
        <div class="input-wrap">
          <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          <input type="text" id="username" name="username"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 autocomplete="username" required autofocus
                 placeholder="Votre identifiant">
        </div>
      </div>

      <div class="form-group">
        <label for="password">Mot de passe</label>
        <div class="input-wrap">
          <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="password" name="password"
                 autocomplete="current-password" required
                 placeholder="••••••••">
          <button type="button" class="toggle-pass" onclick="togglePass()" tabindex="-1" aria-label="Afficher">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-login">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Se connecter
      </button>
    </form>
    <?php endif; ?>

    <a href="../index.php" class="back-link">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Retour au portfolio
    </a>

  </div>
</div>

<script>
function togglePass() {
  const inp = document.getElementById('password');
  inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
