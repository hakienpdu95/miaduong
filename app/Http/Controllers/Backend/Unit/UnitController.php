<?php

namespace App\Http\Controllers\Backend\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use Exception;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::paginate(10);
        return view('backend.unit.index', compact('units'));
    }

    public function create()
    {
        return view('backend.unit.create');
    }

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
                'quantity' => $validated['quantity'] ?? null,
                'description' => $validated['description'] ?? null,
            ];

            $unit = Unit::create($data);

            DB::commit();
            return redirect()->route('units.index')->with('success', 'Đơn vị đã được tạo thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi tạo đơn vị: ' . $e->getMessage());
        }
    }

    public function show(Unit $unit)
    {
        return view('backend.unit.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('backend.unit.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        // Validation và update logic
        $unit->update($request->validated());
        return redirect()->route('unit.index');
    }
}