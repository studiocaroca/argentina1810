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
        try { storedLang = localStorage.getItem(STORAGE_KEY); } catch (e) { }
        var initialLang = LANGS.indexOf(storedLang) !== -1 ? storedLang : DEFAULT_LANG;

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
                    try { localStorage.setItem(STORAGE_KEY, lang); } catch (e) { }
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
