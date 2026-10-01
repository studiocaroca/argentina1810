<?php
$pageTitle = 'Destinos para tu propuesta | Argentina 1810 Partner Agency';
$pageDescription = 'Información resumida y práctica de cada destino para armar tu propuesta rápido.';
$i18nSrc = '/partners/translations.json';
include __DIR__ . '/../partials/head.php';

$destinosData = json_decode(file_get_contents(__DIR__ . '/../destinos.json'), true);
$destinos = $destinosData['destinations'] ?? [];
usort($destinos, function ($a, $b) { return ($a['order'] ?? 0) <=> ($b['order'] ?? 0); });

$labels = [
    'es' => ['placeholder' => 'Fotos — muy pronto'],
    'en' => ['placeholder' => 'Photos — coming soon'],
    'it' => ['placeholder' => 'Foto — in arrivo a breve'],
];
$langs = ['es', 'en', 'it'];
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="compact-title page-white-canvas">
<?php $activePage = 'destinos'; include __DIR__ . '/../partials/nav-partners.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810">
        <h1 class="section-title destinos-title" data-section="destinos" data-translate="title">Destinos</h1>
        <p class="section-lede destinos-lede" data-section="destinos" data-translate="lede">Lo esencial de cada destino, listo para armar tu propuesta.</p>

        <div class="destino-grid">
            <?php foreach ($destinos as $dest): ?>
                <a class="destino-card" href="/partners/destino.php?slug=<?= urlencode($dest['id']) ?>">
                    <?php if (!empty($dest['images'][0])): ?>
                        <img src="/assets/imgs/<?= htmlspecialchars($dest['images'][0]) ?>" alt="<?= htmlspecialchars($dest['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <?php foreach ($langs as $lang): ?>
                            <span class="destino-card__placeholder" data-lang="<?= $lang ?>"><?= htmlspecialchars($labels[$lang]['placeholder']) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="destino-card__scrim" aria-hidden="true"></div>
                    <h2 class="destino-card__name"><?= htmlspecialchars($dest['name']) ?></h2>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php $footerTrack = 'partners'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
