import Choices from 'choices.js';

window.Choices = Choices;

function initializeChoices() {
    const selectElements = document.querySelectorAll('.choices-select:not(.choices-initialized)');
    selectElements.forEach(element => {
        if (element.classList.contains('choices-initialized')) return;

        try {
            // Kiểm tra sự tồn tại của option mặc định để tránh lỗi null
            const defaultOption = element.querySelector('option[value=""]');
            const placeholderValue = defaultOption ? defaultOption.text : 'Chọn một giá trị...';

            const choices = new Choices(element, {
                searchEnabled: true,
                searchChoices: true,
                itemSelectText: '',
                shouldSort: false,
                placeholder: true,
                placeholderValue: placeholderValue,
                noChoicesText: 'Không có lựa chọn',
                noResultsText: 'Không tìm thấy kết quả',
                removeItemButton: true,
            });

            element.classList.add('choices-initialized');
            element.__choices = choices;

            // Đồng bộ giá trị với wire:model
            const wireModel = element.getAttribute('wire:model.debounce.150ms') || element.getAttribute('wire:model.debounce.500ms');
            if (wireModel) {
                const componentId = element.closest('[wire\\:id]')?.getAttribute('wire:id');
                if (componentId) {
                    let currentValue = window.Livewire.find(componentId).get(wireModel);
                    if (currentValue && typeof currentValue === 'object' && 'value' in currentValue) {
                        currentValue = currentValue.value;
                    }
                    console.log('Syncing Choices for', wireModel, 'with value:', currentValue);
                    if (currentValue) {
                        choices.setChoiceByValue(currentValue);
                    }
                }

                // Cập nhật Livewire khi giá trị thay đổi
                element.addEventListener('change', function () {
                    const componentId = element.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        console.log('Updating Livewire for', wireModel, 'with value:', element.value);
                        window.Livewire.find(componentId).set(wireModel, element.value);
                    }
                });
            }
        } catch (error) {
            console.error('Error initializing Choices:', element, error);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded triggered');
    initializeChoices();
});

document.addEventListener('livewire:load', function () {
    console.log('livewire:load triggered');
    setTimeout(initializeChoices, 50);
});

// Thêm sự kiện để xử lý cập nhật DOM từ Livewire
document.addEventListener('livewire:navigated', function () {
    console.log('livewire:navigated triggered');
    setTimeout(initializeChoices, 50);
});