<?php
return [
    'category' => 'system_management', 
    'category_label' => 'System Management',
    'label' => 'equipment_management',
    'icon' => '<i class="fa-light fa-shredder me-2"></i>',
    'children' => [
        ['name' => 'equipment.index', 'label' => 'equipment_management.index'],
        ['name' => 'equipment.create', 'label' => 'equipment_management.create'],
    ],
];