<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseTableDataController;
use Illuminate\Support\Facades\Log;

class EventController extends BaseTableDataController
{
    protected $model = \App\Models\Event::class;
    protected $columns = ['id', 'title', 'poster_image', 'status', 'created_at', 'updated_at'];
    protected $searchFields = ['title'];
    protected $customFilters;
    protected $actionsCallback;
    protected $formatters;

    public function __construct()
    {
        $this->customFilters = [
            'title' => function ($query, $value) {
                return $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($value) . '%']);
            },
            'status' => function ($query, $value) {
                if (in_array($value, ['publish', 'draft', 'pending', 'trash'])) {
                    return $query->where('status', $value);
                }
                return $query;
            },
            'categories' => function ($query, $value) {
                return $query->whereHas('categories', function ($q) use ($value) {
                    $q->where('event_categories.id', $value);
                });
            }
        ];

        $this->customQueryCallback = function ($query) {
            return $query->with(['categories' => function ($q) {
                $q->select('event_categories.id', 'event_categories.name');
            }])->orderBy('id', 'desc');
        };

        $this->formatters = [
            'title' => function ($item) {
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
                    htmlspecialchars($item->title),
                    htmlspecialchars($code),
                    htmlspecialchars($product),
                    date('d/m/Y h:i:s A', strtotime($item->created_at)),
                    date('d/m/Y h:i:s A', strtotime($item->updated_at)),
                    $totalStamps
                );
            },
            'status' => function ($item) {
                $statusMap = [
                    'publish' => '<span class="text-success">Công khai</span>',
                    'draft' => '<span class="text-warning">Nháp</span>',
                    'pending' => '<span class="text-info">Chờ duyệt</span>',
                    'trash' => '<span class="text-danger">Đã xóa</span>'
                ];
                return $statusMap[$item->status] ?? $item->status;
            },
            'categories' => function ($item) {
                return $item->categories->pluck('name')->implode(', ') ?: 'Không có danh mục';
            }
        ];

        $this->actionsCallback = function ($item) {
            return sprintf(
                '<div class="text-center">' .
                '<a href="%s" class="me-2" title="Edit">' .
                '<i class="fa-light fa-pen-to-square"></i>' .
                '</a>' .
                '<a onclick="Livewire.dispatch(\'deleteEvent\', [%d])" class="cursor-pointer" title="Delete">' .
                '<i class="fa-light fa-trash"></i>' .
                '</a>' .
                '</div>',
                route('event.edit', $item->id),
                $item->id
            );
        };
    }
}