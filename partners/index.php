<?php
$pageTitle = 'Argentina 1810 | DMC boutique en Argentina';
$pageDescription = 'Tu socio local para vender Argentina con confianza: respuesta ágil, procesos claros y cuidado real del pasajero.';
$i18nSrc = '/partners/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="page-hero-overlay theme-partners-violet">
<?php $activePage = 'home'; include __DIR__ . '/../partials/nav-partners.php'; ?>

<main id="contenido-principal">
    <section class="hero hero--video">
        <div class="hero__bg">
            <video class="hero__bg-video" autoplay muted loop playsinline aria-hidden="true">
                <source src="/assets/videos/paisajes-argentina.mp4" type="video/mp4">
            </video>
            <div class="hero__bg-overlay"></div>
            <div class="hero__bg-content container-a1810">
                <p class="section-eyebrow" data-section="home" data-translate="eyebrow">Partner Agency</p>
                <h1 class="hero__title"><span class="accent" data-section="home" data-translate="title">Tu socio local en Argentina.</span></h1>
                <p class="hero__lede" data-section="home" data-translate="lede">Tu pasajero es nuestra prioridad. Trabajaremos contigo para cuidar su experiencia y mantenerte informado durante todo el viaje. A través de Argentine Host dispondrá de asistencia las 24 horas y, cuando sea necesario, podrá contactar con una persona que conoce su itinerario.</p>
                <div class="hero__ctas">
                    <a class="btn-cta btn-cta--gold" href="/partners/como-trabajamos.php" data-section="home" data-translate="cta_primary">Conoce cómo trabajamos</a>
                    <a class="btn-cta btn-cta--outline" href="/partners/contacto.php" data-section="home" data-translate="cta_secondary">Únete como agencia socia</a>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
