<?php
require __DIR__ . '/config.php';
admin_require_login();

// Blog posts, same idea as destinos.json: /cliente/blog.php reads this file
// directly. Zero posts today — the site shows a "muy pronto" empty state
// until the first one is published here.
define('BLOG_FILE', SITE_ROOT . '/blog.json');
$allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$maxBytes = 12 * 1024 * 1024;
$langs = ['es' => 'Español', 'en' => 'English', 'it' => 'Italiano'];

function admin_post_too_large_b() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return false;
    if (!empty($_POST) || !empty($_FILES)) return false;
    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
    return $contentLength > 0;
}

function admin_load_blog() {
    $raw = file_exists(BLOG_FILE) ? file_get_contents(BLOG_FILE) : '{"posts":[]}';
    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['posts']) || !is_array($data['posts'])) {
        $data = ['posts' => []];
    }
    return $data;
}

function admin_save_blog($data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) return false;
    return file_put_contents(BLOG_FILE, $json, LOCK_EX) !== false;
}

function admin_slugify_b($title) {
    $map = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ñ' => 'n',
    ];
    $slug = strtr($title, $map);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'post';
}

function admin_unique_slug_b($base, $existingIds) {
    $slug = $base;
    $n = 2;
    while (in_array($slug, $existingIds, true)) {
        $slug = $base . '-' . $n;
        $n++;
    }
    return $slug;
}

function admin_save_post_cover($fileError, $tmpName, $sizeBytes, $originalName, $postId, &$error) {
    global $allowedExt, $maxBytes;

    if ($fileError === UPLOAD_ERR_NO_FILE) return null;
    if ($fileError !== UPLOAD_ERR_OK) {
        $error = 'Hubo un problema subiendo la imagen de portada.';
        return null;
    }
    if ($sizeBytes > $maxBytes) {
        $error = 'La imagen de portada pesa más de 12MB.';
        return null;
    }
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        $error = 'La imagen de portada no es un formato soportado (jpg, png, webp, gif).';
        return null;
    }
    if (@getimagesize($tmpName) === false) {
        $error = 'La imagen de portada no parece ser válida.';
        return null;
    }

    $filename = 'blog-' . $postId . '-cover.' . $ext;
    if (!move_uploaded_file($tmpName, IMAGES_DIR . '/' . $filename)) {
        $error = 'No se pudo guardar la imagen de portada.';
        return null;
    }
    return $filename;
}

function admin_read_tri_b($prefix) {
    global $langs;
    $out = [];
    foreach (array_keys($langs) as $lang) {
        $out[$lang] = trim(str_replace(["\r\n", "\r"], "\n", $_POST[$prefix . '_' . $lang] ?? ''));
    }
    return $out;
}

