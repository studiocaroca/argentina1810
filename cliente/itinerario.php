<?php
$itinerariosData = json_decode(file_get_contents(__DIR__ . '/../itinerarios.json'), true);
$vibesData = $itinerariosData['vibes'] ?? [];
$vibeSlug = $_GET['vibe'] ?? '';
$itinId = $_GET['id'] ?? '';

$vibe = $vibesData[$vibeSlug] ?? null;
$itin = null;
if ($vibe) {
    foreach (($vibe['itineraries'] ?? []) as $item) {
        if ($item['id'] === $itinId) { $itin = $item; break; }
    }
}

if (!$vibe || !$itin) {
    header('Location: /cliente/travel-vibe.php');
    exit;
}

$langs = ['es', 'en', 'it'];
$labels = [
    'es' => ['duration' => 'Duración', 'difficulty' => 'Dificultad', 'itinerary' => 'Itinerario', 'day' => 'Día', 'back' => '← Volver a Travel Vibe', 'cta' => 'Empecemos tu aventura'],
    'en' => ['duration' => 'Duration', 'difficulty' => 'Difficulty', 'itinerary' => 'Itinerary', 'day' => 'Day', 'back' => '← Back to Travel Vibe', 'cta' => "Let's start your adventure"],
    'it' => ['duration' => 'Durata', 'difficulty' => 'Difficoltà', 'itinerary' => 'Itinerario', 'day' => 'Giorno', 'back' => '← Torna a Travel Vibe', 'cta' => 'Iniziamo la tua avventura'],
];

$pageTitle = ($itin['title']['es'] ?? '') . ' | Argentina 1810';
$pageDescription = $itin['summary']['es'] ?? 'Descubre este itinerario con Argentina 1810.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas page-hero-overlay">
<?php $activePage = 'travel-vibe'; $headerOverlay = true; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <div class="destino-hero">
        <video autoplay muted loop playsinline aria-hidden="true">
            <source src="/assets/videos/paisajes-argentina.mp4" type="video/mp4">
        </video>
        <div class="destino-hero__scrim" aria-hidden="true"></div>
        <?php foreach ($langs as $lang): ?>
            <h1 class="destino-hero__title" data-lang="<?= $lang ?>"><?= htmlspecialchars($itin['title'][$lang] ?? '') ?></h1>
        <?php endforeach; ?>
    </div>

    <section class="section container-a1810 destino-detail">
        <?php foreach ($langs as $lang): ?>
            <div class="destino-detail__meta" data-lang="<?= $lang ?>">
                <div class="destino-detail__meta-item">
                    <span class="destino-detail__meta-icon" aria-hidden="true">⏱</span>
                    <div>
                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['duration']) ?></p>
                        <p class="destino-detail__meta-value"><?= htmlspecialchars($itin['duration'][$lang] ?? '') ?></p>
                    </div>
                </div>
                <div class="destino-detail__meta-item">
                    <span class="destino-detail__meta-icon" aria-hidden="true">⛰</span>
                    <div>
                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['difficulty']) ?></p>
                        <p class="destino-detail__meta-value"><?= htmlspecialchars($itin['difficulty'][$lang] ?? '') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php foreach ($langs as $lang): ?>
            <div data-lang="<?= $lang ?>">
                <p class="destino-detail__text"><?= nl2br(htmlspecialchars($itin['summary'][$lang] ?? '')) ?></p>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($itin['days'])): ?>
            <div class="itinerary-layout">
                <div class="itinerary-layout__days">
                    <?php foreach ($langs as $lang): ?>
                        <h2 class="itinerary-days__title" data-lang="<?= $lang ?>"><?= htmlspecialchars($labels[$lang]['itinerary']) ?></h2>
                    <?php endforeach; ?>
                    <div class="itinerary-days">
                        <?php foreach ($itin['days'] as $i => $day): ?>
                            <details class="itinerary-day">
                                <summary class="itinerary-day__summary">
                                    <?php foreach ($langs as $lang): ?>
                                        <span class="itinerary-day__label" data-lang="<?= $lang ?>"><?= htmlspecialchars($labels[$lang]['day']) ?> <?= $i + 1 ?></span>
                                    <?php endforeach; ?>
                                    <?php foreach ($langs as $lang): ?>
                                        <span class="itinerary-day__title" data-lang="<?= $lang ?>"><?= htmlspecialchars($day['title'][$lang] ?? '') ?></span>
                                    <?php endforeach; ?>
                                    <span class="itinerary-day__chevron" aria-hidden="true">&#9662;</span>
                                </summary>
                                <?php foreach ($langs as $lang): ?>
                                    <p class="itinerary-day__desc" data-lang="<?= $lang ?>"><?= htmlspecialchars($day['description'][$lang] ?? '') ?></p>
                                <?php endforeach; ?>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php
                    // Placeholder preview — a real deployment would pull hand-placed
                    // points from Google My Maps (or similar) per itinerary, set from
                    // the admin panel. For now this just centers a basic embed (no API
                    // key needed) on the itinerary's title so the layout can be judged.
                    $mapQuery = urlencode($itin['title']['es'] . ', Argentina');
                ?>
                <div class="itinerary-layout__map">
                    <iframe src="https://maps.google.com/maps?q=<?= $mapQuery ?>&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" aria-hidden="true"></iframe>
                </div>
            </div>
        <?php endif; ?>

        <div class="destino-detail__cta">
            <?php foreach ($langs as $lang): ?>
                <a class="btn-cta btn-cta--gold" data-lang="<?= $lang ?>" href="/cliente/contacto.php?vibe=<?= urlencode($vibeSlug) ?>"><?= htmlspecialchars($labels[$lang]['cta']) ?></a>
            <?php endforeach; ?>
        </div>

        <?php foreach ($langs as $lang): ?>
            <a class="blog-post__back destino-detail__back-below" data-lang="<?= $lang ?>" href="/cliente/travel-vibe.php"><?= htmlspecialchars($labels[$lang]['back']) ?></a>
        <?php endforeach; ?>
    </section>
</main>

<?php $footerTrack = 'cliente'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
