<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Module extends Model
{
    protected $fillable = ['name', 'category', 'description'];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_name', 'name');
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'module_name', 'name');
    }
    
    protected static function boot()
    {
        parent::boot();
        static::created(fn() => Cache::forget('modules'));
        static::updated(fn() => Cache::forget('modules'));
        static::deleted(fn() => Cache::forget('modules'));
    }
}