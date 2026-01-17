<?php

// Ví dụ file config.php cho module Role (app/Http/Controllers/Backend/Role/config.php)
// Cấu trúc này tốt hơn vì config nằm trong folder module, dễ đồng bộ với code/views.

return [
    'category' => 'system_management',
    'category_label' => 'System Management', // Thêm để tùy chỉnh label category
    'label' => 'role_management',
    'icon' => '<i class="fa-light fa-user-shield me-2"></i>',
    'children' => [
        ['name' => 'role.index', 'label' => 'role_management.index'],
        ['name' => 'role.create', 'label' => 'role_management.create'],
    ],
];