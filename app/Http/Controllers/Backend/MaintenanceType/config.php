<?php
return [
    'category' => 'system_management', 
    'category_label' => 'System Management',
    'label' => 'maintenance_type_management',
    'icon' => '<i class="fa-light fa-user-shield me-2"></i>',
    'children' => [
        ['name' => 'maintenance_type.index', 'label' => 'maintenance_type_management.index'],
        ['name' => 'maintenance_type.create', 'label' => 'maintenance_type_management.create'],
    ],
];