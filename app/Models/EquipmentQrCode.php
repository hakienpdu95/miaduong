<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentQrCode extends Model
{
    use HasFactory;

    protected $table = 'equipment_qr_codes';

    protected $fillable = [
        'equipment_id',
        'serial_number',
        'managed_by',
        'created_by',
        'updated_by',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}