<?php
$pageTitle = 'Argentina 1810 | Viajes a medida por Argentina';
$pageDescription = 'Diseñamos tu viaje ideal a Argentina, acompañado por especialistas locales de principio a fin.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="page-hero-overlay">
<?php $activePage = 'home'; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <section class="hero hero--video">
        <div class="hero__bg">
            <video class="hero__bg-video" autoplay muted loop playsinline aria-hidden="true">
                <source src="/assets/videos/paisajes-argentina.mp4" type="video/mp4">
            </video>
            <div class="hero__bg-overlay"></div>
            <div class="hero__bg-content container-a1810">
                <p class="section-eyebrow" data-section="home" data-translate="eyebrow">Free Explorer</p>
                <h1 class="hero__title">
                    <span data-section="home" data-translate="title1">This is where</span><br>
                    <span class="accent" data-section="home" data-translate="title2">Argentina begins.</span>
                </h1>
                <p class="hero__lede" data-section="home" data-translate="lede1">Diseñamos viajes por la Argentina más auténtica: organizados, cercanos y pensados únicamente para ti.</p>
                <p class="hero__lede" data-section="home" data-translate="lede2">Esta es tu oportunidad de descubrir Argentina, de concretar todas tus ideas mientras escuchas propuestas de profesionales apasionados por su país y su cultura. Estamos aquí para acompañarte en una aventura con recuerdos para toda la vida a través de un viaje que solamente te hará querer seguir descubriendo nuestro rincón del mundo.</p>
                <div class="hero__ctas">
                    <a class="btn-cta btn-cta--gold" href="/cliente/travel-vibe.php" data-section="home" data-translate="cta_primary">Descubre tu Travel Vibe</a>
                    <a class="btn-cta btn-cta--outline" href="/cliente/nosotros.php" data-section="home" data-translate="cta_secondary">Conoce Argentina 1810</a>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
