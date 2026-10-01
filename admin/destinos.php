<?php
require __DIR__ . '/config.php';
admin_require_login();

// Destinos are data, not hand-authored HTML: both /cliente/destinos.php and
// /partners/destinos.php read this file directly and render their own
// (different) copy per track. This admin page is the only place that ever
// writes to it. Unlike obras.json, every text field here is trilingual —
// the site actually switches ES/EN/IT, so the content has to exist in all
// three, not just Spanish.
define('DESTINOS_FILE', SITE_ROOT . '/destinos.json');
$allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$maxBytes = 12 * 1024 * 1024;
$langs = ['es' => 'Español', 'en' => 'English', 'it' => 'Italiano'];

function admin_post_too_large_d() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return false;
    if (!empty($_POST) || !empty($_FILES)) return false;
    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
    return $contentLength > 0;
}

function admin_load_destinos() {
    $raw = file_exists(DESTINOS_FILE) ? file_get_contents(DESTINOS_FILE) : '{"destinations":[]}';
    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['destinations']) || !is_array($data['destinations'])) {
        $data = ['destinations' => []];
    }
    return $data;
}

function admin_save_destinos($data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) return false;
    return file_put_contents(DESTINOS_FILE, $json, LOCK_EX) !== false;
}

function admin_slugify_d($title) {
    $map = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ñ' => 'n',
    ];
    $slug = strtr($title, $map);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'destino';
}

function admin_unique_slug_d($base, $existingIds) {
    $slug = $base;
    $n = 2;
    while (in_array($slug, $existingIds, true)) {
        $slug = $base . '-' . $n;
        $n++;
    }
    return $slug;
}

// Saves an uploaded photo as assets/imgs/destinos-{id}-{n}.{ext} — same
// numbering convention admin_save_play_image uses for obras.
function admin_save_destino_image($fileError, $tmpName, $sizeBytes, $originalName, $destinoId, &$existingImages, &$error) {
    global $allowedExt, $maxBytes;

    if ($fileError === UPLOAD_ERR_NO_FILE) return null;
    if ($fileError !== UPLOAD_ERR_OK) {
        $error = 'Hubo un problema subiendo una de las fotos.';
        return null;
    }
    if ($sizeBytes > $maxBytes) {
        $error = 'Una de las fotos pesa más de 12MB.';
        return null;
    }
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        $error = 'Una de las fotos no es un formato de imagen soportado (jpg, png, webp, gif).';
        return null;
    }
    if (@getimagesize($tmpName) === false) {
        $error = 'Uno de los archivos subidos no parece ser una imagen válida.';
        return null;
    }

    $n = 1;
    $prefix = 'destinos-' . $destinoId . '-';
    do {
        $candidate = $prefix . $n . '.' . $ext;
        $n++;
    } while (in_array($candidate, $existingImages, true) || file_exists(IMAGES_DIR . '/' . $candidate));

    if (!move_uploaded_file($tmpName, IMAGES_DIR . '/' . $candidate)) {
        $error = 'No se pudo guardar una de las fotos.';
        return null;
    }
    $existingImages[] = $candidate;
    return $candidate;
}

// Reads the trilingual fields for one destino out of $_POST, given a field
// prefix like "unique" -> unique_es / unique_en / unique_it.
function admin_read_tri($prefix) {
    global $langs;
    $out = [];
    foreach (array_keys($langs) as $lang) {
        $out[$lang] = trim(str_replace(["\r\n", "\r"], "\n", $_POST[$prefix . '_' . $lang] ?? ''));
    }
    return $out;
}

