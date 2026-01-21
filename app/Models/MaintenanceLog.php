<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $fillable = [
        'equipment_qr_code_id',
        'maintenance_type_id',
        'start_date',
        'end_date',
        'performer',
        'description',
        'status',
        'setup_time',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'string', 
        'start_date' => 'date',
        'end_date' => 'date',
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
}