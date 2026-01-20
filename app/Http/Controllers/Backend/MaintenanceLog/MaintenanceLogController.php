<?php

namespace App\Http\Controllers\Backend\MaintenanceLog;

use App\Http\Controllers\Controller;
use App\Models\Role; // Giả sử model tồn tại
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    public function index()
    {
        $roles = Role::paginate(10);
        return view('backend.role.index', compact('roles'));
    }

    public function create()
    {
        return view('backend.role.create');
    }

    public function store(Request $request)
    {
        // Validation và store logic
        Role::create($request->validated());
        return redirect()->route('role.index');
    }

    public function show(Role $role)
    {
        return view('backend.role.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('backend.role.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        // Validation và update logic
        $role->update($request->validated());
        return redirect()->route('role.index');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('role.index');
    }
}