<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseTableDataController;

class StandardController extends BaseTableDataController
{
    protected $model = \App\Models\Standard::class;
    protected $columns = ['id', 'code', 'name', 'description', 'is_active', 'created_at', 'updated_at'];
    protected $searchFields = ['name'];
    protected $actionsCallback;
    protected $formatters;

    public function __construct()
    {
        $this->formatters = [];

        $this->actionsCallback = function ($item) {
            return sprintf(
                '<a href="%s" class="me-2" title="Edit">' .
                '<i class="fa-light fa-pen-to-square"></i>' .
                '</a>' .
                '<a onclick="Livewire.dispatch(\'deleteRole\', [%d])" class="" title="Delete">' .
                '<i class="fa-light fa-trash"></i>' .
                '</a>',
                route('standard.edit', $item->id),
                $item->id
            );
        };
    }
}