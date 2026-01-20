<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EquipmentController extends Controller
{
    public function datatable(Request $request)
    {
        $columns = ['equipments.id', 'equipments.import_batch_id', 'equipments.name', 'equipments.import_date', 'equipments.country_id', 'equipments.unit_id', 'equipments.created_at'];
        $query = Equipment::select($columns)
            ->leftJoin('import_batches', 'equipments.import_batch_id', '=', 'import_batches.id')
            ->leftJoin('country', 'equipments.country_id', '=', 'country.id')
            ->leftJoin('units', 'equipments.unit_id', '=', 'units.id')
            ->with(['importBatch', 'country', 'unit']);

        // Optional: Show soft deleted nếu param ?withTrashed=1
        if ($request->filled('withTrashed')) {
            $query->withTrashed();
        }

        if ($request->filled('filter_sku')) {
            $query->where('import_batches.sku', 'like', '%' . $request->filter_sku . '%');
        }

        if ($request->filled('filter_name')) {
            $query->where('equipments.name', 'like', '%' . $request->filter_name . '%');
        }

        if ($request->filled('filter_unit_id')) {
            $query->where('equipments.unit_id', $request->filter_unit_id);
        }

        return DataTables::eloquent($query)
            ->addColumn('formatted_import_date', function ($equipment) {
                return $equipment->import_date ? $equipment->import_date->format('d/m/Y') : '';
            })
            ->addColumn('actions', function ($equipment) {
                return '<a href="' . route('equipment.edit', $equipment->id) . '" class="btn btn-sm btn-primary me-1"><i class="fa-light fa-pen-to-square"></i></a>' .
                       '<button class="btn btn-sm btn-danger delete-equipment" data-id="' . $equipment->id . '"><i class="fa-light fa-trash"></i></button>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('equipments.name', 'like', "%{$keyword}%");
            })
            ->orderColumn('import_batch.sku', 'import_batches.sku $1')
            ->orderColumn('country.name', 'country.name $1')
            ->orderColumn('unit.name', 'units.name $1')
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        // Kiểm tra quyền nếu cần: if (!auth()->user()->can('delete-equipment')) { ... }
        $equipment->delete();
        return response()->json(['success' => true, 'message' => 'Thiết bị đã được xóa thành công.']);
    }
}