<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseTableDataController;
use Illuminate\Support\Facades\Log;

class PostController extends BaseTableDataController
{
    protected $model = \App\Models\Post::class;
    protected $columns = ['id', 'title', 'image', 'is_active', 'created_at'];
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
            'is_active' => function ($query, $value) {
                if (in_array($value, ['0', '1'])) {
                    return $query->where('is_active', $value);
                }
                return $query;
            },
            'categories' => function ($query, $value) {
                return $query->whereHas('categories', function ($q) use ($value) {
                    $q->where('categories.id', $value);
                });
            }
        ];

        $this->customQueryCallback = function ($query) {
            return $query->where('item_type', 'basic')->with('categories')->orderBy('id', 'desc');
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
            'is_active' => function ($item) {
                return $item->is_active ? '<span class="text-success">Hoạt động</span>' : '<span class="text-danger">Không hoạt động</span>';
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
                '<a onclick="Livewire.dispatch(\'deletePost\', [%d])" class="cursor-pointer" title="Delete">' .
                '<i class="fa-light fa-trash"></i>' .
                '</a>' .
                '</div>',
                route('post.edit', $item->id),
                $item->id
            );
        };
    }
}