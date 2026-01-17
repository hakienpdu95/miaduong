<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseTableDataController;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseTableDataController
{
    protected $model = \App\Models\Category::class;
    protected $columns = ['id', 'name', 'is_menu', 'created_at', 'depth'];
    protected $searchFields = ['name'];
    protected $customFilters;
    protected $actionsCallback;
    protected $formatters;

    public function __construct()
    {
        $this->customFilters = [
            'name' => function ($query, $value) {
                return $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($value) . '%']);
            },
            'is_menu' => function ($query, $value) {
                if (in_array($value, ['0', '1'])) {
                    return $query->where('is_menu', $value);
                }
                return $query;
            }
        ];

        $this->customQueryCallback = function ($query) {
            return $query->orderBy('id', 'asc'); // Đảm bảo orderBy id cho cursor pagination
        };

        $this->formatters = [
            'name' => function ($item) {
                $prefix = str_repeat('— ', $item->depth);
                return $prefix . htmlspecialchars($item->name);
            },
            'is_menu' => function ($item) {
                return $item->is_menu ? '<span class="text-success">Hiển thị</span>' : '<span class="text-danger">Ẩn</span>';
            },
            'created_at' => function ($item) {
                return date('d/m/Y H:i:s', strtotime($item->created_at));
            }
        ];

        $this->actionsCallback = function ($item) {
            return sprintf(
                '<div class="text-center">' .
                '<a href="%s" class="me-2" title="Edit">' .
                '<i class="fa-light fa-pen-to-square"></i>' .
                '</a>' .
                '<a onclick="Livewire.dispatch(\'deleteCategory\', [%d])" class="cursor-pointer" title="Delete">' .
                '<i class="fa-light fa-trash"></i>' .
                '</a>' .
                '</div>',
                route('category.edit', $item->id),
                $item->id
            );
        };
    }
}