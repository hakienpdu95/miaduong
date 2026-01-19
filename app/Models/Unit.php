<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'supervisor_name',
        'supervisor_phone',
        'quantity',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'unit_id');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y') : null;
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y') : null;
    }
}