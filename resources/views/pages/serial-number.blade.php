@extends('layouts.serial')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-none">Thông Tin Thiết Bị Từ Serial: {{ $qrCode->serial_number }}</div>
                <div class="card-body p-0">
                    @if ($equipment->image_url)
                    <div class="image mb-3">
                        <img src="{{ $equipment->image_url }}" class="w-100" alt="Hình ảnh thiết bị" />
                    </div>
                    @endif
                    <div class="content">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Thông tin thiết bị</button>
                                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Lịch sử bảo dưỡng</button>
                            </div>
                        </nav>
                        <div class="tab-content p-2" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                                @if ($equipment)
                                    <ul>
                                        <li><strong>Tên Thiết Bị:</strong> {{ $equipment->name }}</li>
                                        <li><strong>Mã SKU:</strong> {{ $equipment->sku }}</li>
                                        <li><strong>Đơn Vị Tính:</strong> {{ ['box' => 'Hộp', 'set_kit' => 'Bộ', 'device_equipment' => 'Thiết bị', 'piece_item' => 'Cái', 'unit_piece' => 'Chiếc'][$equipment->unit_type] ?? $equipment->unit_type }}</li>
                                        <li><strong>Đơn Vị Sử Dụng:</strong> {{ $equipment->unit ? $equipment->unit->name : 'N/A' }}</li>
                                        <li><strong>Thông Tin Bổ Sung:</strong> {!! $equipment->additional_info ?? 'N/A' !!}</li>
                                    </ul>
                                @else
                                    <p>Không tìm thấy thông tin thiết bị cho serial này.</p>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                                @foreach($maintenanceLogs as $log)
                                    <p>- Loại: {{ $log->maintenanceType->name ?? 'Không xác định' }} <br >Nội dung: {!! $log->description !!} - Từ {{ $log->start_date }} đến {{ $log->end_date }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection