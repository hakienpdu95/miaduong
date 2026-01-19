<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MaintenanceTypeController extends Controller
{
    public function datatable(Request $request)
    {
        $columns = ['id', 'name', 'description', 'created_at'];
        $query = MaintenanceType::select($columns);

        // Optional: Show soft deleted nếu param ?withTrashed=1
        if ($request->filled('withTrashed')) {
            $query->withTrashed();
        }

        if ($request->filled('filter_name')) {
            $query->where('name', 'like', '%' . $request->filter_name . '%');
        }

        return DataTables::eloquent($query)
            ->addColumn('formatted_created_at', function ($maintenanceType) {
                return $maintenanceType->formatted_created_at;
            })
            ->addColumn('actions', function ($maintenanceType) {
                return '<a href="' . route('maintenance-type.edit', $maintenanceType->id) . '" class="btn btn-sm btn-primary me-1"><i class="fa-light fa-pen-to-square"></i></a>' .
                       '<button class="btn btn-sm btn-danger delete-maintenance-type" data-id="' . $maintenanceType->id . '"><i class="fa-light fa-trash"></i></button>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function destroy($id)
    {
        $maintenanceType = MaintenanceType::findOrFail($id);
        if (!auth()->user()->can('delete-maintenance-type')) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa.'], 403);
        }
        $maintenanceType->delete();
        return response()->json(['success' => true, 'message' => 'Loại bảo trì đã được xóa thành công.']);
    }
}