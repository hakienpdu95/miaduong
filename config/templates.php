<?php

return [
    'default' => 'livewire.frontend.templates.blog-default',
    'item_types' => [
        'basic' => 'livewire.frontend.templates.blog-standard',
        'redirect' => 'livewire.frontend.templates.blog-redirect',
        'review' => 'livewire.frontend.templates.blog-review',
        'school' => 'livewire.frontend.templates.blog-school',
        'quote' => 'livewire.frontend.templates.blog-quote',
    ],
    'layouts' => [  
        'post' => 'layouts.app',
        'page' => 'layouts.page',  
        'custom' => 'layouts.custom',
    ],
    'valid_templates' => [
        'livewire.frontend.templates.blog-standard',
        'livewire.frontend.templates.blog-default',
        'livewire.frontend.templates.blog-redirect',
        'livewire.frontend.templates.blog-review',
        'livewire.frontend.templates.blog-school',
        'livewire.frontend.templates.blog-quote',
        'livewire.frontend.templates.full-width',
        'livewire.frontend.pages.enterprise',
        'livewire.frontend.pages.map',
    ],
];