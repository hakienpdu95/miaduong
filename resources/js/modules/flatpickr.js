import flatpickr from 'flatpickr';
import 'flatpickr/dist/l10n/vn.js';

// Định nghĩa class để khởi tạo flatpickr
class FlatpickrInitializer {
    static initialize() {
        const datepickers = document.querySelectorAll('.flatpickr:not(.flatpickr-initialized)');
        datepickers.forEach(element => {
            if (element.classList.contains('flatpickr-initialized')) return;

            try {
                // Cấu hình mặc định cho flatpickr
                const config = {
                    dateFormat: 'Y-m-d',
                    locale: 'vn', // Ngôn ngữ tiếng Việt
                    placeholder: element.getAttribute('placeholder') || 'Chọn ngày...',
                    onChange: (selectedDates, dateStr, instance) => {
                        // Đồng bộ với wire:model
                        const wireModel = element.getAttribute('wire:model.debounce.150ms') || element.getAttribute('wire:model');
                        if (wireModel) {
                            const componentId = element.closest('[wire\\:id]')?.getAttribute('wire:id');
                            if (componentId) {
                                console.log('Syncing Flatpickr for', wireModel, 'with value:', dateStr);
                                window.Livewire.find(componentId).set(wireModel, dateStr);
                            }
                        }
                    }
                };

                const customConfig = element.getAttribute('data-flatpickr-config');
                if (customConfig) {
                    try {
                        Object.assign(config, JSON.parse(customConfig));
                    } catch (error) {
                        console.error('Error parsing custom Flatpickr config:', customConfig, error);
                    }
                }
                
                // Khởi tạo flatpickr
                const fpInstance = flatpickr(element, config);
                element.classList.add('flatpickr-initialized');
                element.__flatpickr = fpInstance; // Lưu instance để tái sử dụng nếu cần

                // Đồng bộ giá trị ban đầu từ Livewire
                const wireModel = element.getAttribute('wire:model.debounce.150ms') || element.getAttribute('wire:model');
                if (wireModel) {
                    const componentId = element.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        let currentValue = window.Livewire.find(componentId).get(wireModel);
                        if (currentValue) {
                            console.log('Setting initial Flatpickr value for', wireModel, 'to:', currentValue);
                            fpInstance.setDate(currentValue, true);
                        }
                    }
                }
            } catch (error) {
                console.error('Error initializing Flatpickr:', element, error);
            }
        });
    }
}

// Xuất class để sử dụng toàn cục
window.FlatpickrInitializer = FlatpickrInitializer;

// Khởi tạo khi DOM hoặc Livewire load
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded triggered for Flatpickr');
    FlatpickrInitializer.initialize();
});

document.addEventListener('livewire:load', () => {
    console.log('livewire:load triggered for Flatpickr');
    setTimeout(FlatpickrInitializer.initialize, 50);
});

document.addEventListener('livewire:navigated', () => {
    console.log('livewire:navigated triggered for Flatpickr');
    setTimeout(FlatpickrInitializer.initialize, 50);
});