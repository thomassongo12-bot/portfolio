<?php
require_once __DIR__ . '/config.php';
require_login();

// Vider les logs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['act'] ?? '') === 'clear_logs') {
    csrf_check();
    if (file_exists(LOGS_FILE)) {
        file_put_contents(LOGS_FILE, '');
    }
    // Vider aussi bruteforce.json
    if (file_exists(DATA_PATH . '/bruteforce.json')) {
        file_put_contents(DATA_PATH . '/bruteforce.json', '{}');
    }
    flash('success', 'Logs vidés.');
    header('Location: security.php'); exit;
}

// Lire logs
$logs     = [];
$log_size = 0;
if (file_exists(LOGS_FILE)) {
    $log_size = filesize(LOGS_FILE);
    $raw      = file_get_contents(LOGS_FILE);
    $lines    = array_filter(array_reverse(explode("\n", $raw)));
    foreach (array_slice($lines, 0, 200) as $line) {
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) >= 4) {
            $event = strtolower($parts[1] ?? '');
            $logs[] = [
                'date'   => $parts[0] ?? '',
                'event'  => $parts[1] ?? '',
                'detail' => $parts[2] ?? '',
                'ua'     => $parts[3] ?? '',
                'level'  => str_contains($event, 'fail') || str_contains($event, 'block') || str_contains($event, 'mismatch')
                    ? 'danger' : (str_contains($event, 'success') ? 'success' : 'info'),
            ];
        }
    }
}

// Brute-force actifs
$bf_data = [];
$bf_file = DATA_PATH . '/bruteforce.json';
if (file_exists($bf_file)) {
    $raw_bf = json_decode(file_get_contents($bf_file), true) ?: [];
    $now    = time();
    foreach ($raw_bf as $key => $times) {
        $active = array_filter($times, fn($t) => ($now - $t) < 900);
        if (!empty($active)) {
            $bf_data[] = [
                'ip_hash'   => $key,
                'attempts'  => count($active),
                'last'      => date('H:i:s', max($active)),
                'blocked'   => count($active) >= 5,
            ];
        }
    }
}

$title = 'Sécurité';
require_once __DIR__ . '/partials/header.php';
?>

<div class="page-header">
  <div>
    <h1>Sécurité</h1>
    <p class="page-desc">Journaux d'accès et tentatives de connexion.</p>
  </div>
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="act" value="clear_logs">
    <button type="submit" class="btn btn-danger btn-sm"
      onclick="return confirm('Vider tous les logs de sécurité ?')">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
      Vider les logs
    </button>
  </form>
</div>

<!-- Stats -->
<div class="stats-grid" style="margin-bottom:1.5rem;">
  <div class="stat-card">
    <div class="stat-icon stat-blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= count($logs) ?></div>
      <div class="stat-label">Événements (200 derniers)</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon <?= !empty($bf_data) ? 'stat-orange' : 'stat-green' ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= count($bf_data) ?></div>
      <div class="stat-label">IP(s) suspectes actives</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon stat-blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    </div>
    <div class="stat-info">
      <div class="stat-value"><?= $log_size > 0 ? round($log_size/1024, 1) . ' Ko' : '0' ?></div>
      <div class="stat-label">Taille du fichier log</div>
    </div>
  </div>
</div>

<?php if (!empty($bf_data)): ?>
<div class="card" style="margin-bottom:1.5rem;">
  <div class="card-header">
    <h2 class="card-title" style="color:var(--danger);">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      IP(s) en cours de blocage
    </h2>
  </div>
  <div class="card-body p0">
    <table class="log-table">
      <thead><tr><th>Hash IP</th><th>Tentatives</th><th>Dernière tentative</th><th>Statut</th></tr></thead>
      <tbody>
        <?php foreach ($bf_data as $bf): ?>
        <tr>
          <td><code><?= htmlspecialchars($bf['ip_hash']) ?></code></td>
          <td><?= $bf['attempts'] ?>/5</td>
          <td><?= $bf['last'] ?></td>
          <td><span class="log-badge <?= $bf['blocked'] ? 'badge-danger' : 'badge-warning' ?>"><?= $bf['blocked'] ? 'BLOQUÉ' : 'Actif' ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Journal -->
<div class="card">
  <div class="card-header">
    <h2 class="card-title">Journal de sécurité</h2>
    <span class="card-hint">200 derniers événements</span>
  </div>
  <div class="card-body p0">
    <?php if (empty($logs)): ?>
      <div class="empty-state">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
        <p>Aucun événement enregistré.</p>
      </div>
    <?php else: ?>
      <table class="log-table">
        <thead>
          <tr><th>Date</th><th>Événement</th><th>Détail</th></tr>
        </thead>
        <tbody>
          <?php foreach ($logs as $log): ?>
          <tr>
            <td class="log-date"><?= htmlspecialchars($log['date']) ?></td>
            <td><span class="log-badge badge-<?= $log['level'] ?>"><?= htmlspecialchars($log['event']) ?></span></td>
            <td class="log-detail"><?= htmlspecialchars($log['detail']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
