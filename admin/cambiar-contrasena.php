<?php
require __DIR__ . '/config.php';
admin_require_login();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_check()) {
        $message = 'La sesión expiró, volvé a cargar la página e intentá de nuevo.';
        $messageType = 'error';
    } else {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, admin_get_password_hash())) {
            $message = 'La contraseña actual no es correcta.';
            $messageType = 'error';
        } elseif (strlen($new) < 8) {
            $message = 'La contraseña nueva tiene que tener al menos 8 caracteres.';
            $messageType = 'error';
        } elseif ($new !== $confirm) {
            $message = 'La contraseña nueva no coincide con la confirmación.';
            $messageType = 'error';
        } else {
            if (admin_set_password_hash(password_hash($new, PASSWORD_DEFAULT))) {
                $message = 'Contraseña actualizada. La vas a usar desde el próximo ingreso.';
                $messageType = 'success';
            } else {
                $message = 'No se pudo guardar la contraseña nueva.';
                $messageType = 'error';
            }
        }
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
    <title>Cambiar contraseña — Admin Argentina 1810</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-topbar">
        <a href="destinos.php">Argentina 1810 — Admin</a>
        <nav>
            <a href="destinos.php">Destinos</a>
            <a href="itinerarios.php">Itinerarios</a>
            <a href="blog.php">Blog</a>
            <a href="cambiar-contrasena.php">Cambiar contraseña</a>
            <a href="/" target="_blank">Ver sitio ↗</a>
            <a href="logout.php">Salir</a>
        </nav>
    </div>

    <div class="admin-wrap">
        <h1 class="admin-title">Cambiar contraseña</h1>
        <p class="admin-subtitle">Se aplica la próxima vez que alguien ingrese al panel.</p>

        <?php if ($message): ?>
            <div class="admin-alert <?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <div class="admin-field">
                <label for="current_password">Contraseña actual</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
            </div>
            <div class="admin-field">
                <label for="new_password">Contraseña nueva</label>
                <input type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="8" required>
            </div>
            <div class="admin-field">
                <label for="confirm_password">Repetir contraseña nueva</label>
                <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="8" required>
            </div>
            <button type="submit" class="admin-btn">Guardar contraseña</button>
        </form>
    </div>
</body>
</html>
