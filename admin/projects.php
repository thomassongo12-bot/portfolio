<?php
// ── Toute la logique POST se fait AVANT tout output HTML ──
require_once __DIR__ . '/config.php';
require_login();

$projects = read_json(PROJECTS_FILE, []);
$action   = $_GET['action'] ?? 'list';
$edit_id  = isset($_GET['id']) ? (int)$_GET['id'] : null;

// ─── Handle POST ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['act'] ?? '';

    // REORDER (réponse JSON, pas de redirect)
    if ($act === 'reorder') {
        $order = json_decode($_POST['order'] ?? '[]', true);
        if (is_array($order)) {
            $indexed   = [];
            foreach ($projects as $p) $indexed[(int)$p['id']] = $p;
            $reordered = [];
            foreach ($order as $id) {
                if (isset($indexed[(int)$id])) $reordered[] = $indexed[(int)$id];
            }
            write_json(PROJECTS_FILE, array_values($reordered));
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }

    // DELETE
    if ($act === 'delete') {
        $del_id   = (int)($_POST['id'] ?? 0);
        $projects = array_values(array_filter($projects, fn($p) => (int)$p['id'] !== $del_id));
        write_json(PROJECTS_FILE, $projects);
        flash('success', 'Projet supprimé.');
        header('Location: projects.php');
        exit;
    }

    // SAVE (add / edit)
    if ($act === 'save') {
        $pid  = (int)($_POST['id'] ?? 0);
        $tech = array_values(array_filter(array_map('trim', explode(',', $_POST['technologies'] ?? ''))));

        // Image upload
        $image_path = trim($_POST['existing_image'] ?? '');
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file    = $_FILES['image'];
            $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allowed)) {
                flash('error', "Format d'image non autorisé (jpg, png, webp).");
                header('Location: projects.php?action=' . ($pid ? 'edit&id='.$pid : 'new'));
                exit;
            }
            if ($file['size'] > MAX_UPLOAD_SIZE) {
                flash('error', 'Image trop volumineuse (max 5 Mo).');
                header('Location: projects.php?action=' . ($pid ? 'edit&id='.$pid : 'new'));
                exit;
            }
            $slug     = preg_replace('/[^a-z0-9_-]/', '', strtolower(str_replace(' ', '-', $_POST['name'] ?? 'project')));
            $filename = 'projects/' . $slug . '-' . time() . '.' . $ext;
            $dest     = UPLOADS_PATH . '/' . $filename;
            if (!is_dir(UPLOADS_PATH . '/projects')) mkdir(UPLOADS_PATH . '/projects', 0755, true);
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $image_path = 'assets/images/' . $filename;
            }
        }

        $next_id = empty($projects) ? 1 : max(array_column($projects, 'id')) + 1;
        $entry   = [
            'id'             => $pid ?: $next_id,
            'name'           => trim($_POST['name'] ?? ''),
            'type_fr'        => trim($_POST['type_fr'] ?? ''),
            'type_en'        => trim($_POST['type_en'] ?? ''),
            'description_fr' => trim($_POST['description_fr'] ?? ''),
            'description_en' => trim($_POST['description_en'] ?? ''),
            'technologies'   => $tech,
            'image'          => $image_path,
            'demo_url'       => trim($_POST['demo_url'] ?? ''),
            'github_url'     => trim($_POST['github_url'] ?? ''),
            'color'          => trim($_POST['color'] ?? '#2563EB'),
        ];

        if ($pid) {
            $projects = array_map(fn($p) => (int)$p['id'] === $pid ? $entry : $p, $projects);
            flash('success', 'Projet "' . $entry['name'] . '" mis à jour.');
        } else {
            $projects[] = $entry;
            flash('success', 'Projet "' . $entry['name'] . '" ajouté.');
        }
        write_json(PROJECTS_FILE, $projects);
        header('Location: projects.php');
        exit;
    }
}

// ─── Projet à éditer ─────────────────────────────────────
$editing = null;
if ($action === 'edit' && $edit_id) {
    foreach ($projects as $p) {
        if ((int)$p['id'] === $edit_id) { $editing = $p; break; }
    }
    if (!$editing) {
        flash('error', 'Projet introuvable.');
        header('Location: projects.php');
        exit;
    }
}

// ── Maintenant on peut afficher le HTML ──
$title = 'Projets';
require_once __DIR__ . '/partials/header.php';
?>

<div class="page-header">
  <div>
    <h1>Projets</h1>
    <p class="page-desc"><?= count($projects) ?> projet(s) au total</p>
  </div>
  <?php if ($action === 'list'): ?>
  <a href="?action=new" class="btn btn-primary btn-sm">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nouveau projet
  </a>
  <?php else: ?>
  <a href="projects.php" class="btn btn-outline btn-sm">← Retour à la liste</a>
  <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>

