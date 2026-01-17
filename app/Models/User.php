<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasPermissions;
use App\Constants\ModuleConst;

class User extends Authenticatable
{
    use HasPermissions, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'password',
        'email_verified_at',
        'managed_by',
        'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Mối quan hệ với roles (nhiều-nhiều)
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    // Mối quan hệ với permissions
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    // Mối quan hệ với managed users
    public function managedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'managed_by');
    }

    // Mối quan hệ với profile
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function enterprise()
    {
        return $this->hasOneThrough(Enterprise::class, UserProfile::class, 'user_id', 'id', 'id', 'enterprise_id');
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }
    
    // Kiểm tra vai trò
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // Kiểm tra nhiều vai trò
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    // Lấy tất cả quyền của người dùng
    public function getAllPermissions(): array
    {
        $permissions = [];

        // Quyền cá nhân
        $personalPermissions = $this->permissions()
            ->where(function ($query) {
                $query->whereNull('expired_at')
                      ->orWhere('expired_at', '>', now());
            })
            ->get()
            ->map(function ($permission) {
                $perms = [];
                foreach (ModuleConst::getActions() as $action) {
                    if ($permission->{"can_$action"}) {
                        $perms[$action] = true;
                    }
                }
                return [
                    'module_name' => $permission->module_name,
                    'actions' => $perms,
                ];
            });

        foreach ($personalPermissions as $perm) {
            $permissions[$perm['module_name']] = array_merge(
                $permissions[$perm['module_name']] ?? [],
                $perm['actions']
            );
        }

        // Quyền từ vai trò
        $rolePermissions = $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(function ($role) {
                return $role->permissions->map(function ($permission) {
                    $perms = [];
                    foreach (ModuleConst::getActions() as $action) {
                        if ($permission->{"can_$action"}) {
                            $perms[$action] = true;
                        }
                    }
                    return [
                        'module_name' => $permission->module_name,
                        'actions' => $perms,
                    ];
                });
            });

        foreach ($rolePermissions as $perm) {
            $permissions[$perm['module_name']] = array_merge(
                $permissions[$perm['module_name']] ?? [],
                $perm['actions']
            );
        }

        return $permissions;
    }

    // Kiểm tra quyền
    public function hasPermission(string $module, string $action): bool
    {
        $permissions = $this->getAllPermissions();
        return isset($permissions[$module][$action]) && $permissions[$module][$action] === true;
    }

    // Xóa mềm các quan hệ khi xóa user
    protected static function booted(): void
    {
        static::deleting(function ($user) {
            $user->permissions()->delete();
            $user->roles()->detach();
        });
    }
}