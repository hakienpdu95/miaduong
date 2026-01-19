<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ImportBatch extends Model
{
    protected $fillable = [
        'sku',
        'unit_type',
        'import_method',
        'importer_id',
        'quantity',
        'import_date',
        'notes',
    ];

    protected $casts = [
        'unit_type' => 'string', 
        'import_method' => 'string', 
        'import_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function importer()
    {
        return $this->belongsTo(User::class, 'importer_id');
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'import_batch_id');
    }
}