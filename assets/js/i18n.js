// Generic ES/EN/IT switcher for the Argentina 1810 site.
//
// Same swap mechanism the old ATP site used (data-section + data-translate
// looked up in a JSON file, data-translate-placeholder/-value redirect the
// swap into an attribute instead of textContent) — kept because it works
// and every page's markup already speaks it. What's new: the translations
// file path comes from <body data-i18n-src="..."> instead of being
// hardcoded, so one script serves both /cliente/ and /partners/, and the
// chosen language persists across page loads via localStorage (the old
// version reset to Spanish on every navigation).
(function () {
    var LANGS = ['es', 'en', 'it'];
    var STORAGE_KEY = 'a1810-lang';
    var DEFAULT_LANG = 'es';

    document.addEventListener('DOMContentLoaded', function () {
        var body = document.body;
        var src = body.getAttribute('data-i18n-src');
        if (!src) return;

        var langCurrent = document.getElementById('lang-current');
        var langDropdown = document.getElementById('lang-dropdown');
        var langSelector = document.getElementById('lang-selector');
        if (!langCurrent || !langDropdown) return;

        var storedLang = null;
        try { storedLang = localStorage.getItem(STORAGE_KEY); } catch (e) { /* private mode etc — ignore */ }
        var initialLang = LANGS.indexOf(storedLang) !== -1 ? storedLang : DEFAULT_LANG;

        // For content rendered server-side in all 3 languages at once (destino
        // cards, blog posts — anything sourced from a JSON data file rather
        // than translations.json) — each language's markup carries
        // data-lang="es"/"en"/"it" and only the active one is shown. Applied
        // immediately (not gated on the translations.json fetch below) so
        // there's no flash of every language at once on first paint.
        function applyDataLangVisibility(lang) {
            document.querySelectorAll('[data-lang]').forEach(function (element) {
                element.hidden = element.getAttribute('data-lang') !== lang;
            });
        }
        applyDataLangVisibility(initialLang);

        fetch(src)
            .then(function (response) {
                if (!response.ok) throw new Error('Network response was not ok ' + response.statusText);
                return response.json();
            })
            .then(function (translations) {
                function applyTranslations(lang) {
                    var translation = translations[lang] || translations[DEFAULT_LANG] || {};
                    document.querySelectorAll('[data-section][data-translate]').forEach(function (element) {
                        var section = element.getAttribute('data-section');
                        var key = element.getAttribute('data-translate');
                        if (translation[section] && translation[section][key] != null && translation[section][key] !== '') {
                            var value = translation[section][key];
                            if (element.hasAttribute('data-translate-placeholder')) {
                                element.setAttribute('placeholder', value);
                            } else if (element.hasAttribute('data-translate-value')) {
                                element.setAttribute('value', value);
                            } else if (element.hasAttribute('data-translate-html')) {
                                element.innerHTML = value;
                            } else {
                                element.textContent = value;
                            }
                        }
                    });
                    document.documentElement.setAttribute('lang', lang);
                }

                function setLanguage(lang) {
                    if (LANGS.indexOf(lang) === -1) lang = DEFAULT_LANG;
                    applyTranslations(lang);
                    applyDataLangVisibility(lang);
                    langCurrent.textContent = lang.toUpperCase();
                    langCurrent.setAttribute('aria-label', 'Idioma actual: ' + lang.toUpperCase());
                    langDropdown.innerHTML = LANGS
                        .filter(function (l) { return l !== lang; })
                        .map(function (l) { return '<div class="lang-selector__option" data-language="' + l + '" role="menuitem" tabindex="0">' + l.toUpperCase() + '</div>'; })
                        .join('');
                    try { localStorage.setItem(STORAGE_KEY, lang); } catch (e) { /* ignore */ }
                    document.dispatchEvent(new CustomEvent('a1810:language-changed', { detail: { lang: lang } }));
                }

                setLanguage(initialLang);

                langCurrent.addEventListener('click', function (e) {
                    e.stopPropagation();
                    langDropdown.classList.toggle('open');
                    langSelector && langSelector.classList.toggle('is-open');
                });

                langDropdown.addEventListener('click', function (e) {
                    var option = e.target.closest('.lang-selector__option');
                    if (!option) return;
                    setLanguage(option.getAttribute('data-language'));
                    langDropdown.classList.remove('open');
                    langSelector && langSelector.classList.remove('is-open');
                });

                document.addEventListener('click', function () {
                    langDropdown.classList.remove('open');
                    langSelector && langSelector.classList.remove('is-open');
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        langDropdown.classList.remove('open');
                        langSelector && langSelector.classList.remove('is-open');
                    }
                });
            })
            .catch(function (error) {
                console.error('No se pudieron cargar las traducciones:', error);
            });
    });
})();
