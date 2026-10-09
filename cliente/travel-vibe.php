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
$labels = [
    'es' => ['duration' => 'Duración', 'difficulty' => 'Dificultad', 'itinerary' => 'Itinerario', 'day' => 'Día'],
    'en' => ['duration' => 'Duration', 'difficulty' => 'Difficulty', 'itinerary' => 'Itinerary', 'day' => 'Day'],
    'it' => ['duration' => 'Durata', 'difficulty' => 'Difficoltà', 'itinerary' => 'Itinerario', 'day' => 'Giorno'],
];
$emptyLabel = ['es' => 'Itinerario — muy pronto', 'en' => 'Itinerary — coming soon', 'it' => 'Itinerario — in arrivo a breve'];
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas">
<?php $activePage = 'travel-vibe'; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810" style="text-align:center">
        <h1 class="section-title title-tight" data-section="travel_vibe" data-translate="title">¿Cuál es tu Travel Vibe?</h1>
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
                <?php $itin = $vibesData[$slug] ?? null; ?>
                <div class="vibe-itinerary" id="itinerary-<?= htmlspecialchars($slug) ?>" hidden>
                    <p class="vibe-itinerary__vibe"><?= htmlspecialchars($vibe['name']) ?></p>

                    <?php if ($itin && !empty($itin['days'])): ?>
                        <?php foreach ($langs as $lang): ?>
                            <h2 class="vibe-itinerary__title" data-lang="<?= $lang ?>"><?= htmlspecialchars($itin['title'][$lang] ?? '') ?></h2>
                        <?php endforeach; ?>

                        <?php foreach ($langs as $lang): ?>
                            <div class="destino-detail__meta vibe-itinerary__meta" data-lang="<?= $lang ?>">
                                <div class="destino-detail__meta-item">
                                    <span class="destino-detail__meta-icon" aria-hidden="true">⏱</span>
                                    <div>
                                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['duration']) ?></p>
                                        <p class="destino-detail__meta-value"><?= htmlspecialchars($itin['duration'][$lang] ?? '') ?></p>
                                    </div>
                                </div>
                                <div class="destino-detail__meta-item">
                                    <span class="destino-detail__meta-icon" aria-hidden="true">⛰</span>
                                    <div>
                                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['difficulty']) ?></p>
                                        <p class="destino-detail__meta-value"><?= htmlspecialchars($itin['difficulty'][$lang] ?? '') ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php foreach ($langs as $lang): ?>
                            <p class="vibe-itinerary__summary" data-lang="<?= $lang ?>"><?= nl2br(htmlspecialchars($itin['summary'][$lang] ?? '')) ?></p>
                        <?php endforeach; ?>

                        <div class="itinerary-layout">
                            <div class="itinerary-layout__days">
                                <?php foreach ($langs as $lang): ?>
                                    <h3 class="itinerary-days__title" data-lang="<?= $lang ?>"><?= htmlspecialchars($labels[$lang]['itinerary']) ?></h3>
                                <?php endforeach; ?>
                                <div class="itinerary-days">
                                    <?php foreach ($itin['days'] as $i => $day): ?>
                                        <details class="itinerary-day">
                                            <summary class="itinerary-day__summary">
                                                <?php foreach ($langs as $lang): ?>
                                                    <span class="itinerary-day__label" data-lang="<?= $lang ?>"><?= htmlspecialchars($labels[$lang]['day']) ?> <?= $i + 1 ?></span>
                                                <?php endforeach; ?>
                                                <?php foreach ($langs as $lang): ?>
                                                    <span class="itinerary-day__title" data-lang="<?= $lang ?>"><?= htmlspecialchars($day['title'][$lang] ?? '') ?></span>
                                                <?php endforeach; ?>
                                                <span class="itinerary-day__chevron" aria-hidden="true">&#9662;</span>
                                            </summary>
                                            <?php foreach ($langs as $lang): ?>
                                                <p class="itinerary-day__desc" data-lang="<?= $lang ?>"><?= htmlspecialchars($day['description'][$lang] ?? '') ?></p>
                                            <?php endforeach; ?>
                                        </details>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php $mapQuery = urlencode(($itin['title']['es'] ?? $vibe['name']) . ', Argentina'); ?>
                            <div class="itinerary-layout__map">
                                <iframe src="https://maps.google.com/maps?q=<?= $mapQuery ?>&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" aria-hidden="true"></iframe>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($langs as $lang): ?>
                            <span class="vibe-itinerary__placeholder" data-lang="<?= $lang ?>"><?= htmlspecialchars($emptyLabel[$lang]) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
    // toggle shows/hides its own itinerary block below, per the "elegí
    // una o varias" behavior in the content brief.
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
