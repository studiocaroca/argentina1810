// Keeps each .video-placeholder's underlying <source> pointed at the right
// language file (institucional-{track}-{lang}.mp4) whenever the site's
// language changes. The actual click-to-play lightbox is handled by the
// existing video-modal-overlay logic in site.js, which clones whatever
// this element's current <source src> is at the moment it's opened — so
// as long as this script keeps that src correct, the modal just works.
//
// The 6 language/track video files don't exist yet. Until the client
// uploads them, every language 404s and the <video>'s error handler below
// quietly falls back to the site's existing ambient clip, so the page
// never shows a broken/black video. Drop the real files in at
// /assets/videos/institucional-{cliente|partners}-{es|en|it}.mp4 and they
// start playing automatically — no code changes needed.
(function () {
    var FALLBACK_SRC = '/assets/videos/paisajes-argentina.mp4';

    function setSource(video, src) {
        var source = video.querySelector('source');
        if (!source) return;
        var absolute = new URL(src, location.href).href;
        if (source.src === absolute) return;
        source.src = src;
        video.load();
    }

    function applyLang(lang) {
        document.querySelectorAll('.video-placeholder[data-institutional-video]').forEach(function (wrapper) {
            var track = wrapper.getAttribute('data-institutional-video');
            var video = wrapper.querySelector('video');
            if (!video) return;
            setSource(video, '/assets/videos/institucional-' + track + '-' + lang + '.mp4');
        });
    }

    document.addEventListener('a1810:language-changed', function (e) {
        applyLang(e.detail.lang);
    });

    document.querySelectorAll('.video-placeholder[data-institutional-video]').forEach(function (wrapper) {
        var video = wrapper.querySelector('video');
        if (!video) return;
        video.addEventListener('error', function () {
            var source = video.querySelector('source');
            if (source && source.src.indexOf('/institucional-') !== -1) {
                source.src = FALLBACK_SRC;
                video.load();
            }
        });
    });
})();
