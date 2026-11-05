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
    'es' => ['duration' => 'Días', 'nights' => 'Noches', 'stops' => 'Recorrido', 'difficulty' => 'Dificultad', 'itinerary' => 'Itinerario', 'day' => 'Día', 'includes' => 'Incluye', 'stay' => 'Alojamiento'],
    'en' => ['duration' => 'Days', 'nights' => 'Nights', 'stops' => 'Route', 'difficulty' => 'Difficulty', 'itinerary' => 'Itinerary', 'day' => 'Day', 'includes' => 'Includes', 'stay' => 'Stay'],
    'it' => ['duration' => 'Giorni', 'nights' => 'Notti', 'stops' => 'Percorso', 'difficulty' => 'Difficoltà', 'itinerary' => 'Itinerario', 'day' => 'Giorno', 'includes' => 'Include', 'stay' => 'Alloggio'],
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
                    <span class="vibe-card__check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4 10-10"/></svg></span>
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
                                    <span class="destino-detail__meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l3 2"/><path d="M10 2h4"/></svg></span>
                                    <div>
                                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['duration']) ?></p>
                                        <p class="destino-detail__meta-value"><?= htmlspecialchars($itin['duration'][$lang] ?? '') ?></p>
                                    </div>
                                </div>
                                <div class="destino-detail__meta-item">
                                    <span class="destino-detail__meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a7 7 0 0 0 10.5 10.5Z"/></svg></span>
                                    <div>
                                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['nights']) ?></p>
                                        <p class="destino-detail__meta-value"><?= htmlspecialchars($itin['nights'][$lang] ?? '') ?></p>
                                    </div>
                                </div>
                                <div class="destino-detail__meta-item">
                                    <span class="destino-detail__meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 18c3-1 3-5 6-5s3 4 6 4 3-5 4-9"/><path d="M20 8 17.3 9.3M20 8 21.2 11.4"/></svg></span>
                                    <div>
                                        <p class="destino-detail__meta-label"><?= htmlspecialchars($labels[$lang]['stops']) ?></p>
                                        <p class="destino-detail__meta-value"><?= htmlspecialchars($itin['stops'][$lang] ?? '') ?></p>
                                    </div>
                                </div>
                                <div class="destino-detail__meta-item">
                                    <span class="destino-detail__meta-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18 9 7l4 6 2-3 6 8Z"/></svg></span>
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
                                            <?php foreach ($langs as $lang): ?>
                                                <?php if (!empty($day['includes'][$lang])): ?>
                                                    <p class="itinerary-day__meta-line" data-lang="<?= $lang ?>"><strong><?= htmlspecialchars($labels[$lang]['includes']) ?></strong> <?= htmlspecialchars($day['includes'][$lang]) ?></p>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php foreach ($langs as $lang): ?>
                                                <?php if (!empty($day['stay'][$lang])): ?>
                                                    <p class="itinerary-day__meta-line" data-lang="<?= $lang ?>"><strong><?= htmlspecialchars($labels[$lang]['stay']) ?></strong> <?= htmlspecialchars($day['stay'][$lang]) ?></p>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php foreach ($langs as $lang): ?>
                                                <?php if (!empty($day['tip'][$lang])): ?>
                                                    <p class="itinerary-day__tip" data-lang="<?= $lang ?>"><strong>TIP</strong> <?= htmlspecialchars($day['tip'][$lang]) ?></p>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </details>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if (!empty($itin['map_mid'])): ?>
                                <?php $mapSrc = 'https://www.google.com/maps/d/embed?mid=' . urlencode($itin['map_mid']); ?>
                            <?php else: ?>
                                <?php
                                    $mapLocation = !empty($itin['map_query']) ? $itin['map_query'] : (($itin['title']['es'] ?? $vibe['name']) . ', Argentina');
                                    $mapSrc = 'https://maps.google.com/maps?q=' . urlencode($mapLocation) . '&output=embed';
                                ?>
                            <?php endif; ?>
                            <div class="itinerary-layout__map">
                                <iframe src="<?= htmlspecialchars($mapSrc) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" aria-hidden="true"></iframe>
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
            <p class="vibe-cta__hint" data-section="travel_vibe" data-translate="selection_hint">Elegiste estos Travel Vibe:</p>
            <p id="vibe-cta-selection" class="vibe-cta__selection"></p>
            <p class="vibe-cta__instruction" data-section="travel_vibe" data-translate="selection_instruction">Para pedir información hacé clic en el botón.</p>
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
    var ctaSelection = document.getElementById('vibe-cta-selection');
    document.querySelectorAll('.vibe-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var slug = card.getAttribute('data-vibe');
            var panel = document.getElementById('itinerary-' + slug);
            var isActive = card.classList.toggle('is-active');
            card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            if (panel) panel.hidden = !isActive;
            if (isActive && panel) panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            var selectedCards = document.querySelectorAll('.vibe-card.is-active');
            var selected = Array.prototype.map.call(selectedCards, function (c) {
                return c.getAttribute('data-vibe');
            });
            if (selected.length) {
                ctaWrap.hidden = false;
                ctaLink.href = '/cliente/contacto.php?vibe=' + selected.join(',');
                var names = Array.prototype.map.call(selectedCards, function (c) {
                    var nameEl = c.querySelector('.vibe-card__name');
                    return nameEl ? nameEl.textContent : '';
                });
                ctaSelection.textContent = names.join(' + ');
            } else {
                ctaWrap.hidden = true;
            }
        });
    });
</script>
</body>
</html>
