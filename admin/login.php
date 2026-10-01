<?php
require __DIR__ . '/config.php';

// Kept AJAX-submittable (checked via this header) even though nothing on
// the public site currently submits here via fetch — a plain form POST
// falls back to the classic full-page flow below either way.
$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

if (admin_is_logged_in()) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    header('Location: destinos.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_check()) {
        $error = 'La sesión expiró, volvé a intentar.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            session_regenerate_id(true);
            $_SESSION['a1810_admin_logged_in'] = true;
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
            header('Location: destinos.php');
            exit;
        }

        $error = 'Usuario o contraseña incorrectos.';
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }
}

$csrf = admin_csrf_token();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Ingresar — Admin Argentina 1810</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-login-wrap">
        <a class="admin-login-back" href="/">← Volver al sitio</a>
        <h1>Argentina 1810<br>Panel de edición</h1>

        <?php if ($error): ?>
            <div class="admin-alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <div class="admin-field">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" autocomplete="username" required>
            </div>
            <div class="admin-field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="admin-btn" style="width:100%;">Ingresar</button>
        </form>

        <details class="admin-login-forgot">
            <summary>¿Te olvidaste la contraseña?</summary>
            <p>Este panel no tiene recuperación automática. Escribile a quien administra el sitio para que te la reestablezca.</p>
        </details>
    </div>
</body>
</html>
