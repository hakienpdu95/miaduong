<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseTableDataController;
use Illuminate\Support\Facades\Log;

class ProductController extends BaseTableDataController
{
    protected $model = \App\Models\Product::class;
    protected $columns = ['id', 'name', 'status', 'created_at'];
    protected $searchFields = ['name'];
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
                    '%s<br>' .
                    'Sản phẩm: %s<br>' .
                    'Đăng bởi: vào lúc %s<br>' .
                    'Sửa bởi: vào lúc %s<br>' .
                    'Tổng số %d tem' .
                    '</div>',
                    htmlspecialchars($item->name),
                    htmlspecialchars($code),
                    htmlspecialchars($product),
                    date('d/m/Y h:i:s A', strtotime($item->created_at)),
                    date('d/m/Y h:i:s A', strtotime($item->updated_at)),
                    $totalStamps
                );
            },
            'is_active' => function ($item) {
                return '-'; // Force 0 or 1
            }
        ];

        $this->actionsCallback = function ($item) {
            return sprintf(
                '<div class="text-center">' .
                '<a href="%s" class="me-2" title="Edit">' .
                '<i class="fa-light fa-pen-to-square"></i>' .
                '</a>' .
                '<a onclick="Livewire.dispatch(\'deletePost\', [%d])" class="cursor-pointer" title="Delete">' .
                '<i class="fa-light fa-trash"></i>' .
                '</a>' .
                '</div>',
                route('product.edit', $item->id),
                $item->id
            );
        };
    }
}