<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'sku',
        'unit_type',
        'import_method',
        'quantity',
        'name',
        'image_path',
        'import_date',
        'country_id',
        'unit_id',
        'attachment',
        'additional_info',
        'managed_by',
    ];

    protected $casts = [
        'import_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(EquipmentQrCode::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path || !Storage::disk('public')->exists($this->image_path)) {
            return config('app.placeholder_image');
        }

        return Storage::url($this->image_path);
    }
}