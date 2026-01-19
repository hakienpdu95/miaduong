<?php

namespace App\Http\Controllers\Backend\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Equipment; 
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $roles = Equipment::paginate(10);
        return view('backend.role.index', compact('roles'));
    }

    public function create()
    {
        return view('backend.role.create');
    }

    public function store(Request $request)
    {
        // Validation và store logic
        Equipment::create($request->validated());
        return redirect()->route('role.index');
    }

    public function show(Equipment $role)
    {
        return view('backend.role.show', compact('role'));
    }

    public function edit(Equipment $role)
    {
        return view('backend.role.edit', compact('role'));
    }

    public function update(Request $request, Equipment $role)
    {
        // Validation và update logic
        $role->update($request->validated());
        return redirect()->route('role.index');
    }

    public function destroy(Equipment $role)
    {
        $role->delete();
        return redirect()->route('role.index');
    }
}