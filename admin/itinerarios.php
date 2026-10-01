<?php
require __DIR__ . '/config.php';
admin_require_login();

// Itineraries are data, not hand-authored HTML: /cliente/travel-vibe.php
// reads this file directly and shows a clickable tile per itinerary inside
// each Travel Vibe category; clicking one opens /cliente/itinerario.php
// with that itinerary's full day-by-day content. The 4 categories
// themselves are fixed (they're brand-level names, never renamed on the
// site) and always exist even with zero itineraries, but each one can
// hold any number of itineraries — added, edited or removed here.
define('ITINERARIOS_FILE', SITE_ROOT . '/itinerarios.json');
$allowedImgExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$maxImgBytes = 12 * 1024 * 1024;
$langs = ['es' => 'Español', 'en' => 'English', 'it' => 'Italiano'];
$vibeOrder = [
    'active-wild' => 'Active & Wild',
    'slow-immersive' => 'Slow & Immersive',
    'family-journeys' => 'Family Journeys',
    'food-wine-comfort' => 'Food, Wine & Comfort',
];

function admin_post_too_large_i() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return false;
    if (!empty($_POST) || !empty($_FILES)) return false;
    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
    return $contentLength > 0;
}

function admin_load_itinerarios() {
    global $vibeOrder;
    $raw = file_exists(ITINERARIOS_FILE) ? file_get_contents(ITINERARIOS_FILE) : '';
    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['vibes']) || !is_array($data['vibes'])) {
        $data = ['vibes' => []];
    }
    // Every fixed vibe always exists, even with no itineraries yet.
    foreach ($vibeOrder as $slug => $name) {
        if (!isset($data['vibes'][$slug]) || !is_array($data['vibes'][$slug])) {
            $data['vibes'][$slug] = ['name' => $name, 'itineraries' => []];
        }
        if (!isset($data['vibes'][$slug]['itineraries']) || !is_array($data['vibes'][$slug]['itineraries'])) {
            $data['vibes'][$slug]['itineraries'] = [];
        }
        $data['vibes'][$slug]['name'] = $name;
    }
    return $data;
}

function admin_save_itinerarios($data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) return false;
    return file_put_contents(ITINERARIOS_FILE, $json, LOCK_EX) !== false;
}

function admin_slugify_i($title) {
    $map = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ñ' => 'n',
    ];
    $slug = strtolower(strtr($title, $map));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'itinerario';
}

function admin_unique_slug_i($base, $existingIds) {
    $slug = $base;
    $n = 2;
    while (in_array($slug, $existingIds, true)) {
        $slug = $base . '-' . $n;
        $n++;
    }
    return $slug;
}

function admin_all_itinerary_ids($data) {
    $ids = [];
    foreach ($data['vibes'] as $vibe) {
        foreach ($vibe['itineraries'] as $it) $ids[] = $it['id'];
    }
    return $ids;
}

// One cover photo per itinerary, fixed filename (overwritten on re-upload)
// rather than the numbered convention destinos uses for photo galleries.
function admin_save_itinerary_cover($fileError, $tmpName, $sizeBytes, $originalName, $itinId, &$error) {
    global $allowedImgExt, $maxImgBytes;

    if ($fileError === UPLOAD_ERR_NO_FILE) return null;
    if ($fileError !== UPLOAD_ERR_OK) {
        $error = 'Hubo un problema subiendo la imagen.';
        return null;
    }
    if ($sizeBytes > $maxImgBytes) {
        $error = 'La imagen pesa más de 12MB.';
        return null;
    }
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedImgExt, true)) {
        $error = 'La imagen no es un formato soportado (jpg, png, webp, gif).';
        return null;
    }
    if (@getimagesize($tmpName) === false) {
        $error = 'El archivo subido no parece ser una imagen válida.';
        return null;
    }

    $filename = 'itinerarios-' . $itinId . '.' . $ext;
    if (!move_uploaded_file($tmpName, IMAGES_DIR . '/' . $filename)) {
        $error = 'No se pudo guardar la imagen.';
        return null;
    }
    return $filename;
}

// Reads the trilingual fields for one field out of $_POST, given a prefix
// like "title" -> title_es / title_en / title_it.
function admin_read_tri_i($prefix) {
    global $langs;
    $out = [];
    foreach (array_keys($langs) as $lang) {
        $out[$lang] = trim(str_replace(["\r\n", "\r"], "\n", $_POST[$prefix . '_' . $lang] ?? ''));
    }
    return $out;
}

