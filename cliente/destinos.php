<?php
$pageTitle = 'Destinos en Argentina | Argentina 1810';
$pageDescription = 'Descubre los destinos que preparamos a medida para ti, con la mirada de un equipo local.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';

$destinosData = json_decode(file_get_contents(__DIR__ . '/../destinos.json'), true);
$destinos = $destinosData['destinations'] ?? [];
usort($destinos, function ($a, $b) { return ($a['order'] ?? 0) <=> ($b['order'] ?? 0); });

$labels = [
    'es' => ['best_season' => 'Mejor época', 'duration' => 'Tiempo recomendado', 'local_tip' => 'Consejo local', 'placeholder' => 'Fotos — muy pronto'],
    'en' => ['best_season' => 'Best time to visit', 'duration' => 'Recommended time', 'local_tip' => 'Local tip', 'placeholder' => 'Photos — coming soon'],
    'it' => ['best_season' => 'Periodo migliore', 'duration' => 'Durata consigliata', 'local_tip' => 'Consiglio locale', 'placeholder' => 'Foto — in arrivo a breve'],
];
$langs = ['es', 'en', 'it'];
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas">
<?php $activePage = 'destinos'; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810">
        <h1 class="section-title" data-section="destinos" data-translate="title">Destinos</h1>
        <p class="section-lede" data-section="destinos" data-translate="lede">Lo esencial de cada lugar, antes de que lo vivas.</p>

        <div class="destino-grid">
            <?php foreach ($destinos as $dest): ?>
                <a class="destino-card" href="/cliente/destino.php?slug=<?= urlencode($dest['id']) ?>">
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

<?php $footerTrack = 'cliente'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
