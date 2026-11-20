<?php
/**
 * Diagnostic complet – SUPPRIMER après usage
 * URL : https://votre-site.com/admin/diag.php
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<pre style='font:13px monospace;padding:20px;background:#0F172A;color:#94a3b8;'>";
echo "<span style='color:#60a5fa;font-size:16px;font-weight:bold;'>DIAGNOSTIC ADMIN\n\n</span>";

// 1. PHP
echo "<span style='color:#34d399;'>── PHP ──────────────────────────────────────────</span>\n";
echo "Version PHP    : " . PHP_VERSION . "\n";
echo "OS             : " . PHP_OS . "\n";
echo "SAPI           : " . php_sapi_name() . "\n\n";

// 2. Chemins
echo "<span style='color:#34d399;'>── CHEMINS ──────────────────────────────────────</span>\n";
$root      = dirname(__DIR__);
$data      = $root . '/data';
$auth_file = $data . '/auth.json';
echo "ROOT           : $root\n";
echo "DATA           : $data\n";
echo "SCRIPT_NAME    : " . ($_SERVER['SCRIPT_NAME'] ?? 'undefined') . "\n\n";

// 3. Fichiers critiques
echo "<span style='color:#34d399;'>── FICHIERS ─────────────────────────────────────</span>\n";
$files = [
    'config.php'              => __DIR__ . '/config.php',
    'partials/header.php'     => __DIR__ . '/partials/header.php',
    'partials/footer.php'     => __DIR__ . '/partials/footer.php',
    'data/auth.json'          => $auth_file,
    'data/settings.json'      => $data . '/settings.json',
    'data/projects.json'      => $data . '/projects.json',
    'data/messages.json'      => $data . '/messages.json',
];
foreach ($files as $label => $path) {
    $exists = file_exists($path);
    $write  = $exists ? is_writable($path) : is_writable(dirname($path));
    $size   = $exists ? filesize($path) . ' o' : '-';
    $color  = $exists ? '#34d399' : '#f87171';
    echo "<span style='color:$color'>" . ($exists ? '✓' : '✗') . "</span> $label ($size)" . (!$exists ? " <span style='color:#f87171'>ABSENT</span>" : '') . "\n";
}

// 4. auth.json
echo "\n<span style='color:#34d399;'>── AUTH.JSON ────────────────────────────────────</span>\n";
if (file_exists($auth_file)) {
    $d = json_decode(file_get_contents($auth_file), true);
    if ($d) {
        echo "username : " . ($d['username'] ?? 'MANQUANT') . "\n";
        echo "hash     : " . substr($d['hash'] ?? '', 0, 25) . "...\n";
        echo "verify 'admin2024' : " . (password_verify('admin2024', $d['hash'] ?? '') ? "<span style='color:#34d399'>OUI ✓</span>" : "<span style='color:#f87171'>NON ✗</span>") . "\n";
    } else {
        echo "<span style='color:#f87171'>JSON invalide !</span>\n";
        echo "Contenu brut : " . htmlspecialchars(file_get_contents($auth_file)) . "\n";
    }
} else {
    echo "<span style='color:#f87171'>FICHIER ABSENT</span> → créer data/auth.json\n";
}

// 5. Permissions dossiers
echo "\n<span style='color:#34d399;'>── PERMISSIONS ──────────────────────────────────</span>\n";
$dirs = [
    'data/'            => $data,
    'assets/images/'   => $root . '/assets/images',
    'assets/files/'    => $root . '/assets/files',
    'admin/'           => __DIR__,
];
foreach ($dirs as $label => $path) {
    $exists  = is_dir($path);
    $writable= $exists && is_writable($path);
    $col     = $writable ? '#34d399' : '#f87171';
    echo "<span style='color:$col'>" . ($writable ? '✓ rw' : ($exists ? '✗ ro' : '✗ absent')) . "</span>  $label\n";
}

// 6. Config.php – test include
echo "\n<span style='color:#34d399;'>── INCLUDE CONFIG.PHP ───────────────────────────</span>\n";
try {
    ob_start();
    require_once __DIR__ . '/config.php';
    $buf = ob_get_clean();
    echo "<span style='color:#34d399;'>✓ config.php inclus sans erreur</span>\n";
    if ($buf) echo "Output inattendu : " . htmlspecialchars($buf) . "\n";

    // Test admin_url()
    echo "admin_url()            : " . admin_url() . "\n";
    echo "admin_url('login.php') : " . admin_url('login.php') . "\n";

    // Test get_settings()
    $s = get_settings();
    echo "get_settings()[name]   : " . ($s['name'] ?? 'VIDE') . "\n";

    // Test auth
    $creds = get_admin_credentials();
    echo "credentials username   : " . $creds['username'] . "\n";
    echo "credentials hash ok    : " . (strlen($creds['hash']) > 20 ? 'OUI' : 'NON') . "\n";

} catch (Throwable $e) {
    ob_end_clean();
    echo "<span style='color:#f87171;'>ERREUR : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . " ligne " . $e->getLine() . "</span>\n";
}

// 7. Erreur PHP dernière
echo "\n<span style='color:#34d399;'>── DERNIERE ERREUR PHP ──────────────────────────</span>\n";
$last = error_get_last();
if ($last) {
    echo "<span style='color:#f87171;'>" . $last['type'] . " : " . $last['message'] . "\n";
    echo "Fichier : " . $last['file'] . " ligne " . $last['line'] . "</span>\n";
} else {
    echo "Aucune erreur détectée\n";
}

echo "\n<span style='color:#60a5fa;'>─────────────────────────────────────────────────</span>\n";
echo "FIN DU DIAGNOSTIC – Supprimez ce fichier après usage\n";
echo "</pre>";
