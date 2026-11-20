<?php
require_once __DIR__ . '/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $s = get_settings();
    $s['name']          = trim($_POST['name'] ?? $s['name']);
    $s['profession_fr'] = trim($_POST['profession_fr'] ?? $s['profession_fr']);
    $s['profession_en'] = trim($_POST['profession_en'] ?? $s['profession_en']);
    $s['about_fr']      = trim($_POST['about_fr'] ?? $s['about_fr']);
    $s['about_en']      = trim($_POST['about_en'] ?? $s['about_en']);
    if (write_json(SETTINGS_FILE, $s)) {
        flash('success', 'Profil mis à jour avec succès.');
    } else {
        flash('error', 'Erreur lors de la sauvegarde.');
    }
    header('Location: profile.php');
    exit;
}

$s = get_settings();
$title = 'Profil & À propos';
require_once __DIR__ . '/partials/header.php';
?>

<div class="page-header">
  <div>
    <h1>Profil & À propos</h1>
    <p class="page-desc">Modifiez votre nom, profession et textes de présentation.</p>
  </div>
</div>

<form method="POST" action="">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <div class="form-grid-2">

    <!-- Identité -->
    <div class="card">
      <div class="card-header"><h2 class="card-title">Identité</h2></div>
      <div class="card-body">
        <div class="form-group">
          <label>Nom complet</label>
          <input type="text" name="name" value="<?= htmlspecialchars($s['name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Profession (Français)</label>
          <input type="text" name="profession_fr" value="<?= htmlspecialchars($s['profession_fr']) ?>" required>
        </div>
        <div class="form-group">
          <label>Profession (English)</label>
          <input type="text" name="profession_en" value="<?= htmlspecialchars($s['profession_en']) ?>" required>
        </div>
      </div>
    </div>

    <!-- Photo de profil actuelle -->
    <div class="card">
      <div class="card-header"><h2 class="card-title">Photo de profil</h2></div>
      <div class="card-body center-content">
        <?php
        $photo = '';
        if (!empty($s['profile_photo']) && file_exists(UPLOADS_PATH . '/' . $s['profile_photo'])) {
            $photo = '../assets/images/' . htmlspecialchars($s['profile_photo']);
        }
        ?>
        <?php if ($photo): ?>
          <img src="<?= $photo ?>" alt="Photo de profil" class="profile-preview">
        <?php else: ?>
          <div class="profile-preview-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <span>Aucune photo</span>
          </div>
        <?php endif; ?>
        <a href="files.php" class="btn btn-outline btn-sm" style="margin-top:1rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Changer la photo
        </a>
      </div>
    </div>

  </div>

  <!-- À propos -->
  <div class="card" style="margin-top:1.5rem;">
    <div class="card-header">
      <h2 class="card-title">Section "À propos"</h2>
      <div class="tab-switcher">
        <button type="button" class="tab-btn active" onclick="switchTab('fr', this)">🇫🇷 Français</button>
        <button type="button" class="tab-btn"        onclick="switchTab('en', this)">🇬🇧 English</button>
      </div>
    </div>
    <div class="card-body">
      <div id="tab-fr" class="tab-content active">
        <div class="form-group">
          <label>Présentation (Français)</label>
          <p class="form-hint">Utilisez des lignes vides pour séparer les paragraphes.</p>
          <textarea name="about_fr" rows="10"><?= htmlspecialchars($s['about_fr']) ?></textarea>
        </div>
      </div>
      <div id="tab-en" class="tab-content">
        <div class="form-group">
          <label>Presentation (English)</label>
          <p class="form-hint">Use blank lines to separate paragraphs.</p>
          <textarea name="about_en" rows="10"><?= htmlspecialchars($s['about_en']) ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      Enregistrer les modifications
    </button>
  </div>

</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
