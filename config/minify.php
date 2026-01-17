<?php
return [
    'enabled' => env('MINIFY_ENABLED', true),  // Tắt nếu dev: false
    'html' => [
        'enabled' => true,
        'comments' => false,  // Loại bỏ comments để giảm size
    ],
    'css' => [
        'enabled' => true,
        'semicolon' => env('MINIFY_CSS_SEMICOLON', true),  // Tự insert ; cho CSS
    ],
    'js' => [
        'enabled' => true,
        'semicolon' => env('MINIFY_JS_SEMICOLON', true),   // Tự insert ; cho JS (experimental, test với Livewire)
    ],
    'skip_ld_json' => env('MINIFY_SKIP_LD_JSON', true),  // Quan trọng: Skip minify JsonLd (bạn có {!! JsonLd::generate() !!})
    'ignore' => [  // Routes skip minify (e.g., dynamic Livewire)
        '/admin',  // Ignore tất cả admin routes
        '/register',
        '/login',
    ],
    'directives' => [  // Giữ directives cho Livewire (thay @ bằng x-on: nếu minify break)
        '@' => 'x-on:',
        '@livewireStyles' => '@livewireStyles',  // Giữ nguyên
        '@livewireScripts' => '@livewireScripts',
    ],
    'keep_directives' => [  // Bảo vệ các directive không minify
        '@province',
        '@vite',
        '@stack',
        '@yield',
        '@livewire*'
    ],
];