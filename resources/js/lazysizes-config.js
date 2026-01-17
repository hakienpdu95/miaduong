// Config LazySizes: Tối ưu global, dễ override per-page nếu cần
window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.init = true;  // Auto-init on load
window.lazySizesConfig.preloadAfterLoad = true;  // Load ahead 1-2 images → Giảm lag scroll
window.lazySizesConfig.expFactor = 1.2;  // Expand viewport check sớm hơn → Tối ưu mobile
window.lazySizesConfig.lazyClass = 'lazyload';
window.lazySizesConfig.loadingClass = 'lazyloading';
window.lazySizesConfig.loadedClass = 'lazyloaded';
window.lazySizesConfig.minSize = 40; // skip small images

// Hook Livewire: Re-trigger sau update (tối ưu: checkElems thay init full để nhanh, giảm dư thừa)
document.addEventListener('livewire:load', function () {
    Livewire.hook('message.processed', (message, component) => {
        if (typeof lazySizes !== 'undefined') {
            lazySizes.loader.checkElems();  // Chỉ check images mới, không re-scan toàn DOM → Tối ưu perf
        }
    });
});

// Export nếu cần import (modular cho scale)
export function reinitializeLazySizes() {
    if (typeof lazySizes !== 'undefined') {
        lazySizes.loader.checkElems();
    }
}