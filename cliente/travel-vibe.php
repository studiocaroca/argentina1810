<?php
$pageTitle = 'Elige tu Travel Vibe | Argentina 1810';
$pageDescription = 'Active & Wild, Slow & Immersive, Family Journeys o Food, Wine & Comfort: encuentra el itinerario que combina con tu forma de viajar.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';

$vibes = [
    'active-wild' => ['name' => 'Active & Wild', 'key' => 'active_wild_desc'],
    'slow-immersive' => ['name' => 'Slow & Immersive', 'key' => 'slow_immersive_desc'],
    'family-journeys' => ['name' => 'Family Journeys', 'key' => 'family_journeys_desc'],
    'food-wine-comfort' => ['name' => 'Food, Wine & Comfort', 'key' => 'food_wine_desc'],
];

$itinerariosData = json_decode(file_get_contents(__DIR__ . '/../itinerarios.json'), true);
$vibesData = $itinerariosData['vibes'] ?? [];
$langs = ['es', 'en', 'it'];
$chooseLabel = ['es' => 'Elegí el que más te guste.', 'en' => 'Pick the one you like best.', 'it' => 'Scegli quello che ti piace di più.'];
$emptyLabel = ['es' => 'Itinerarios — muy pronto', 'en' => 'Itineraries — coming soon', 'it' => 'Itinerari — in arrivo a breve'];
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas">
<?php $activePage = 'travel-vibe'; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810" style="text-align:center">
        <h1 class="section-title" data-section="travel_vibe" data-translate="title">¿Cuál es tu Travel Vibe?</h1>
        <p class="section-lede" style="margin:0 auto" data-section="travel_vibe" data-translate="lede">Elige la energía de tu próximo viaje. Nosotros nos ocupamos del resto.</p>

        <div class="vibe-grid" id="vibe-grid">
            <?php foreach ($vibes as $slug => $vibe): ?>
                <button type="button" class="vibe-card" data-vibe="<?= htmlspecialchars($slug) ?>" aria-pressed="false">
                    <p class="vibe-card__name"><?= htmlspecialchars($vibe['name']) ?></p>
                    <p class="vibe-card__desc" data-section="travel_vibe" data-translate="<?= htmlspecialchars($vibe['key']) ?>"></p>
                </button>
            <?php endforeach; ?>
        </div>

        <div id="vibe-itineraries">
            <?php foreach ($vibes as $slug => $vibe): ?>
                <?php $vibeItineraries = $vibesData[$slug]['itineraries'] ?? []; ?>
                <div class="vibe-itinerary" id="itinerary-<?= htmlspecialchars($slug) ?>" hidden>
                    <p class="vibe-itinerary__vibe"><?= htmlspecialchars($vibe['name']) ?></p>
                    <?php foreach ($langs as $lang): ?>
                        <p class="vibe-itinerary__choose" data-lang="<?= $lang ?>"><?= htmlspecialchars($chooseLabel[$lang]) ?></p>
                    <?php endforeach; ?>

                    <div class="vibe-itinerary__gallery">
                        <?php if (!empty($vibeItineraries)): ?>
                            <?php foreach ($vibeItineraries as $item): ?>
                                <a class="vibe-itinerary__media" href="/cliente/itinerario.php?vibe=<?= urlencode($slug) ?>&id=<?= urlencode($item['id']) ?>">
                                    <img src="/assets/imgs/<?= htmlspecialchars($item['cover']) ?>" alt="<?= htmlspecialchars($item['title']['es'] ?? '') ?>">
                                    <div class="vibe-itinerary__media-scrim"></div>
                                    <?php foreach ($langs as $lang): ?>
                                        <p class="vibe-itinerary__media-title" data-lang="<?= $lang ?>"><?= htmlspecialchars($item['title'][$lang] ?? '') ?></p>
                                    <?php endforeach; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php foreach ($langs as $lang): ?>
                                <span class="vibe-itinerary__placeholder" data-lang="<?= $lang ?>"><?= htmlspecialchars($emptyLabel[$lang]) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="vibe-cta" class="vibe-cta" hidden>
            <a class="btn-cta btn-cta--gold" id="vibe-cta-link" href="/cliente/contacto.php" data-section="travel_vibe" data-translate="cta">Empecemos tu aventura</a>
        </div>
    </section>
</main>

<?php $footerTrack = 'cliente'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
<script>
    // One or several Travel Vibe cards can be selected at once — each
    // toggle shows/hides its own itinerary placeholder block below,
    // per the "elegí una o varias" behavior in the content brief.
    var ctaWrap = document.getElementById('vibe-cta');
    var ctaLink = document.getElementById('vibe-cta-link');
    document.querySelectorAll('.vibe-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var slug = card.getAttribute('data-vibe');
            var panel = document.getElementById('itinerary-' + slug);
            var isActive = card.classList.toggle('is-active');
            card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            if (panel) panel.hidden = !isActive;
            if (isActive && panel) panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            var selected = Array.prototype.map.call(document.querySelectorAll('.vibe-card.is-active'), function (c) {
                return c.getAttribute('data-vibe');
            });
            if (selected.length) {
                ctaWrap.hidden = false;
                ctaLink.href = '/cliente/contacto.php?vibe=' + selected.join(',');
            } else {
                ctaWrap.hidden = true;
            }
        });
    });
</script>
</body>
</html>
