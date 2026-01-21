<?php
return [
    'roles' => [
        'administrator' => [
            'user_management' => [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ],
            'role_management' => [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
            ],
            // Thêm các module khác nếu cần
        ],
        'warehouse_management' => [
            'user_management' => [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
            ],
            'equipment_management' => [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ],
        ],
        // 'commune' => [
        //     'enterprise_management' => [
        //         'can_view' => true,
        //         'can_create' => false,
        //         'can_edit' => false,
        //         'can_delete' => false,
        //     ],
        // ],
        // Thêm các vai trò khác: content_manager, shop_manager, v.v.
    ],
];