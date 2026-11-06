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
