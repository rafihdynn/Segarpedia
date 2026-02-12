<?php
$lifetime = 3600; // 1 jam

ini_set('session.gc_maxlifetime', $lifetime);

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'secure' => false,   // true kalau HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// auto-expire tapi juga auto-refresh
if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > $lifetime) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();
