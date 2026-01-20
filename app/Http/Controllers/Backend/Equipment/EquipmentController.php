<?php

namespace App\Http\Controllers\Backend\Equipment;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EquipmentRequest;
use App\Models\Country;
use App\Models\ImportBatch;
use App\Models\Equipment;
use App\Models\Unit;
use Exception;

class EquipmentController extends Controller
{
    public function create()
    {
        $countries = Country::all();
        $units = Unit::all();
        return view('backend.equipment.create', compact('countries', 'units'));
    }

    public function store(EquipmentRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $batchData = [
                'sku' => $validated['sku'],
                'unit_type' => $validated['unit_type'],
                'import_method' => $validated['import_method'],
                'importer_id' => Auth::id(),
                'quantity' => $validated['import_method'] === 'single_item' ? 1 : $validated['quantity'],
            ];
            $batch = ImportBatch::create($batchData);

            $equipmentsData = $validated['import_method'] === 'single_item' ? [$validated] : $validated['equipments'];

            foreach ($equipmentsData as $key => $data) {
                $equipmentData = [
                    'import_batch_id' => $batch->id,
                    'name' => $data['name'],
                    'import_date' => $data['import_date'],
                    'country_id' => $data['country_id'] ?? null,
                    'unit_id' => $data['unit_id'],
                    'attachment' => $data['attachment'] ?? null,
                    'additional_info' => $data['additional_info'] ?? null,
                ];

                $imageField = $validated['import_method'] === 'single_item' ? 'image' : "equipments.$key.image";
                if ($request->hasFile($imageField)) {
                    $file = $request->file($imageField);
                    $path = $file->store('equipments', 'public');
                    $equipmentData['image_path'] = $path;
                }

                Equipment::create($equipmentData);
            }

            DB::commit();
            return redirect()->route('equipment.index')->with('success', 'Thiết bị đã được nhập thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi nhập thiết bị: ' . $e->getMessage());
        }
    }
}