import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const motionEnabled = () => document.documentElement.classList.contains('motion');

/** Wrap each word in a masked span so headings can rise line by line. */
function splitWords(el) {
    const walk = (node) => {
        [...node.childNodes].forEach((child) => {
            if (child.nodeType === Node.TEXT_NODE) {
                const fragment = document.createDocumentFragment();
                child.textContent.split(/(\s+)/).forEach((part) => {
                    if (!part) return;
                    if (/^\s+$/.test(part)) {
                        fragment.appendChild(document.createTextNode(' '));
                        return;
                    }
                    const outer = document.createElement('span');
                    const inner = document.createElement('span');
                    outer.className = 'split-word';
                    inner.textContent = part;
                    outer.appendChild(inner);
                    fragment.appendChild(outer);
                });
                child.replaceWith(fragment);
            } else if (child.nodeType === Node.ELEMENT_NODE && child.tagName !== 'BR') {
                walk(child);
            }
        });
    };
    walk(el);
}

export function initAnimations() {
    if (!motionEnabled()) return;

    document.querySelectorAll('[data-split]').forEach((el) => {
        splitWords(el);
        const immediate = el.hasAttribute('data-split-immediate');
        gsap.to(el.querySelectorAll('.split-word > span'), {
            keyframes: { yPercent: [110, 0], opacity: [0, 1] },
            duration: 1.3,
            ease: 'expo.out',
            stagger: 0.055,
            delay: immediate ? parseFloat(el.dataset.delay ?? 0.4) : 0,
            scrollTrigger: immediate ? undefined : { trigger: el, start: 'top 86%', once: true },
        });
    });

    ScrollTrigger.batch('[data-reveal]', {
        start: 'top 90%',
        once: true,
        onEnter: (batch) =>
            batch.forEach((el, i) =>
                gsap.fromTo(
                    el,
                    { y: 36, opacity: 0 },
                    { y: 0, opacity: 1, duration: 1.2, ease: 'expo.out', delay: parseFloat(el.dataset.delay ?? 0) + i * 0.08, clearProps: 'transform' },
                ),
            ),
    });

    document.querySelectorAll('[data-reveal-image]').forEach((el) => {
        const trigger = { trigger: el, start: 'top 88%', once: true };
        gsap.fromTo(el, { clipPath: 'inset(100% 0% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.6, ease: 'expo.inOut', scrollTrigger: trigger });
        const img = el.querySelector('img:not([data-parallax])');
        if (img) gsap.fromTo(img, { scale: 1.22 }, { scale: 1, duration: 2.2, ease: 'expo.out', scrollTrigger: trigger });
    });

    document.querySelectorAll('[data-parallax]').forEach((el) => {
        const speed = parseFloat(el.dataset.parallax || 0.12);
        gsap.fromTo(
            el,
            { yPercent: -speed * 50 },
            { yPercent: speed * 50, ease: 'none', scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true } },
        );
    });

    // Timeline rails draw as the process section scrolls through.
    const mm = gsap.matchMedia();
    mm.add({ desktop: '(min-width: 1024px)', mobile: '(max-width: 1023px)' }, (ctx) => {
        document.querySelectorAll('[data-rail]').forEach((rail) => {
            const fill = rail.querySelector('[data-rail-fill]');
            const scope = rail.closest('[data-rail-scope]') ?? rail;
            const axis = ctx.conditions.desktop ? 'scaleX' : 'scaleY';
            gsap.fromTo(fill, { [axis]: 0 }, { [axis]: 1, ease: 'none', scrollTrigger: { trigger: scope, start: 'top 70%', end: 'bottom 70%', scrub: 0.6 } });
        });
    });

    // Lists that light up item by item as they cross the viewport centre.
    document.querySelectorAll('[data-spotlight] > *').forEach((item) => {
        ScrollTrigger.create({ trigger: item, start: 'top 65%', end: 'bottom 35%', toggleClass: { targets: item, className: 'is-lit' } });
    });

    window.addEventListener('load', () => ScrollTrigger.refresh());
    window.addEventListener('layout:changed', () => ScrollTrigger.refresh());
}
