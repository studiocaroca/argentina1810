<?php
$destinosData = json_decode(file_get_contents(__DIR__ . '/../destinos.json'), true);
$destinos = $destinosData['destinations'] ?? [];
$slug = $_GET['slug'] ?? '';
$dest = null;
foreach ($destinos as $d) {
    if ($d['id'] === $slug) { $dest = $d; break; }
}

if (!$dest) {
    header('Location: /partners/destinos.php');
    exit;
}

$langs = ['es', 'en', 'it'];
$labels = [
    'es' => ['best_season' => 'Mejor época', 'duration' => 'Duración sugerida', 'ideal_for' => 'Ideal para', 'placeholder' => 'Fotos — muy pronto', 'back' => '← Volver a destinos'],
    'en' => ['best_season' => 'Best time to visit', 'duration' => 'Suggested duration', 'ideal_for' => 'Ideal for', 'placeholder' => 'Photos — coming soon', 'back' => '← Back to destinations'],
    'it' => ['best_season' => 'Periodo migliore', 'duration' => 'Durata suggerita', 'ideal_for' => 'Ideale per', 'placeholder' => 'Foto — in arrivo a breve', 'back' => '← Torna alle destinazioni'],
];

$pageTitle = $dest['name'] . ' | Argentina 1810 Partner Agency';
$pageDescription = $dest['b2b']['value_prop']['es'] ?? 'Descubre este destino con Argentina 1810.';
$i18nSrc = '/partners/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="page-white-canvas page-hero-overlay">
<?php $activePage = 'destinos'; $headerOverlay = true; include __DIR__ . '/../partials/nav-partners.php'; ?>

<main id="contenido-principal">
    <div class="destino-hero">
        <video autoplay muted loop playsinline aria-hidden="true">
            <source src="/assets/videos/paisajes-argentina.mp4" type="video/mp4">
        </video>
        <div class="destino-hero__scrim" aria-hidden="true"></div>
        <h1 class="destino-hero__title"><?= htmlspecialchars($dest['name']) ?></h1>
    </div>

    <section class="section container-a1810 destino-detail">
        <?php foreach ($langs as $lang): ?>
            <div class="destino-detail__meta destino-detail__meta--row3" data-lang="<?= $lang ?>">
                <div class="destino-detail__meta-item">
                    <span class="destino-detail__meta-icon" aria-hidden="true">★</span>
                    <div>
                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['ideal_for']) ?></p>
                        <p class="destino-detail__meta-value"><?= htmlspecialchars($dest['b2b']['ideal_for'][$lang] ?? '') ?></p>
                    </div>
                </div>
                <div class="destino-detail__meta-item">
                    <span class="destino-detail__meta-icon" aria-hidden="true">☀</span>
                    <div>
                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['best_season']) ?></p>
                        <p class="destino-detail__meta-value"><?= htmlspecialchars($dest['best_season'][$lang] ?? '') ?></p>
                    </div>
                </div>
                <div class="destino-detail__meta-item">
                    <span class="destino-detail__meta-icon" aria-hidden="true">⏱</span>
                    <div>
                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['duration']) ?></p>
                        <p class="destino-detail__meta-value"><?= htmlspecialchars($dest['duration'][$lang] ?? '') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php
            $galleryPaths = array_map(function ($src) { return '/assets/imgs/' . $src; }, $dest['images'] ?? []);
        ?>
        <?php if (!empty($galleryPaths)): ?>
            <div class="blog-post__aside" aria-hidden="true" data-gallery='<?= htmlspecialchars(json_encode($galleryPaths)) ?>'>
                <div class="blog-post__aside-frame"></div>
            </div>
        <?php endif; ?>

        <?php foreach ($langs as $lang): ?>
            <div data-lang="<?= $lang ?>">
                <p class="destino-detail__text"><?= nl2br(htmlspecialchars($dest['b2b']['value_prop'][$lang] ?? '')) ?></p>
                <blockquote class="destino-detail__tip">
                    <?= nl2br(htmlspecialchars($dest['b2b']['operational_note'][$lang] ?? '')) ?>
                </blockquote>
            </div>
        <?php endforeach; ?>

        <div class="destino-detail__cta">
            <a class="btn-cta btn-cta--gold" href="/partners/contacto.php">Quiero cotizar <?= htmlspecialchars($dest['name']) ?></a>
        </div>

        <?php foreach ($langs as $lang): ?>
            <a class="blog-post__back destino-detail__back-below" data-lang="<?= $lang ?>" href="/partners/destinos.php"><?= htmlspecialchars($labels[$lang]['back']) ?></a>
        <?php endforeach; ?>
    </section>
</main>

<?php $footerTrack = 'partners'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
