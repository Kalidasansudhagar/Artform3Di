/**
 * Hero media.
 *
 * With client footage: lazy-attach the right MP4 for the viewport.
 * Without it: render a cinematic print sequence on canvas — nozzle close-up,
 * extrusion, the sculpture forming layer by layer under an orbiting light,
 * then the finished reveal.
 */

const LAYERS = 150;
const CYCLE = 17000;
const BRONZE = { lo: '#2e2418', mid: '#a8895a', hi: '#efdcb4' };

const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
const lerp = (a, b, t) => a + (b - a) * t;
const ease = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
const ell = (t, c, r, a) => (Math.abs(t - c) < r ? a * Math.sqrt(1 - ((t - c) / r) ** 2) : 0);
const band = (t, a, b, v) => (t >= a && t <= b ? v : 0);

// Mirrors the "deity" profile in App\Support\PlaceholderArt.
const profile = (t) =>
    Math.max(
        band(t, 0, 0.13, 0.62 + 0.14 * Math.abs(Math.sin(t * 70))),
        ell(t, 0.24, 0.12, 0.86),
        ell(t, 0.47, 0.19, 0.46),
        band(t, 0.6, 0.68, 0.16),
        ell(t, 0.75, 0.085, 0.22),
        t > 0.82 && t <= 0.99 ? 0.17 * (1 - (t - 0.82) / 0.17) : 0,
    );

export function initHeroVideo() {
    const video = document.querySelector('[data-hero-video]');
    if (!video) return;

    const src = window.innerWidth < 768 && video.dataset.srcMobile ? video.dataset.srcMobile : video.dataset.srcDesktop;
    const saveData = navigator.connection?.saveData;
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!src || saveData || reduce) return; // poster image remains

    video.src = src;
    video.addEventListener('canplay', () => video.classList.add('opacity-100'), { once: true });
    video.play().catch(() => {});
}

