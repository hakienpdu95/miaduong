import SunEditor from 'suneditor';
import plugins from 'suneditor/src/plugins';

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function destroySunEditor(editor) {
    console.log('Destroying SunEditor:', editor?.id);
    if (editor?.__suneditor) {
        editor.__suneditor.destroy();
        editor.__suneditor = null;
        delete editor.__suneditor;
        editor.innerHTML = '';
        editor.classList.remove('suneditor-initialized');
        editor.dataset = {};
    }
}

function deleteEditorImage(fullUrl) {
    fetch('/api/upload/editor-images/revert', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, // Thêm CSRF nếu dùng session auth
        },
        body: JSON.stringify({ full_url: fullUrl }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Image deleted from server:', fullUrl);
        } else {
            console.error('Delete failed:', data.message);
        }
    })
    .catch(error => console.error('Delete error:', error));
}

function checkRemovedImages(suneditor) {
    const currentContent = suneditor.getContents();
    const srcMatches = currentContent.matchAll(/<img[^>]*src="([^"]+)"[^>]*>/gi);
    const currentSrcs = new Set(Array.from(srcMatches, match => match[1]));
    suneditor.uploadedImages.forEach(src => {
        if (!currentSrcs.has(src)) {
            deleteEditorImage(src);
            suneditor.uploadedImages.delete(src);
            console.log('Detected and deleted removed image:', src);
        }
    });
}

function initializeSunEditor(editor) {
    if (!editor) {
        console.error('Editor element is null or undefined');
        return;
    }
    if (editor.classList.contains('suneditor-initialized') || !editor.dataset.input || !editor.dataset.index) {
        console.log('Skipping SunEditor initialization for editor:', editor.id, 'Already initialized or missing data attributes');
        return;
    }

    const editorId = `editor-${editor.dataset.index}-${editor.dataset.input}`;
    editor.id = editorId;
    editor.classList.add('suneditor-initialized');
    console.log('Initializing SunEditor for editor:', editorId);

    const editorHeight = editor.dataset.height || '200px';

    try {
        const suneditor = SunEditor.create(editor, {
            plugins: plugins,
            buttonList: [
                ['undo', 'redo'],
                ['fontSize', 'formatBlock'],
                ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
                ['fontColor', 'hiliteColor'],
                ['align', 'horizontalRule', 'list', 'lineHeight'],
                ['table', 'link', 'image', 'video', 'audio'],
                ['codeView'],
                ['removeFormat']
            ],
            height: editorHeight,
            width: '100%',
            placeholder: 'Nhập nội dung...',
            mode: 'classic',
            imageFileInput: true,
            imageUrlInput: false,
            imageUploadHeader: null,
            imageUploadUrl: '/api/upload/editor-images', // Không dùng, vì xử lý manual ở onImageUploadBefore
            defaultStyle: 'font-size: 20px;',
            attributesWhitelist: {
                'img': 'data-editor-image-id|data-module-id|data-proportion|data-align|data-file-name|data-file-size|data-origin|data-size|data-rotate|data-percentage|origin-size|style|alt|src'
            }
        });

        suneditor.uploadedImages = new Set();

        const contentInput = document.querySelector(`#${editor.dataset.input}`);
        if (!contentInput) {
            console.error(`Content input not found for editor ${editorId}, selector: #${editor.dataset.input}`);
            return;
        }

        if (contentInput.value) {
            suneditor.setContents(contentInput.value);
            console.log('Set initial content for SunEditor:', editorId, contentInput.value);
            const initialContent = suneditor.getContents();
            const srcMatches = initialContent.matchAll(/<img[^>]*src="([^"]+)"[^>]*>/gi);
            Array.from(srcMatches).forEach(match => {
                const src = match[1];
                if (src && src.includes('/storage/uploads/')) {
                    suneditor.uploadedImages.add(src);
                }
            });
            console.log('Initialized uploadedImages from existing content:', Array.from(suneditor.uploadedImages));
        }

        const debouncedDispatch = debounce((contents) => {
            try {
                if (contentInput) {
                    contentInput.value = contents;
                    contentInput.dispatchEvent(new Event('input', { bubbles: true }));
                    console.log('Updated content for SunEditor:', editorId, contents);
                }
                checkRemovedImages(suneditor);
            } catch (error) {
                console.error('Error updating SunEditor content:', error);
            }
        }, 600);

        suneditor.onChange = (contents) => {
            debouncedDispatch(contents);
        };

        suneditor.onImageUploadBefore = (files, info, core, uploadHandler) => {
            if (!files || !files.length || !files[0]) {
                console.warn('No valid files provided to onImageUploadBefore, skipping');
                uploadHandler();
                return;
            }

            const file = files[0];
            console.log('File type:', file.type);

            if (file && /^image\/(png|jpeg|jpg)$/.test(file.type)) {
                const formData = new FormData();
                formData.append('single_image', file);

                fetch('/api/upload/editor-images', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, // Thêm CSRF
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Upload response:', data);
                    if (data.success && data.files && data.files.length > 0) {
                        const imageData = data.files[0];
                        const imageUrl = imageData.full_url;
                        const editorImageId = imageData.editor_image_id || 'temp-id'; // Fallback nếu không có
                        const originalName = imageData.name;
                        const alt = imageData.alt || originalName;
                        const width = imageData.width;
                        const height = imageData.height;

                        // Insert HTML img với attributes
                        const imgHtml = `
                            <div class="se-component se-image-container __se__float-none" contenteditable="false">
                                <figure>
                                    <img src="${imageUrl}" alt="${alt}" data-proportion="true" data-align="none" data-file-name="${originalName}" data-file-size="0" data-origin="${width},${height}" data-size="${width},${height}" data-rotate="" data-percentage="auto,auto" origin-size="${width},${height}" data-editor-image-id="${editorImageId}" style="max-width: 100%;">
                                </figure>
                            </div>
                        `;
                        suneditor.insertHTML(imgHtml, true);
                        suneditor.uploadedImages.add(imageUrl);

                        const updatedContent = suneditor.getContents();
                        if (contentInput) {
                            contentInput.value = updatedContent;
                            contentInput.dispatchEvent(new Event('input', { bubbles: true }));
                            console.log('Image inserted and content updated:', imageUrl, updatedContent);
                        }
                        uploadHandler(false); // Ngăn insert default
                    } else {
                        console.error('Upload failed:', data.message);
                        alert('Tải ảnh thất bại: ' + data.message);
                        uploadHandler();
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Lỗi khi tải ảnh lên: ' + error.message);
                    uploadHandler();
                });
            } else {
                console.warn('Invalid file type:', file ? file.type : 'undefined');
                alert('Vui lòng chọn file ảnh PNG hoặc JPG/JPEG.');
                uploadHandler();
            }
        };

        suneditor.onImageUpload = (targetImgElement, index, state, imageInfo, remainingFilesCount) => {
            console.log('onImageUpload called:', { targetImgElement, index, state, imageInfo, remainingFilesCount });
            if (state === 'delete' && imageInfo && imageInfo.src) {
                const fullUrl = imageInfo.src;
                deleteEditorImage(fullUrl);
                suneditor.uploadedImages.delete(fullUrl);
                const updatedContent = suneditor.getContents();
                if (contentInput) {
                    contentInput.value = updatedContent;
                    contentInput.dispatchEvent(new Event('input', { bubbles: true }));
                    console.log('Image deleted via onImageUpload:', fullUrl, updatedContent);
                }
            }
        };

        editor.__suneditor = suneditor;
        console.log('SunEditor initialized successfully:', editorId);

        const toolbar = document.querySelector(`#${editorId} .sun-editor .se-toolbar`);
        if (!toolbar) {
            console.error('SunEditor toolbar not found for editor:', editorId);
        } else {
            console.log('SunEditor toolbar rendered successfully for editor:', editorId);
        }
    } catch (error) {
        console.error(`Error initializing SunEditor for editor ${editorId}:`, error);
        editor.classList.remove('suneditor-initialized');
    }
}

