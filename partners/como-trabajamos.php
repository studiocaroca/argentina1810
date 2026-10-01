<?php
$pageTitle = 'Cómo trabajamos con la agencia | Argentina 1810';
$pageDescription = 'Así diseñamos, cotizamos y acompañamos cada viaje que preparamos junto a tu agencia.';
$i18nSrc = '/partners/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="compact-title">
<?php $activePage = 'como-trabajamos'; include __DIR__ . '/../partials/nav-partners.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810">
        <h1 class="section-title" data-section="como_trabajamos" data-translate="title">Cómo trabajamos</h1>
        <p class="section-lede" data-section="como_trabajamos" data-translate="intro">Conocemos los destinos, los hoteles y a nuestros operadores locales… pero sobre todo, conocemos los principales desafíos a los que nos enfrentaremos juntos a lo largo de cada viaje, y sabemos cómo resolverlos para que la experiencia sea inolvidable:</p>

        <ol class="process-list">
            <li data-section="como_trabajamos" data-translate="step1">Nos cuentas el perfil de tu pasajero y lo que estás buscando.</li>
            <li data-section="como_trabajamos" data-translate="step2">Diseñamos un itinerario a medida con proveedores verificados en destino.</li>
            <li data-section="como_trabajamos" data-translate="step3">Te enviamos un presupuesto clara y profesional, sin letra pequeña.</li>
            <li data-section="como_trabajamos" data-translate="step4">Nuestro Argentine Host acompañará a tu pasajero antes y durante el viaje, con una comunicación proactiva.</li>
            <li data-section="como_trabajamos" data-translate="step5">Cerramos con un reporte de la experiencia y seguimiento posterior al viaje.</li>
        </ol>

        <h2 class="section-title" style="margin-top:88px" data-section="como_trabajamos" data-translate="why_title">Por qué elegirnos</h2>
        <div class="trust-coverflow trust-coverflow--tall">
            <button type="button" class="trust-coverflow-arrow trust-coverflow-arrow--prev" aria-label="Anterior">&#10094;</button>
            <button type="button" class="trust-coverflow-arrow trust-coverflow-arrow--next" aria-label="Siguiente">&#10095;</button>
            <div class="trust-coverflow-wrapper">
                <div class="trust-card" data-section="como_trabajamos" data-translate="why1">Respondemos entre 24 a 48 horas buscando las mejores soluciones.</div>
                <div class="trust-card" data-section="como_trabajamos" data-translate="why2">Itinerarios realmente a medida, nunca propuestas genéricas, pero sí proveedores probados.</div>
                <div class="trust-card" data-section="como_trabajamos" data-translate="why3">Servicio Argentine Host: comunicación constante y un número de teléfono disponible las 24 horas. Cuando sea necesario, tu pasajero podrá hablar con una persona que conozca su itinerario y hable su idioma. Tú recibirás la información necesaria sin tener que perseguirnos para saber qué está ocurriendo.</div>
                <div class="trust-card" data-section="como_trabajamos" data-translate="why4">Ante cualquier imprevisto, te informamos de las alternativas y compartimos nuestra recomendación. Después decidimos contigo cómo actuar para que tu pasajero nunca esté solo.</div>
                <div class="trust-card" data-section="como_trabajamos" data-translate="why5">Procesos y documentación claros, pensados para integrarse fácilmente a tu forma de trabajar.</div>
            </div>
        </div>
    </section>
</main>

<?php $footerTrack = 'partners'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