export function initHeroPrint() {
    const canvas = document.querySelector('[data-hero-print]');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const counter = document.querySelector('[data-layer-counter]');
    const status = document.querySelector('[data-print-status]');
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let w = 0;
    let h = 0;
    let visible = true;
    let raf = 0;
    let startedAt = performance.now();
    let lastLabel = '';

    const resize = () => {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const rect = canvas.getBoundingClientRect();
        w = rect.width;
        h = rect.height;
        canvas.width = Math.round(w * dpr);
        canvas.height = Math.round(h * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    };

    const draw = (now) => {
        const t = reduce ? 0.86 : ((now - startedAt) % CYCLE) / CYCLE;
        const printP = clamp((t - 0.05) / 0.66);
        const reveal = ease(clamp((t - 0.72) / 0.14));
        const fade = clamp((t - 0.94) / 0.06);
        const orbit = Math.sin(t * Math.PI * 2);

        const wide = w > 900;
        const cx = wide ? w * 0.68 : w * 0.5;
        const baseY = h * (wide ? 0.84 : 0.8);
        const height = Math.min(h * 0.6, w * (wide ? 0.42 : 0.95));
        const maxHalf = height * 0.4;
        const lh = height / LAYERS;

        const done = printP * LAYERS;
        const layer = Math.min(LAYERS - 1, Math.floor(done));
        const frac = done - layer;
        const halfNow = profile(layer / (LAYERS - 1)) * maxHalf;
        const dir = layer % 2 ? -1 : 1;
        const nozzleX = printP >= 1 ? cx : cx + dir * (frac * 2 - 1) * Math.max(halfNow, 6);
        const nozzleY = baseY - (layer + 1) * lh;
        const headLift = reveal * h * 0.5;

        // Camera: begins as an extreme close-up on the nozzle and pulls back.
        const zoom = lerp(3.2, 1, ease(clamp(t / 0.26))) * (1 + reveal * 0.05);
        const closeUp = clamp((zoom - 1) / 2.2);
        const focusX = nozzleX;
        const focusY = lerp(nozzleY, baseY - height * 0.5, reveal);
        const anchorX = lerp(focusX, cx, closeUp);
        const anchorY = lerp(focusY, h * 0.52, closeUp);

        ctx.fillStyle = '#0c0c0d';
        ctx.fillRect(0, 0, w, h);
        const bg = ctx.createRadialGradient(cx, baseY - height * 0.55, 0, cx, baseY - height * 0.55, Math.max(w, h) * 0.75);
        bg.addColorStop(0, `rgba(58, 48, 34, ${0.55 + reveal * 0.25})`);
        bg.addColorStop(0.45, 'rgba(20, 18, 16, 0.9)');
        bg.addColorStop(1, '#0c0c0d');
        ctx.fillStyle = bg;
        ctx.fillRect(0, 0, w, h);

        ctx.save();
        ctx.translate(anchorX, anchorY);
        ctx.scale(zoom, zoom);
        ctx.translate(-focusX, -focusY);

        // Machine frame, shifting slightly as the "camera" orbits.
        const frameX = maxHalf * 1.85;
        const parallax = orbit * 14;
        ctx.globalAlpha = 0.55 * (1 - fade);
        ctx.fillStyle = '#1d1a17';
        ctx.fillRect(cx - frameX + parallax, baseY - height * 1.35, 10, height * 1.35 + 30);
        ctx.fillRect(cx + frameX - 10 + parallax, baseY - height * 1.35, 10, height * 1.35 + 30);
        ctx.fillStyle = '#2a251f';
        ctx.fillRect(cx - frameX - 40, baseY + 6, frameX * 2 + 80, 8);
        ctx.fillStyle = 'rgba(214, 196, 160, 0.35)';
        ctx.fillRect(cx - frameX - 40, baseY + 6, frameX * 2 + 80, 1);
        ctx.globalAlpha = 1;

        // Sculpture layers with a light source that sweeps around the form.
        const light = clamp(0.38 + orbit * 0.16 + reveal * Math.sin(t * 40) * 0.04, 0.12, 0.85);
        ctx.globalAlpha = 1 - fade;
        for (let i = 0; i <= layer; i++) {
            const half = profile(i / (LAYERS - 1)) * maxHalf;
            if (half < 0.6) continue;
            let x0 = cx - half;
            let x1 = cx + half;
            if (i === layer && printP < 1) {
                if (dir > 0) x1 = Math.max(x0, nozzleX);
                else x0 = Math.min(x1, nozzleX);
            }
            if (x1 - x0 < 0.5) continue;
            const g = ctx.createLinearGradient(cx - half, 0, cx + half, 0);
            g.addColorStop(0, BRONZE.lo);
            g.addColorStop(clamp(light - 0.16), BRONZE.mid);
            g.addColorStop(light, BRONZE.hi);
            g.addColorStop(clamp(light + 0.22), BRONZE.mid);
            g.addColorStop(1, BRONZE.lo);
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.roundRect(x0, baseY - (i + 1) * lh, x1 - x0, lh * 0.74, lh * 0.37);
            ctx.fill();
        }

        // Freshly extruded layers glow warm for a moment.
        if (printP < 1) {
            const glow = ctx.createRadialGradient(nozzleX, nozzleY, 0, nozzleX, nozzleY, 70);
            glow.addColorStop(0, 'rgba(255, 196, 120, 0.55)');
            glow.addColorStop(1, 'rgba(255, 196, 120, 0)');
            ctx.fillStyle = glow;
            ctx.fillRect(nozzleX - 70, nozzleY - 70, 140, 140);
        }

        // Final reveal: a soft light sweep crosses the finished piece.
        if (reveal > 0 && reveal < 1) {
            const sweepX = lerp(cx - maxHalf * 1.6, cx + maxHalf * 1.6, reveal);
            const sweep = ctx.createLinearGradient(sweepX - 60, 0, sweepX + 60, 0);
            sweep.addColorStop(0, 'rgba(255,244,220,0)');
            sweep.addColorStop(0.5, 'rgba(255,244,220,0.16)');
            sweep.addColorStop(1, 'rgba(255,244,220,0)');
            ctx.fillStyle = sweep;
            ctx.fillRect(sweepX - 60, baseY - height - 20, 120, height + 20);
        }
        ctx.globalAlpha = 1;

        // Gantry, print head and nozzle.
        const headY = nozzleY - headLift;
        ctx.globalAlpha = (1 - reveal * 0.85) * (1 - fade);
        ctx.fillStyle = '#3a332b';
        ctx.fillRect(cx - frameX + parallax, headY - 46, frameX * 2, 4);
        ctx.fillStyle = '#16140f';
        ctx.beginPath();
        ctx.roundRect(nozzleX - 22, headY - 58, 44, 34, 4);
        ctx.fill();
        ctx.fillStyle = '#6d5b40';
        ctx.fillRect(nozzleX - 22, headY - 58, 44, 2);
        ctx.fillStyle = '#a8895a';
        ctx.beginPath();
        ctx.moveTo(nozzleX - 6, headY - 24);
        ctx.lineTo(nozzleX + 6, headY - 24);
        ctx.lineTo(nozzleX + 1.4, headY - 3);
        ctx.lineTo(nozzleX - 1.4, headY - 3);
        ctx.closePath();
        ctx.fill();
        if (printP < 1) {
            ctx.fillStyle = '#ffe2b0';
            ctx.fillRect(nozzleX - 0.8, headY - 3, 1.6, 3 + lh * 0.3);
            ctx.beginPath();
            ctx.arc(nozzleX, nozzleY + lh * 0.2, 1.6, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.globalAlpha = 1;
        ctx.restore();

        const label = printP < 1 ? `Layer ${String(layer + 1).padStart(3, '0')} / ${LAYERS}` : `Layer ${LAYERS} / ${LAYERS}`;
        if (counter && label !== lastLabel) {
            counter.textContent = label;
            lastLabel = label;
            if (status) status.textContent = printP < 1 ? 'Printing' : 'Complete';
        }
    };

    const loop = (now) => {
        draw(now);
        if (!reduce && visible && !document.hidden) raf = requestAnimationFrame(loop);
    };

    const play = () => {
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(loop);
    };

    resize();
    window.addEventListener('resize', () => {
        resize();
        if (reduce) draw(performance.now());
    });
    new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        if (visible) play();
    }).observe(canvas);
    document.addEventListener('visibilitychange', () => !document.hidden && play());

    startedAt = performance.now();
    play();
    canvas.classList.add('opacity-100');
}
