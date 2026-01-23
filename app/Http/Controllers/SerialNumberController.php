<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentQrCode;
use Illuminate\Http\Request;

class SerialNumberController extends Controller
{
    public function getSerialNumber($serial_number)
    {
        $qrCode = EquipmentQrCode::where('serial_number', $serial_number)->firstOrFail();

        $equipment = $qrCode->equipment;

        $maintenanceLogs = $qrCode->maintenanceLogs()
                                   ->with('maintenanceType')
                                   ->orderBy('start_date', 'desc')
                                   ->get();
        
        return view('pages.serial-number', compact('qrCode', 'equipment', 'maintenanceLogs'));
    }
}