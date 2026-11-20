<?php
require_once __DIR__ . '/config.php';
require_login();

$s = get_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['act'] ?? '';

    if ($act === 'upload_photo') {
        if (empty($_FILES['photo']['name'])) {
            flash('error', 'Aucun fichier sélectionné.');
        } else {
            $file = $_FILES['photo'];
            if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
                flash('error', 'Format non autorisé. Utilisez JPG, PNG ou WebP.');
            } elseif ($file['size'] > MAX_UPLOAD_SIZE) {
                flash('error', 'Fichier trop volumineux (max 5 Mo).');
            } else {
                $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = 'profile.' . $ext;
                $dest     = UPLOADS_PATH . '/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $s['profile_photo'] = $filename;
                    write_json(SETTINGS_FILE, $s);
                    flash('success', 'Photo de profil mise à jour.');
                } else {
                    flash('error', 'Erreur lors de l\'upload. Vérifiez les permissions du dossier.');
                }
            }
        }
        header('Location: files.php'); exit;
    }

    // Upload CV
    if ($act === 'upload_cv') {
        if (empty($_FILES['cv']['name'])) {
            flash('error', 'Aucun fichier sélectionné.');
        } else {
            $file = $_FILES['cv'];
            if ($file['type'] !== 'application/pdf') {
                flash('error', 'Seul le format PDF est accepté.');
            } elseif ($file['size'] > MAX_UPLOAD_SIZE) {
                flash('error', 'Fichier trop volumineux (max 5 Mo).');
            } else {
                $filename = trim($_POST['cv_filename'] ?? 'CV-Rodrigue-Ekoko.pdf');
                if (!preg_match('/\.pdf$/i', $filename)) $filename .= '.pdf';
                $dest = FILES_PATH . '/' . $filename;
                if (!is_dir(FILES_PATH)) mkdir(FILES_PATH, 0755, true);
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $s['cv_filename'] = $filename;
                    write_json(SETTINGS_FILE, $s);
                    flash('success', 'CV mis à jour : ' . $filename);
                } else {
                    flash('error', 'Erreur lors de l\'upload. Vérifiez les permissions du dossier.');
                }
            }
        }
        header('Location: files.php'); exit;
    }

    // Supprimer photo
    if ($act === 'delete_photo') {
        if (!empty($s['profile_photo'])) {
            $path = UPLOADS_PATH . '/' . $s['profile_photo'];
            if (file_exists($path)) unlink($path);
        }
        $s['profile_photo'] = '';
        write_json(SETTINGS_FILE, $s);
        flash('success', 'Photo de profil supprimée.');
        header('Location: files.php'); exit;
    }

    // Supprimer CV
    if ($act === 'delete_cv') {
        $path = FILES_PATH . '/' . $s['cv_filename'];
        if (file_exists($path)) unlink($path);
        flash('success', 'CV supprimé.');
        header('Location: files.php'); exit;
    }
}

$s          = get_settings();
$photo_path = !empty($s['profile_photo']) ? UPLOADS_PATH . '/' . $s['profile_photo'] : '';
$photo_url  = $photo_path && file_exists($photo_path) ? '../assets/images/' . htmlspecialchars($s['profile_photo']) : '';
$cv_path    = FILES_PATH . '/' . $s['cv_filename'];
$cv_exists  = file_exists($cv_path);
$cv_size    = $cv_exists ? round(filesize($cv_path) / 1024) . ' Ko' : '';

$title = 'Fichiers & Médias';
require_once __DIR__ . '/partials/header.php';
?>

<div class="page-header">
  <div>
    <h1>Fichiers & Médias</h1>
    <p class="page-desc">Gérez votre photo de profil et votre CV.</p>
  </div>
</div>