$message = '';
$messageType = '';
$data = admin_load_blog();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (admin_post_too_large_b()) {
        $message = 'Lo que subiste pesa demasiado para el servidor. Probá con un archivo más liviano.';
        $messageType = 'error';
    } elseif (!admin_csrf_check()) {
        $message = 'La sesión expiró, volvé a cargar la página e intentá de nuevo.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_post') {
            $postId = $_POST['post_id'] ?? '';
            $index = null;
            foreach ($data['posts'] as $i => $p) {
                if ($p['id'] === $postId) { $index = $i; break; }
            }
            if ($index === null) {
                $message = 'No se encontró ese artículo.';
                $messageType = 'error';
            } else {
                $post = $data['posts'][$index];
                $title = trim(preg_replace('/\s+/', ' ', $_POST['title_es'] ?? ''));
                if ($title !== '') $post['title']['es'] = $title;
                $post['title']['en'] = trim($_POST['title_en'] ?? '');
                $post['title']['it'] = trim($_POST['title_it'] ?? '');
                $post['excerpt'] = admin_read_tri_b('excerpt');
                $post['body'] = admin_read_tri_b('body');
                $post['category'] = trim($_POST['category'] ?? ($post['category'] ?? ''));
                $post['published'] = !empty($_POST['published']);
                $post['date'] = trim($_POST['date'] ?? ($post['date'] ?? date('Y-m-d')));

                $coverError = '';
                if (!empty($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $cover = admin_save_post_cover(
                        $_FILES['cover']['error'], $_FILES['cover']['tmp_name'], $_FILES['cover']['size'], $_FILES['cover']['name'],
                        $post['id'], $coverError
                    );
                    if ($cover) $post['cover'] = $cover;
                }

                if ($coverError) {
                    $message = $coverError;
                    $messageType = 'error';
                } else {
                    $data['posts'][$index] = $post;
                    if (admin_save_blog($data)) {
                        $message = '"' . $post['title']['es'] . '" actualizado.';
                        $messageType = 'success';
                    } else {
                        $message = 'No se pudieron guardar los cambios.';
                        $messageType = 'error';
                    }
                }
            }
        } elseif ($action === 'delete_post') {
            $postId = $_POST['post_id'] ?? '';
            $index = null;
            foreach ($data['posts'] as $i => $p) {
                if ($p['id'] === $postId) { $index = $i; break; }
            }
            if ($index === null) {
                $message = 'No se encontró ese artículo.';
                $messageType = 'error';
            } else {
                $removed = $data['posts'][$index];
                array_splice($data['posts'], $index, 1);
                if (admin_save_blog($data)) {
                    if (!empty($removed['cover'])) {
                        $path = IMAGES_DIR . '/' . basename($removed['cover']);
                        if (is_file($path)) @unlink($path);
                    }
                    $message = '"' . $removed['title']['es'] . '" borrado.';
                    $messageType = 'success';
                } else {
                    $message = 'No se pudo borrar el artículo.';
                    $messageType = 'error';
                }
            }
        } elseif ($action === 'add_post') {
            $titleEs = trim(preg_replace('/\s+/', ' ', $_POST['title_es'] ?? ''));
            if ($titleEs === '') {
                $message = 'Completá al menos el título en español.';
                $messageType = 'error';
            } else {
                $existingIds = array_map(function ($p) { return $p['id']; }, $data['posts']);
                $id = admin_unique_slug_b(admin_slugify_b($titleEs), $existingIds);

                $coverError = '';
                $cover = '';
                if (!empty($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $saved = admin_save_post_cover(
                        $_FILES['cover']['error'], $_FILES['cover']['tmp_name'], $_FILES['cover']['size'], $_FILES['cover']['name'],
                        $id, $coverError
                    );
                    if ($saved) $cover = $saved;
                }

                if ($coverError) {
                    $message = $coverError;
                    $messageType = 'error';
                } else {
                    $data['posts'][] = [
                        'id' => $id,
                        'title' => ['es' => $titleEs, 'en' => trim($_POST['title_en'] ?? ''), 'it' => trim($_POST['title_it'] ?? '')],
                        'excerpt' => admin_read_tri_b('excerpt'),
                        'body' => admin_read_tri_b('body'),
                        'category' => trim($_POST['category'] ?? ''),
                        'cover' => $cover,
                        'date' => trim($_POST['date'] ?? date('Y-m-d')),
                        'published' => !empty($_POST['published']),
                    ];
                    if (admin_save_blog($data)) {
                        $message = '"' . $titleEs . '" agregado.';
                        $messageType = 'success';
                    } else {
                        $message = 'No se pudo guardar el artículo nuevo.';
                        $messageType = 'error';
                    }
                }
            }
        }
    }
    $data = admin_load_blog();
}

$csrf = admin_csrf_token();
$cacheBust = time();

