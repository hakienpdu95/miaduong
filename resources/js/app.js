import * as Popper from '@popperjs/core';
window.Popper = Popper;

// Import Bootstrap bundle (native JS, no jQuery)
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
window.bootstrap = bootstrap;

import { loadTranslations, trans } from './lang';

// Load translations và code IIFE (chuyển sang native, xóa jQuery)
document.addEventListener('DOMContentLoaded', async () => {
    const locale = window.APP_LOCALE || 'en';
    await loadTranslations(locale);
});

window.trans = trans;