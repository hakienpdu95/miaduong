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

        // Optional: Show soft deleted nếu param ?withTrashed=1
        if ($request->filled('withTrashed')) {
            $query->withTrashed();
        }

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
                       '<button class="btn btn-sm btn-danger delete-unit" data-id="' . $unit->id . '"><i class="fa-light fa-trash"></i></button>';
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

        if (!auth()->user()->can('delete-unit')) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa.'], 403);
        }

        $unit->delete();

        return response()->json(['success' => true, 'message' => 'Đơn vị đã được xóa thành công.']);
    }

    // Optional: Bulk delete nếu cần mở rộng (gọi via POST /api/units/bulk-delete with ids array)
    // public function bulkDestroy(Request $request) {
    //     $request->validate(['ids' => 'required|array']);
    //     Unit::whereIn('id', $request->ids)->delete();
    //     return response()->json(['success' => true, 'message' => 'Đã xóa các đơn vị thành công.']);
    // }
}