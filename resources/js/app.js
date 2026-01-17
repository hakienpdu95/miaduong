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

    // Native event for shown.bs.modal (no jQuery)
    const previewModal = document.getElementById('previewModal');
    previewModal.addEventListener('shown.bs.modal', function() {
        var content_height = (window.innerHeight - 200);
        const projectFileArea = document.querySelector('.project_file_area');
        projectFileArea.style.maxHeight = content_height + 'px';
        projectFileArea.style.overflowY = 'auto';
        // Reset scroll
        document.querySelector('.modal-body').scrollTop = 0;
        projectFileArea.scrollTop = 0;
    });

    // Delegation for data-dismiss (native click handler)
    document.body.addEventListener('click', function(event) {
        if (event.target.matches('[data-dismiss="modal"]')) {
            const modal = event.target.closest('.modal');
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        }
    });
});

window.trans = trans;

// Native previewDocument (no jQuery)
window.previewDocument = function(docId, fileName, fileUrl) {
    document.querySelector('#previewModal .modal-title').textContent = fileName;
    document.querySelector('#previewModal .project_file_area img').src = fileUrl;
    const previewModal = document.getElementById('previewModal');
    const bsModal = new bootstrap.Modal(previewModal);
    bsModal.show();
};

// Event delegation for preview-link (native, no jQuery)
document.addEventListener('click', function(event) {
    const target = event.target.closest('a.preview-link');
    if (target) {
        const docId = target.getAttribute('data-doc-id');
        const fileName = target.getAttribute('data-file-name');
        const fileUrl = target.getAttribute('data-file-url');
        if (docId && fileName && fileUrl) {
            window.previewDocument(docId, fileName, fileUrl);
            event.preventDefault();
        }
    }
});