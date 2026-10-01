// Mobile nav toggle, shared by every Argentina 1810 page (cliente/ and
// partners/). Kept separate from i18n.js since it has nothing to do with
// language switching.
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.querySelector('.site-navbar__toggle');
    var links = document.getElementById('site-navbar-links');
    if (!toggle || !links) return;

    toggle.addEventListener('click', function () {
        var isOpen = links.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    links.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            links.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
});

// "Por qué viajar con nosotros" coverflow — same behavior as ATP's
// "Quienes somos" carousel (one active card up front, the rest stacked
// behind left/right, blurred and scaled down; auto-advances, pauses on
// hover, has prev/next arrows, and steps toward whichever side the
// pointer rests on) ported onto the site's own .trust-card markup/look
// instead of reusing the legacy .about-* classes.
(function () {
    var carousel = document.querySelector('.trust-coverflow');
    var cards = document.querySelectorAll('.trust-coverflow-wrapper .trust-card');
    if (!carousel || !cards.length) return;

    var current = 0;
    var count = cards.length;

    function posClass(offset) {
        if (offset === 0) return 'pos--active';
        if (offset === 1) return 'pos--next1';
        if (offset === -1) return 'pos--prev1';
        if (offset === 2) return 'pos--next2';
        if (offset === -2) return 'pos--prev2';
        return null;
    }

    function render() {
        cards.forEach(function (card, i) {
            var diff = i - current;
            if (diff > count / 2) diff -= count;
            if (diff < -count / 2) diff += count;

            card.classList.remove('pos--active', 'pos--next1', 'pos--prev1', 'pos--next2', 'pos--prev2');
            var cls = posClass(diff);
            if (cls) card.classList.add(cls);
        });
    }

    function goTo(index) {
        current = ((index % count) + count) % count;
        render();
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    // No JS timer driving auto-advance — the active card's violet dash IS
    // the timer (see the CSS animation on .pos--active::before): it grows
    // to the right while the card sits still, and reaching full width
    // fires this animationend, which is what actually advances.
    carousel.addEventListener('animationend', function (e) {
        if (e.animationName === 'trustCardProgress') next();
    });

    // Hovering anywhere on the carousel pauses that growing line (via CSS
    // animation-play-state, see .trust-coverflow.is-paused) instead of a
    // separate JS timer, so the bar and the advance can never drift apart.
    carousel.addEventListener('mouseenter', function () { carousel.classList.add('is-paused'); });
    carousel.addEventListener('mouseleave', function () { carousel.classList.remove('is-paused'); });

    var nextArrow = carousel.querySelector('.trust-coverflow-arrow--next');
    var prevArrow = carousel.querySelector('.trust-coverflow-arrow--prev');
    if (nextArrow) nextArrow.addEventListener('click', next);
    if (prevArrow) prevArrow.addEventListener('click', prev);

    var hoverNavTimer = null;
    var hoverNavSide = null;

    function clearHoverNav() {
        if (hoverNavTimer) clearInterval(hoverNavTimer);
        hoverNavTimer = null;
        hoverNavSide = null;
    }

    var HOVER_NAV_MS = 2200;

    function startHoverNav(side) {
        if (hoverNavSide === side) return;
        clearHoverNav();
        hoverNavSide = side;
        var step = side === 'left' ? prev : next;
        step();
        hoverNavTimer = setInterval(step, HOVER_NAV_MS);
    }

    function handlePointerPosition(clientX, clientY) {
        var activeCard = carousel.querySelector('.pos--active');
        if (!activeCard) {
            clearHoverNav();
            return;
        }
        var rect = activeCard.getBoundingClientRect();
        var overActive = clientX >= rect.left && clientX <= rect.right &&
            clientY >= rect.top && clientY <= rect.bottom;
        if (overActive) {
            clearHoverNav();
            return;
        }
        var isLeft = clientX < rect.left + rect.width / 2;
        startHoverNav(isLeft ? 'left' : 'right');
    }

    carousel.addEventListener('mousemove', function (e) {
        handlePointerPosition(e.clientX, e.clientY);
    });
    carousel.addEventListener('mouseleave', clearHoverNav);

    carousel.addEventListener('touchstart', function (e) {
        var t = e.touches[0];
        if (t) handlePointerPosition(t.clientX, t.clientY);
    }, { passive: true });
    carousel.addEventListener('touchmove', function (e) {
        var t = e.touches[0];
        if (t) handlePointerPosition(t.clientX, t.clientY);
    }, { passive: true });
    carousel.addEventListener('touchend', clearHoverNav);
    carousel.addEventListener('touchcancel', clearHoverNav);

    render();
})();

// Footer shapes — Argentina 1810's own "Destacadas" icon set standing in
// for ATP's old footer formas. A handful of independent "slots" each pick
// a random icon/size/spot anywhere in the footer, fade in, hold, fade out,
// then pick a new random one — staggered so they drift in and out out of
// sync with each other. Each pick avoids the actual text columns, avoids
// overlapping whichever other slots are currently visible, and avoids
// repeating an icon another visible slot is already showing.
document.addEventListener('DOMContentLoaded', function () {
    var sequence = [
        '/assets/imgs/iconos/Destacadas-06.png',
        '/assets/imgs/iconos/Destacadas-07.png',
        '/assets/imgs/iconos/Destacadas-08.png',
        '/assets/imgs/iconos/Destacadas-09.png',
        '/assets/imgs/iconos/Destacadas-10.png'
    ];

    var containers = document.querySelectorAll('.site-footer__shapes');
    if (!containers.length) return;

    var SLOT_COUNT = 3;
    var FADE_MS = 1000;
    var MIN_HOLD_MS = 2200;
    var MAX_HOLD_MS = 3800;
    var MIN_SIZE = 56;
    var MAX_SIZE = 140;
    var STAGGER_MS = 900;
    var CLEARANCE = 12; // px kept between a shape and text columns / other shapes
    var MAX_TRIES = 30;

    function rand(min, max) { return Math.random() * (max - min) + min; }
    function pick(list) { return list[Math.floor(Math.random() * list.length)]; }

    function overlaps(a, b) {
        return !(a.right + CLEARANCE < b.left || a.left - CLEARANCE > b.right ||
            a.bottom + CLEARANCE < b.top || a.top - CLEARANCE > b.bottom);
    }

    containers.forEach(function (container) {
        var footer = container.parentElement;
        var initialRect = footer.getBoundingClientRect();
        if (!initialRect.width || !initialRect.height) return; // hidden (e.g. the mobile layout) — nothing to animate

        var textCols = footer.querySelectorAll('.site-footer__logo, .site-footer__contact, .site-footer__social, .site-footer__sitemap, .site-footer__switch');

        // getBoundingClientRect() is viewport-relative, so the footer's own
        // rect and the columns' rects both need to be re-measured together
        // at the exact same moment — caching the footer's rect once and
        // reusing it later breaks the moment the page scrolls in between.
        function measure() {
            var footerRect = footer.getBoundingClientRect();
            var cols = Array.prototype.map.call(textCols, function (el) {
                var r = el.getBoundingClientRect();
                return {
                    left: r.left - footerRect.left,
                    top: r.top - footerRect.top,
                    right: r.right - footerRect.left,
                    bottom: r.bottom - footerRect.top
                };
            });
            return { width: footerRect.width, height: footerRect.height, forbidden: cols };
        }

        var slots = [];
        for (var i = 0; i < SLOT_COUNT; i++) {
            var el = document.createElement('img');
            el.className = 'site-footer__shape';
            container.appendChild(el);
            slots.push({ el: el, img: null, rect: null });
        }

        function pickSpot(self) {
            var m = measure();
            var others = slots.filter(function (s) { return s !== self && s.rect; });
            var usedImages = others.map(function (s) { return s.img; });
            var choices = sequence.filter(function (src) { return usedImages.indexOf(src) === -1; });
            if (!choices.length) choices = sequence;

            var size, x, y, rect, tries = 0, clear = false;
            do {
                size = rand(MIN_SIZE, MAX_SIZE);
                x = rand(0, Math.max(0, m.width - size));
                y = rand(0, Math.max(0, m.height - size));
                rect = { left: x, top: y, right: x + size, bottom: y + size };
                clear = m.forbidden.every(function (f) { return !overlaps(rect, f); }) &&
                    others.every(function (s) { return !overlaps(rect, s.rect); });
                tries++;
            } while (!clear && tries < MAX_TRIES);

            return { img: pick(choices), size: size, x: x, y: y, rect: rect };
        }

        slots.forEach(function (slot, i) {
            function step() {
                var spot = pickSpot(slot);
                slot.img = spot.img;
                slot.rect = spot.rect;

                slot.el.style.width = spot.size + 'px';
                slot.el.style.height = spot.size + 'px';
                slot.el.style.left = spot.x + 'px';
                slot.el.style.top = spot.y + 'px';
                slot.el.src = spot.img;

                requestAnimationFrame(function () {
                    slot.el.style.opacity = '1';
                });

                setTimeout(function () {
                    slot.el.style.opacity = '0';
                    slot.img = null;
                    slot.rect = null;
                    setTimeout(step, FADE_MS);
                }, rand(MIN_HOLD_MS, MAX_HOLD_MS));
            }

            setTimeout(step, i * STAGGER_MS);
        });
    });
});

// Blog post aside images — a single slot beside the article body that
// cross-fades through a couple of photos, one at a time: fade in, hold,
// fade out, swap, repeat. Same appear/disappear idea as the footer shapes
// above, just simpler (one fixed spot, no collision avoidance). Reads its
// image list from the wrapper's data-gallery attribute (set per-post from
// blog.json — provisional until there's an admin panel to manage it), and
// falls back to a couple of "Destacadas" icons when a post has none.
document.addEventListener('DOMContentLoaded', function () {
    var wrapper = document.querySelector('.blog-post__aside');
    var container = document.querySelector('.blog-post__aside-frame');
    if (!container) return;

    var sequence = [];
    var isPhotoGallery = false;
    try {
        var raw = wrapper ? JSON.parse(wrapper.getAttribute('data-gallery') || '[]') : [];
        if (raw.length) { sequence = raw; isPhotoGallery = true; }
    } catch (e) { /* malformed data-gallery — fall through to the icon default */ }

    if (!sequence.length) {
        sequence = [
            '/assets/imgs/iconos/Destacadas-07.png',
            '/assets/imgs/iconos/Destacadas-08.png',
            '/assets/imgs/iconos/Destacadas-09.png'
        ];
    }

    var FADE_MS = 1000;
    var HOLD_MS = 3200;
    var imgClass = 'blog-post__aside-img' + (isPhotoGallery ? ' blog-post__aside-img--photo' : '');

    // Two stacked layers instead of one: the bottom layer stays fully
    // opaque holding whatever photo is currently showing, so the frame's
    // background gradient is never exposed mid-transition. The top layer
    // fades 0→1 to reveal the next photo, then its src is copied down to
    // the bottom layer and it resets to 0 for the next swap.
    var bottom = document.createElement('img');
    bottom.className = imgClass;
    bottom.style.transition = 'none';
    var top = document.createElement('img');
    top.className = imgClass;
    container.appendChild(bottom);
    container.appendChild(top);

    var i = 0;
    bottom.src = sequence[0];
    bottom.style.opacity = '1';

    function step() {
        i++;
        top.src = sequence[i % sequence.length];
        requestAnimationFrame(function () {
            top.style.opacity = '1';
        });
        setTimeout(function () {
            bottom.src = top.src;
            bottom.style.opacity = '1';
            top.style.transition = 'none';
            top.style.opacity = '0';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    top.style.transition = '';
                });
            });
            setTimeout(step, HOLD_MS);
        }, FADE_MS);
    }
    setTimeout(step, HOLD_MS);
});

