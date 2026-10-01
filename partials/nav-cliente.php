<?php
// Nav for the Free Explorer (B2C) track. Caller sets $activePage before
// including this (one of: home, nosotros, travel-vibe, blog, destinos,
// contacto) to highlight the current link. Expects to be included right
// after <body data-i18n-src="...">.
if (!isset($activePage)) $activePage = '';
if (!isset($headerOverlay)) $headerOverlay = false;
function a1810_nav_class($page, $active) { return $page === $active ? 'nav-link active' : 'nav-link'; }
?>
<a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

<header class="site-header site-header--cliente<?= ($activePage === 'home' || $headerOverlay) ? ' site-header--overlay' : '' ?>">
    <nav class="site-navbar" aria-label="Principal">
        <a class="site-brand" href="/cliente/">
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
            <a class="<?= a1810_nav_class('home', $activePage) ?>" data-section="menu" data-translate="home" href="/cliente/">Home</a>
            <a class="<?= a1810_nav_class('nosotros', $activePage) ?>" data-section="menu" data-translate="about" href="/cliente/nosotros.php">Nosotros</a>
            <a class="<?= a1810_nav_class('travel-vibe', $activePage) ?>" data-section="menu" data-translate="travel_vibe" href="/cliente/travel-vibe.php">Travel Vibe</a>
            <a class="<?= a1810_nav_class('blog', $activePage) ?>" data-section="menu" data-translate="blog" href="/cliente/blog.php">Blog</a>
            <a class="<?= a1810_nav_class('destinos', $activePage) ?>" data-section="menu" data-translate="destinos" href="/cliente/destinos.php">Destinos</a>
            <a class="<?= a1810_nav_class('contacto', $activePage) ?>" data-section="menu" data-translate="contacto" href="/cliente/contacto.php">Contacto</a>
            <a class="nav-link nav-link--ghost" href="/partners/">Soy agencia ↗</a>
        </div>

        <?php include __DIR__ . '/admin-login-widget.php'; ?>
    </nav>
</header>
