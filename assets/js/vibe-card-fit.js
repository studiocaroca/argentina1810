// Travel Vibe card titles on mobile: grow each one as large as it can go
// while still fitting on a single line. A flat font-size can't do this
// well since the four names are very different lengths ("Active & Wild"
// vs "Food, Wine & Comfort") — this measures each card's actual rendered
// text against its available width instead of guessing a size that
// happens to fit the longest one and leaves the short ones small.
(function () {
    var MOBILE_QUERY = '(max-width: 767.98px)';
    var MAX_SIZE = 26; // px — generous ceiling for short names like "Active & Wild"
    var MIN_SIZE = 14; // px — floor so a long name never gets unreadably small

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
