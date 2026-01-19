<?php

namespace App\Http\Controllers\Backend\MaintenanceType;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MaintenanceTypeRequest;
use App\Models\MaintenanceType;
use Exception;

/**
 * Controller for managing maintenance types in the backend.
 */
class MaintenanceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.maintenance-type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.maintenance-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaintenanceTypeRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ];
            $maintenanceType = MaintenanceType::create($data);
            DB::commit();
            return redirect()->route('maintenance-type.index')->with('success', 'Loại bảo trì đã được tạo thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi tạo loại bảo trì: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $maintenanceType = MaintenanceType::findOrFail($id);
        return view('backend.maintenance-type.edit', compact('maintenanceType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaintenanceTypeRequest $request, $id)
    {
        $maintenanceType = MaintenanceType::findOrFail($id);
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ];
            $updated = $maintenanceType->update($data);
            if (!$updated) {
                throw new Exception('Update failed – no changes detected or DB error.');
            }
            DB::commit();
            return redirect()->route('maintenance-type.index')->with('success', 'Loại bảo trì đã được cập nhật thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi cập nhật loại bảo trì: ' . $e->getMessage());
        }
    }
}