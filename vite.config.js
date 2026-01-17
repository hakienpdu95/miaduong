import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/css/ag-grid.css',
        'resources/css/custom.css',
        'resources/js/app.js',
        'resources/js/custom.js',
        'resources/js/modules/ag-grid.js',
      ],
      refresh: true,
    }),
  ],
  build: {
    outDir: 'public/build',
    rollupOptions: {
      input: {
        main: 'resources/js/app.js',
        main_css: 'resources/css/app.css',
        grid_css: 'resources/css/ag-grid.css',
        custom: 'resources/js/custom.js',
        custom_css: 'resources/css/custom.css',
        ag_grid: 'resources/js/modules/ag-grid.js',
      },
      output: {
        entryFileNames: (chunkInfo) => {
          if (chunkInfo.name === 'main') return 'assets/app-common.[hash].js';
          if (chunkInfo.name === 'custom') return 'assets/custom.[hash].js';
          if (chunkInfo.name === 'ag_grid') return 'assets/ag-grid.[hash].js';
          return 'assets/[name].[hash].js';
        },
        chunkFileNames: 'assets/chunk-[name].[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'custom.css') return 'assets/custom.[hash].css';
          if (assetInfo.name === 'ag-grid.css') return 'assets/grid.[hash].css';
          if (assetInfo.name && assetInfo.name.endsWith('.css')) return 'assets/app-common.[hash].css';
          return 'assets/app-common.[ext]';
        },
        manualChunks: (id) => {
          if (id.includes('ag-grid-community')) {
            if (id.includes('infinite-row-model')) return 'ag-grid-infinite';
            return 'ag-grid-core';
          }
          if (id.includes('choices.js')) return 'choices';
          if (id.includes('bootstrap')) return 'bootstrap';
        },
      },
    },
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
        pure_funcs: ['console.info', 'console.debug', 'console.log'],
        passes: 3,
        reduce_vars: true,
        pure_getters: true,
        dead_code: true,
        module: true,
        toplevel: true,
      },
      mangle: {
        toplevel: true,
        properties: {
          regex: /^_/,
        },
      },
      format: {
        comments: false,
      },
    },
    cssMinify: 'esbuild',
    cssCodeSplit: true,
    emptyOutDir: true,
    sourcemap: true,
    chunkSizeWarningLimit: 350,
    reportCompressedSize: true,
    optimizeDeps: {
      include: [],
    },
  },
});