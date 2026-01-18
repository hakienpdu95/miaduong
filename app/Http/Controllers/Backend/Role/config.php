<?php
return [
    'category' => 'system_management', // Chủ đề group (cùng key sẽ group chung)
    'category_label' => 'System Management', // Label cho category (tùy chọn, override default)
    'label' => 'role_management',
    'icon' => '<i class="fa-light fa-user-shield me-2"></i>',
    'children' => [
        ['name' => 'role.index', 'label' => 'role_management.index'],
        ['name' => 'role.create', 'label' => 'role_management.create'],
    ],
];