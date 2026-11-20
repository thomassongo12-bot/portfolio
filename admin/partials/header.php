<?php
/**
 * Admin Header – inclus en PREMIER dans chaque page admin
 * config.php + require_login() sont déjà appelés AVANT cet include
 */

// Activer les erreurs en développement, les masquer en prod
// Sur hébergeur : changer à 0 / E_ALL & ~E_NOTICE
if (!defined('ADMIN_VERSION')) {
    // config.php pas encore chargé – le charger maintenant
    require_once dirname(__DIR__) . '/config.php';
}
require_login();

// Vider tout output accidentel avant d'émettre le HTML
while (ob_get_level() > 0) ob_end_clean();
ob_start();

$flash    = get_flash();
$settings = get_settings();
$page     = basename($_SERVER['PHP_SELF'], '.php');

$messages_data = read_json(MESSAGES_FILE, []);
$unread        = count(array_filter($messages_data, fn($m) => empty($m['read'])));

// Chemin relatif vers assets admin (toujours ../assets/ depuis /admin/pages/)
$admin_base = admin_url();
$assets_css = $admin_base . 'assets/admin.css';
$assets_js  = $admin_base . 'assets/admin.js';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Administration') ?> – Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- CSS admin en chemin relatif direct — fonctionne partout -->
  <link rel="stylesheet" href="<?= htmlspecialchars($assets_css) ?>">
</head>
<body>

<div class="admin-layout">

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <a href="<?= htmlspecialchars($admin_base . 'index.php') ?>" class="sidebar-logo">
        RODRIGUE<span>.</span>
      </a>
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Fermer le menu">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Principal</div>
      <a href="<?= htmlspecialchars($admin_base . 'index.php') ?>" class="nav-item <?= $page === 'index' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Tableau de bord
      </a>

      <div class="nav-section-label">Contenu</div>
      <a href="<?= htmlspecialchars($admin_base . 'profile.php') ?>" class="nav-item <?= $page === 'profile' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Profil & À propos
      </a>
      <a href="<?= htmlspecialchars($admin_base . 'projects.php') ?>" class="nav-item <?= $page === 'projects' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        Projets
      </a>
      <a href="<?= htmlspecialchars($admin_base . 'messages.php') ?>" class="nav-item <?= $page === 'messages' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Messages
        <?php if ($unread > 0): ?>
          <span class="badge-count"><?= $unread ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= htmlspecialchars($admin_base . 'files.php') ?>" class="nav-item <?= $page === 'files' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Fichiers & Médias
      </a>

      <div class="nav-section-label">Réglages</div>
      <a href="<?= htmlspecialchars($admin_base . 'settings.php') ?>" class="nav-item <?= $page === 'settings' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M20 12h2M2 12h2M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41M12 20v2M12 2v2"/></svg>
        Paramètres
      </a>
      <a href="<?= htmlspecialchars($admin_base . 'password.php') ?>" class="nav-item <?= $page === 'password' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Mot de passe
      </a>
      <a href="<?= htmlspecialchars($admin_base . 'security.php') ?>" class="nav-item <?= $page === 'security' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Sécurité / Logs
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="../index.php" target="_blank" class="sidebar-view-site">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Voir le portfolio
      </a>
      <a href="<?= htmlspecialchars($admin_base . 'logout.php') ?>" class="sidebar-logout">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Déconnexion
      </a>
    </div>
  </aside>

  <!-- Overlay mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Main -->
  <div class="admin-main">

    <!-- Topbar -->
    <header class="topbar">
      <button class="topbar-menu-btn" id="menuBtn" aria-label="Ouvrir le menu">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div class="topbar-title"><?= htmlspecialchars($title ?? 'Administration') ?></div>
      <div class="topbar-actions">
        <?php if ($unread > 0): ?>
        <a href="<?= htmlspecialchars($admin_base . 'messages.php') ?>" class="topbar-notif" title="<?= $unread ?> message(s)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <span><?= $unread ?></span>
        </a>
        <?php endif; ?>
        <div class="topbar-user">
          <div class="topbar-avatar"><?= strtoupper(substr(ADMIN_USERNAME ?? 'A', 0, 1)) ?></div>
        </div>
      </div>
    </header>

    <!-- Flash -->
    <?php if ($flash): ?>
    <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>" id="flashMsg">
      <?php if ($flash['type'] === 'success'): ?>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <?php else: ?>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?php endif; ?>
      <?= htmlspecialchars($flash['msg']) ?>
      <button onclick="this.parentElement.remove()" class="flash-close">×</button>
    </div>
    <?php endif; ?>

    <div class="admin-content">
