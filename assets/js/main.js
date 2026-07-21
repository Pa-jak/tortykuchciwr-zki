(function () {
    'use strict';

    // Oferta — filtry per blok (jak w galerii, ale niezależne dla każdego bloku)
    document.querySelectorAll('[data-oferta-block]').forEach(function (block) {
        const btns = block.querySelectorAll('.filter-btn');
        const groups = block.querySelectorAll('.oferta-cat-group');
        btns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                btns.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                const filter = btn.dataset.filter;
                groups.forEach(function (g) {
                    g.style.display = (filter === 'all' || g.dataset.cat === filter) ? '' : 'none';
                });
            });
        });
    });

    // Galeria — filtry i lightbox
    const gallery = document.getElementById('gallery');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = gallery ? gallery.querySelectorAll('.gallery-item') : [];
    const lightbox = document.getElementById('lightbox');
    const lightboxStage = lightbox ? lightbox.querySelector('.lightbox-stage') : null;
    const allItems = Array.from(galleryItems);

    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterButtons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            const filter = btn.dataset.filter;
            allItems.forEach(function (item) {
                item.style.display = (filter === 'all' || item.dataset.tag === filter) ? '' : 'none';
            });
        });
    });

    function visibleItems() {
        return allItems.filter(function (item) { return item.style.display !== 'none'; });
    }

    function openLightbox(index) {
        if (!lightbox || !lightboxStage) return;
        const items = visibleItems();
        if (!items.length) return;
        const item = items[index];
        if (!item) return;
        const source = item.querySelector('img, .placeholder');
        if (!source) return;
        lightboxStage.innerHTML = '';
        const clone = source.cloneNode(true);
        clone.removeAttribute('id');
        lightboxStage.appendChild(clone);
        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        lightbox.dataset.index = String(index);
    }

    function closeLightbox() {
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        lightboxStage.innerHTML = '';
    }

    function moveLightbox(direction) {
        if (!lightbox.dataset.index) return;
        const count = visibleItems().length;
        if (!count) return;
        let index = parseInt(lightbox.dataset.index, 10) + direction;
        if (index < 0) index = count - 1;
        if (index >= count) index = 0;
        openLightbox(index);
    }

    allItems.forEach(function (item) {
        item.addEventListener('click', function () {
            openLightbox(visibleItems().indexOf(item));
        });
    });

    document.querySelector('.lightbox-close')?.addEventListener('click', closeLightbox);
    document.querySelector('.lightbox-prev')?.addEventListener('click', function (e) { e.stopPropagation(); moveLightbox(-1); });
    document.querySelector('.lightbox-next')?.addEventListener('click', function (e) { e.stopPropagation(); moveLightbox(1); });
    lightbox?.addEventListener('click', function (e) { if (e.target === lightbox) closeLightbox(); });
    document.addEventListener('keydown', function (e) {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') moveLightbox(-1);
        if (e.key === 'ArrowRight') moveLightbox(1);
    });

    // Karuzela opinii
    const carousel = document.querySelector('[data-carousel]');
    if (carousel) {
        const track = carousel.querySelector('.carousel-track');
        const slides = track ? Array.from(track.querySelectorAll('.testimonial')) : [];
        const dotsContainer = carousel.querySelector('.carousel-dots');
        const prevBtn = carousel.querySelector('.carousel-prev');
        const nextBtn = carousel.querySelector('.carousel-next');
        if (!slides.length) return;

        let activeIndex = 0;
        let autoTimer;

        function show(index) {
            activeIndex = index;
            slides.forEach(function (slide, i) { slide.classList.toggle('active', i === index); });
            if (dotsContainer) {
                dotsContainer.querySelectorAll('button').forEach(function (dot, i) {
                    dot.classList.toggle('active', i === index);
                });
            }
        }

        function next() { show((activeIndex + 1) % slides.length); }
        function prev() { show((activeIndex - 1 + slides.length) % slides.length); }

        slides.forEach(function (_slide, index) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('aria-label', 'Opinia ' + (index + 1));
            dot.addEventListener('click', function () { show(index); resetTimer(); });
            dotsContainer?.appendChild(dot);
        });

        show(0);

        prevBtn?.addEventListener('click', function () { prev(); resetTimer(); });
        nextBtn?.addEventListener('click', function () { next(); resetTimer(); });

        function resetTimer() {
            clearInterval(autoTimer);
            autoTimer = setInterval(next, 7000);
        }

        resetTimer();
    }
})();
