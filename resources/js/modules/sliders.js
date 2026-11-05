import Swiper from 'swiper';
import { A11y, Keyboard, Navigation } from 'swiper/modules';

const presets = {
    products: {
        slidesPerView: 1.18,
        spaceBetween: 16,
        breakpoints: {
            640: { slidesPerView: 2.15, spaceBetween: 20 },
            1024: { slidesPerView: 3.15, spaceBetween: 28 },
            1440: { slidesPerView: 3.5, spaceBetween: 32 },
        },
    },
    projects: {
        slidesPerView: 1.1,
        spaceBetween: 16,
        breakpoints: {
            768: { slidesPerView: 1.6, spaceBetween: 24 },
            1280: { slidesPerView: 2.25, spaceBetween: 32 },
        },
    },
};

export function initSliders() {
    document.querySelectorAll('[data-slider]').forEach((root) => {
        const bar = root.querySelector('.slider-progress span');
        const updateBar = (swiper) => {
            if (!bar) return;
            const visible = swiper.slides.length ? Math.min(1, swiper.params.slidesPerView / swiper.slides.length) : 1;
            bar.style.transform = `scaleX(${visible + (1 - visible) * Math.max(0, Math.min(1, swiper.progress))})`;
        };

        new Swiper(root.querySelector('.swiper'), {
            modules: [Navigation, A11y, Keyboard],
            speed: 900,
            grabCursor: true,
            watchOverflow: true,
            keyboard: { enabled: true, onlyInViewport: true },
            navigation: {
                prevEl: root.querySelector('[data-prev]'),
                nextEl: root.querySelector('[data-next]'),
                disabledClass: 'is-disabled',
            },
            ...presets[root.dataset.slider],
            on: { init: updateBar, progress: updateBar, resize: updateBar },
        });
    });

    document.querySelectorAll('[data-gallery]').forEach((root) => {
        const thumbs = [...root.querySelectorAll('[data-thumb]')];
        const gallery = new Swiper(root.querySelector('.swiper'), {
            modules: [Navigation, A11y, Keyboard],
            speed: 800,
            keyboard: { enabled: true, onlyInViewport: true },
            navigation: {
                prevEl: root.querySelector('[data-prev]'),
                nextEl: root.querySelector('[data-next]'),
                disabledClass: 'is-disabled',
            },
        });
        const sync = () =>
            thumbs.forEach((thumb, i) => {
                thumb.classList.toggle('is-active', i === gallery.activeIndex);
                thumb.setAttribute('aria-current', i === gallery.activeIndex ? 'true' : 'false');
            });
        thumbs.forEach((thumb, i) => thumb.addEventListener('click', () => gallery.slideTo(i)));
        gallery.on('slideChange', sync);
        sync();
    });
}
