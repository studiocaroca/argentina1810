<?php
$pageTitle = 'Nosotros | Argentina 1810 Partner Agency';
$pageDescription = 'Somos jóvenes, sofisticados e intrépidos — pero antes que nada, confiables.';
$i18nSrc = '/partners/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="compact-title page-white-canvas">
<div class="fondo08" aria-hidden="true">
    <div class="fondo08__arc fondo08__arc--a"></div>
    <div class="fondo08__arc fondo08__arc--b"></div>
    <div class="fondo08__arc fondo08__arc--c"></div>
</div>
<?php $activePage = 'nosotros'; include __DIR__ . '/../partials/nav-partners.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810">
        <div class="nosotros-intro">
            <div>
                <h1 class="section-title title-tight" data-section="nosotros" data-translate="title">Somos jóvenes, sofisticados e intrépidos — pero antes que nada, confiables</h1>
                <p class="section-lede" data-section="nosotros" data-translate="paragraph1">Argentina 1810 nace para que tu agencia disponga de un equipo local que responda con rapidez, se comunique con transparencia y cuide a tu pasajero como si fuera propio. Cuando vendes Argentina con nosotros, dejas de contratar a un proveedor y ganas un socio.</p>
                <p class="section-lede" data-section="nosotros" data-translate="paragraph2">Elegimos colaboradores que comparten nuestros principios de respeto, transparencia y compromiso con el destino, porque cada pasajero que reciben representa también la reputación de tu agencia.</p>
            </div>
            <div class="video-placeholder" role="img" aria-label="Video institucional">
                <video class="video-placeholder__bg" autoplay muted loop playsinline aria-hidden="true">
                    <source src="/assets/videos/paisajes-argentina.mp4" type="video/mp4">
                </video>
                <span class="video-placeholder__icon">▶</span>
                <span class="video-placeholder__label" data-section="home" data-translate="video_placeholder">Video institucional</span>
            </div>
        </div>

        <h2 class="section-title title-tight" style="margin-top:88px" data-section="home" data-translate="trust_title">Por qué trabajar con nosotros</h2>
        <div class="trust-coverflow">
            <button type="button" class="trust-coverflow-arrow trust-coverflow-arrow--prev" aria-label="Anterior">&#10094;</button>
            <button type="button" class="trust-coverflow-arrow trust-coverflow-arrow--next" aria-label="Siguiente">&#10095;</button>
            <div class="trust-coverflow-wrapper">
                <div class="trust-card" data-section="home" data-translate="highlight1">Respondemos rápido — entre 24 y 48 horas.</div>
                <div class="trust-card" data-section="home" data-translate="highlight2">Itinerarios a medida — nunca propuestas genéricas.</div>
                <div class="trust-card" data-section="home" data-translate="highlight3">Argentine Host — comunicación constante, 24/7.</div>
            </div>
        </div>
    </section>
</main>

<?php $footerTrack = 'partners'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
