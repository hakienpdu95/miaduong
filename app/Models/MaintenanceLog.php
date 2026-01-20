<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'equipment_qr_code_id',
        'maintenance_type_id',
        'maintenance_time',
        'performer_id',
        'description',
        'status',
        'setup_time',
    ];

    protected $casts = [
        'status' => 'string', 
        'maintenance_time' => 'date',
        'setup_time' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(EquipmentQrCode::class, 'equipment_qr_code_id');
    }

    public function maintenanceType()
    {
        return $this->belongsTo(MaintenanceType::class, 'maintenance_type_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performer_id');
    }
}