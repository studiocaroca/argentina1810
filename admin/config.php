<?php
// Shared bootstrap for every admin/ page: starts the session and
// centralizes the login credentials, paths and auth helpers so the
// password only lives in one place.

session_start();

define('ADMIN_USERNAME', 'admin');
// bcrypt hash of "a1810admin" — generated once with password_hash(); the
// plaintext password is never stored anywhere, in this file or any other.
// This is only the ORIGINAL/fallback password. Once the client sets their
// own from "Cambiar contraseña", the real hash lives in ADMIN_PASSWORD_FILE
// instead (gitignored, so a deploy never overwrites what they chose). If
// that file doesn't exist — including if it's deleted on purpose — login
// falls back to this constant, which only the developer knows.
define('ADMIN_PASSWORD_HASH', '$2y$10$revRZtu10Pq70fv2ZCDWd.xbWi1D1RQED19RDZQk5GyN4RMFbe.2O');

define('SITE_ROOT', dirname(__DIR__));
define('IMAGES_DIR', SITE_ROOT . '/assets/imgs');
define('ADMIN_PASSWORD_FILE', __DIR__ . '/admin-password.json');

// The hash actually in effect: whatever the client last set via "Cambiar
// contraseña", or the constant above if they never have (or if the file
// was deleted to force a reset back to the known original password).
function admin_get_password_hash() {
    if (is_file(ADMIN_PASSWORD_FILE)) {
        $data = json_decode(file_get_contents(ADMIN_PASSWORD_FILE), true);
        if (is_array($data) && !empty($data['hash'])) {
            return $data['hash'];
        }
    }
    return ADMIN_PASSWORD_HASH;
}

function admin_set_password_hash($hash) {
    $json = json_encode(['hash' => $hash, 'updated_at' => date('c')], JSON_PRETTY_PRINT);
    return file_put_contents(ADMIN_PASSWORD_FILE, $json, LOCK_EX) !== false;
}

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
