@extends('layouts.serial')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Thông Tin Thiết Bị Từ Serial: {{ $qrCode->serial_number }}</div>
                <div class="card-body">
                    @if ($equipment)
                        <h5>Tên Thiết Bị: {{ $equipment->name }}</h5>
                        <p><strong>Mã SKU:</strong> {{ $equipment->sku }}</p>
                        <p><strong>Đơn Vị Tính:</strong> {{ $equipment->unit_type }} (Label: {{ ['box' => 'Hộp', 'set_kit' => 'Bộ', 'device_equipment' => 'Thiết bị', 'piece_item' => 'Cái', 'unit_piece' => 'Chiếc'][$equipment->unit_type] ?? $equipment->unit_type }})</p>
                        <p><strong>Phương Pháp Nhập:</strong> {{ $equipment->import_method }} (Label: {{ ['single_item' => 'Đơn chiếc', 'batch_series' => 'Hàng Loạt'][$equipment->import_method] ?? $equipment->import_method }})</p>
                        <p><strong>Ngày Nhập:</strong> {{ optional($equipment->import_date)->format('d/m/Y') ?? 'N/A' }}</p>
                        <p><strong>Đơn Vị Sử Dụng:</strong> {{ $equipment->unit ? $equipment->unit->name : 'N/A' }}</p>
                        <p><strong>Thông Tin Bổ Sung:</strong> {!! $equipment->additional_info ?? 'N/A' !!}</p>
                        @if ($equipment->image_url)
                            <img src="{{ $equipment->image_url }}" alt="Hình ảnh thiết bị" style="max-width: 300px;">
                        @endif
                    @else
                        <p>Không tìm thấy thông tin thiết bị cho serial này.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection