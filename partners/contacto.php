<?php
$pageTitle = 'Contacto | Argentina 1810 Partner Agency';
$pageDescription = 'Contanos sobre tu agencia y te respondemos en menos de 48 horas.';
$i18nSrc = '/partners/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="compact-title">
<div class="contact-bowtie contact-bowtie--violet" aria-hidden="true">
    <div class="contact-bowtie__layer contact-bowtie__layer--a-left"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--a-right"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--b-left"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--b-right"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--c-left"></div>
    <div class="contact-bowtie__layer contact-bowtie__layer--c-right"></div>
    <div class="contact-bowtie__line contact-bowtie__line--top"></div>
</div>
<?php $activePage = 'contacto'; include __DIR__ . '/../partials/nav-partners.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810 contact-page">
        <h1 class="section-title title-tight" data-section="contacto" data-translate="title">Únete como agencia socia</h1>
        <p class="section-lede" data-section="contacto" data-translate="lede">Cuéntanos sobre tu agencia y el perfil de pasajero con el que trabajas. Te respondemos en menos de 48 horas para coordinar los próximos pasos.</p>

        <div class="contact-layout">
            <form class="contact-form-a1810" id="contact-form" action="/contact.php" method="post" novalidate>
                <input type="hidden" name="form_type" value="partner">

                <div class="form-row">
                    <div>
                        <label for="agencia" data-section="contacto" data-translate="label_agencia">Nombre de la agencia</label>
                        <input type="text" id="agencia" name="agencia" required>
                    </div>
                    <div>
                        <label for="pais" data-section="contacto" data-translate="label_pais">País</label>
                        <input type="text" id="pais" name="pais">
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="nombre" data-section="contacto" data-translate="label_contacto_nombre">Nombre de contacto</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div>
                        <label for="cargo" data-section="contacto" data-translate="label_cargo">Cargo</label>
                        <input type="text" id="cargo" name="cargo">
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

                <div>
                    <label for="mercado" data-section="contacto" data-translate="label_mercado">Mercado en el que trabajas (luxury, adventure, honeymoon, etc.)</label>
                    <input type="text" id="mercado" name="mercado">
                </div>

                <div>
                    <label for="message" data-section="contacto" data-translate="label_mensaje">Cuéntanos más</label>
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

<?php $footerTrack = 'partners'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
<script>
    (function () {
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
