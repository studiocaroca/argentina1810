<?php
// Shared footer for both tracks. Caller sets $footerTrack to 'cliente' or
// 'partners' before including this, so the sitemap links point at the
// right set of pages.
if (!isset($footerTrack)) $footerTrack = 'cliente';
$isPartners = $footerTrack === 'partners';

// Placeholder business info — none of this exists yet (no real address,
// and the phone doubles as the WhatsApp number). Swap in the real values
// whenever the client has them; nothing else needs to change.
$footerAddress = 'Av. Corrientes 1810, CABA, Argentina';
$footerPhone = '+54 9 11 1810-1810';
$footerEmail = 'hola@argentina1810.com';
?>
<footer class="site-footer">
    <div class="site-footer__shapes" aria-hidden="true"></div>

    <div class="site-footer__grid">
        <div class="site-footer__col site-footer__col--brand">
            <img class="site-footer__logo" src="/assets/imgs/logos/logo-horizontal-white.png" alt="Argentina 1810">

            <ul class="site-footer__contact">
                <li><?= htmlspecialchars($footerAddress) ?></li>
                <li><a href="tel:<?= htmlspecialchars(preg_replace('/[^+0-9]/', '', $footerPhone)) ?>"><?= htmlspecialchars($footerPhone) ?></a></li>
                <li><a href="mailto:<?= htmlspecialchars($footerEmail) ?>"><?= htmlspecialchars($footerEmail) ?></a></li>
            </ul>

            <div class="site-footer__social">
                <a href="#" aria-label="WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm0 18a8 8 0 01-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1112 20zm4.4-5.9c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.8 1-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.2.2-.4 0-.1 0-.3 0-.4 0-.1-.5-1.3-.7-1.7-.2-.5-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.4c.1.2 1.6 2.5 3.9 3.5.5.2.9.4 1.3.5.5.2 1 .1 1.3.1.4-.1 1.4-.6 1.6-1.1.2-.5.2-1 .1-1.1z"/></svg></a>
                <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a>
                <a href="#" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.3 2 1.6 3.6 3.5 4v2.6c-1.3 0-2.6-.4-3.7-1.1v6.6c0 3.2-2.6 5.9-5.9 5.9S4.5 18.2 4.5 15s2.6-5.9 5.9-5.9c.3 0 .6 0 .9.1v2.7c-.3-.1-.6-.2-.9-.2-1.8 0-3.3 1.5-3.3 3.3s1.5 3.3 3.3 3.3 3.3-1.5 3.3-3.3V3h2.8z"/></svg></a>
                <a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3.2h-3.1V7.7c0-.9.3-1.6 1.6-1.6h1.7V3.2C16.5 3.1 15.4 3 14.2 3c-2.6 0-4.4 1.6-4.4 4.5v2.3H7v3.2h2.8V21h3.7z"/></svg></a>
                <a href="#" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="4" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M10 9l6 3-6 3z"/></svg></a>
            </div>
        </div>

        <div class="site-footer__col site-footer__col--links">
            <nav class="site-footer__sitemap" aria-label="Mapa del sitio">
                <?php if ($isPartners): ?>
                    <a href="/partners/" data-section="menu" data-translate="home">Home</a>
                    <a href="/partners/nosotros.php" data-section="menu" data-translate="about">Nosotros</a>
                    <a href="/partners/como-trabajamos.php" data-section="menu" data-translate="how_we_work">Cómo trabajamos</a>
                    <a href="/partners/destinos.php" data-section="menu" data-translate="destinos">Destinos</a>
                    <a href="/partners/contacto.php" data-section="menu" data-translate="contacto">Contacto</a>
                <?php else: ?>
                    <a href="/cliente/" data-section="menu" data-translate="home">Home</a>
                    <a href="/cliente/nosotros.php" data-section="menu" data-translate="about">Nosotros</a>
                    <a href="/cliente/travel-vibe.php" data-section="menu" data-translate="travel_vibe">Travel Vibe</a>
                    <a href="/cliente/blog.php" data-section="menu" data-translate="blog">Blog</a>
                    <a href="/cliente/destinos.php" data-section="menu" data-translate="destinos">Destinos</a>
                    <a href="/cliente/contacto.php" data-section="menu" data-translate="contacto">Contacto</a>
                <?php endif; ?>
            </nav>

            <div class="site-footer__switch">
                <a href="/">← Elegir otro acceso</a>
            </div>
        </div>
    </div>

    <p class="site-footer__copyright">Copyright <script>document.write(new Date().getFullYear())</script> © by <a href="http://www.studiocaroca.com.ar" target="_blank" rel="noopener noreferrer"><img src="/assets/imgs/logo-caroca.svg" alt="Studio Caroca" class="logo-caroca"></a></p>
</footer>

<a class="whatsapp-float <?= $isPartners ? 'whatsapp-float--gold' : 'whatsapp-float--violet' ?>" href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9]/', '', $footerPhone)) ?>" target="_blank" rel="noopener noreferrer" aria-label="Escribinos por WhatsApp">
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm0 18a8 8 0 01-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1112 20zm4.4-5.9c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.8 1-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.2.2-.4 0-.1 0-.3 0-.4 0-.1-.5-1.3-.7-1.7-.2-.5-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.4c.1.2 1.6 2.5 3.9 3.5.5.2.9.4 1.3.5.5.2 1 .1 1.3.1.4-.1 1.4-.6 1.6-1.1.2-.5.2-1 .1-1.1z"/></svg>
</a>
