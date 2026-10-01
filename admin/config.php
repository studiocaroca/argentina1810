<?php
// Shared bootstrap for every admin/ page: starts the session and
// centralizes the login credentials, paths and auth helpers so the
// password only lives in one place.

session_start();

define('ADMIN_USERNAME', 'admin');
// bcrypt hash of "a1810admin" — generated once with password_hash(); the
// plaintext password is never stored in this file.
define('ADMIN_PASSWORD_HASH', '$2y$10$revRZtu10Pq70fv2ZCDWd.xbWi1D1RQED19RDZQk5GyN4RMFbe.2O');

define('SITE_ROOT', dirname(__DIR__));
define('IMAGES_DIR', SITE_ROOT . '/assets/imgs');

function admin_is_logged_in() {
    return !empty($_SESSION['a1810_admin_logged_in']);
}

// Call at the top of every page that requires a session, before any
// HTML is written.
function admin_require_login() {
    if (!admin_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function admin_csrf_token() {
    if (empty($_SESSION['a1810_admin_csrf'])) {
        $_SESSION['a1810_admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['a1810_admin_csrf'];
}

function admin_csrf_check() {
    $token = $_POST['csrf_token'] ?? '';
    return !empty($_SESSION['a1810_admin_csrf']) && hash_equals($_SESSION['a1810_admin_csrf'], $token);
}
