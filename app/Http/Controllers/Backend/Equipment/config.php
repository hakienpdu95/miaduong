<?php

return [
    'category' => 'system_management',
    'category_label' => 'System Management',
    'label' => 'equipment_management',
    'icon' => '<i class="fa-light fa-shredder me-2"></i>',
    'children' => [
        ['name' => 'equipment.index', 'label' => 'Danh sách Thiết bị'],
        ['name' => 'equipment.create', 'label' => 'Tạo Thiết bị'],
    ],
    'visible' => true,
    'submodules' => [ // Định nghĩa submodules với full name làm key
        'maintenance_log_management' => [
            'label' => 'Quản lý Nhật ký Bảo dưỡng',
            'icon' => '<i class="fa-light fa-tools me-2"></i>', // Tùy chọn
            'children' => [ // Children cho sub nếu cần
                ['name' => 'maintenance-log.index', 'label' => 'Danh sách Nhật ký'],
                ['name' => 'maintenance-log.create', 'label' => 'Tạo Nhật ký'],
            ],
            'visible' => false,
        ],
        // Thêm sub khác nếu có, e.g., 'another_sub_management' => [...]
    ],
];