$message = '';
$messageType = '';
$data = admin_load_destinos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (admin_post_too_large_d()) {
        $message = 'Lo que subiste pesa demasiado para el servidor. Probá con un archivo más liviano.';
        $messageType = 'error';
    } elseif (!admin_csrf_check()) {
        $message = 'La sesión expiró, volvé a cargar la página e intentá de nuevo.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_destino') {
            $destinoId = $_POST['destino_id'] ?? '';
            $index = null;
            foreach ($data['destinations'] as $i => $d) {
                if ($d['id'] === $destinoId) { $index = $i; break; }
            }
            if ($index === null) {
                $message = 'No se encontró ese destino.';
                $messageType = 'error';
            } else {
                $dest = $data['destinations'][$index];

                $name = trim(preg_replace('/\s+/', ' ', $_POST['name'] ?? ''));
                if ($name !== '') $dest['name'] = $name;
                $order = (int) ($_POST['order'] ?? $dest['order'] ?? 0);
                $dest['order'] = $order;

                $dest['best_season'] = admin_read_tri('best_season');
                $dest['duration'] = admin_read_tri('duration');
                $dest['b2c']['unique'] = admin_read_tri('b2c_unique');
                $dest['b2c']['local_tip'] = admin_read_tri('b2c_local_tip');
                $dest['b2b']['value_prop'] = admin_read_tri('b2b_value_prop');
                $dest['b2b']['ideal_for'] = admin_read_tri('b2b_ideal_for');
                $dest['b2b']['operational_note'] = admin_read_tri('b2b_operational_note');

                $toDelete = $_POST['delete_images'] ?? [];
                if (is_array($toDelete) && $toDelete) {
                    $dest['images'] = array_values(array_diff($dest['images'], $toDelete));
                }

                $imgError = '';
                if (!empty($_FILES['new_images'])) {
                    $files = $_FILES['new_images'];
                    $count = is_array($files['tmp_name']) ? count($files['tmp_name']) : 0;
                    for ($i = 0; $i < $count; $i++) {
                        admin_save_destino_image(
                            $files['error'][$i], $files['tmp_name'][$i], $files['size'][$i], $files['name'][$i],
                            $dest['id'], $dest['images'], $imgError
                        );
                        if ($imgError) break;
                    }
                }

                if ($imgError) {
                    $message = $imgError;
                    $messageType = 'error';
                } else {
                    if (is_array($toDelete)) {
                        foreach ($toDelete as $img) {
                            $path = IMAGES_DIR . '/' . basename($img);
                            if (is_file($path)) @unlink($path);
                        }
                    }
                    $data['destinations'][$index] = $dest;
                    usort($data['destinations'], function ($a, $b) { return ($a['order'] ?? 0) <=> ($b['order'] ?? 0); });
                    if (admin_save_destinos($data)) {
                        $message = '"' . $dest['name'] . '" actualizado. Ya se ve en los dos sitios.';
                        $messageType = 'success';
                    } else {
                        $message = 'No se pudieron guardar los cambios.';
                        $messageType = 'error';
                    }
                }
            }
        } elseif ($action === 'delete_destino') {
            $destinoId = $_POST['destino_id'] ?? '';
            $index = null;
            foreach ($data['destinations'] as $i => $d) {
                if ($d['id'] === $destinoId) { $index = $i; break; }
            }
            if ($index === null) {
                $message = 'No se encontró ese destino.';
                $messageType = 'error';
            } else {
                $removed = $data['destinations'][$index];
                array_splice($data['destinations'], $index, 1);
                if (admin_save_destinos($data)) {
                    foreach ($removed['images'] as $img) {
                        $path = IMAGES_DIR . '/' . basename($img);
                        if (is_file($path)) @unlink($path);
                    }
                    $message = '"' . $removed['name'] . '" borrado.';
                    $messageType = 'success';
                } else {
                    $message = 'No se pudo borrar el destino.';
                    $messageType = 'error';
                }
            }
        } elseif ($action === 'add_destino') {
            $name = trim(preg_replace('/\s+/', ' ', $_POST['name'] ?? ''));
            $uniqueEs = trim($_POST['b2c_unique_es'] ?? '');

            if ($name === '' || $uniqueEs === '') {
                $message = 'Completá al menos el nombre y "Qué lo hace único" en español.';
                $messageType = 'error';
            } else {
                $existingIds = array_map(function ($d) { return $d['id']; }, $data['destinations']);
                $id = admin_unique_slug_d(admin_slugify_d($name), $existingIds);

                $images = [];
                $imgError = '';
                if (!empty($_FILES['new_images'])) {
                    $files = $_FILES['new_images'];
                    $count = is_array($files['tmp_name']) ? count($files['tmp_name']) : 0;
                    for ($i = 0; $i < $count; $i++) {
                        admin_save_destino_image(
                            $files['error'][$i], $files['tmp_name'][$i], $files['size'][$i], $files['name'][$i],
                            $id, $images, $imgError
                        );
                        if ($imgError) break;
                    }
                }

                if ($imgError) {
                    foreach ($images as $img) {
                        $path = IMAGES_DIR . '/' . basename($img);
                        if (is_file($path)) @unlink($path);
                    }
                    $message = $imgError;
                    $messageType = 'error';
                } else {
                    $maxOrder = 0;
                    foreach ($data['destinations'] as $d) $maxOrder = max($maxOrder, (int) ($d['order'] ?? 0));
                    $data['destinations'][] = [
                        'id' => $id,
                        'order' => $maxOrder + 1,
                        'name' => $name,
                        'images' => $images,
                        'best_season' => admin_read_tri('best_season'),
                        'duration' => admin_read_tri('duration'),
                        'b2c' => [
                            'unique' => admin_read_tri('b2c_unique'),
                            'local_tip' => admin_read_tri('b2c_local_tip'),
                        ],
                        'b2b' => [
                            'value_prop' => admin_read_tri('b2b_value_prop'),
                            'ideal_for' => admin_read_tri('b2b_ideal_for'),
                            'operational_note' => admin_read_tri('b2b_operational_note'),
                        ],
                    ];
                    if (admin_save_destinos($data)) {
                        $message = '"' . $name . '" agregado. Ya se ve en los dos sitios.';
                        $messageType = 'success';
                    } else {
                        foreach ($images as $img) {
                            $path = IMAGES_DIR . '/' . basename($img);
                            if (is_file($path)) @unlink($path);
                        }
                        $message = 'No se pudo guardar el destino nuevo.';
                        $messageType = 'error';
                    }
                }
            }
        }
    }
    $data = admin_load_destinos();
}

