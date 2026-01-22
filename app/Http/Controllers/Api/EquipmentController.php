<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EquipmentController extends Controller
{
    public function datatable(Request $request)
    {
        $columns = ['id', 'sku', 'name', 'unit_type', 'import_method', 'import_date', 'unit_id', 'created_at'];
        $query = Equipment::select($columns)
            ->with(['unit:id,name'])
            ->withCount('qrCodes'); // Tối ưu: Load count serial batch (1 query cho toàn trang)

        // Thêm subqueries cho min và max serial để tránh N+1 queries
        $query->addSelect([
            'min_serial' => EquipmentQrCode::selectRaw('min(serial_number)')
                ->whereColumn('equipment_id', 'equipments.id'),
            'max_serial' => EquipmentQrCode::selectRaw('max(serial_number)')
                ->whereColumn('equipment_id', 'equipments.id'),
        ]);

        // Optional: Show soft deleted nếu param ?withTrashed=1
        if ($request->filled('withTrashed')) {
            $query->withTrashed();
        }

        if ($request->filled('filter_sku')) {
            $query->where('sku', 'like', '%' . $request->filter_sku . '%');
        }

        if ($request->filled('filter_name')) {
            $query->where('name', 'like', '%' . $request->filter_name . '%');
        }

        if ($request->filled('filter_unit_type')) {
            $query->where('unit_type', $request->filter_unit_type);
        }

        if ($request->filled('filter_import_method')) {
            $query->where('import_method', $request->filter_import_method);
        }

        // Bổ sung: Filter theo serial_number từ EquipmentQrCode
        if ($request->filled('filter_serial')) {
            $query->whereHas('qrCodes', function ($q) use ($request) {
                $q->where('serial_number', 'like', '%' . $request->filter_serial . '%');
            });
        }

        return DataTables::eloquent($query)
            ->addColumn('unit_type_label', function ($equipment) {
                $labels = [
                    'box' => 'Hộp',
                    'set_kit' => 'Bộ',
                    'device_equipment' => 'Thiết bị',
                    'piece_item' => 'Cái',
                    'unit_piece' => 'Chiếc',
                ];
                return $labels[$equipment->unit_type] ?? $equipment->unit_type;
            })
            ->addColumn('import_method_label', function ($equipment) {
                $labels = [
                    'single_item' => 'Đơn chiếc',
                    'batch_series' => 'Hàng Loạt',
                ];
                return $labels[$equipment->import_method] ?? $equipment->import_method;
            })
            ->addColumn('formatted_import_date', function ($equipment) {
                return optional($equipment->import_date)->format('d/m/Y') ?? '';
            })
            ->addColumn('unit_name', function ($equipment) {
                return $equipment->unit ? $equipment->unit->name : '';
            })
            ->addColumn('formatted_created_at', function ($equipment) {
                return optional($equipment->created_at)->format('d/m/Y H:i') ?? '';
            })
            ->addColumn('serial_count', function ($equipment) {
                return $equipment->qr_codes_count; // Sử dụng count đã load từ withCount
            })
            ->addColumn('name', function ($equipment) {
                $serialInfo = '';
                if ($equipment->qr_codes_count > 0) {
                    $minSerial = $equipment->min_serial ?? 'N/A';
                    $maxSerial = $equipment->max_serial ?? 'N/A';
                    $serialInfo = '<br><small>Tổng số: ' . $equipment->qr_codes_count . ' tem<br>Serial từ: ' . $minSerial . ' đến: ' . $maxSerial . '</small>';
                }
                return $equipment->name . $serialInfo;
            })
            ->addColumn('actions', function ($equipment) {
                return '<a href="' . route('equipment.edit', $equipment->id) . '" class="btn btn-sm btn-primary me-1"><i class="fa-light fa-pen-to-square"></i></a>' .
                       '<a href="' . route('admin.equipment.serials', $equipment->id) . '" class="btn btn-sm btn-info me-1"><i class="fa fa-qrcode"></i></a>' .
                       '<a href="' . route('admin.equipment.export-serials', $equipment->id) . '" class="btn btn-sm btn-success me-1"><i class="fa fa-download"></i></a>' .
                       '<button class="btn btn-sm btn-danger delete-equipment" data-id="' . $equipment->id . '"><i class="fa-light fa-trash"></i></button>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->filterColumn('unit.name', function ($query, $keyword) {
                $query->whereHas('unit', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['name', 'actions']) // Thêm 'name' để render HTML
            ->make(true);
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        if (!auth()->user()->can('delete-equipment')) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa.'], 403);
        }
        $equipment->delete();
        return response()->json(['success' => true, 'message' => 'Thiết bị đã được xóa thành công.']);
    }
}