<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseTableDataController;

class UserController extends BaseTableDataController
{
    protected $model = \App\Models\User::class;
    protected $columns = ['id', 'name', 'email', 'username', 'is_active', 'created_at', 'updated_at'];
    protected $searchFields = ['name', 'email', 'username'];
    protected $actionsCallback;
    protected $formatters;

    public function __construct()
    {
        $this->formatters = [
            'name' => function ($item) {
                $product = 'Lợn Thịт';
                $code = "Lon Thit-" . date('Y.m.d-H:i:s', strtotime($item->created_at)) . "-0";
                $totalStamps = 200;
                return sprintf(
                    '<div class="user-info">' .
                    '<strong>%s</strong><br>' .
                    '%s<br>' .
                    'Sản phẩm: %s<br>' .
                    'Đăng bởi: %s vào lúc %s<br>' .
                    'Sửa bởi: %s vào lúc %s<br>' .
                    'Tổng số %d tem' .
                    '</div>',
                    htmlspecialchars($item->name),
                    htmlspecialchars($code),
                    htmlspecialchars($product),
                    htmlspecialchars($item->username),
                    date('d/m/Y h:i:s A', strtotime($item->created_at)),
                    htmlspecialchars($item->username),
                    date('d/m/Y h:i:s A', strtotime($item->updated_at)),
                    $totalStamps
                );
            }
        ];

        $this->actionsCallback = function ($item) {
            return sprintf(
                '<div class="text-center">' .
                '<a href="%s" class="me-2" title="Edit">' .
                '<i class="fa-light fa-pen-to-square"></i>' .
                '</a>' .
                '<a href="%s" class="me-2" title="Permission">' .
                '<i class="fa-light fa-shield-check"></i>' .
                '</a>' .
                '<a onclick="Livewire.dispatch(\'deleteUser\', [%d])" class="cursor-pointer" title="Delete">' .
                '<i class="fa-light fa-trash"></i>' .
                '</a>' .
                '</div>',
                route('user.edit', $item->id),
                route('user.permissions', $item->id),
                $item->id
            );
        };
    }
}