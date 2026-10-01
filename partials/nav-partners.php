<?php
// Nav for the Partner Agency (B2B) track. Caller sets $activePage before
// including this (one of: home, nosotros, como-trabajamos, destinos,
// contacto). Expects to be included right after <body data-i18n-src="...">.
if (!isset($activePage)) $activePage = '';
if (!isset($headerOverlay)) $headerOverlay = false;
function a1810_nav_class_p($page, $active) { return $page === $active ? 'nav-link active' : 'nav-link'; }
?>
<a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

<header class="site-header site-header--partners<?= ($activePage === 'home' || $headerOverlay) ? ' site-header--overlay' : '' ?>">
    <nav class="site-navbar" aria-label="Principal">
        <a class="site-brand" href="/partners/">
            <img class="site-brand__logo" src="/assets/imgs/logos/logo-horizontal-tagline-white.png" alt="Argentina 1810 — Journeys, made personal">
        </a>

        <div id="lang-selector" class="lang-selector">
            <button class="lang-selector__current" id="lang-current" type="button" aria-haspopup="true" aria-expanded="false">ES</button>
            <div class="lang-selector__dropdown" id="lang-dropdown" role="menu"></div>
        </div>

        <button class="site-navbar__toggle" type="button" aria-controls="site-navbar-links" aria-expanded="false" aria-label="Abrir menú">
            <span></span><span></span><span></span>
        </button>

        <div class="site-navbar__links" id="site-navbar-links">
            <a class="<?= a1810_nav_class_p('home', $activePage) ?>" data-section="menu" data-translate="home" href="/partners/">Home</a>
            <a class="<?= a1810_nav_class_p('nosotros', $activePage) ?>" data-section="menu" data-translate="about" href="/partners/nosotros.php">Nosotros</a>
            <a class="<?= a1810_nav_class_p('como-trabajamos', $activePage) ?>" data-section="menu" data-translate="how_we_work" href="/partners/como-trabajamos.php">Cómo trabajamos</a>
            <a class="<?= a1810_nav_class_p('destinos', $activePage) ?>" data-section="menu" data-translate="destinos" href="/partners/destinos.php">Destinos</a>
            <a class="<?= a1810_nav_class_p('contacto', $activePage) ?>" data-section="menu" data-translate="contacto" href="/partners/contacto.php">Contacto</a>
            <a class="nav-link nav-link--ghost" href="/cliente/">Soy viajero ↗</a>
        </div>

        <?php include __DIR__ . '/admin-login-widget.php'; ?>
    </nav>
</header>
