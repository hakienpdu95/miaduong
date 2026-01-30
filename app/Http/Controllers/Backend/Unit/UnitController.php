<?php

namespace App\Http\Controllers\Backend\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UnitRequest;
use App\Models\Unit;
use Exception;

/**
 * Controller for managing units in the backend.
 */
class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.unit.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.unit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnitRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $data = [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'supervisor_name' => $validated['supervisor_name'],
                'supervisor_phone' => $validated['supervisor_phone'] ?? null,
                'description' => $validated['description'] ?? null,
            ];

            $unit = Unit::create($data);

            DB::commit();
            return redirect()->route('unit.index')->with('success', 'Đơn vị đã được tạo thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi tạo đơn vị: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);

        $batchQuantity = $unit->equipments()->where('import_method', 'batch_series')->sum('quantity');
        $singleCount = $unit->equipments()->where('import_method', 'single_item')->count();
        $totalQuantity = $batchQuantity + $singleCount;

        return view('backend.unit.edit', compact('unit', 'totalQuantity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnitRequest $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $data = [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'supervisor_name' => $validated['supervisor_name'],
                'supervisor_phone' => $validated['supervisor_phone'] ?? null,
                'description' => $validated['description'] ?? null,
            ];

            $updated = $unit->update($data);

            if (!$updated) {
                throw new Exception('Update failed – no changes detected or DB error.');
            }

            DB::commit();
            return redirect()->route('unit.index')->with('success', 'Đơn vị đã được cập nhật thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi cập nhật đơn vị: ' . $e->getMessage());
        }
    }
}