function admin_tri_fields_b($fieldName, $values, $idSuffix, $label) {
    global $langs;
    echo '<div class="admin-field"><label>' . htmlspecialchars($label) . '</label>';
    foreach ($langs as $lang => $langLabel) {
        $inputId = $fieldName . '_' . $lang . '_' . $idSuffix;
        $value = htmlspecialchars($values[$lang] ?? '');
        echo '<p class="admin-hint" style="margin-bottom:2px">' . $langLabel . '</p>';
        echo '<textarea id="' . $inputId . '" name="' . $fieldName . '_' . $lang . '">' . $value . '</textarea>';
    }
    echo '</div>';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Blog — Admin Argentina 1810</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-topbar">
        <a href="destinos.php">Argentina 1810 — Admin</a>
        <nav>
            <a href="destinos.php">Destinos</a>
            <a href="itinerarios.php">Itinerarios</a>
            <a href="blog.php">Blog</a>
            <a href="/cliente/" target="_blank">Ver Free Explorer ↗</a>
            <a href="/partners/" target="_blank">Ver Partner Agency ↗</a>
            <a href="logout.php">Salir</a>
        </nav>
    </div>

    <div class="admin-wrap admin-wrap-wide">
        <h1 class="admin-title">Blog</h1>
        <p class="admin-subtitle">Solo aparece en <code>/cliente/blog.php</code> (Free Explorer). Un artículo sin "Publicado" tildado queda guardado como borrador y no se muestra en el sitio.</p>

        <?php if ($message): ?>
            <div class="admin-alert <?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (empty($data['posts'])): ?>
            <p class="admin-hint">Todavía no hay artículos — el sitio muestra el estado "Muy pronto" mientras tanto.</p>
        <?php endif; ?>

        <?php foreach ($data['posts'] as $post): ?>
            <details class="admin-section">
                <summary><?= htmlspecialchars($post['title']['es']) ?><?= empty($post['published']) ? ' (borrador)' : '' ?></summary>
                <div class="admin-section-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="action" value="save_post">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">

                        <div class="admin-field">
                            <label>Título</label>
                            <p class="admin-hint" style="margin-bottom:2px">Español</p>
                            <input type="text" name="title_es" value="<?= htmlspecialchars($post['title']['es'] ?? '') ?>">
                            <p class="admin-hint" style="margin-bottom:2px">English</p>
                            <input type="text" name="title_en" value="<?= htmlspecialchars($post['title']['en'] ?? '') ?>">
                            <p class="admin-hint" style="margin-bottom:2px">Italiano</p>
                            <input type="text" name="title_it" value="<?= htmlspecialchars($post['title']['it'] ?? '') ?>">
                        </div>

                        <?php admin_tri_fields_b('excerpt', $post['excerpt'] ?? [], $post['id'], 'Bajada / resumen'); ?>
                        <?php admin_tri_fields_b('body', $post['body'] ?? [], $post['id'], 'Cuerpo del artículo'); ?>

                        <div class="admin-field">
                            <label for="category_<?= htmlspecialchars($post['id']) ?>">Categoría (Active & Wild / Slow & Immersive / Family Journeys / Food, Wine & Comfort / Novedades)</label>
                            <input type="text" id="category_<?= htmlspecialchars($post['id']) ?>" name="category" value="<?= htmlspecialchars($post['category'] ?? '') ?>">
                        </div>
                        <div class="admin-field">
                            <label for="date_<?= htmlspecialchars($post['id']) ?>">Fecha</label>
                            <input type="date" id="date_<?= htmlspecialchars($post['id']) ?>" name="date" value="<?= htmlspecialchars($post['date'] ?? '') ?>">
                        </div>
                        <div class="admin-field">
                            <label><input type="checkbox" name="published" value="1" <?= !empty($post['published']) ? 'checked' : '' ?>> Publicado</label>
                        </div>

                        <div class="admin-field">
                            <label>Portada</label>
                            <?php if (!empty($post['cover'])): ?>
                                <div class="admin-image-card-thumb">
                                    <img src="../assets/imgs/<?= htmlspecialchars($post['cover']) ?>?v=<?= $cacheBust ?>" alt="" loading="lazy">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="cover" accept="image/*">
                            <p class="admin-hint">Subir una nueva reemplaza la actual.</p>
                        </div>

                        <button type="submit" class="admin-btn admin-btn-small">Guardar cambios</button>
                    </form>

                    <form method="post" class="admin-delete-form" onsubmit="return confirm('¿Borrar &quot;<?= htmlspecialchars(addslashes($post['title']['es'])) ?>&quot;? No se puede deshacer.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="action" value="delete_post">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                        <button type="submit" class="admin-btn admin-btn-small admin-btn-danger">Borrar este artículo</button>
                    </form>
                </div>
            </details>
        <?php endforeach; ?>

        <fieldset class="admin-section admin-section-add">
            <legend>Agregar artículo nuevo</legend>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action" value="add_post">
                <div class="admin-field">
                    <label>Título</label>
                    <p class="admin-hint" style="margin-bottom:2px">Español (requerido)</p>
                    <input type="text" name="title_es" required>
                    <p class="admin-hint" style="margin-bottom:2px">English</p>
                    <input type="text" name="title_en">
                    <p class="admin-hint" style="margin-bottom:2px">Italiano</p>
                    <input type="text" name="title_it">
                </div>
                <?php admin_tri_fields_b('excerpt', [], 'new', 'Bajada / resumen'); ?>
                <?php admin_tri_fields_b('body', [], 'new', 'Cuerpo del artículo'); ?>
                <div class="admin-field">
                    <label for="new_category">Categoría</label>
                    <input type="text" id="new_category" name="category">
                </div>
                <div class="admin-field">
                    <label for="new_date">Fecha</label>
                    <input type="date" id="new_date" name="date">
                </div>
                <div class="admin-field">
                    <label><input type="checkbox" name="published" value="1"> Publicado</label>
                </div>
                <div class="admin-field">
                    <label for="new_cover">Portada</label>
                    <input type="file" id="new_cover" name="cover" accept="image/*">
                </div>
                <button type="submit" class="admin-btn">Agregar artículo</button>
            </form>
        </fieldset>
    </div>
</body>
</html>
