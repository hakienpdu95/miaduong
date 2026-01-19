<?php

namespace App\Http\Controllers\Backend\MaintenanceType;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;

class MaintenanceTypeController extends Controller
{
    public function index()
    {
        $maintenance_types = MaintenanceType::paginate(10);
        return view('backend.maintenance-type.index', compact('roles'));
    }

    public function create()
    {
        return view('backend.maintenance-type.create');
    }

    public function store(Request $request)
    {
        // Validation và store logic
        MaintenanceType::create($request->validated());
        return redirect()->route('maintenance-type.index');
    }

    public function show(MaintenanceType $maintenance_type)
    {
        return view('backend.maintenance-type.show', compact('role'));
    }

    public function edit(MaintenanceType $maintenance_type)
    {
        return view('backend.maintenance-type.edit', compact('role'));
    }

    public function update(Request $request, MaintenanceType $maintenance_type)
    {
        // Validation và update logic
        $maintenance_type->update($request->validated());
        return redirect()->route('maintenance-type.index');
    }

    public function destroy(MaintenanceType $maintenance_type)
    {
        $maintenance_type->delete();
        return redirect()->route('maintenance-type.index');
    }
}