// Video placeholder lightbox: click it to expand into a blurred/tinted
// overlay (same treatment ATP used for its play modals), click anywhere to
// close and stop playback. Works for every .video-placeholder on the page
// (currently just Nosotros) by cloning its markup into a shared overlay.
document.addEventListener('DOMContentLoaded', function () {
    var placeholders = document.querySelectorAll('.video-placeholder');
    if (!placeholders.length) return;

    var overlay = document.createElement('div');
    overlay.className = 'video-modal-overlay';
    document.body.appendChild(overlay);

    function close() {
        overlay.classList.remove('is-open');
        var video = overlay.querySelector('video');
        if (video) {
            video.pause();
            video.currentTime = 0;
        }
        overlay.innerHTML = '';
        document.body.classList.remove('modal-open');
    }

    function open(sourceEl) {
        var clone = sourceEl.cloneNode(true);
        overlay.innerHTML = '';
        overlay.appendChild(clone);
        var video = clone.querySelector('video');
        if (video) {
            video.setAttribute('controls', '');
            video.play().catch(function () { /* autoplay may be blocked — controls are there anyway */ });
        }
        overlay.classList.add('is-open');
        document.body.classList.add('modal-open');
    }

    placeholders.forEach(function (el) {
        el.setAttribute('role', 'button');
        el.setAttribute('tabindex', '0');
        el.addEventListener('click', function () { open(el); });
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                open(el);
            }
        });
    });

    overlay.addEventListener('click', close);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
    });
});

// Header overlay on the home pages: transparent (matching the splash
// buttons) while at the very top of the hero video, solid brand purple as
// soon as the page scrolls.
document.addEventListener('DOMContentLoaded', function () {
    var header = document.querySelector('.site-header--overlay');
    if (!header) return;

    function syncScrolledState() {
        header.classList.toggle('is-scrolled', window.scrollY > 10);
    }

    syncScrolledState();
    window.addEventListener('scroll', syncScrolledState, { passive: true });
});