window.initializeSunEditor = initializeSunEditor;
window.destroySunEditor = destroySunEditor;

function initializeAllSunEditors() {
    console.log('Initializing all SunEditor editors...');
    const editors = document.querySelectorAll('.suneditor-block:not(.suneditor-initialized)');
    if (editors.length === 0) {
        console.warn('No SunEditor editors found to initialize');
        return;
    }
    editors.forEach((editor, index) => {
        console.log(`Initializing SunEditor ${index}:`, editor.id);
        initializeSunEditor(editor);
    });
}

function observeEditors() {
    const container = document.querySelector('#blocks-container');
    if (!container) {
        console.log('No blocks container found, skipping observer');
        return;
    }

    const debouncedCallback = debounce((mutations) => {
        let shouldProcess = false;
        mutations.forEach((mutation) => {
            if (mutation.removedNodes.length) {
                mutation.removedNodes.forEach(node => {
                    if (node.querySelectorAll) {
                        node.querySelectorAll('.suneditor-block').forEach(destroySunEditor);
                    }
                });
            }
            if (mutation.addedNodes.length) {
                shouldProcess = true;
            }
        });
        if (shouldProcess) {
            console.log('Detected new nodes, initializing SunEditor editors...');
            const editors = document.querySelectorAll('.suneditor-block:not(.suneditor-initialized)');
            if (editors.length === 0) {
                console.warn('No new SunEditor editors found to initialize');
            }
            editors.forEach((editor, index) => {
                console.log(`Initializing new SunEditor ${index}:`, editor.id);
                initializeSunEditor(editor);
            });
        }
    }, 300);

    const observer = new MutationObserver(debouncedCallback);
    observer.observe(container, { childList: true, subtree: true });
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, starting SunEditor initialization');
    initializeAllSunEditors();
    observeEditors();
});