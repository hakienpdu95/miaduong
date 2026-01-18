<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\HasPermissions;
use App\Constants\ModuleConst;

/**
 * User model representing the users table.
 */
class User extends Authenticatable
{
    use HasPermissions, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'email_verified_at',
        'managed_by',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's roles (many-to-many relationship).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Get the user's permissions (one-to-many relationship).
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    /**
     * Get the users managed by this user (one-to-many relationship).
     */
    public function managedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'managed_by');
    }

    /**
     * Get the user's profile (one-to-one relationship).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if the user has any of the specified roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Get all permissions for the user (merged from personal and roles).
     * Cached internally per request for performance.
     *
     * @return array
     */
    public function getAllPermissions(): array
    {
        // Cache nội bộ per request (static để tránh query lặp, reset per request)
        static $permissionsCache = null;

        if ($permissionsCache !== null) {
            return $permissionsCache;
        }

        $permissions = [];

        // Eager load permissions cá nhân (tối ưu query)
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
                return ['module_name' => $permission->module_name, 'actions' => $perms];
            });

        foreach ($personalPermissions as $perm) {
            $permissions[$perm['module_name']] = array_merge(
                $permissions[$perm['module_name']] ?? [],
                $perm['actions']
            );
        }

        // Eager load role permissions (tối ưu bằng with)
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
                    return ['module_name' => $permission->module_name, 'actions' => $perms];
                });
            });

        foreach ($rolePermissions as $perm) {
            // Merge với ưu tiên personal (nếu conflict, personal override)
            $permissions[$perm['module_name']] = array_merge(
                $perm['actions'],
                $permissions[$perm['module_name']] ?? []
            );
        }

        $permissionsCache = $permissions;

        return $permissions;
    }

    /**
     * Check if the user has permission for a module and action.
     * Uses cached getAllPermissions() for high performance.
     *
     * @param string $module
     * @param string $action
     * @return bool
     */
    public function hasPermission(string $module, string $action): bool
    {
        $permissions = $this->getAllPermissions();
        return isset($permissions[$module][$action]) && $permissions[$module][$action] === true;
    }

    /**
     * Boot the model and handle deleting relationships.
     */
    protected static function booted(): void
    {
        static::deleting(function ($user) {
            $user->permissions()->delete();
            $user->roles()->detach();
            // Optional: Xử lý soft delete cho managedUsers nếu cần
        });
    }
}