<div class="card">
  <div class="card-header">
    <h2 class="card-title">Liste des projets</h2>
    <span class="card-hint">Glissez-déposez pour réordonner</span>
  </div>
  <div class="card-body p0">
    <?php if (empty($projects)): ?>
      <div class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        <p>Aucun projet. <a href="?action=new">Ajoutez le premier.</a></p>
      </div>
    <?php else: ?>
      <ul class="project-list" id="projectList">
        <?php foreach ($projects as $p): ?>
        <li class="project-row" data-id="<?= (int)$p['id'] ?>">
          <span class="drag-handle" title="Réordonner">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="18" x2="16" y2="18"/></svg>
          </span>
          <div class="project-row-img">
            <?php $img = !empty($p['image']) && file_exists(ROOT_PATH . '/' . $p['image']) ? '../' . $p['image'] : ''; ?>
            <?php if ($img): ?>
              <img src="<?= htmlspecialchars($img) ?>" alt="">
            <?php else: ?>
              <div class="project-row-img-ph"><?= htmlspecialchars(strtoupper(substr($p['name'], 0, 1))) ?></div>
            <?php endif; ?>
          </div>
          <div class="project-row-info">
            <div class="project-row-name"><?= htmlspecialchars($p['name']) ?></div>
            <div class="project-row-type"><?= htmlspecialchars($p['type_fr'] ?? '') ?></div>
            <div class="project-row-tech">
              <?php foreach (array_slice($p['technologies'] ?? [], 0, 4) as $t): ?>
                <span class="tech-mini"><?= htmlspecialchars($t) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="project-row-links">
            <?php if (!empty($p['demo_url'])): ?>
              <a href="<?= htmlspecialchars($p['demo_url']) ?>" target="_blank" class="row-link" title="Voir le site">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              </a>
            <?php endif; ?>
            <?php if (!empty($p['github_url'])): ?>
              <a href="<?= htmlspecialchars($p['github_url']) ?>" target="_blank" class="row-link" title="GitHub">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
              </a>
            <?php endif; ?>
          </div>
          <div class="project-row-actions">
            <a href="?action=edit&id=<?= (int)$p['id'] ?>" class="btn btn-outline btn-xs">Modifier</a>
            <button type="button" class="btn btn-danger btn-xs"
              onclick="confirmDelete(<?= (int)$p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')">
              Supprimer
            </button>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<form method="POST" id="deleteForm" style="display:none;">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <input type="hidden" name="act" value="delete">
  <input type="hidden" name="id"  id="deleteId">
</form>

<?php else: // ── FORMULAIRE AJOUT / MODIFICATION ── ?>

