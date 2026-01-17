import Tagify from '@yaireo/tagify';
window.Tagify = Tagify;

function initializeTagify() {
    console.log('Initializing Tagify...');
    const inputs = document.querySelectorAll('input[name="tags"]:not(.tagify-initialized)');
    if (inputs.length === 0) {
        console.warn('No tags input found to initialize Tagify');
        return;
    }

    inputs.forEach((input, index) => {
        const inputId = input.id || `tags-${index}`;
        input.id = inputId;
        input.classList.add('tagify-initialized');
        console.log('Initializing Tagify for input:', inputId);

        try {
            const tagify = new Tagify(input, {
                delimiters: ',', // Tách tags bằng dấu phẩy
                duplicates: false, // Không cho phép tags trùng lặp
                placeholder: 'Nhập tags, cách nhau bằng dấu phẩy',
                dropdown: {
                    enabled: 0, // Tắt dropdown gợi ý
                },
            });

            // Đồng bộ giá trị với Livewire
            tagify.on('change', (e) => {
                const tags = e.detail.value ? JSON.parse(e.detail.value) : [];
                const value = tags.map(tag => tag.value).join(',');
                input.value = JSON.stringify(tags); // Lưu giá trị JSON
                input.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('Tagify change:', value);
            });

            // Đặt giá trị ban đầu từ input
            if (input.value) {
                try {
                    let tags = input.value;
                    // Parse giá trị input, có thể là chuỗi JSON hoặc mảng
                    if (typeof tags === 'string' && tags.startsWith('[')) {
                        tags = JSON.parse(tags);
                    }
                    // Xử lý tags là mảng các chuỗi hoặc mảng các object
                    const tagValues = Array.isArray(tags)
                        ? tags.map(tag => typeof tag === 'object' && tag.value ? tag.value : tag).filter(tag => tag && typeof tag === 'string')
                        : [];
                    // Xóa toàn bộ tags hiện tại trước khi thêm để tránh nhân đôi
                    tagify.removeAllTags();
                    // Thêm tags đã lọc
                    if (tagValues.length > 0) {
                        tagify.addTags(tagValues);
                        console.log('Set initial tags for Tagify:', inputId, tagValues);
                    }
                } catch (error) {
                    console.error('Error parsing initial tags for Tagify:', inputId, error);
                }
            }
        } catch (error) {
            console.error(`Error initializing Tagify for input ${inputId}:`, error);
            input.classList.remove('tagify-initialized');
        }
    });
}

function destroyTagify(input) {
    console.log('Destroying Tagify:', input?.id);
    if (input?.tagify) {
        input.tagify.destroy();
        input.tagify = null;
        input.classList.remove('tagify-initialized');
    }
}

window.initializeTagify = initializeTagify;
window.destroyTagify = destroyTagify;

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, starting Tagify initialization');
    initializeTagify();
});

// Hỗ trợ Livewire re-render
document.addEventListener('livewire:navigated', () => {
    console.log('Livewire navigated, reinitializing Tagify');
    initializeTagify();
});