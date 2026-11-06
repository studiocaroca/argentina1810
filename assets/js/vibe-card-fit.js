(function () {
    var MOBILE_QUERY = '(max-width: 767.98px)';
    var MAX_SIZE = 26;
    var MIN_SIZE = 14;

    function fit(el) {
        el.style.whiteSpace = 'nowrap';
        var size = MAX_SIZE;
        el.style.fontSize = size + 'px';
        while (el.scrollWidth > el.clientWidth && size > MIN_SIZE) {
            size -= 1;
            el.style.fontSize = size + 'px';
        }
    }

    function run() {
        var names = document.querySelectorAll('.vibe-card__name');
        var isMobile = window.matchMedia(MOBILE_QUERY).matches;
        names.forEach(function (el) {
            if (isMobile) {
                fit(el);
            } else {
                el.style.fontSize = '';
                el.style.whiteSpace = '';
            }
        });
    }

    window.addEventListener('load', run);
    window.addEventListener('resize', run);
})();
