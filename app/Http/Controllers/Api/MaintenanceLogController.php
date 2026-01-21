<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MaintenanceLogController extends Controller
{
    public function datatable(Request $request, $equipment_qr_code_id)
    {
        $columns = ['id', 'maintenance_type_id', 'start_date', 'end_date', 'performer', 'status', 'created_at'];
        $query = MaintenanceLog::select($columns)
            ->where('equipment_qr_code_id', $equipment_qr_code_id)
            ->with('maintenanceType'); // Eager load relationship

        // Filters
        if ($request->filled('filter_maintenance_type_id')) {
            $query->where('maintenance_type_id', $request->filter_maintenance_type_id);
        }
        if ($request->filled('filter_start_date')) {
            $query->whereDate('start_date', $request->filter_start_date);
        }
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        return DataTables::eloquent($query)
            ->addColumn('maintenance_type_name', function ($log) {
                return $log->maintenanceType->name ?? $log->maintenanceType->title ?? 'N/A';
            })
            ->addColumn('status_label', function ($log) {
                return match ($log->status) {
                    'operating_active' => 'Đang hoạt động',
                    'under_repair' => 'Đang sửa chữa',
                    'broken_damaged' => 'Đã hỏng',
                    default => 'Không xác định',
                };
            })
            ->addColumn('formatted_created_at', function ($log) {
                return $log->created_at->format('d/m/Y H:i'); // Adjust format as needed
            })
            ->addColumn('actions', function ($log) use ($equipment_qr_code_id) {
                return '<a href="' . route('maintenance-log.edit', ['equipment_qr_code_id' => $equipment_qr_code_id, 'id' => $log->id]) . '" class="btn btn-sm btn-primary me-1"><i class="fa-light fa-pen-to-square"></i></a>' .
                       '<button class="btn btn-sm btn-danger delete-maintenance-log" data-id="' . $log->id . '"><i class="fa-light fa-trash"></i></button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function destroy($equipment_qr_code_id, $id)
    {
        $log = MaintenanceLog::where('equipment_qr_code_id', $equipment_qr_code_id)->findOrFail($id);
        $log->delete();
        return response()->json(['success' => true, 'message' => 'Nhật ký bảo dưỡng đã được xóa thành công.']);
    }
}