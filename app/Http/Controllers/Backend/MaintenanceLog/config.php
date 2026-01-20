<?php
return [
    'category' => 'system_management', 
    'category_label' => 'System Management',
    'label' => 'maintenance_log_management',
    'icon' => '<i class="fa-light fa-rectangle-history-circle-user me-2"></i>',
    'children' => [
        ['name' => 'maintenance-log.index', 'label' => 'maintenance_log_management.index'],
        ['name' => 'maintenance-log.create', 'label' => 'maintenance_log_management.create'],
    ],
];