<form method="POST" action="projects.php" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token"      value="<?= csrf_token() ?>">
  <input type="hidden" name="act"             value="save">
  <input type="hidden" name="id"              value="<?= $editing ? (int)$editing['id'] : 0 ?>">
  <input type="hidden" name="existing_image"  value="<?= htmlspecialchars($editing['image'] ?? '') ?>">

  <div class="form-grid-2">

    <div>
      <!-- Infos générales -->
      <div class="card">
        <div class="card-header"><h2 class="card-title">Informations générales</h2></div>
        <div class="card-body">
          <div class="form-group">
            <label>Nom du projet <span class="required">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($editing['name'] ?? '') ?>"
                   placeholder="ex: WALTEN" required>
          </div>
          <div class="form-group">
            <label>Technologies <span class="required">*</span></label>
            <input type="text" name="technologies"
                   value="<?= htmlspecialchars(implode(', ', $editing['technologies'] ?? [])) ?>"
                   placeholder="React, TypeScript, NestJS" required>
            <p class="form-hint">Séparez par des virgules.</p>
          </div>
          <div class="form-row-2">
            <div class="form-group">
              <label>URL de démo</label>
              <input type="url" name="demo_url"
                     value="<?= htmlspecialchars($editing['demo_url'] ?? '') ?>"
                     placeholder="https://...">
              <p class="form-hint">Laisser vide → bouton masqué.</p>
            </div>
            <div class="form-group">
              <label>URL GitHub</label>
              <input type="url" name="github_url"
                     value="<?= htmlspecialchars($editing['github_url'] ?? '') ?>"
                     placeholder="https://github.com/...">
            </div>
          </div>
          <div class="form-group">
            <label>Couleur</label>
            <div class="color-pick">
              <input type="color" name="color" value="<?= htmlspecialchars($editing['color'] ?? '#2563EB') ?>">
              <span class="color-hint">Accentuation visuelle de la carte</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Image -->
      <div class="card" style="margin-top:1.5rem;">
        <div class="card-header"><h2 class="card-title">Image du projet</h2></div>
        <div class="card-body">
          <?php if (!empty($editing['image']) && file_exists(ROOT_PATH . '/' . $editing['image'])): ?>
            <div class="current-image">
              <img src="../<?= htmlspecialchars($editing['image']) ?>" alt="Image actuelle">
              <span class="current-image-label">Image actuelle (conservée si aucune nouvelle image)</span>
            </div>
          <?php endif; ?>
          <div class="upload-zone" id="uploadZone">
            <input type="file" name="image" id="imageInput" accept="image/*" class="upload-input">
            <div class="upload-content" id="uploadContent">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              <p><strong>Cliquez</strong> ou glissez une image</p>
              <span>JPG, PNG, WebP – max 5 Mo</span>
            </div>
            <div class="upload-preview" id="uploadPreview" style="display:none;">
              <img id="previewImg" src="" alt="Aperçu">
              <button type="button" onclick="clearUpload()" class="upload-clear">×</button>
            </div>
          </div>
          <p class="form-hint">Recommandé : 800×450 px (16:9)</p>
        </div>
      </div>
    </div>

    <!-- Descriptions FR / EN -->
    <div>
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Descriptions</h2>
          <div class="tab-switcher">
            <button type="button" class="tab-btn active" onclick="switchTab('fr', this)">🇫🇷 Français</button>
            <button type="button" class="tab-btn"        onclick="switchTab('en', this)">🇬🇧 English</button>
          </div>
        </div>
        <div class="card-body">
          <div id="tab-fr" class="tab-content active">
            <div class="form-group">
              <label>Type de projet (FR) <span class="required">*</span></label>
              <input type="text" name="type_fr"
                     value="<?= htmlspecialchars($editing['type_fr'] ?? '') ?>"
                     placeholder="Application SaaS de gestion commerciale" required>
            </div>
            <div class="form-group">
              <label>Description (FR) <span class="required">*</span></label>
              <textarea name="description_fr" rows="7" required
                        placeholder="Description du projet en français..."><?= htmlspecialchars($editing['description_fr'] ?? '') ?></textarea>
            </div>
          </div>
          <div id="tab-en" class="tab-content">
            <div class="form-group">
              <label>Project type (EN)</label>
              <input type="text" name="type_en"
                     value="<?= htmlspecialchars($editing['type_en'] ?? '') ?>"
                     placeholder="Commercial Management SaaS Application">
            </div>
            <div class="form-group">
              <label>Description (EN)</label>
              <textarea name="description_en" rows="7"
                        placeholder="Project description in English..."><?= htmlspecialchars($editing['description_en'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="form-actions">
    <a href="projects.php" class="btn btn-outline">Annuler</a>
    <button type="submit" class="btn btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      <?= $editing ? 'Mettre à jour le projet' : 'Ajouter le projet' ?>
    </button>
  </div>

</form>
<?php endif; ?>

<script>
function confirmDelete(id, name) {
  if (confirm('Supprimer "' + name + '" ?\nCette action est irréversible.')) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteForm').submit();
  }
}

const imageInput = document.getElementById('imageInput');
if (imageInput) {
  imageInput.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('previewImg').src = e.target.result;
      document.getElementById('uploadPreview').style.display = 'block';
      document.getElementById('uploadContent').style.display = 'none';
    };
    reader.readAsDataURL(file);
  });
  const zone = document.getElementById('uploadZone');
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('drag');
    const f = e.dataTransfer.files[0];
    if (f && f.type.startsWith('image/')) {
      const dt = new DataTransfer(); dt.items.add(f);
      imageInput.files = dt.files;
      imageInput.dispatchEvent(new Event('change'));
    }
  });
}
function clearUpload() {
  imageInput.value = '';
  document.getElementById('uploadPreview').style.display = 'none';
  document.getElementById('uploadContent').style.display = 'flex';
}

// Drag & drop reorder (sans lib externe)
const list = document.getElementById('projectList');
if (list) {
  let dragging = null;
  list.querySelectorAll('.project-row').forEach(row => {
    row.setAttribute('draggable', 'true');
    row.addEventListener('dragstart', () => { dragging = row; row.style.opacity = '.4'; });
    row.addEventListener('dragend', () => {
      row.style.opacity = '1';
      const order = [...list.querySelectorAll('.project-row')].map(r => r.dataset.id);
      const csrf  = document.querySelector('#deleteForm input[name="csrf_token"]').value;
      fetch('projects.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'act=reorder&order=' + encodeURIComponent(JSON.stringify(order)) + '&csrf_token=' + encodeURIComponent(csrf)
      });
    });
    row.addEventListener('dragover', e => {
      e.preventDefault();
      if (!dragging || dragging === row) return;
      const mid = row.getBoundingClientRect().top + row.getBoundingClientRect().height / 2;
      list.insertBefore(dragging, e.clientY < mid ? row : row.nextSibling);
    });
  });
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
