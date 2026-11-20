<?php
require_once __DIR__ . '/config.php';
require_login();

$settings     = get_settings();
$projects     = read_json(PROJECTS_FILE, []);
$messages     = read_json(MESSAGES_FILE, []);
$unreadM      = count(array_filter($messages, fn($m) => empty($m['read'])));
$totalMsg     = count($messages);
$totalProj    = count($projects);
$cv_exists    = file_exists(FILES_PATH . '/' . ($settings['cv_filename'] ?? 'CV.pdf'));
$photo_exists = !empty($settings['profile_photo']) && file_exists(UPLOADS_PATH . '/' . $settings['profile_photo']);

$title = 'Tableau de bord';
require_once __DIR__ . '/partials/header.php';

// Messages récents (5 derniers)
$recent = array_slice(array_reverse($messages), 0, 5);
?>

<div class="page-header">
  <div>
    <h1>Tableau de bord</h1>
    <p class="page-desc">Bienvenue dans votre espace d'administration.</p>
  </div>
  <a href="../index.php" target="_blank" class="btn btn-outline btn-sm">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
    Voir le portfolio
  </a>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon stat-blue">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $totalProj ?></div>
      <div class="stat-label">Projets</div>
    </div>
    <a href="projects.php" class="stat-link">Gérer →</a>
  </div>

  <div class="stat-card">
    <div class="stat-icon stat-green">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $totalMsg ?></div>
      <div class="stat-label">Messages reçus</div>
    </div>
    <a href="messages.php" class="stat-link">Voir →</a>
  </div>

  <div class="stat-card">
    <div class="stat-icon <?= $unreadM > 0 ? 'stat-orange' : 'stat-green' ?>">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $unreadM ?></div>
      <div class="stat-label">Non lus</div>
    </div>
    <a href="messages.php?filter=unread" class="stat-link">Voir →</a>
  </div>

  <div class="stat-card">
    <div class="stat-icon <?= $cv_exists ? 'stat-green' : 'stat-red' ?>">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $cv_exists ? 'Présent' : 'Absent' ?></div>
      <div class="stat-label">CV</div>
    </div>
    <a href="files.php" class="stat-link">Gérer →</a>
  </div>
</div>

<!-- Checklist + Recent messages -->
<div class="dash-grid">

  <!-- Checklist -->
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Checklist de mise en ligne
      </h2>
    </div>
    <div class="card-body">
      <?php
      $checks = [
        ['ok' => $photo_exists,                                            'label' => 'Photo de profil ajoutée',         'link' => 'files.php'],
        ['ok' => $cv_exists,                                               'label' => 'CV uploadé',                       'link' => 'files.php'],
        ['ok' => $settings['email'] !== 'votre.email@example.com',         'label' => 'Adresse e-mail configurée',        'link' => 'settings.php'],
        ['ok' => $settings['phone'] !== '+237 6XX XXX XXX',                'label' => 'Numéro de téléphone renseigné',    'link' => 'settings.php'],
        ['ok' => strpos($settings['linkedin'], 'votre-profil') === false,  'label' => 'Lien LinkedIn configuré',          'link' => 'settings.php'],
        ['ok' => strpos($settings['github'], 'votre-username') === false,  'label' => 'Lien GitHub configuré',            'link' => 'settings.php'],
        ['ok' => strpos($settings['site_url'], 'rodrigue-ekoko.com')===false || $settings['site_url'] !== 'https://rodrigue-ekoko.com', 'label' => 'URL du site mise à jour', 'link' => 'settings.php'],
      ];
      $done = count(array_filter($checks, fn($c) => $c['ok']));
      $total = count($checks);
      $pct = round($done / $total * 100);
      ?>
      <div class="checklist-progress">
        <div class="checklist-pct"><?= $done ?>/<?= $total ?> complété</div>
        <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct ?>%"></div></div>
      </div>
      <ul class="checklist">
        <?php foreach ($checks as $c): ?>
        <li class="check-item <?= $c['ok'] ? 'done' : 'todo' ?>">
          <?php if ($c['ok']): ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
          <?php else: ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          <?php endif; ?>
          <span><?= htmlspecialchars($c['label']) ?></span>
          <?php if (!$c['ok']): ?>
            <a href="<?= $c['link'] ?>" class="check-fix">Configurer</a>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <!-- Recent messages -->
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Messages récents
      </h2>
      <a href="messages.php" class="card-link">Tous les messages →</a>
    </div>
    <div class="card-body">
      <?php if (empty($recent)): ?>
        <div class="empty-state">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <p>Aucun message reçu pour l'instant.</p>
        </div>
      <?php else: ?>
        <ul class="msg-list">
          <?php foreach ($recent as $msg): ?>
          <li class="msg-item <?= empty($msg['read']) ? 'unread' : '' ?>">
            <div class="msg-avatar"><?= strtoupper(mb_substr($msg['name'] ?? 'A', 0, 1)) ?></div>
            <div class="msg-info">
              <div class="msg-name"><?= htmlspecialchars($msg['name'] ?? '') ?></div>
              <div class="msg-subject"><?= htmlspecialchars(mb_substr($msg['subject'] ?? '', 0, 50)) ?></div>
              <div class="msg-date"><?= date('d/m/Y H:i', strtotime($msg['date'] ?? 'now')) ?></div>
            </div>
            <?php if (empty($msg['read'])): ?>
              <span class="unread-dot" title="Non lu"></span>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- Quick actions -->
<div class="card">
  <div class="card-header">
    <h2 class="card-title">Actions rapides</h2>
  </div>
  <div class="card-body">
    <div class="quick-actions">
      <a href="projects.php?action=new" class="quick-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouveau projet
      </a>
      <a href="profile.php" class="quick-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Modifier le profil
      </a>
      <a href="files.php" class="quick-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Uploader un fichier
      </a>
      <a href="settings.php" class="quick-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M20 12h2M2 12h2M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41M12 20v2M12 2v2"/></svg>
        Paramètres
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
