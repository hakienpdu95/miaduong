<?php
return [
    'roles' => [
        'administrator' => [
            'user_management' => [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
                'can_export' => true,
                'can_view_report' => true,
                'can_export_report' => true,
                'can_assign_permission' => true,
                'can_approve' => true,
                'can_reject' => true,
            ],
            'role_management' => [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
                'can_assign_permission' => true,
            ],
            // Thêm các module khác nếu cần
        ],
        'province' => [
            'user_management' => [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => false,
                'can_export' => true,
                'can_view_report' => true,
                'can_export_report' => false,
                'can_assign_permission' => false,
                'can_approve' => true,
                'can_reject' => true,
            ],
            // 'enterprise_management' => [
            //     'can_view' => true,
            //     'can_create' => true,
            //     'can_edit' => true,
            //     'can_delete' => false,
            // ],
        ],
        // 'commune' => [
        //     'enterprise_management' => [
        //         'can_view' => true,
        //         'can_create' => false,
        //         'can_edit' => false,
        //         'can_delete' => false,
        //     ],
        // ],
        // 'enterprise' => [
        //     'employee_management' => [
        //         'can_view' => true,
        //         'can_create' => true,
        //         'can_edit' => true,
        //         'can_delete' => true,
        //     ],
        // ],
        // 'editor' => [
        //     'content_management' => [
        //         'can_view' => true,
        //         'can_create' => true,
        //         'can_edit' => true,
        //         'can_delete' => false,
        //     ],
        // ],
        // 'subscriber' => [
        //     'content_management' => [
        //         'can_view' => true,
        //         'can_create' => false,
        //         'can_edit' => false,
        //         'can_delete' => false,
        //     ],
        // ],
        // Thêm các vai trò khác: content_manager, shop_manager, v.v.
    ],
];