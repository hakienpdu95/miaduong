<?php

namespace App\Http\Controllers\Backend\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentRequest;
use App\Models\Country;
use App\Models\Equipment;
use App\Models\EquipmentQrCode;
use App\Models\SerialCounter;
use App\Models\Unit;
use App\Services\ImageUploadService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

/**
 * Controller for managing equipments in the backend.
 */
class EquipmentController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.equipment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all();
        $units = Unit::all();
        return view('backend.equipment.create', compact('countries', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EquipmentRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $data = [
                'sku' => $validated['sku'],
                'unit_type' => $validated['unit_type'],
                'import_method' => $validated['import_method'],
                'name' => $validated['name'],
                'import_date' => $validated['import_date'] ?? now()->format('Y-m-d'),
                'country_id' => $validated['country_id'] ?? null,
                'unit_id' => $validated['unit_id'],
                'attachment' => $validated['attachment'] ?? null,
                'additional_info' => $validated['additional_info'] ?? null,
                'managed_by' => Auth::id(),
            ];

            if ($request->hasFile('image')) {
                $data['image_path'] = $this->imageUploadService->upload($request->file('image'));
            }

            if ($validated['import_method'] === 'batch_series') {
                $data['quantity'] = $validated['quantity'];
            }

            $equipment = Equipment::create($data);

            $userId = Auth::id();

            // Linh hoạt prefix dựa trên bảng chữ cái (ví dụ: map theo unit_type)
            // Bạn có thể customize map này theo nhu cầu (A cho box, B cho set_kit, v.v.)
            // Hoặc dựa trên import_date, unit_id, hoặc config từ DB
            $prefixMap = [
                'box' => 'A',
                'set_kit' => 'B',
                'device_equipment' => 'C',
                'piece_item' => 'D',
                'unit_piece' => 'E',
            ];
            $prefix = $prefixMap[$validated['unit_type']] ?? 'A'; // Default 'A' nếu không match

            $quantity = $validated['import_method'] === 'batch_series' ? $validated['quantity'] : 1;
            $serials = $this->generateSerials($prefix, $quantity);

            $qrData = [];
            foreach ($serials as $serial) {
                $qrData[] = [
                    'equipment_id' => $equipment->id,
                    'serial_number' => $serial,
                    'managed_by' => $userId,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            EquipmentQrCode::insert($qrData); // Bulk insert: 1 query cho toàn bộ

            DB::commit();
            return redirect()->route('equipment.index')->with('success', 'Thiết bị đã được tạo thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi tạo thiết bị: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        $countries = Country::all();
        $units = Unit::all();
        return view('backend.equipment.edit', compact('equipment', 'countries', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EquipmentRequest $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $data = [
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'import_date' => $validated['import_date'] ?? now()->format('Y-m-d'),
                'country_id' => $validated['country_id'] ?? null,
                'unit_id' => $validated['unit_id'],
                'attachment' => $validated['attachment'] ?? null,
                'additional_info' => $validated['additional_info'] ?? null,
                // KHÔNG update unit_type và import_method
                // Nếu import_method là batch_series, KHÔNG update quantity và KHÔNG tạo lại serial
            ];

            if ($request->hasFile('image')) {
                $data['image_path'] = $this->imageUploadService->upload(
                    $request->file('image'),
                    $equipment->image_path // Truyền path cũ để xóa nếu tồn tại
                );
            }

            $equipment->update($data);

            DB::commit();
            return redirect()->route('equipment.index')->with('success', 'Thiết bị đã được cập nhật thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi cập nhật thiết bị: ' . $e->getMessage());
        }
    }

    /**
     * Generate an array of unique sequential serial numbers with the given prefix and count.
     * Ensures sequential increment across creations for each prefix independently.
     */
    private function generateSerials(string $prefix, int $count): array
    {
        // Lấy hoặc tạo counter cho prefix, dùng lock để atomic (tránh race condition)
        $counter = SerialCounter::lockForUpdate()->firstOrCreate(
            ['prefix' => $prefix],
            ['last_number' => 0]
        );

        $start = $counter->last_number + 1;
        $serials = [];
        for ($i = 0; $i < $count; $i++) {
            $number = $start + $i;
            // Format: Prefix + 7 digits (tổng 8 chars), đảm bảo đủ và đều (0000001, 0000002, ...)
            $serials[] = $prefix . str_pad($number, 7, '0', STR_PAD_LEFT);
        }

        // Cập nhật last_number, đảm bảo tịnh tiến tăng dần cho lần tạo tiếp theo
        $counter->last_number = $start + $count - 1;
        $counter->save();

        return $serials;
    }

    /**
     * Display the list of serials for a specific equipment.
     */
    public function serials($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('backend.equipment.serials', compact('equipment'));
    }

    public function exportSerials($id)
    {
        $equipment = Equipment::findOrFail($id);
        $qrCodes = $equipment->qrCodes()->select('serial_number')->get();

        // Prepare data as collection
        $data = $qrCodes->map(function ($qr) {
            return [
                'serial_number' => $qr->serial_number,
                'qr_link' => url('/serial/' . $qr->serial_number),
            ];
        });

        // Generate and download Excel
        return (new FastExcel($data))->download($equipment->sku . '_serials.xlsx');
    }

    // Thêm method mới (hoặc cập nhật nếu có)
    public function exportAll(Request $request)
    {
        // Query với filters (tương tự datatable)
        $query = Equipment::query()
            ->with(['unit:id,name']);

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
        if ($request->filled('filter_serial')) {
            $query->whereHas('qrCodes', function ($q) use ($request) {
                $q->where('serial_number', 'like', '%' . $request->filter_serial . '%');
            });
        }

        $equipments = $query->get();

        // Prepare data collection cho Excel (chỉ 2 cột như yêu cầu)
        $data = $equipments->map(function ($equipment) {
            return [
                'Tên thiết bị' => $equipment->name,
                'Đơn vị tính' => $this->getUnitTypeLabel($equipment->unit_type), // Helper cho label
            ];
        });

        // Download Excel
        return (new FastExcel($data))->download('danh_sach_thiet_bi.xlsx');
    }

    // Helper cho label đơn vị tính (copy từ code cũ)
    private function getUnitTypeLabel($type)
    {
        $labels = ['box' => 'Hộp', 'set_kit' => 'Bộ', 'device_equipment' => 'Thiết bị', 'piece_item' => 'Cái', 'unit_piece' => 'Chiếc'];
        return $labels[$type] ?? $type;
    }
}