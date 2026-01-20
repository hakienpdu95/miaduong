<?php

namespace App\Http\Controllers\Backend\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentRequest;
use App\Models\Country;
use App\Models\Equipment;
use App\Models\EquipmentQrCode;
use App\Models\Unit;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller for managing equipments in the backend.
 */
class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.equipment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all();
        $units = Unit::all();
        return view('backend.equipment.create', compact('countries', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EquipmentRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $data = [
                'sku' => $validated['sku'],
                'unit_type' => $validated['unit_type'],
                'import_method' => $validated['import_method'],
                'name' => $validated['name'],
                'import_date' => $validated['import_date'] ?? now()->format('Y-m-d'),
                'country_id' => $validated['country_id'] ?? null,
                'unit_id' => $validated['unit_id'],
                'attachment' => $validated['attachment'] ?? null,
                'additional_info' => $validated['additional_info'] ?? null,
                'managed_by' => Auth::id(),
            ];

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('equipments/images', 'public');
                $data['image_path'] = Storage::url($path);
            }

            if ($validated['import_method'] === 'batch_series') {
                $data['quantity'] = $validated['quantity'];
            }

            $equipment = Equipment::create($data);

            if ($validated['import_method'] === 'batch_series') {
                for ($i = 0; $i < $validated['quantity']; $i++) {
                    $serial = $this->generateUniqueSerial();
                    EquipmentQrCode::create([
                        'equipment_id' => $equipment->id,
                        'serial_number' => $serial,
                        'managed_by' => Auth::id(),
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            } else {
                // For single_item, create one QR code
                $serial = $this->generateUniqueSerial();
                EquipmentQrCode::create([
                    'equipment_id' => $equipment->id,
                    'serial_number' => $serial,
                    'managed_by' => Auth::id(),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('equipment.index')->with('success', 'Thiết bị đã được tạo thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi tạo thiết bị: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique 8-character serial number.
     */
    private function generateUniqueSerial(): string
    {
        do {
            $serial = Str::random(8);
        } while (EquipmentQrCode::where('serial_number', $serial)->exists());

        return $serial;
    }

    // Additional methods like edit, update, etc., can be added similarly.
}