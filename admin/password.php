<?php
require_once __DIR__ . '/config.php';
require_login();

$pw_file = AUTH_FILE;

function get_password_hash(): string {
    $creds = get_admin_credentials();
    return $creds['hash'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $stored  = get_password_hash();

    if (!password_verify($current, $stored)) {
        flash('error', 'Mot de passe actuel incorrect.');
    } elseif (strlen($new) < 8) {
        flash('error', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
    } elseif ($new !== $confirm) {
        flash('error', 'Les deux nouveaux mots de passe ne correspondent pas.');
    } else {
        $creds = get_admin_credentials();
        write_json($pw_file, [
            'username' => $creds['username'],
            'hash'     => password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);
        security_log('password_changed', $creds['username']);
        flash('success', 'Mot de passe mis à jour avec succès.');
    }
    header('Location: password.php');
    exit;
}

$title = 'Changer le mot de passe';
require_once __DIR__ . '/partials/header.php';
?>

<div class="page-header">
  <div>
    <h1>Changer le mot de passe</h1>
    <p class="page-desc">Mettez à jour votre mot de passe d'accès à l'administration.</p>
  </div>
</div>

<div class="card" style="max-width:520px;">
  <div class="card-header"><h2 class="card-title">Nouveau mot de passe</h2></div>
  <div class="card-body">
    <form method="POST" action="">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="form-group">
        <label for="current_password">Mot de passe actuel</label>
        <div class="input-wrap">
          <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="current_password" name="current_password"
                 autocomplete="current-password" required placeholder="••••••••">
        </div>
      </div>

      <div class="form-group">
        <label for="new_password">Nouveau mot de passe</label>
        <div class="input-wrap">
          <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="new_password" name="new_password"
                 autocomplete="new-password" required placeholder="Min. 8 caractères"
                 minlength="8" oninput="checkStrength(this.value)">
        </div>
        <div class="password-strength" id="strengthBar">
          <div class="strength-fill" id="strengthFill"></div>
        </div>
        <div class="strength-label" id="strengthLabel"></div>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirmer le nouveau mot de passe</label>
        <div class="input-wrap">
          <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="confirm_password" name="confirm_password"
                 autocomplete="new-password" required placeholder="••••••••">
        </div>
      </div>

      <div class="alert alert-info" style="margin-bottom:1rem;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Le mot de passe par défaut est <strong>admin2024</strong>. Changez-le immédiatement.
      </div>

      <button type="submit" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Mettre à jour le mot de passe
      </button>
    </form>
  </div>
</div>

<script>
function checkStrength(val) {
  const fill  = document.getElementById('strengthFill');
  const label = document.getElementById('strengthLabel');
  if (!fill) return;
  let score = 0;
  if (val.length >= 8)  score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const levels = [
    { pct: '0%',   color: 'transparent', text: '' },
    { pct: '25%',  color: '#ef4444',     text: 'Très faible' },
    { pct: '50%',  color: '#f97316',     text: 'Faible' },
    { pct: '75%',  color: '#eab308',     text: 'Moyen' },
    { pct: '90%',  color: '#22c55e',     text: 'Fort' },
    { pct: '100%', color: '#16a34a',     text: 'Très fort' },
  ];
  const lvl   = levels[Math.min(score, 5)];
  fill.style.width      = lvl.pct;
  fill.style.background = lvl.color;
  label.textContent     = lvl.text;
  label.style.color     = lvl.color;
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
