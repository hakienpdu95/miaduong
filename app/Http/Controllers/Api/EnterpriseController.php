<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseTableDataController;
use Illuminate\Support\Facades\Log;

class EnterpriseController extends BaseTableDataController
{
    protected $model = \App\Models\Enterprise::class;
    protected $columns = ['id', 'name', 'code', 'is_active', 'created_at'];
    protected $searchFields = ['name', 'code'];
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
                    '<strong class="text-uppercase">%s <span class="badge text-bg-success fw-medium">Đã phê duyệt</span></strong><br>' .
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
                $value = (int) $item->is_active;
                Log::debug('is_active formatter:', ['raw' => $item->is_active, 'formatted' => $value]);
                return $value; // Force 0 or 1
            }
        ];

        $this->actionsCallback = function ($item) {
            return sprintf(
                '<div class="text-center">' .
                '<a href="%s" class="me-2" title="Status">' .
                '<i class="fa-light fa-check"></i>' .
                '</a>' .
                '<a href="%s" class="me-2" title="Edit">' .
                '<i class="fa-light fa-pen-to-square"></i>' .
                '</a>' .
                '<a onclick="Livewire.dispatch(\'deleteEnterprise\', [%d])" class="cursor-pointer" title="Delete">' .
                '<i class="fa-light fa-trash"></i>' .
                '</a>' .
                '</div>',
                route('enterprise.edit', $item->id),
                route('enterprise.edit', $item->id),
                $item->id
            );
        };
    }
}