<div class="form-grid-2">

  <!-- Photo de profil -->
  <div class="card">
    <div class="card-header"><h2 class="card-title">Photo de profil</h2></div>
    <div class="card-body">

      <!-- Current photo -->
      <div class="file-preview">
        <?php if ($photo_url): ?>
          <img src="<?= $photo_url ?>?v=<?= time() ?>" alt="Photo de profil" class="photo-preview-lg">
          <div class="file-info">
            <span class="file-name"><?= htmlspecialchars($s['profile_photo']) ?></span>
            <span class="file-size"><?= round(filesize($photo_path) / 1024) ?> Ko</span>
          </div>
          <form method="POST" style="margin-top:.5rem;">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="act" value="delete_photo">
            <button type="submit" class="btn btn-danger btn-xs"
              onclick="return confirm('Supprimer la photo de profil ?')">Supprimer</button>
          </form>
        <?php else: ?>
          <div class="file-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <p>Aucune photo</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Upload form -->
      <form method="POST" enctype="multipart/form-data" style="margin-top:1.5rem;">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="act" value="upload_photo">
        <div class="upload-zone" id="photoZone">
          <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" class="upload-input">
          <div class="upload-content" id="photoContent">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <p><strong>Cliquez</strong> ou glissez votre photo</p>
            <span>JPG, PNG, WebP – max 5 Mo</span>
          </div>
          <div class="upload-preview" id="photoPreview" style="display:none;">
            <img id="photoPreviewImg" src="" alt="Aperçu">
          </div>
        </div>
        <p class="form-hint">Dimensions recommandées : 500×500 px (carré)</p>
        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:.75rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Uploader la photo
        </button>
      </form>
    </div>
  </div>

  <!-- CV -->
  <div class="card">
    <div class="card-header"><h2 class="card-title">CV (PDF)</h2></div>
    <div class="card-body">

      <!-- Current CV -->
      <div class="file-preview">
        <?php if ($cv_exists): ?>
          <div class="cv-preview">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <div>
              <div class="file-name"><?= htmlspecialchars($s['cv_filename']) ?></div>
              <div class="file-size"><?= $cv_size ?></div>
            </div>
          </div>
          <div style="display:flex;gap:.5rem;margin-top:.75rem;flex-wrap:wrap;">
            <a href="../assets/files/<?= urlencode($s['cv_filename']) ?>" target="_blank" class="btn btn-outline btn-xs">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              Ouvrir
            </a>
            <a href="../assets/files/<?= urlencode($s['cv_filename']) ?>" download class="btn btn-outline btn-xs">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              Télécharger
            </a>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <input type="hidden" name="act" value="delete_cv">
              <button type="submit" class="btn btn-danger btn-xs"
                onclick="return confirm('Supprimer le CV ?')">Supprimer</button>
            </form>
          </div>
        <?php else: ?>
          <div class="file-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <p>Aucun CV uploadé</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Upload form -->
      <form method="POST" enctype="multipart/form-data" style="margin-top:1.5rem;">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="act" value="upload_cv">

        <div class="form-group">
          <label>Nom du fichier</label>
          <input type="text" name="cv_filename"
                 value="<?= htmlspecialchars($s['cv_filename']) ?>"
                 placeholder="CV-Rodrigue-Ekoko.pdf">
          <p class="form-hint">Ce nom sera utilisé pour le téléchargement.</p>
        </div>

        <div class="upload-zone" id="cvZone">
          <input type="file" name="cv" id="cvInput" accept="application/pdf" class="upload-input">
          <div class="upload-content" id="cvContent">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <p><strong>Cliquez</strong> ou glissez votre CV</p>
            <span>PDF uniquement – max 5 Mo</span>
          </div>
          <div class="upload-preview cv-selected" id="cvPreview" style="display:none;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span id="cvFilename">fichier.pdf</span>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:.75rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Uploader le CV
        </button>
      </form>
    </div>
  </div>

</div>

<!-- Images des projets -->
<div class="card" style="margin-top:1.5rem;">
  <div class="card-header">
    <h2 class="card-title">Images des projets</h2>
    <a href="projects.php" class="card-link">Gérer les projets →</a>
  </div>
  <div class="card-body">
    <?php
    $proj_dir   = UPLOADS_PATH . '/projects';
    $proj_files = [];
    if (is_dir($proj_dir)) {
        foreach (glob($proj_dir . '/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE) as $f) {
            $proj_files[] = $f;
        }
    }
    ?>
    <?php if (empty($proj_files)): ?>
      <div class="empty-state small">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        <p>Aucune image de projet. Ajoutez des images depuis la gestion des projets.</p>
      </div>
    <?php else: ?>
      <div class="media-grid">
        <?php foreach ($proj_files as $f): ?>
          <?php $fname = basename($f); $fsize = round(filesize($f)/1024) . ' Ko'; ?>
          <div class="media-item">
            <img src="../assets/images/projects/<?= htmlspecialchars($fname) ?>?v=<?= filemtime($f) ?>" alt="<?= htmlspecialchars($fname) ?>">
            <div class="media-name"><?= htmlspecialchars($fname) ?></div>
            <div class="media-size"><?= $fsize ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Photo preview
const photoInput = document.getElementById('photoInput');
if (photoInput) {
  photoInput.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('photoPreviewImg').src = e.target.result;
      document.getElementById('photoPreview').style.display = 'block';
      document.getElementById('photoContent').style.display = 'none';
    };
    reader.readAsDataURL(file);
  });
}

// CV preview
const cvInput = document.getElementById('cvInput');
if (cvInput) {
  cvInput.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    document.getElementById('cvFilename').textContent = file.name;
    document.getElementById('cvPreview').style.display = 'flex';
    document.getElementById('cvContent').style.display = 'none';
  });
}

// Drag & drop for photo zone
['photoZone','cvZone'].forEach(id => {
  const zone = document.getElementById(id);
  if (!zone) return;
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag');
    const input = zone.querySelector('input[type="file"]');
    const file  = e.dataTransfer.files[0];
    if (file) {
      const dt = new DataTransfer();
      dt.items.add(file);
      input.files = dt.files;
      input.dispatchEvent(new Event('change'));
    }
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
