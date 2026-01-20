<?php
return [
    'category' => 'system_management', 
    'category_label' => 'System Management',
    'label' => 'maintenance_type_management',
    'icon' => '<i class="fa-light fa-screwdriver-wrench me-2"></i>',
    'children' => [
        ['name' => 'maintenance-type.index', 'label' => 'maintenance_type_management.index'],
        ['name' => 'maintenance-type.create', 'label' => 'maintenance_type_management.create'],
    ],
];