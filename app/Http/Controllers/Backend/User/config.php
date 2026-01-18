<?php
return [
    'category' => 'system_management', // Group chung với Role
    'label' => 'user_management',
    'icon' => '<i class="fa-light fa-users me-2"></i>',
    'children' => [
        ['name' => 'user.index', 'label' => 'user_management.index'],
        ['name' => 'user.create', 'label' => 'user_management.create'],
    ],
];