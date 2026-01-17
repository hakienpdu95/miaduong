<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseTableDataController;

class RoleController extends BaseTableDataController
{
    protected $model = \App\Models\Role::class;
    protected $columns = ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at'];
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
                '<a href="%s" class="me-2" title="Permission">' .
                '<i class="fa-light fa-shield-check"></i>' .
                '</a>' .
                '<a onclick="Livewire.dispatch(\'deleteRole\', [%d])" class="" title="Delete">' .
                '<i class="fa-light fa-trash"></i>' .
                '</a>',
                route('role.edit', $item->id),
                route('role.permissions', $item->id),
                $item->id
            );
        };
    }
}