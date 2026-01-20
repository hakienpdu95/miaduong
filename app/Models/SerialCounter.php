<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerialCounter extends Model {
    protected $fillable = ['prefix', 'last_number'];
}