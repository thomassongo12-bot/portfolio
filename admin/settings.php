<?php
require_once __DIR__ . '/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $s      = get_settings();
    $fields = ['email','phone','whatsapp','location_fr','location_en','linkedin','github','site_url'];
    foreach ($fields as $f) {
        $s[$f] = trim($_POST[$f] ?? $s[$f]);
    }
    if (write_json(SETTINGS_FILE, $s)) {
        flash('success', 'Paramètres enregistrés.');
    } else {
        flash('error', 'Erreur lors de la sauvegarde.');
    }
    header('Location: settings.php');
    exit;
}

$s     = get_settings();
$title = 'Paramètres';
require_once __DIR__ . '/partials/header.php';
?>

<div class="page-header">
  <div>
    <h1>Paramètres</h1>
    <p class="page-desc">Coordonnées, réseaux sociaux et URL du site.</p>
  </div>
</div>

<form method="POST" action="">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <!-- Contact -->
  <div class="card">
    <div class="card-header"><h2 class="card-title">Coordonnées</h2></div>
    <div class="card-body">
      <div class="form-row-2">
        <div class="form-group">
          <label>Adresse e-mail <span class="required">*</span></label>
          <div class="input-wrap">
            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <input type="email" name="email" value="<?= htmlspecialchars($s['email']) ?>" required
                   placeholder="votre@email.com">
          </div>
          <p class="form-hint">Utilisé pour le formulaire de contact et l'affichage.</p>
        </div>
        <div class="form-group">
          <label>Téléphone / WhatsApp (affichage)</label>
          <div class="input-wrap">
            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.4 2 2 0 0 1 3.6 2.21h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.1 6.1l1.27-.85a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <input type="text" name="phone" value="<?= htmlspecialchars($s['phone']) ?>"
                   placeholder="+237 6XX XXX XXX">
          </div>
        </div>
      </div>
      <div class="form-row-2">
        <div class="form-group">
          <label>Numéro WhatsApp (lien)</label>
          <div class="input-wrap">
            <span class="input-prefix">+</span>
            <input type="text" name="whatsapp" value="<?= htmlspecialchars($s['whatsapp']) ?>"
                   placeholder="237600000000" style="padding-left:2.2rem;">
          </div>
          <p class="form-hint">Format international sans le + (ex: 237600000000)</p>
        </div>
        <div class="form-group">
          <label>Localisation (FR)</label>
          <input type="text" name="location_fr" value="<?= htmlspecialchars($s['location_fr']) ?>"
                 placeholder="Cameroun">
        </div>
      </div>
      <div class="form-group" style="max-width:320px;">
        <label>Localisation (EN)</label>
        <input type="text" name="location_en" value="<?= htmlspecialchars($s['location_en']) ?>"
               placeholder="Cameroon">
      </div>
    </div>
  </div>

  <!-- Réseaux sociaux -->
  <div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h2 class="card-title">Réseaux sociaux</h2></div>
    <div class="card-body">
      <div class="form-row-2">
        <div class="form-group">
          <label>LinkedIn</label>
          <div class="input-wrap">
            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
            <input type="url" name="linkedin" value="<?= htmlspecialchars($s['linkedin']) ?>"
                   placeholder="https://www.linkedin.com/in/votre-profil">
          </div>
        </div>
        <div class="form-group">
          <label>GitHub</label>
          <div class="input-wrap">
            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
            <input type="url" name="github" value="<?= htmlspecialchars($s['github']) ?>"
                   placeholder="https://github.com/votre-username">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- SEO / Site -->
  <div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h2 class="card-title">SEO & URL du site</h2></div>
    <div class="card-body">
      <div class="form-group">
        <label>URL du site (sans slash final)</label>
        <div class="input-wrap">
          <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          <input type="url" name="site_url" value="<?= htmlspecialchars($s['site_url']) ?>"
                 placeholder="https://rodrigue-ekoko.com">
        </div>
        <p class="form-hint">Utilisé dans les balises Open Graph et le sitemap.</p>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      Enregistrer les paramètres
    </button>
  </div>

</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