// Days are a variable-length list, each with a trilingual title+description.
// $dayCount tells us how many day_title_{lang}_{i}/day_desc_{lang}_{i} sets
// to read back (skipping any marked with delete_day_{i}); a trailing
// "new_day_*" set (only its ES title required) appends one more if filled.
function admin_read_days($dayCount) {
    global $langs;
    $days = [];
    for ($i = 0; $i < $dayCount; $i++) {
        if (!empty($_POST['delete_day_' . $i])) continue;
        $title = [];
        $desc = [];
        foreach (array_keys($langs) as $lang) {
            $title[$lang] = trim($_POST['day_title_' . $lang . '_' . $i] ?? '');
            $desc[$lang] = trim(str_replace(["\r\n", "\r"], "\n", $_POST['day_desc_' . $lang . '_' . $i] ?? ''));
        }
        $days[] = ['title' => $title, 'description' => $desc];
    }
    $newTitleEs = trim($_POST['new_day_title_es'] ?? '');
    if ($newTitleEs !== '') {
        $title = [];
        $desc = [];
        foreach (array_keys($langs) as $lang) {
            $title[$lang] = trim($_POST['new_day_title_' . $lang] ?? '');
            $desc[$lang] = trim(str_replace(["\r\n", "\r"], "\n", $_POST['new_day_desc_' . $lang] ?? ''));
        }
        $days[] = ['title' => $title, 'description' => $desc];
    }
    return $days;
}

