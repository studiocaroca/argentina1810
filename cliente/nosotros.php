<?php
$pageTitle = 'Nosotros | Argentina 1810';
$pageDescription = 'Somos tu equipo local en Argentina — conocé cómo trabajamos y qué es el Argentine Host.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas">
<div class="fondo08" aria-hidden="true">
    <div class="fondo08__arc fondo08__arc--a"></div>
    <div class="fondo08__arc fondo08__arc--b"></div>
    <div class="fondo08__arc fondo08__arc--c"></div>
</div>
<?php $activePage = 'nosotros'; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810">
        <div class="nosotros-intro">
            <div>
                <h1 class="section-title title-tight" data-section="nosotros" data-translate="title">Somos tu equipo local en Argentina</h1>
                <p class="section-lede" data-section="nosotros" data-translate="paragraph1">Argentina 1810 nace para ti, que quieres descubrir nuestro país sin preocuparte por la organización. Escuchamos lo que buscas y diseñamos el viaje contigo para que puedas compartirlo con quien elijas. Nosotros te ayudaremos a recorrer este país inmenso y a crear una experiencia personal en cada destino.</p>
                <p class="section-lede" data-section="nosotros" data-translate="paragraph2">Antes de que llegues, nos conoceremos; cuando regreses a casa, seguiremos estando a tu disposición.</p>
                <p class="section-lede" data-section="nosotros" data-translate="paragraph3">Somos jóvenes, pero no improvisamos. Somos intrépidos, pero nunca ponemos la aventura por encima de tu seguridad y tu tranquilidad. Cada recomendación nace de cómo nos sentimos nosotros al visitar estos destinos.</p>
            </div>
            <div class="video-placeholder" role="img" aria-label="Video institucional">
                <video class="video-placeholder__bg" autoplay muted loop playsinline aria-hidden="true">
                    <source src="/assets/videos/paisajes-argentina.mp4" type="video/mp4">
                </video>
                <span class="video-placeholder__icon">▶</span>
                <span class="video-placeholder__label" data-section="home" data-translate="video_placeholder">Video institucional</span>
            </div>
        </div>

        <h2 class="section-title title-tight" style="margin-top:56px" data-section="home" data-translate="trust_title">Por qué viajar con nosotros</h2>
        <div class="trust-coverflow trust-coverflow--nosotros">
            <button type="button" class="trust-coverflow-arrow trust-coverflow-arrow--prev" aria-label="Anterior">&#10094;</button>
            <button type="button" class="trust-coverflow-arrow trust-coverflow-arrow--next" aria-label="Siguiente">&#10095;</button>
            <div class="trust-coverflow-wrapper">
                <div class="trust-card" data-section="principles" data-translate="p1" data-translate-html>Viajamos con <strong>responsabilidad</strong> — solo trabajamos con proveedores que cuidan el destino y a sus comunidades.</div>
                <div class="trust-card" data-section="principles" data-translate="p2" data-translate-html><strong>Nunca estás solo</strong> — ante cualquier imprevisto, estaremos ahí para ayudarte y ofrecerte todas las posibilidades mientras te damos nuestro consejo profesional para que tu viaje no se vea afectado.</div>
                <div class="trust-card" data-section="principles" data-translate="p3" data-translate-html>La <strong>honestidad</strong> está por encima de la venta — aconsejamos, no presionamos.</div>
                <div class="trust-card" data-section="principles" data-translate="p4" data-translate-html><strong>Cero sorpresas</strong> — sabes exactamente qué incluye tu viaje en cada etapa.</div>
                <div class="trust-card" data-section="principles" data-translate="p5" data-translate-html><strong>Cada viaje es único</strong> — escuchamos antes de proponer.</div>
            </div>
        </div>

        <div class="host-block">
            <div class="host-block__media">
                <img src="/assets/imgs/iconos/Destacadas-10.png" alt="" class="host-block__icon">
            </div>
            <div class="host-block__content">
                <h2 class="host-block__title" data-section="nosotros" data-translate="host_title">Argentine Host</h2>
                <p data-section="nosotros" data-translate="host_paragraph1" data-translate-html>Antes de tu llegada, tu <strong>Argentine Host</strong> se pondrá en contacto contigo por el canal que prefieras para responder a tus preguntas. También te ayudará a preparar el equipaje y te facilitará la información necesaria para llegar con tranquilidad. En el aeropuerto, tu conductor te esperará con un cartel en el que aparecerán tu nombre y nuestro logotipo para que puedas identificarlo. Durante el primer día, tu <strong>Argentine Host</strong> se reunirá contigo para repasar el itinerario y resolver las últimas dudas. Después, mantendréis el contacto por WhatsApp o correo electrónico para que recibas la información y la ayuda que necesites durante el viaje.</p>
                <p data-section="nosotros" data-translate="host_paragraph2" data-translate-html>En caso de emergencia o si necesitas una respuesta inmediata, dispondrás de un <strong>número de asistencia activo las 24 horas</strong>. Te atenderá una persona que conoce los servicios incluidos en tu viaje y dispone de los contactos necesarios de cada operador local y alojamiento. Te ayudará a valorar las alternativas y a encontrar una solución adecuada.</p>
            </div>
        </div>
    </section>
</main>

<?php $footerTrack = 'cliente'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
