<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentQrCode;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EquipmentQrCodeController extends Controller
{
    public function datatable(Request $request, $equipmentId)
    {
        $columns = ['id', 'serial_number', 'created_at'];
        $query = EquipmentQrCode::select($columns)
            ->where('equipment_id', $equipmentId)
            ->with('equipment:id,name'); // Load tên thiết bị

        // Optional: Show soft deleted nếu param ?withTrashed=1
        if ($request->filled('withTrashed')) {
            $query->withTrashed();
        }

        // Thêm filters nếu cần (ví dụ: filter serial)
        if ($request->filled('filter_serial')) {
            $query->where('serial_number', 'like', '%' . $request->filter_serial . '%');
        }

        return DataTables::eloquent($query)
            ->addColumn('qr_code', function ($qr) {
                // Placeholder cho client-side generation
                $url = url('/serial/' . $qr->serial_number);
                return '<div class="qr-code-placeholder" data-url="' . $url . '" data-serial="' . $qr->serial_number . '"></div>';
            })
            ->addColumn('serial_number', function ($qr) {
                return $qr->serial_number;
            })
            ->addColumn('equipment_name', function ($qr) {
                return $qr->equipment ? $qr->equipment->name : '';
            })
            ->addColumn('formatted_created_at', function ($qr) {
                return optional($qr->created_at)->format('d/m/Y H:i') ?? '';
            })
            ->addColumn('actions', function ($qr) {
                // Icon cập nhật bảo trì - giả sử route 'qr-code.maintenance.edit'
                // Bạn có thể thay bằng route thực tế khi implement tính năng bảo trì
                return '<a href="#" class="btn btn-sm btn-warning"><i class="fa fa-wrench"></i></a>';
            })
            ->filterColumn('serial_number', function ($query, $keyword) {
                $query->where('serial_number', 'like', "%{$keyword}%");
            })
            ->rawColumns(['qr_code', 'actions'])
            ->make(true);
    }
}