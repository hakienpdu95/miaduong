<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name', 'description', 'priority', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // Mối quan hệ với users
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    // Mối quan hệ với role_permissions
    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    // Kiểm tra quyền của vai trò
    public function hasPermission(string $module, string $action): bool
    {
        return $this->permissions()
            ->where('module_name', $module)
            ->where("can_{$action}", true)
            ->exists();
    }
}