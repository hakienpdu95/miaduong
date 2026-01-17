<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    protected $fillable = [
        'role_id', 'module_name', 'can_view', 'can_create', 'can_edit',
        'can_delete', 'can_export', 'can_view_report', 'can_export_report',
        'can_assign_permission', 'can_approve', 'can_reject'
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_export' => 'boolean',
        'can_view_report' => 'boolean',
        'can_export_report' => 'boolean',
        'can_assign_permission' => 'boolean',
        'can_approve' => 'boolean',
        'can_reject' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}