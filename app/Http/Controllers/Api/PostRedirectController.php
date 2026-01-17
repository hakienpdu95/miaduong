<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseTableDataController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostRedirectController extends BaseTableDataController
{
    protected $model = \App\Models\Post::class;
    protected $columns = [
        'posts.id',
        'posts.title',
        'posts.image',
        'posts.is_active',
        'posts.created_at',
        'publishers.name as publisher_name', // Từ join
    ];
    protected $searchFields = ['title'];
    protected $customFilters;
    protected $actionsCallback;
    protected $formatters;
    protected $joins = [
        [
            'table' => 'post_redirects',
            'first' => 'posts.reference_id',
            'operator' => '=',
            'second' => 'post_redirects.id',
        ],
        [
            'table' => 'publishers',
            'first' => 'post_redirects.publisher_id',
            'operator' => '=',
            'second' => 'publishers.id',
        ],
    ];
    protected $subQueries = [];

    public function __construct()
    {
        $this->subQueries = [
            DB::raw('(SELECT COUNT(*) FROM post_tag WHERE post_tag.post_id = posts.id) as tag_count'),
        ];

        $this->customFilters = [
            'title' => function ($query, $value) {
                return $query->whereRaw('LOWER(posts.title) LIKE ?', ['%' . strtolower($value) . '%']);
            },
            'is_active' => function ($query, $value) {
                if (in_array($value, ['0', '1'])) {
                    return $query->where('posts.is_active', $value);
                }
                return $query;
            },
            'categories' => function ($query, $value) {
                return $query->whereHas('categories', function ($q) use ($value) {
                    $q->where('categories.id', $value);
                });
            },
            'publisher_id' => function ($query, $value) {
                return $query->where('post_redirects.publisher_id', $value);
            },
            'published_recently' => function ($query, $value) {
                if ($value === 'yes') {
                    return $query->where('posts.published_at', '>', now()->subDays(7));
                }
                return $query;
            },
        ];

        $this->customQueryCallback = function ($query) {
            return $query
                ->where('posts.item_type', 'redirect')
                ->where('posts.status', 'publish') // Điều kiện thêm
                ->with('categories')
                ->orderBy('posts.id', 'desc');
        };

        $this->formatters = [
            'title' => function ($item) {
                return sprintf(
                    '<div class="user-info">' .
                    '%s<br>' .
                    '- Nhà xuất bản: %s<br>' .
                    '- Đăng bởi: vào lúc %s<br>' .
                    '- Sửa bởi: vào lúc %s<br>' .
                    '- Số tag: %d' .
                    '</div>',
                    htmlspecialchars($item->title),
                    htmlspecialchars($item->publisher_name),
                    date('d/m/Y h:i:s A', strtotime($item->created_at)),
                    date('d/m/Y h:i:s A', strtotime($item->updated_at)),
                    $item->tag_count
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
                route('redirect-posts.edit', $item->id),
                $item->id
            );
        };
    }
}