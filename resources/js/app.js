import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

import registerAlpine from './modules/alpine';
import { initIcons } from './modules/icons';
import { initAnimations } from './modules/animations';
import { initSliders } from './modules/sliders';
import { initHeroPrint, initHeroVideo } from './modules/hero';
import { initPageTransitions } from './modules/transitions';

// Icons are swapped in before Alpine starts so bindings on them (e.g. :class) survive.
initIcons();

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.plugin(focus);
registerAlpine(Alpine);
Alpine.start();

initSliders();
initHeroVideo();
initHeroPrint();
initAnimations();
initPageTransitions();

// The 3D viewer is heavy, so it is only fetched on pages that contain a <model-viewer>.
if (document.querySelector('model-viewer')) {
    import('@google/model-viewer');
}

// Admin charts load only on the admin dashboard.
const revenueChart = document.querySelector('[data-admin-chart]');
if (revenueChart) {
    import('./admin/revenue-chart').then(({ initRevenueChart }) => initRevenueChart(revenueChart));
}
