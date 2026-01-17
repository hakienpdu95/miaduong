let translations = {};
let currentLocale = 'en'; // Mặc định

// Hàm tải bản dịch từ JSON
export async function loadTranslations(locale) {
    try {
        const response = await fetch(`/lang/${locale}.json`);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        translations = await response.json();
        currentLocale = locale;
        // console.log(`Loaded translations for ${locale}:`, translations);
    } catch (error) {
        // console.error(`Error loading translations for ${locale}:`, error);
    }
}

// Hàm lấy bản dịch
export function trans(key, replace = {}) {
    let translation = translations[key] || key;
    for (const [placeholder, value] of Object.entries(replace)) {
        translation = translation.replace(`:${placeholder}`, value);
    }
    return translation;
}

// Hàm lấy ngôn ngữ hiện tại
export function getLocale() {
    return currentLocale;
}