<?php
$pageTitle = 'Contacto | Argentina 1810';
$pageDescription = 'Contanos qué estás imaginando y te respondemos en 24 a 48 horas.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas page-contact-gold">
<div class="contact-bowtie contact-bowtie--gold" aria-hidden="true">
    <div class="contact-bowtie__layer contact-bowtie__layer--a-left"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--a-right"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--b-left"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--b-right"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--c-left"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--c-right"></div>
</div>
<?php $activePage = 'contacto'; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810 contact-page">
        <h1 class="section-title" data-section="contacto" data-translate="title">Empecemos tu aventura.</h1>
        <p class="section-lede" data-section="contacto" data-translate="lede">Cuéntanos qué estás imaginando —el destino, las fechas, con quién viajas y qué Travel Vibe te representa— y te responderemos en un plazo de 24 a 48 horas para acordar los próximos pasos.</p>

        <div class="contact-layout">
            <form class="contact-form-a1810" id="contact-form" action="/contact.php" method="post" novalidate>
                <input type="hidden" name="form_type" value="explorer">

                <div class="form-row">
                    <div>
                        <label for="nombre" data-section="contacto" data-translate="label_nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div>
                        <label for="pais" data-section="contacto" data-translate="label_pais">País de origen</label>
                        <input type="text" id="pais" name="pais">
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="email" data-section="contacto" data-translate="label_email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div>
                        <label for="telefono" data-section="contacto" data-translate="label_telefono">Teléfono / WhatsApp</label>
                        <input type="tel" id="telefono" name="telefono">
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="fechas" data-section="contacto" data-translate="label_fechas">Fechas aproximadas de viaje</label>
                        <input type="text" id="fechas" name="fechas">
                    </div>
                    <div>
                        <label for="viajeros" data-section="contacto" data-translate="label_viajeros">Número de viajeros</label>
                        <input type="number" id="viajeros" name="viajeros" min="1">
                    </div>
                </div>

                <fieldset>
                    <label data-section="contacto" data-translate="label_travel_vibe">Travel Vibe</label>
                    <div class="checkbox-group">
                        <label class="checkbox-pill"><input type="checkbox" name="travel_vibe[]" value="Active & Wild" data-vibe-slug="active-wild"> Active & Wild</label>
                        <label class="checkbox-pill"><input type="checkbox" name="travel_vibe[]" value="Slow & Immersive" data-vibe-slug="slow-immersive"> Slow & Immersive</label>
                        <label class="checkbox-pill"><input type="checkbox" name="travel_vibe[]" value="Family Journeys" data-vibe-slug="family-journeys"> Family Journeys</label>
                        <label class="checkbox-pill"><input type="checkbox" name="travel_vibe[]" value="Food, Wine & Comfort" data-vibe-slug="food-wine-comfort"> Food, Wine &amp; Comfort</label>
                    </div>
                </fieldset>

                <div>
                    <label for="message" data-section="contacto" data-translate="label_mensaje">Cuéntanos más sobre tu viaje ideal</label>
                    <textarea id="message" name="message" required></textarea>
                </div>

                <div class="contact-hp-field" aria-hidden="true">
                    <label for="empresa">No completar este campo</label>
                    <input type="text" id="empresa" name="empresa" tabindex="-1" autocomplete="off">
                </div>

                <button type="submit" class="btn-cta btn-cta--gold" data-section="contacto" data-translate="submit">Enviar</button>
                <div id="contact-form-msg" class="contact-form-msg" hidden></div>
            </form>

        </div>
    </section>
</main>

<?php $footerTrack = 'cliente'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
<script>
    (function () {
        var preselected = new URLSearchParams(location.search).get('vibe');
        if (preselected) {
            var slugs = preselected.split(',');
            document.querySelectorAll('.checkbox-pill input[data-vibe-slug]').forEach(function (cb) {
                if (slugs.indexOf(cb.getAttribute('data-vibe-slug')) !== -1) cb.checked = true;
            });
        }

        var form = document.getElementById('contact-form');
        var msg = document.getElementById('contact-form-msg');
        var successText = { es: 'Gracias — ya recibimos tu mensaje. Te responderemos en las próximas 24 a 48 horas.', en: "Thank you — we've received your message. We'll get back to you within 24 to 48 hours.", it: 'Grazie — abbiamo ricevuto il tuo messaggio. Ti risponderemo entro 24-48 ore.' };
        var errorText = { es: 'Hubo un error. Probá de nuevo en un rato.', en: 'Something went wrong. Please try again shortly.', it: 'Si è verificato un errore. Riprova tra poco.' };

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var lang = (document.getElementById('lang-current')?.textContent || 'ES').toLowerCase();
            fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' } })
                .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
                .then(function (result) {
                    msg.hidden = false;
                    if (result.ok && result.data.success) {
                        msg.className = 'contact-form-msg success';
                        msg.textContent = successText[lang] || successText.es;
                        form.reset();
                    } else {
                        msg.className = 'contact-form-msg error';
                        msg.textContent = result.data.error || errorText[lang] || errorText.es;
                    }
                })
                .catch(function () {
                    msg.hidden = false;
                    msg.className = 'contact-form-msg error';
                    msg.textContent = errorText[lang] || errorText.es;
                });
        });
    })();
</script>
</body>
</html>
