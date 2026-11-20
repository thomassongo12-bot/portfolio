<?php
/**
 * Section Projets – lit depuis data/projects.json (mis à jour par l'admin)
 * $projects est déjà injecté par data/load.php via index.php
 */

// Sécurité : si $projects n'est pas défini (inclusion directe), le charger
if (!isset($projects)) {
    $pf = __DIR__ . '/../data/projects.json';
    $projects = file_exists($pf) ? (json_decode(file_get_contents($pf), true) ?: []) : [];
}

// Racine absolue du projet (compatible Windows et Linux)
$project_root = realpath(__DIR__ . '/..');

// Résout le chemin absolu d'une image et vérifie son existence
function project_img_abs(string $img_rel, string $root): string {
    if (!$img_rel) return '';
    return $root . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $img_rel), '\\/');
}
?>

<section id="projects" class="section-padding" aria-label="Projets">
  <div class="container">

    <div class="section-header reveal">
      <span class="badge">Work</span>
      <h2 data-i18n="projects.title">Mes Projets</h2>
      <p class="section-subtitle" data-i18n="projects.subtitle">Quelques réalisations</p>
      <div class="section-line"></div>
    </div>

    <div class="projects-grid">
      <?php foreach ($projects as $i => $p): ?>
        <?php
        $img_rel = $p['image'] ?? '';
        $img_abs = project_img_abs($img_rel, $project_root);
        $has_img = $img_abs && file_exists($img_abs);
        $img_url = $has_img ? htmlspecialchars($img_rel) : '';
        ?>
        <article class="project-card reveal" style="transition-delay:<?= ($i % 3) * 0.1 ?>s;"
                 aria-label="Projet <?= htmlspecialchars($p['name']) ?>">

          <!-- Image -->
          <div class="project-image">
            <?php if ($has_img): ?>
              <img src="<?= $img_url ?>?v=<?= filemtime($img_abs) ?>"
                   alt="Aperçu du projet <?= htmlspecialchars($p['name']) ?>"
                   loading="lazy">
            <?php else: ?>
              <div class="project-placeholder">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                  <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                </svg>
                <div class="p-name"><?= htmlspecialchars($p['name']) ?></div>
              </div>
            <?php endif; ?>
            <span class="project-type-badge"
                  data-project-type-fr="<?= htmlspecialchars($p['type_fr'] ?? '') ?>"
                  data-project-type-en="<?= htmlspecialchars($p['type_en'] ?? '') ?>">
              <?= htmlspecialchars($p['type_fr'] ?? '') ?>
            </span>
          </div>

          <!-- Body -->
          <div class="project-body">
            <h3 class="project-name"><?= htmlspecialchars($p['name']) ?></h3>

            <p class="project-desc"
               data-desc-fr="<?= htmlspecialchars($p['description_fr'] ?? '') ?>"
               data-desc-en="<?= htmlspecialchars($p['description_en'] ?? '') ?>">
              <?= htmlspecialchars($p['description_fr'] ?? '') ?>
            </p>

            <div class="project-tech-list">
              <?php foreach ($p['technologies'] ?? [] as $tech): ?>
                <span class="tech-pill"><?= htmlspecialchars($tech) ?></span>
              <?php endforeach; ?>
            </div>

            <div class="project-actions">
              <?php if (!empty(trim($p['demo_url'] ?? ''))): ?>
                <a href="<?= htmlspecialchars(trim($p['demo_url'])) ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="btn btn-primary btn-sm">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                  </svg>
                  <span data-i18n="projects.btn_view">Voir le projet</span>
                </a>
              <?php endif; ?>

              <button class="btn btn-outline btn-sm"
                      onclick="openModal(<?= (int)$p['id'] ?>)"
                      aria-label="En savoir plus sur <?= htmlspecialchars($p['name']) ?>">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <circle cx="12" cy="12" r="10"/>
                  <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span data-i18n="projects.btn_more">En savoir plus</span>
              </button>
            </div>
          </div>

        </article>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<!-- ── Modals ─────────────────────────────────────────────── -->
<?php foreach ($projects as $p):
  $img_rel = $p['image'] ?? '';
  $img_abs = project_img_abs($img_rel, $project_root);
  $has_img = $img_abs && file_exists($img_abs);
  $img_url = $has_img ? htmlspecialchars($img_rel) : '';
?>
<div class="modal-overlay"
     id="modal-<?= (int)$p['id'] ?>"
     role="dialog" aria-modal="true"
     aria-label="Détails du projet <?= htmlspecialchars($p['name']) ?>"
     onclick="closeModalOnOverlay(event, <?= (int)$p['id'] ?>)">
  <div class="modal">

    <div class="modal-image">
      <?php if ($has_img): ?>
        <img src="<?= $img_url ?>?v=<?= filemtime($img_abs) ?>"
             alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
      <?php else: ?>
        <div class="modal-image-placeholder" aria-hidden="true">
          <?= htmlspecialchars($p['name']) ?>
        </div>
      <?php endif; ?>
      <button class="modal-close" onclick="closeModal(<?= (int)$p['id'] ?>)" aria-label="Fermer">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <div class="modal-body">
      <h3 class="modal-title"><?= htmlspecialchars($p['name']) ?></h3>
      <p class="modal-type"
         data-type-fr="<?= htmlspecialchars($p['type_fr'] ?? '') ?>"
         data-type-en="<?= htmlspecialchars($p['type_en'] ?? '') ?>">
        <?= htmlspecialchars($p['type_fr'] ?? '') ?>
      </p>
      <p class="modal-desc"
         data-desc-fr="<?= htmlspecialchars($p['description_fr'] ?? '') ?>"
         data-desc-en="<?= htmlspecialchars($p['description_en'] ?? '') ?>">
        <?= htmlspecialchars($p['description_fr'] ?? '') ?>
      </p>

      <div class="modal-tech-title" data-i18n="projects.modal_technologies">Technologies utilisées</div>
      <div class="modal-tech-list">
        <?php foreach ($p['technologies'] ?? [] as $tech): ?>
          <span class="tech-pill"><?= htmlspecialchars($tech) ?></span>
        <?php endforeach; ?>
      </div>

      <div class="modal-actions">
        <?php if (!empty(trim($p['demo_url'] ?? ''))): ?>
          <a href="<?= htmlspecialchars(trim($p['demo_url'])) ?>"
             target="_blank" rel="noopener noreferrer"
             class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
              <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
            <span data-i18n="projects.modal_visit">Visiter le site</span>
          </a>
        <?php endif; ?>

        <?php if (!empty(trim($p['github_url'] ?? ''))): ?>
          <a href="<?= htmlspecialchars(trim($p['github_url'])) ?>"
             target="_blank" rel="noopener noreferrer"
             class="btn btn-outline">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>
            </svg>
            <span data-i18n="projects.modal_github">Voir le code</span>
          </a>
        <?php endif; ?>

        <button class="btn btn-outline" onclick="closeModal(<?= (int)$p['id'] ?>)">
          <span data-i18n="projects.modal_close">Fermer</span>
        </button>
      </div>
    </div>

  </div>
</div>
<?php endforeach; ?>
