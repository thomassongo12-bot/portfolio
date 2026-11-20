<?php
require_once __DIR__ . '/config.php';
admin_session_start();
session_destroy();
header('Location: ' . admin_url('login.php') . '?logged_out=1');
exit;
