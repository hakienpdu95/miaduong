import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    base: process.env.NODE_ENV === 'production' ? '/build/frontend/' : '/',
    plugins: [
        laravel({
            input: [
                'resources/js/jquery.js',
                'resources/js/lazysizes.js',
                'resources/js/modules/choices.js',
                'resources/js/modules/glide.js',
                'resources/js/web/main.js',
                'resources/js/mobile/main.js',
                'resources/css/bootstrap.css',
                'resources/css/choices.css',
                'resources/css/glide.css',
                'resources/css/web/main.css',
                'resources/css/mobile/main.css',
                'resources/css/fonts.css',
            ],
            refresh: true,
            buildDirectory: 'frontend',
            modulePreload: { polyfill: true },
        }),
    ],
    resolve: {
        alias: {
            '@fonts': path.resolve(__dirname, 'resources/webfonts'),
        },
        extensions: ['.js', '.mjs', '.esm.js'],
    },
    build: {
        outDir: 'public/build/frontend',
        manifest: 'manifest.json',
        rollupOptions: {
            input: {
                jquery_js: 'resources/js/jquery.js',
                lazysizes_js: 'resources/js/lazysizes.js',
                choices_js: 'resources/js/modules/choices.js',
                glide_js: 'resources/js/modules/glide.js',
                web_main_js: 'resources/js/web/main.js',
                mobile_main_js: 'resources/js/mobile/main.js',
                bootstrap_css: 'resources/css/bootstrap.css',
                choices_css: 'resources/css/choices.css',
                glide_css: 'resources/css/glide.css',
                web_main_css: 'resources/css/web/main.css',
                mobile_main_css: 'resources/css/mobile/main.css',
                fonts_css: 'resources/css/fonts.css',
            },
            output: {
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name === 'jquery_js') return 'assets/jquery.[hash].js';
                    if (chunkInfo.name === 'lazysizes_js') return 'assets/lazysizes.[hash].js';
                    if (chunkInfo.name === 'choices_js') return 'assets/choices.[hash].js';
                    if (chunkInfo.name === 'glide_js') return 'assets/glide.[hash].js';
                    if (chunkInfo.name === 'web_main_js') return 'assets/web/main.[hash].js';
                    if (chunkInfo.name === 'mobile_main_js') return 'assets/mobile/main.[hash].js';
                    return 'assets/[name].[hash].js';
                },
                chunkFileNames: 'assets/chunk-[name].[hash].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        if (assetInfo.name === 'bootstrap_css') return 'assets/bootstrap.[hash].css';
                        if (assetInfo.name === 'choices_css') return 'assets/choices.[hash].css';
                        if (assetInfo.name === 'glide_css') return 'assets/glide.[hash].css';
                        if (assetInfo.name === 'web_main_css') return 'assets/web/main.[hash].css';
                        if (assetInfo.name === 'mobile_main_css') return 'assets/mobile/main.[hash].css';
                        if (assetInfo.name === 'fonts_css') return 'assets/fonts.[hash].css';
                    }
                    if (assetInfo.name && (assetInfo.name.endsWith('.woff2') || assetInfo.name.endsWith('.woff') || assetInfo.name.endsWith('.svg'))) {
                        return 'assets/fonts/[name].[hash].[ext]';  // Hash fonts files
                    }
                    return 'assets/[name].[hash].[ext]';
                },
            },
        },
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: false,
                // drop_console: false,
                drop_debugger: false,
                // pure_funcs: ['console.info', 'console.debug', 'console.log', 'console.warn'],
                pure_funcs: [],
                passes: 6,
                reduce_vars: true,
                pure_getters: true,
                dead_code: true,
                module: true,
                toplevel: true,
                inline: 3,
                collapse_vars: true,
                reduce_funcs: true,
            },
            mangle: {
                toplevel: true,
                keep_fnames: false,
                properties: { regex: /^_/ },
            },
            format: {
                comments: false,
                beautify: false,
            },
        },
        cssMinify: 'esbuild',
        cssCodeSplit: true,
        emptyOutDir: true,
        sourcemap: false,
        chunkSizeWarningLimit: 100,
        reportCompressedSize: true,
    },
    optimizeDeps: {
        include: ['jquery', 'lazysizes'],
        exclude: ['choices.js', 'glide.js'],
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
    server: {
        hmr: true,
    }
});