$csrf = admin_csrf_token();
$cacheBust = time();

// Renders the 3 textareas (ES/EN/IT) for one trilingual field.
function admin_tri_fields($fieldName, $values, $idSuffix, $label, $multiline = true) {
    global $langs;
    echo '<div class="admin-field"><label>' . htmlspecialchars($label) . '</label>';
    foreach ($langs as $lang => $langLabel) {
        $inputId = $fieldName . '_' . $lang . '_' . $idSuffix;
        $value = htmlspecialchars($values[$lang] ?? '');
        echo '<p class="admin-hint" style="margin-bottom:2px">' . $langLabel . '</p>';
        if ($multiline) {
            echo '<textarea id="' . $inputId . '" name="' . $fieldName . '_' . $lang . '">' . $value . '</textarea>';
        } else {
            echo '<input type="text" id="' . $inputId . '" name="' . $fieldName . '_' . $lang . '" value="' . $value . '">';
        }
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
    <title>Destinos — Admin Argentina 1810</title>
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
        <h1 class="admin-title">Destinos</h1>
        <p class="admin-subtitle">Cada destino aparece como tarjeta en <code>/cliente/destinos.php</code> (copy para viajeros) y <code>/partners/destinos.php</code> (copy para agencias) con el mismo set de fotos. Cargá los tres idiomas — el selector ES/EN/IT del sitio depende de que estén completos.</p>

        <?php if ($message): ?>
            <div class="admin-alert <?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php foreach ($data['destinations'] as $dest): ?>
            <details class="admin-section">
                <summary><?= htmlspecialchars($dest['name']) ?></summary>
                <div class="admin-section-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="action" value="save_destino">
                        <input type="hidden" name="destino_id" value="<?= htmlspecialchars($dest['id']) ?>">

                        <div class="admin-field">
                            <label for="name_<?= htmlspecialchars($dest['id']) ?>">Nombre</label>
                            <input type="text" id="name_<?= htmlspecialchars($dest['id']) ?>" name="name" value="<?= htmlspecialchars($dest['name']) ?>">
                        </div>
                        <div class="admin-field">
                            <label for="order_<?= htmlspecialchars($dest['id']) ?>">Orden (menor = aparece primero)</label>
                            <input type="number" id="order_<?= htmlspecialchars($dest['id']) ?>" name="order" value="<?= (int) ($dest['order'] ?? 0) ?>">
                        </div>

                        <?php admin_tri_fields('best_season', $dest['best_season'] ?? [], $dest['id'], 'Mejor época', false); ?>
                        <?php admin_tri_fields('duration', $dest['duration'] ?? [], $dest['id'], 'Duración recomendada', false); ?>
                        <?php admin_tri_fields('b2c_unique', $dest['b2c']['unique'] ?? [], $dest['id'], 'B2C — Qué lo hace único'); ?>
                        <?php admin_tri_fields('b2c_local_tip', $dest['b2c']['local_tip'] ?? [], $dest['id'], 'B2C — Consejo local'); ?>
                        <?php admin_tri_fields('b2b_value_prop', $dest['b2b']['value_prop'] ?? [], $dest['id'], 'B2B — Propuesta de valor'); ?>
                        <?php admin_tri_fields('b2b_ideal_for', $dest['b2b']['ideal_for'] ?? [], $dest['id'], 'B2B — Ideal para', false); ?>
                        <?php admin_tri_fields('b2b_operational_note', $dest['b2b']['operational_note'] ?? [], $dest['id'], 'B2B — Nota operativa'); ?>

                        <div class="admin-field">
                            <label>Fotos</label>
                            <div class="admin-image-grid">
                                <?php foreach ($dest['images'] as $img): ?>
                                    <div class="admin-image-card">
                                        <div class="admin-image-card-thumb">
                                            <img src="../assets/imgs/<?= htmlspecialchars($img) ?>?v=<?= $cacheBust ?>" alt="" loading="lazy">
                                        </div>
                                        <label class="admin-image-card-delete">
                                            <input type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($img) ?>"> Borrar
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($dest['images'])): ?>
                                    <p class="admin-hint">Sin fotos todavía — el sitio muestra un placeholder mientras tanto.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="admin-field">
                            <label for="new_images_<?= htmlspecialchars($dest['id']) ?>">Agregar fotos nuevas</label>
                            <input type="file" id="new_images_<?= htmlspecialchars($dest['id']) ?>" name="new_images[]" accept="image/*" multiple>
                        </div>

                        <button type="submit" class="admin-btn admin-btn-small">Guardar cambios</button>
                    </form>

                    <form method="post" class="admin-delete-form" onsubmit="return confirm('¿Borrar &quot;<?= htmlspecialchars(addslashes($dest['name'])) ?>&quot;? Se borra de los dos sitios. No se puede deshacer.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="action" value="delete_destino">
                        <input type="hidden" name="destino_id" value="<?= htmlspecialchars($dest['id']) ?>">
                        <button type="submit" class="admin-btn admin-btn-small admin-btn-danger">Borrar este destino</button>
                    </form>
                </div>
            </details>
        <?php endforeach; ?>

        <fieldset class="admin-section admin-section-add">
            <legend>Agregar destino nuevo</legend>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action" value="add_destino">
                <div class="admin-field">
                    <label for="new_name">Nombre</label>
                    <input type="text" id="new_name" name="name" required>
                </div>
                <?php admin_tri_fields('best_season', [], 'new', 'Mejor época', false); ?>
                <?php admin_tri_fields('duration', [], 'new', 'Duración recomendada', false); ?>
                <?php admin_tri_fields('b2c_unique', [], 'new', 'B2C — Qué lo hace único (español requerido)'); ?>
                <?php admin_tri_fields('b2c_local_tip', [], 'new', 'B2C — Consejo local'); ?>
                <?php admin_tri_fields('b2b_value_prop', [], 'new', 'B2B — Propuesta de valor'); ?>
                <?php admin_tri_fields('b2b_ideal_for', [], 'new', 'B2B — Ideal para', false); ?>
                <?php admin_tri_fields('b2b_operational_note', [], 'new', 'B2B — Nota operativa'); ?>
                <div class="admin-field">
                    <label for="new_images_add">Fotos</label>
                    <input type="file" id="new_images_add" name="new_images[]" accept="image/*" multiple>
                </div>
                <button type="submit" class="admin-btn">Agregar destino</button>
            </form>
        </fieldset>
    </div>
</body>
</html>