$message = '';
$messageType = '';
$data = admin_load_itinerarios();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (admin_post_too_large_i()) {
        $message = 'Lo que subiste pesa demasiado para el servidor. Probá con un archivo más liviano.';
        $messageType = 'error';
    } elseif (!admin_csrf_check()) {
        $message = 'La sesión expiró, volvé a cargar la página e intentá de nuevo.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        $vibeSlug = $_POST['vibe'] ?? '';

        if (!isset($data['vibes'][$vibeSlug])) {
            $message = 'No se encontró esa categoría de Travel Vibe.';
            $messageType = 'error';
        } elseif ($action === 'save_itinerary') {
            $itinId = $_POST['itinerary_id'] ?? '';
            $index = null;
            foreach ($data['vibes'][$vibeSlug]['itineraries'] as $i => $it) {
                if ($it['id'] === $itinId) { $index = $i; break; }
            }
            if ($index === null) {
                $message = 'No se encontró ese itinerario.';
                $messageType = 'error';
            } else {
                $itin = $data['vibes'][$vibeSlug]['itineraries'][$index];
                $itin['title'] = admin_read_tri_i('title');
                $itin['duration'] = admin_read_tri_i('duration');
                $itin['difficulty'] = admin_read_tri_i('difficulty');
                $itin['summary'] = admin_read_tri_i('summary');
                $itin['days'] = admin_read_days((int) ($_POST['day_count'] ?? 0));

                $imgError = '';
                if (!empty($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $cover = admin_save_itinerary_cover(
                        $_FILES['cover']['error'], $_FILES['cover']['tmp_name'], $_FILES['cover']['size'], $_FILES['cover']['name'],
                        $itin['id'], $imgError
                    );
                    if ($cover) $itin['cover'] = $cover;
                }

                if ($imgError) {
                    $message = $imgError;
                    $messageType = 'error';
                } else {
                    $data['vibes'][$vibeSlug]['itineraries'][$index] = $itin;
                    if (admin_save_itinerarios($data)) {
                        $message = '"' . $itin['title']['es'] . '" actualizado. Ya se ve en Travel Vibe.';
                        $messageType = 'success';
                    } else {
                        $message = 'No se pudieron guardar los cambios.';
                        $messageType = 'error';
                    }
                }
            }
        } elseif ($action === 'delete_itinerary') {
            $itinId = $_POST['itinerary_id'] ?? '';
            $index = null;
            foreach ($data['vibes'][$vibeSlug]['itineraries'] as $i => $it) {
                if ($it['id'] === $itinId) { $index = $i; break; }
            }
            if ($index === null) {
                $message = 'No se encontró ese itinerario.';
                $messageType = 'error';
            } else {
                $removed = $data['vibes'][$vibeSlug]['itineraries'][$index];
                array_splice($data['vibes'][$vibeSlug]['itineraries'], $index, 1);
                if (admin_save_itinerarios($data)) {
                    if (!empty($removed['cover'])) {
                        $path = IMAGES_DIR . '/' . basename($removed['cover']);
                        if (is_file($path)) @unlink($path);
                    }
                    $message = '"' . $removed['title']['es'] . '" borrado.';
                    $messageType = 'success';
                } else {
                    $message = 'No se pudo borrar el itinerario.';
                    $messageType = 'error';
                }
            }
        } elseif ($action === 'add_itinerary') {
            $titleEs = trim($_POST['title_es'] ?? '');
            if ($titleEs === '') {
                $message = 'Completá al menos el título en español.';
                $messageType = 'error';
            } else {
                $id = admin_unique_slug_i($vibeSlug . '-' . admin_slugify_i($titleEs), admin_all_itinerary_ids($data));

                $cover = '';
                $imgError = '';
                if (!empty($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $saved = admin_save_itinerary_cover(
                        $_FILES['cover']['error'], $_FILES['cover']['tmp_name'], $_FILES['cover']['size'], $_FILES['cover']['name'],
                        $id, $imgError
                    );
                    if ($saved) $cover = $saved;
                }

                if ($imgError) {
                    $message = $imgError;
                    $messageType = 'error';
                } else {
                    $data['vibes'][$vibeSlug]['itineraries'][] = [
                        'id' => $id,
                        'cover' => $cover,
                        'title' => admin_read_tri_i('title'),
                        'duration' => admin_read_tri_i('duration'),
                        'difficulty' => admin_read_tri_i('difficulty'),
                        'summary' => admin_read_tri_i('summary'),
                        'days' => [],
                    ];
                    if (admin_save_itinerarios($data)) {
                        $message = '"' . $titleEs . '" agregado. Editalo para sumar el día a día.';
                        $messageType = 'success';
                    } else {
                        if ($cover) {
                            $path = IMAGES_DIR . '/' . basename($cover);
                            if (is_file($path)) @unlink($path);
                        }
                        $message = 'No se pudo agregar el itinerario.';
                        $messageType = 'error';
                    }
                }
            }
        }
    }
    $data = admin_load_itinerarios();
}

$csrf = admin_csrf_token();
$cacheBust = time();

// Renders the 3 inputs/textareas (ES/EN/IT) for one trilingual field.
function admin_tri_fields_i($fieldName, $values, $idSuffix, $label, $multiline = true) {
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

// Days need the index baked into the field NAME (day_title_es_0,
// day_title_es_1, ...), not just an id suffix, or every day would submit
// to the same name and only the last one would survive — admin_tri_fields_i
// can't do that, so days get their own renderer.
function admin_day_fields($day, $index) {
    global $langs;
    echo '<div class="admin-field"><label>Título del día</label>';
    foreach ($langs as $lang => $langLabel) {
        $value = htmlspecialchars($day['title'][$lang] ?? '');
        echo '<p class="admin-hint" style="margin-bottom:2px">' . $langLabel . '</p>';
        echo '<input type="text" name="day_title_' . $lang . '_' . $index . '" value="' . $value . '">';
    }
    echo '</div>';
    echo '<div class="admin-field"><label>Descripción del día</label>';
    foreach ($langs as $lang => $langLabel) {
        $value = htmlspecialchars($day['description'][$lang] ?? '');
        echo '<p class="admin-hint" style="margin-bottom:2px">' . $langLabel . '</p>';
        echo '<textarea name="day_desc_' . $lang . '_' . $index . '">' . $value . '</textarea>';
    }
    echo '</div>';
}

// Fixed field names (new_day_title_es, etc.) — admin_read_days() only
// appends this as a new day when its Spanish title isn't blank.
function admin_new_day_fields() {
    global $langs;
    echo '<div class="admin-field"><label>Título del día nuevo</label>';
    foreach ($langs as $lang => $langLabel) {
        echo '<p class="admin-hint" style="margin-bottom:2px">' . $langLabel . '</p>';
        echo '<input type="text" name="new_day_title_' . $lang . '">';
    }
    echo '</div>';
    echo '<div class="admin-field"><label>Descripción del día nuevo</label>';
    foreach ($langs as $lang => $langLabel) {
        echo '<p class="admin-hint" style="margin-bottom:2px">' . $langLabel . '</p>';
        echo '<textarea name="new_day_desc_' . $lang . '"></textarea>';
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
    <title>Itinerarios — Admin Argentina 1810</title>
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
        <h1 class="admin-title">Itinerarios de Travel Vibe</h1>
        <p class="admin-subtitle">Cada Travel Vibe (las 4 categorías de la marca, fijas) puede tener cualquier cantidad de itinerarios. Cada uno aparece como una tarjeta con foto en <code>/cliente/travel-vibe.php</code> y, al hacer clic, abre su propia página con el día a día en <code>/cliente/itinerario.php</code>. Cargá los tres idiomas — el selector ES/EN/IT del sitio depende de que estén completos.</p>

        <?php if ($message): ?>
            <div class="admin-alert <?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php foreach ($vibeOrder as $vibeSlug => $vibeName): ?>
            <?php $itineraries = $data['vibes'][$vibeSlug]['itineraries']; ?>
            <details class="admin-section" open>
                <summary><?= htmlspecialchars($vibeName) ?> (<?= count($itineraries) ?>)</summary>
                <div class="admin-section-body">
                    <?php if (empty($itineraries)): ?>
                        <p class="admin-hint">Todavía no tiene itinerarios — el sitio muestra un estado "muy pronto" mientras tanto.</p>
                    <?php endif; ?>

                    <?php foreach ($itineraries as $itin): ?>
                        <details class="admin-section">
                            <summary><?= htmlspecialchars($itin['title']['es'] ?? $itin['id']) ?></summary>
                            <div class="admin-section-body">
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="action" value="save_itinerary">
                                    <input type="hidden" name="vibe" value="<?= htmlspecialchars($vibeSlug) ?>">
                                    <input type="hidden" name="itinerary_id" value="<?= htmlspecialchars($itin['id']) ?>">
                                    <input type="hidden" name="day_count" value="<?= count($itin['days'] ?? []) ?>">

                                    <?php admin_tri_fields_i('title', $itin['title'] ?? [], $itin['id'], 'Título', false); ?>
                                    <?php admin_tri_fields_i('duration', $itin['duration'] ?? [], $itin['id'], 'Duración', false); ?>
                                    <?php admin_tri_fields_i('difficulty', $itin['difficulty'] ?? [], $itin['id'], 'Dificultad', false); ?>
                                    <?php admin_tri_fields_i('summary', $itin['summary'] ?? [], $itin['id'], 'Resumen'); ?>

                                    <div class="admin-field">
                                        <label>Foto de portada</label>
                                        <?php if (!empty($itin['cover'])): ?>
                                            <div class="admin-image-card-thumb">
                                                <img src="../assets/imgs/<?= htmlspecialchars($itin['cover']) ?>?v=<?= $cacheBust ?>" alt="" loading="lazy">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="cover" accept="image/*">
                                        <p class="admin-hint">Subir una nueva reemplaza la actual.</p>
                                    </div>

                                    <div class="admin-field">
                                        <label>Día a día</label>
                                        <?php foreach (($itin['days'] ?? []) as $di => $day): ?>
                                            <div class="admin-day-block">
                                                <p class="admin-hint"><strong>Día <?= $di + 1 ?></strong> — <label class="admin-image-card-delete"><input type="checkbox" name="delete_day_<?= $di ?>" value="1"> Borrar este día</label></p>
                                                <?php admin_day_fields($day, $di); ?>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="admin-day-block">
                                            <p class="admin-hint"><strong>Agregar día nuevo</strong></p>
                                            <?php admin_new_day_fields(); ?>
                                        </div>
                                    </div>

                                    <button type="submit" class="admin-btn admin-btn-small">Guardar cambios</button>
                                </form>

                                <form method="post" class="admin-delete-form" onsubmit="return confirm('¿Borrar &quot;<?= htmlspecialchars(addslashes($itin['title']['es'] ?? $itin['id'])) ?>&quot;? No se puede deshacer.');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="action" value="delete_itinerary">
                                    <input type="hidden" name="vibe" value="<?= htmlspecialchars($vibeSlug) ?>">
                                    <input type="hidden" name="itinerary_id" value="<?= htmlspecialchars($itin['id']) ?>">
                                    <button type="submit" class="admin-btn admin-btn-small admin-btn-danger">Borrar este itinerario</button>
                                </form>
                            </div>
                        </details>
                    <?php endforeach; ?>

                    <fieldset class="admin-section admin-section-add">
                        <legend>Agregar itinerario a <?= htmlspecialchars($vibeName) ?></legend>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="action" value="add_itinerary">
                            <input type="hidden" name="vibe" value="<?= htmlspecialchars($vibeSlug) ?>">
                            <?php admin_tri_fields_i('title', [], 'new_' . $vibeSlug, 'Título (español requerido)', false); ?>
                            <?php admin_tri_fields_i('duration', [], 'new_' . $vibeSlug, 'Duración', false); ?>
                            <?php admin_tri_fields_i('difficulty', [], 'new_' . $vibeSlug, 'Dificultad', false); ?>
                            <?php admin_tri_fields_i('summary', [], 'new_' . $vibeSlug, 'Resumen'); ?>
                            <div class="admin-field">
                                <label for="cover_new_<?= htmlspecialchars($vibeSlug) ?>">Foto de portada</label>
                                <input type="file" id="cover_new_<?= htmlspecialchars($vibeSlug) ?>" name="cover" accept="image/*">
                            </div>
                            <p class="admin-hint">El día a día se agrega editando el itinerario después de crearlo.</p>
                            <button type="submit" class="admin-btn">Agregar itinerario</button>
                        </form>
                    </fieldset>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</body>
</html>
