<?php
require_once __DIR__ . '/config.php';
require_login();

$messages = read_json(MESSAGES_FILE, []);
$messages = array_reverse($messages);
$filter   = $_GET['filter'] ?? 'all';
$view_id  = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['act'] ?? '';
    $all = read_json(MESSAGES_FILE, []);

    if ($act === 'mark_read') {
        $mid = (int)$_POST['id'];
        foreach ($all as &$m) { if ((int)$m['id'] === $mid) $m['read'] = true; }
        write_json(MESSAGES_FILE, $all);
        header('Location: messages.php?id=' . $mid); exit;
    }
    if ($act === 'mark_all_read') {
        foreach ($all as &$m) $m['read'] = true;
        write_json(MESSAGES_FILE, $all);
        flash('success', 'Tous les messages marqués comme lus.');
        header('Location: messages.php'); exit;
    }
    if ($act === 'delete') {
        $mid = (int)$_POST['id'];
        $all = array_values(array_filter($all, fn($m) => (int)$m['id'] !== $mid));
        write_json(MESSAGES_FILE, $all);
        flash('success', 'Message supprimé.');
        header('Location: messages.php'); exit;
    }
    if ($act === 'delete_all') {
        write_json(MESSAGES_FILE, []);
        flash('success', 'Tous les messages supprimés.');
        header('Location: messages.php'); exit;
    }
}

$title = 'Messages';
require_once __DIR__ . '/partials/header.php';

// ─── Filter ───────────────────────────────────────────────
$displayed = $filter === 'unread'
    ? array_values(array_filter($messages, fn($m) => empty($m['read'])))
    : $messages;

// ─── View single message ─────────────────────────────────
$viewing = null;
if ($view_id !== null) {
    foreach ($messages as $m) {
        if ((int)$m['id'] === $view_id) { $viewing = $m; break; }
    }
    // Auto mark as read
    if ($viewing && empty($viewing['read'])) {
        $all = read_json(MESSAGES_FILE, []);
        foreach ($all as &$m) { if ((int)$m['id'] === $view_id) $m['read'] = true; }
        write_json(MESSAGES_FILE, $all);
        $viewing['read'] = true;
    }
}

$unread_count = count(array_filter($messages, fn($m) => empty($m['read'])));
?>

<div class="page-header">
  <div>
    <h1>Messages</h1>
    <p class="page-desc"><?= count($messages) ?> message(s) · <?= $unread_count ?> non lu(s)</p>
  </div>
  <?php if (!empty($messages)): ?>
  <div class="btn-group">
    <form method="POST" style="display:inline;">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="act" value="mark_all_read">
      <button type="submit" class="btn btn-outline btn-sm">Tout marquer lu</button>
    </form>
    <button type="button" class="btn btn-danger btn-sm"
      onclick="if(confirm('Supprimer TOUS les messages ?')) { document.getElementById('deleteAllForm').submit(); }">
      Tout supprimer
    </button>
    <form method="POST" id="deleteAllForm" style="display:none;">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="act" value="delete_all">
    </form>
  </div>
  <?php endif; ?>
</div>

<!-- Filters -->
<div class="filter-bar">
  <a href="messages.php" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">
    Tous <span class="filter-count"><?= count($messages) ?></span>
  </a>
  <a href="messages.php?filter=unread" class="filter-btn <?= $filter === 'unread' ? 'active' : '' ?>">
    Non lus <span class="filter-count"><?= $unread_count ?></span>
  </a>
</div>

<div class="messages-layout">

  <!-- List -->
  <div class="messages-list">
    <?php if (empty($displayed)): ?>
      <div class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <p>Aucun message<?= $filter === 'unread' ? ' non lu' : '' ?>.</p>
      </div>
    <?php else: ?>
      <?php foreach ($displayed as $msg): ?>
        <?php $is_active = $viewing && (int)$viewing['id'] === (int)$msg['id']; ?>
        <a href="messages.php?id=<?= $msg['id'] ?><?= $filter !== 'all' ? '&filter='.$filter : '' ?>"
           class="msg-card <?= empty($msg['read']) ? 'unread' : '' ?> <?= $is_active ? 'active' : '' ?>">
          <div class="msg-card-avatar"><?= strtoupper(mb_substr($msg['name'] ?? 'A', 0, 1)) ?></div>
          <div class="msg-card-body">
            <div class="msg-card-top">
              <span class="msg-card-name"><?= htmlspecialchars($msg['name'] ?? '') ?></span>
              <span class="msg-card-date"><?= date('d/m/Y', strtotime($msg['date'] ?? 'now')) ?></span>
            </div>
            <div class="msg-card-subject"><?= htmlspecialchars(mb_substr($msg['subject'] ?? '', 0, 45)) ?></div>
            <div class="msg-card-preview"><?= htmlspecialchars(mb_substr($msg['message'] ?? '', 0, 70)) ?>…</div>
          </div>
          <?php if (empty($msg['read'])): ?>
            <span class="unread-dot"></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Detail panel -->
  <div class="message-detail">
    <?php if ($viewing): ?>
      <div class="msg-detail-header">
        <div class="msg-detail-avatar"><?= strtoupper(mb_substr($viewing['name'] ?? 'A', 0, 1)) ?></div>
        <div>
          <div class="msg-detail-name"><?= htmlspecialchars($viewing['name'] ?? '') ?></div>
          <div class="msg-detail-email">
            <a href="mailto:<?= htmlspecialchars($viewing['email'] ?? '') ?>">
              <?= htmlspecialchars($viewing['email'] ?? '') ?>
            </a>
          </div>
          <div class="msg-detail-date"><?= date('d/m/Y à H:i', strtotime($viewing['date'] ?? 'now')) ?></div>
        </div>
      </div>

      <div class="msg-detail-subject"><?= htmlspecialchars($viewing['subject'] ?? '') ?></div>
      <div class="msg-detail-body"><?= nl2br(htmlspecialchars($viewing['message'] ?? '')) ?></div>

      <div class="msg-detail-actions">
        <a href="mailto:<?= htmlspecialchars($viewing['email'] ?? '') ?>?subject=Re: <?= urlencode($viewing['subject'] ?? '') ?>"
           class="btn btn-primary btn-sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Répondre par e-mail
        </a>
        <form method="POST" style="display:inline;">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="act" value="delete">
          <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
          <button type="submit" class="btn btn-danger btn-sm"
            onclick="return confirm('Supprimer ce message ?')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            Supprimer
          </button>
        </form>
      </div>
    <?php else: ?>
      <div class="msg-detail-empty">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <p>Sélectionnez un message pour le lire</p>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
