<?php

namespace App\Http\Controllers\Backend\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\EquipmentRequest;
use App\Models\Country;
use App\Models\Equipment;
use App\Models\EquipmentQrCode;
use App\Models\SerialCounter;
use App\Models\Unit;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller for managing equipments in the backend.
 */
class EquipmentController extends Controller
{
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
                $path = $request->file('image')->store('equipments/images', 'public');
                $data['image_path'] = Storage::url($path);
            }

            if ($validated['import_method'] === 'batch_series') {
                $data['quantity'] = $validated['quantity'];
            }

            $equipment = Equipment::create($data);

            $userId = Auth::id();
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

    // Additional methods like edit, update, etc., can be added similarly.
}