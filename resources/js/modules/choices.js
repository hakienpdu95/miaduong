// resources/js/modules/choices.js
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
        } catch (error) {
            console.error('Error initializing Choices:', element, error);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initializeChoices();
});