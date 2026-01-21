<?php
return [
    'category' => 'system_management',
    'category_label' => 'System Management',
    'label' => 'unit_management',
    'icon' => '<i class="fa-light fa-box me-2"></i>',
    'children' => [
        ['name' => 'unit.index', 'label' => 'unit_management.index'],
        ['name' => 'unit.create', 'label' => 'unit_management.create'],
    ],
    'visible' => true,
];