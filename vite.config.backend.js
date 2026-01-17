import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path'

export default defineConfig({
    base: process.env.NODE_ENV === 'production' ? '/build/backend/' : '/',
    plugins: [
        laravel({
            input: [
                'resources/css/bootstrap.css',
                'resources/css/fontawesomepro.css',
                'resources/css/toastify.css',
                'resources/css/choices.css',
                'resources/css/suneditor.css',
                'resources/css/flatpickr.css',
                'resources/css/tagify.css',
                'resources/css/app.css',
                'resources/css/custom.css',
                'resources/css/fonts.css',
                // 'resources/js/jquery.js',
                'resources/js/app.js',
                'resources/js/lang.js',
                'resources/js/toastify.js',
                'resources/js/modules/choices.js',
                'resources/js/modules/suneditor.js',
                'resources/js/modules/flatpickr.js',
                'resources/js/modules/tagify.js',
                'resources/js/defaultmenu.min.js',
            ],
            refresh: true,
            buildDirectory: 'backend',
            modulePreload: { polyfill: true }, // Tối ưu tải ES modules
        }),
    ],
    resolve: {
        alias: {
            '@fonts': path.resolve(__dirname, 'resources/webfonts'),
        },
    },
    build: {
        outDir: 'public/build/backend',
        manifest: 'manifest.json',
        minify: 'terser', // Chuyển sang terser để nén mạnh hơn
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.info', 'console.debug', 'console.log', 'console.warn'],
                passes: 6, // Tăng passes để nén sâu
                reduce_vars: true,
                pure_getters: true,
                dead_code: true,
                module: true,
                toplevel: true,
                inline: 3, // Inline hàm nhỏ
                collapse_vars: true, // Thu gọn biến
                reduce_funcs: true, // Tối ưu hóa hàm
            },
            mangle: {
                toplevel: true,
                keep_fnames: false,
                properties: { regex: /^_/ },
                reserved: ['jQuery', '$', 'Popper'],
            },
            format: {
                comments: false,
                beautify: false, // Tắt beautify để giảm kích thước
            },
        },
        cssMinify: 'esbuild',
        cssCodeSplit: true,
        emptyOutDir: true,
        sourcemap: false,
        chunkSizeWarningLimit: 350,
        reportCompressedSize: true,
        rollupOptions: {
            input: {
                main: 'resources/js/app.js',
                // jquery: 'resources/js/jquery.js',
                lang: 'resources/js/lang.js',
                defaultmenu: 'resources/js/defaultmenu.min.js',
                toastify: 'resources/js/toastify.js',
                choices: 'resources/js/modules/choices.js',
                suneditor: 'resources/js/modules/suneditor.js',
                flatpickr: 'resources/js/modules/flatpickr.js',
                tagify: 'resources/js/modules/tagify.js',
                bootstrap_css: 'resources/css/bootstrap.css',
                fontawesomepro_css: 'resources/css/fontawesomepro.css',
                toastify_css: 'resources/css/toastify.css',
                choices_css: 'resources/css/choices.css',
                suneditor_css: 'resources/css/suneditor.css',
                flatpickr_css: 'resources/css/flatpickr.css',
                tagify_css: 'resources/css/tagify.css',
                main_css: 'resources/css/app.css',
                custom_css: 'resources/css/custom.css',
                fonts_css: 'resources/css/fonts.css',
            },
            output: {
                entryFileNames: (chunkInfo) => {
                    const names = {
                        main: 'assets/app-common.[hash].js',
                        // jquery: 'assets/jquery.[hash].js',
                        lang: 'assets/lang.[hash].js',
                        defaultmenu: 'assets/defaultmenu.[hash].js',
                        toastify: 'assets/toastify.[hash].js',
                        choices: 'assets/choices.[hash].js',
                        suneditor: 'assets/suneditor.[hash].js',
                        flatpickr: 'assets/flatpickr.[hash].js',
                        tagify: 'assets/tagify.[hash].js',
                    };
                    return names[chunkInfo.name] || 'assets/[name].[hash].js';
                },
                chunkFileNames: 'assets/chunk-[name].[hash].js',
                assetFileNames: (assetInfo) => {
                    const cssNames = {
                        'bootstrap.css': 'assets/bootstrap.[hash].css',
                        'fontawesomepro.css': 'assets/fontawesomepro.[hash].css',
                        'toastify.css': 'assets/toastify.[hash].css',
                        'choices.css': 'assets/choices.[hash].css',
                        'app.css': 'assets/app-common.[hash].css',
                        'suneditor.css': 'assets/suneditor.[hash].css',
                        'flatpickr.css': 'assets/flatpickr.[hash].css',
                        'tagify.css': 'assets/tagify.[hash].css',
                        'custom.css': 'assets/custom.[hash].css',
                        'fonts.css': 'assets/fonts.[hash].css',
                    };
                    if (assetInfo.name && (assetInfo.name.endsWith('.woff2') || assetInfo.name.endsWith('.woff') || assetInfo.name.endsWith('.svg'))) {
                        return 'assets/fonts/[name].[hash].[ext]';
                    }
                    return cssNames[assetInfo.name] || 'assets/[name].[hash].[ext]';
                },
            },
        },
    },
    optimizeDeps: {
        include: ['jquery', '@popperjs/core', 'bootstrap/dist/js/bootstrap.bundle.min.js', 'toastify-js', 'defaultmenu.min'], // Pre-bundle chỉ các file dùng trong master
        exclude: ['choices.js', 'suneditor', 'flatpickr'], // Loại trừ module route cụ thể
        esbuildOptions: {
            minify: true,
            target: 'es2020',
            treeShaking: true,
            format: 'esm',
        },
    },
    esbuild: {
        minifyIdentifiers: true,
        minifySyntax: true,
        minify: true,
        keepNames: false,
    },
});