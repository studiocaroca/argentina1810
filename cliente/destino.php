<?php
$destinosData = json_decode(file_get_contents(__DIR__ . '/../destinos.json'), true);
$destinos = $destinosData['destinations'] ?? [];
$slug = $_GET['slug'] ?? '';
$dest = null;
foreach ($destinos as $d) {
    if ($d['id'] === $slug) { $dest = $d; break; }
}

if (!$dest) {
    header('Location: /cliente/destinos.php');
    exit;
}

$langs = ['es', 'en', 'it'];
$labels = [
    'es' => ['best_season' => 'Mejor época', 'duration' => 'Tiempo recomendado', 'local_tip' => 'Consejo local', 'placeholder' => 'Fotos — muy pronto', 'back' => '← Volver a destinos'],
    'en' => ['best_season' => 'Best time to visit', 'duration' => 'Recommended time', 'local_tip' => 'Local tip', 'placeholder' => 'Photos — coming soon', 'back' => '← Back to destinations'],
    'it' => ['best_season' => 'Periodo migliore', 'duration' => 'Durata consigliata', 'local_tip' => 'Consiglio locale', 'placeholder' => 'Foto — in arrivo a breve', 'back' => '← Torna alle destinazioni'],
];

$pageTitle = $dest['name'] . ' | Argentina 1810';
$pageDescription = $dest['b2c']['unique']['es'] ?? 'Descubre este destino con Argentina 1810.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas page-hero-overlay">
<?php $activePage = 'destinos'; $headerOverlay = true; include __DIR__ . '/../partials/nav-cliente.php'; ?>

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
            <div class="destino-detail__meta" data-lang="<?= $lang ?>">
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
                <p class="destino-detail__text"><?= nl2br(htmlspecialchars($dest['b2c']['unique'][$lang] ?? '')) ?></p>
                <blockquote class="destino-detail__tip">
                    <strong><?= htmlspecialchars($labels[$lang]['local_tip']) ?>:</strong>
                    <?= nl2br(htmlspecialchars($dest['b2c']['local_tip'][$lang] ?? '')) ?>
                </blockquote>
            </div>
        <?php endforeach; ?>

        <?php foreach ($langs as $lang): ?>
            <a class="blog-post__back" data-lang="<?= $lang ?>" href="/cliente/destinos.php"><?= htmlspecialchars($labels[$lang]['back']) ?></a>
        <?php endforeach; ?>

        <div class="destino-detail__cta">
            <a class="btn-cta btn-cta--gold" href="/cliente/contacto.php">Quiero viajar a <?= htmlspecialchars($dest['name']) ?></a>
        </div>
    </section>
</main>

<?php $footerTrack = 'cliente'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
