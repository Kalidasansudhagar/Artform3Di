/**
 * Single-series area chart for the admin dashboard.
 * Reads data-series (points per range), data-format ("number" or "currency") and
 * data-label, and listens for `admin-range` to switch ranges.
 * Hover / arrow keys move a crosshair that snaps to the nearest point.
 */

const NS = 'http://www.w3.org/2000/svg';
const COLORS = { series: '#9a6f24', grid: '#ece7df', muted: '#8a867e', surface: '#ffffff' };
const MARGIN = { top: 12, right: 14, bottom: 30, left: 56 };

const compact = new Intl.NumberFormat('en-IN', { notation: 'compact', maximumFractionDigits: 1 });
const currency = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 });
const plain = new Intl.NumberFormat('en-IN');

const el = (tag, attrs = {}) => {
    const node = document.createElementNS(NS, tag);
    Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value));
    return node;
};

// Round a raw tick step up to a clean 1 / 2 / 2.5 / 5 × 10^n value.
const niceStep = (value) => {
    const exp = 10 ** Math.floor(Math.log10(value));
    const f = value / exp;
    return (f <= 1 ? 1 : f <= 2 ? 2 : f <= 2.5 ? 2.5 : f <= 5 ? 5 : 10) * exp;
};

export function initRevenueChart(container) {
    const data = JSON.parse(container.dataset.series);
    const isCurrency = container.dataset.format === 'currency';
    const label = container.dataset.label ?? 'values';
    const formatTick = (v) => (isCurrency ? (v === 0 ? '₹0' : `₹${compact.format(v)}`) : compact.format(v));
    const formatFull = (v) => (isCurrency ? currency.format(v) : `${plain.format(v)} ${label}`);
    let range = container.dataset.range;
    let geometry = null;
    let active = null;

    const svg = el('svg', { class: 'h-full w-full overflow-visible', role: 'img', tabindex: '0' });
    svg.style.outline = 'none';
    const tooltip = document.createElement('div');
    tooltip.className = 'pointer-events-none absolute z-10 hidden -translate-x-1/2 rounded-lg bg-ink px-3 py-2 text-center whitespace-nowrap text-bone shadow-lg';
    const tipValue = document.createElement('p');
    tipValue.className = 'text-sm font-semibold';
    const tipLabel = document.createElement('p');
    tipLabel.className = 'mt-0.5 flex items-center justify-center gap-1.5 text-[11px] text-bone/60';
    const key = document.createElement('span');
    key.className = 'inline-block h-0.5 w-3 rounded-full';
    key.style.background = COLORS.series;
    const tipText = document.createElement('span');
    tipLabel.append(key, tipText);
    tooltip.append(tipValue, tipLabel);
    container.append(svg, tooltip);

    const render = () => {
        const { width, height } = container.getBoundingClientRect();
        if (!width || !height) return;

        const points = data[range];
        const values = points.map((p) => p.value);
        const peak = Math.max(...values) * 1.08;
        // Counts tick in whole numbers; an all-zero series still gets a readable 0–4 axis.
        const rawStep = peak > 0 ? niceStep(peak / 4) : 1;
        const tickStep = isCurrency ? rawStep : Math.max(1, Math.ceil(rawStep));
        const ticks = peak > 0 ? Math.max(1, Math.ceil(peak / tickStep)) : 4;
        const max = tickStep * ticks;
        const innerW = width - MARGIN.left - MARGIN.right;
        const innerH = height - MARGIN.top - MARGIN.bottom;
        const x = (i) => MARGIN.left + (points.length === 1 ? innerW / 2 : (i * innerW) / (points.length - 1));
        const y = (v) => MARGIN.top + (1 - v / max) * innerH;
        const baseline = y(0);
        geometry = { points, x, y };

        svg.replaceChildren();
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
        svg.setAttribute('aria-label', `Chart of ${label}, ${points.length} periods from ${points[0].label} to ${points.at(-1).label}. Use left and right arrow keys to read values.`);

        // Gridlines + y ticks.
        for (let t = 0; t <= ticks; t++) {
            const v = tickStep * t;
            svg.append(el('line', { x1: MARGIN.left, x2: width - MARGIN.right, y1: y(v), y2: y(v), stroke: COLORS.grid, 'stroke-width': 1 }));
            const label = el('text', { x: MARGIN.left - 10, y: y(v) + 4, 'text-anchor': 'end', fill: COLORS.muted, 'font-size': 11 });
            label.textContent = formatTick(v);
            svg.append(label);
        }

        // X labels: about six, always including the last period.
        const step = Math.max(1, Math.ceil(points.length / 6));
        points.forEach((p, i) => {
            if (i % step !== 0 && i !== points.length - 1) return;
            if (i !== points.length - 1 && points.length - 1 - i < step) return;
            const label = el('text', { x: x(i), y: height - 8, 'text-anchor': i === 0 ? 'start' : i === points.length - 1 ? 'end' : 'middle', fill: COLORS.muted, 'font-size': 11 });
            label.textContent = p.label;
            svg.append(label);
        });

        const line = points.map((p, i) => `${i ? 'L' : 'M'}${x(i).toFixed(1)},${y(p.value).toFixed(1)}`).join('');
        svg.append(el('path', { d: `${line}L${x(points.length - 1)},${baseline}L${x(0)},${baseline}Z`, fill: COLORS.series, 'fill-opacity': 0.1 }));
        svg.append(el('path', { d: line, fill: 'none', stroke: COLORS.series, 'stroke-width': 2, 'stroke-linejoin': 'round', 'stroke-linecap': 'round' }));

        const last = points.length - 1;
        svg.append(el('circle', { cx: x(last), cy: y(points[last].value), r: 4, fill: COLORS.series, stroke: COLORS.surface, 'stroke-width': 2 }));

        // Crosshair layer (hidden until hover/focus).
        geometry.cross = el('g', { visibility: 'hidden' });
        geometry.crossLine = el('line', { y1: MARGIN.top, y2: baseline, stroke: COLORS.muted, 'stroke-width': 1 });
        geometry.crossDot = el('circle', { r: 5, fill: COLORS.series, stroke: COLORS.surface, 'stroke-width': 2 });
        geometry.cross.append(geometry.crossLine, geometry.crossDot);
        svg.append(geometry.cross);

        if (active !== null) show(Math.min(active, last));
    };

    const show = (i) => {
        if (!geometry) return;
        active = i;
        const { points, x, y, cross, crossLine, crossDot } = geometry;
        const px = x(i);
        const py = y(points[i].value);
        crossLine.setAttribute('x1', px);
        crossLine.setAttribute('x2', px);
        crossDot.setAttribute('cx', px);
        crossDot.setAttribute('cy', py);
        cross.setAttribute('visibility', 'visible');

        tipValue.textContent = formatFull(points[i].value);
        tipText.textContent = points[i].label;
        tooltip.classList.remove('hidden');
        const half = tooltip.offsetWidth / 2;
        tooltip.style.left = `${Math.min(Math.max(px, half), container.clientWidth - half)}px`;
        tooltip.style.top = `${Math.max(py - tooltip.offsetHeight - 14, -8)}px`;
    };

    const hide = () => {
        active = null;
        geometry?.cross.setAttribute('visibility', 'hidden');
        tooltip.classList.add('hidden');
    };

    svg.addEventListener('pointermove', (event) => {
        if (!geometry) return;
        const rect = svg.getBoundingClientRect();
        const { points, x } = geometry;
        const px = event.clientX - rect.left;
        let nearest = 0;
        points.forEach((_, i) => {
            if (Math.abs(x(i) - px) < Math.abs(x(nearest) - px)) nearest = i;
        });
        show(nearest);
    });
    svg.addEventListener('pointerleave', hide);
    svg.addEventListener('blur', hide);
    svg.addEventListener('focus', () => geometry && show(geometry.points.length - 1));
    svg.addEventListener('keydown', (event) => {
        if (!geometry || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
        event.preventDefault();
        const last = geometry.points.length - 1;
        const current = active ?? last;
        const next = { ArrowLeft: current - 1, ArrowRight: current + 1, Home: 0, End: last }[event.key];
        show(Math.min(Math.max(next, 0), last));
    });

    window.addEventListener('admin-range', (event) => {
        range = event.detail;
        hide();
        render();
    });
    new ResizeObserver(render).observe(container);
    render();
}
