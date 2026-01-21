<?php

namespace App\Http\Controllers\Backend\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceLogRequest;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceType;
use App\Models\EquipmentQrCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class MaintenanceLogController extends Controller
{
    public function index($equipment_qr_code_id)
    {
        try {
            $equipment_qr_code = EquipmentQrCode::findOrFail($equipment_qr_code_id);
            $equipment = Equipment::findOrFail($equipment_qr_code->equipment_id);
            $maintenanceTypes = MaintenanceType::all(); // Load cho filter select nếu cần
            return view('backend.equipment.maintenance-log.index', compact('equipment', 'equipment_qr_code', 'maintenanceTypes'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi tải danh sách: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create($equipment_qr_code_id)
    {
        try {
            $equipment_qr_code = EquipmentQrCode::findOrFail($equipment_qr_code_id);
            $maintenanceTypes = MaintenanceType::all();
            return view('backend.equipment.maintenance-log.create', compact('equipment_qr_code', 'maintenanceTypes'));
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaintenanceLogRequest $request, $equipment_qr_code_id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $data = [
                'equipment_qr_code_id' => $equipment_qr_code_id,
                'maintenance_type_id' => $validated['maintenance_type_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'performer' => $validated['performer'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'setup_time' => $validated['setup_time'] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];
            MaintenanceLog::create($data);
            DB::commit();
            return redirect()->route('maintenance-log.index', ['equipment_qr_code_id' => $equipment_qr_code_id])
                ->with('success', 'Nhật ký bảo dưỡng đã được tạo thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi tạo nhật ký bảo dưỡng: ' . $e->getMessage());
        }
    }

    public function edit($equipment_qr_code_id, $id)
    {
        try {
            $equipment_qr_code = EquipmentQrCode::findOrFail($equipment_qr_code_id);
            $equipment = Equipment::findOrFail($equipment_qr_code->equipment_id);
            $log = MaintenanceLog::where('equipment_qr_code_id', $equipment_qr_code_id)->findOrFail($id);
            $maintenanceTypes = MaintenanceType::all();
            return view('backend.equipment.maintenance-log.edit', compact('equipment', 'equipment_qr_code', 'log', 'maintenanceTypes'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi tải form chỉnh sửa: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaintenanceLogRequest $request, $equipment_qr_code_id, $id)
    {
        $log = MaintenanceLog::where('equipment_qr_code_id', $equipment_qr_code_id)->findOrFail($id);
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $data = [
                'maintenance_type_id' => $validated['maintenance_type_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'performer' => $validated['performer'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'setup_time' => $validated['setup_time'] ?? null,
                'updated_by' => Auth::id(),
            ];
            $log->update($data);
            DB::commit();
            return redirect()->route('maintenance-log.index', ['equipment_qr_code_id' => $equipment_qr_code_id])
                ->with('success', 'Nhật ký bảo dưỡng đã được cập nhật thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi cập nhật nhật ký bảo dưỡng: ' . $e->getMessage());
        }
    }
}