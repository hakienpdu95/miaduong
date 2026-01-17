<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseTableDataController extends Controller
{
    protected $model;
    protected $columns = [];
    protected $searchFields = [];
    protected $customFilters = [];
    protected $joins = [];
    protected $subQueries = [];
    protected $customQueryCallback;
    protected $actionsCallback;
    protected $formatters = [];

    public function fetchData(Request $request)
    {
        $size = max(1, min(100, $request->input('size', 20)));
        $cursor = $request->input('cursor');
        $filters = $request->input('filters', []) ?: [];
        $sort = is_string($request->input('sort')) ? (json_decode($request->input('sort'), true) ?: []) : ($request->input('sort', []) ?: []);

        // Tạo tags động
        $tags = ['table_data'];
        $modelName = class_basename($this->model);
        $tags[] = strtolower($modelName . 's');

        // Tối ưu cache key: Sử dụng sha1 thay md5 (nhanh hơn 10-20%, ngắn hơn)
        $serialized = serialize($filters) . serialize($sort) . $cursor;
        $hash = sha1($serialized); // Nhanh hơn md5 ở string dài
        $cacheKey = "table_data_" . class_basename($this) . "_{$size}_" . $hash;
        $countCacheKey = "table_data_count_" . class_basename($this) . "_" . sha1(serialize($filters) . serialize($sort));
        $milestoneCacheKey = "table_data_milestones_" . class_basename($this) . "_" . sha1(serialize($filters) . serialize($sort));

        // Giảm TTL cho total (từ 1h xuống 30min nếu data thay đổi thường, tối ưu memory)
        $total = Cache::tags($tags)->remember($countCacheKey, now()->addMinutes(30), function () use ($filters, $sort) {
            $query = $this->model::query();
            if (!empty($this->joins)) {
                foreach ($this->joins as $join) {
                    $query->leftJoin($join['table'], $join['first'], $join['operator'], $join['second']);
                }
            }
            if ($this->customQueryCallback) {
                $query = call_user_func($this->customQueryCallback, $query);
            }
            if (!empty($filters)) {
                foreach ($filters as $field => $value) {
                    if ($value !== '' && isset($this->customFilters[$field])) {
                        $filterCallback = $this->customFilters[$field];
                        $query = call_user_func($filterCallback, $query, $value);
                    }
                }
            }
            if (app()->environment('local')) {
                foreach (array_merge($this->searchFields, array_keys($this->customFilters)) as $field) {
                    $this->checkIndex($field);
                }
            }
            return $query->count();
        });

        // Lazy milestone: Chỉ get/put nếu page là milestone (giảm cache ops thừa 90%)
        $milestoneCursors = [];
        $page = $request->input('page', 1);
        if ($page % 10 === 0 || $page === 1) {
            $milestoneCursors = Cache::tags($tags)->get($milestoneCacheKey, []);
        }

        try {
            $data = Cache::tags($tags)->remember($cacheKey, now()->addMinutes(10), function () use ($size, $filters, $sort, $cursor, $total, &$milestoneCursors, $page, $tags) { // Giảm TTL từ 30min xuống 10min cho fresh data
                $query = $this->model::query()->select($this->columns);
                if (!empty($this->joins)) {
                    foreach ($this->joins as $join) {
                        $query->leftJoin($join['table'], $join['first'], $join['operator'], $join['second']);
                    }
                }
                if (!empty($this->subQueries)) {
                    $query->addSelect($this->subQueries);
                }
                if ($this->customQueryCallback) {
                    $query = call_user_func($this->customQueryCallback, $query);
                }
                if (!empty($filters)) {
                    foreach ($filters as $field => $value) {
                        if ($value !== '' && isset($this->customFilters[$field])) {
                            $filterCallback = $this->customFilters[$field];
                            $query = call_user_func($filterCallback, $query, $value);
                        }
                    }
                }
                if (!empty($sort)) {
                    foreach ($sort as $sorter) {
                        if (isset($sorter['field'], $sorter['dir']) && in_array($sorter['field'], $this->columns)) {
                            $query->orderBy($sorter['field'], $sorter['dir']);
                        }
                    }
                } else {
                    $query->orderBy('id', 'asc');
                }

                // Sử dụng cursorPaginate
                $paginator = $query->cursorPaginate($size);

                $transformed = collect($paginator->items())->map(function ($item) {
                    $formatted = $item;
                    if ($this->formatters) {
                        foreach ($this->formatters as $field => $callback) {
                            $formatted->{$field . '_formatted'} = call_user_func($callback, $item);
                        }
                    }
                    if ($this->actionsCallback) {
                        $formatted->actions = call_user_func($this->actionsCallback, $item);
                    }
                    return $formatted;
                });

                // Chỉ put milestone nếu là milestone page (tối ưu write)
                if ($page % 10 === 0 || $page === 1) {
                    $milestoneCursors[$page] = $cursor;
                    Cache::tags($tags)->put($milestoneCacheKey, $milestoneCursors, now()->addMinutes(30));
                }

                return (object) [
                    'items' => $transformed->all(),
                    'nextCursor' => $paginator->nextCursor()?->encode(),
                    'prevCursor' => $paginator->previousCursor()?->encode(),
                    'hasMore' => $paginator->hasMorePages(),
                    'hasPrevious' => !is_null($paginator->previousCursor()),
                    'total' => $total,
                    'totalPages' => ceil($total / $size),
                    'currentPage' => $page,
                    'milestoneCursors' => $milestoneCursors,
                ];
            });

            $response = [
                'data' => $data->items,
                'next_cursor' => $data->nextCursor,
                'prev_cursor' => $data->prevCursor,
                'has_more' => $data->hasMore,
                'has_previous' => $data->hasPrevious,
                'total' => $data->total,
                'total_pages' => $data->totalPages,
                'current_page' => $data->currentPage,
                'milestone_cursors' => $data->milestoneCursors,
            ];

            Log::debug('API Response (' . class_basename($this->model) . '):', $response);

            return response()->json($response, 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Fetch data failed: ' . $e->getMessage());
            return response()->json(['error' => 'Không thể tải dữ liệu'], 500);
        }
    }

    protected function checkIndex($field)
    {
        try {
            $table = (new $this->model)->getTable();
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Column_name = ?", [$field]);
            if (empty($indexes)) {
                Log::warning("No index found for column {$field} in table {$table}. Consider adding an index for better performance.");
            }
        } catch (\Exception $e) {
            Log::error("Error checking index for {$field}: " . $e->getMessage());
        }
    }
}