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

    carousel.addEventListener('animationend', function (e) {
        if (e.animationName === 'trustCardProgress') next();
    });

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
    var CLEARANCE = 12;
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
        if (!initialRect.width || !initialRect.height) return;

        var textCols = footer.querySelectorAll('.site-footer__logo, .site-footer__contact, .site-footer__social, .site-footer__sitemap, .site-footer__switch');

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

document.addEventListener('DOMContentLoaded', function () {
    var wrapper = document.querySelector('.blog-post__aside');
    var container = document.querySelector('.blog-post__aside-frame');
    if (!container) return;

    var sequence = [];
    var isPhotoGallery = false;
    try {
        var raw = wrapper ? JSON.parse(wrapper.getAttribute('data-gallery') || '[]') : [];
        if (raw.length) { sequence = raw; isPhotoGallery = true; }
    } catch (e) { }

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

    var SOUND_LABELS = {
        es: { on: 'Activar sonido', off: 'Silenciar' },
        en: { on: 'Turn sound on', off: 'Mute' },
        it: { on: 'Attiva audio', off: 'Disattiva audio' }
    };
    var ICON_MUTED = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5L6 9H3v6h3l5 4z" fill="currentColor"/><path d="M22 9l-6 6M16 9l6 6"/></svg>';
    var ICON_SOUND = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5L6 9H3v6h3l5 4z" fill="currentColor"/><path d="M15.5 8.5a5 5 0 010 7M19 5a10 10 0 010 14"/></svg>';

    function updateSoundButton(button, video) {
        var labels = SOUND_LABELS[document.documentElement.lang] || SOUND_LABELS.es;
        button.innerHTML = video.muted ? ICON_MUTED : ICON_SOUND;
        button.setAttribute('aria-label', video.muted ? labels.on : labels.off);
        button.setAttribute('aria-pressed', video.muted ? 'false' : 'true');
    }

    function muteInlineVideos() {
        placeholders.forEach(function (el) {
            var video = el.querySelector('video');
            var button = el.querySelector('.video-sound-toggle');
            if (video && button) {
                video.muted = true;
                updateSoundButton(button, video);
            }
        });
    }

    function open(sourceEl) {
        muteInlineVideos();
        var clone = sourceEl.cloneNode(true);
        var cloneButton = clone.querySelector('.video-sound-toggle');
        if (cloneButton) cloneButton.parentNode.removeChild(cloneButton);
        overlay.innerHTML = '';
        overlay.appendChild(clone);
        var video = clone.querySelector('video');
        if (video) {
            video.setAttribute('controls', '');
            video.play().catch(function () { });
        }
        overlay.classList.add('is-open');
        document.body.classList.add('modal-open');
    }

    placeholders.forEach(function (el) {
        el.setAttribute('role', 'button');
        el.setAttribute('tabindex', '0');
        el.addEventListener('click', function () { open(el); });
        el.addEventListener('keydown', function (e) {
            if (e.target !== el) return;
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                open(el);
            }
        });

        var video = el.querySelector('video');
        if (!video) return;
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'video-sound-toggle';
        updateSoundButton(button, video);
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            video.muted = !video.muted;
            if (!video.muted) video.play().catch(function () { });
            updateSoundButton(button, video);
        });
        el.appendChild(button);
        document.addEventListener('a1810:language-changed', function () {
            updateSoundButton(button, video);
        });
    });

    overlay.addEventListener('click', close);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var header = document.querySelector('.site-header--overlay');
    if (!header) return;

    function syncScrolledState() {
        header.classList.toggle('is-scrolled', window.scrollY > 10);
    }

    syncScrolledState();
    window.addEventListener('scroll', syncScrolledState, { passive: true });
});

