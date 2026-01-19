<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{

    public function datatable(Request $request)
    {
        $columns = ['id', 'code', 'name', 'supervisor_name', 'created_at'];
        $query = Unit::select($columns);

        // Custom filters
        if ($request->filled('filter_code')) {
            $query->where('code', 'like', '%' . $request->filter_code . '%');
        }
        if ($request->filled('filter_name')) {
            $query->where('name', 'like', '%' . $request->filter_name . '%');
        }

        return DataTables::eloquent($query)
            ->addColumn('formatted_created_at', function ($unit) {
                return $unit->formatted_created_at;
            })
            ->addColumn('actions', function ($unit) {
                return '<a href="' . route('unit.edit', $unit->id) . '" class="btn btn-sm btn-primary me-1"><i class="fa-light fa-pen-to-square"></i></a>' .
                       '<button class="btn btn-sm btn-danger delete-unit" data-id="' . $unit->id . '"><i class="fa-light fa-trash"></i></button>'; // Add delete button
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete(); // Soft delete

        return response()->json(['success' => true, 'message' => 'Đơn vị đã được xóa thành công.']);
    }
}