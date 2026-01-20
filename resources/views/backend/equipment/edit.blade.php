@extends('layouts.backend')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="equipment-form" action="{{ route('equipment.update', $equipment->id) }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="sku" class="form-label">Mã SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sku" name="sku" value="{{ $equipment->sku }}">
                        @error('sku') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_type" class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
                        <select class="form-select choices" id="unit_type" name="unit_type" disabled>
                            <option value="box" {{ $equipment->unit_type == 'box' ? 'selected' : '' }}>Hộp</option>
                            <option value="set_kit" {{ $equipment->unit_type == 'set_kit' ? 'selected' : '' }}>Bộ</option>
                            <option value="device_equipment" {{ $equipment->unit_type == 'device_equipment' ? 'selected' : '' }}>Thiết bị</option>
                            <option value="piece_item" {{ $equipment->unit_type == 'piece_item' ? 'selected' : '' }}>Cái</option>
                            <option value="unit_piece" {{ $equipment->unit_type == 'unit_piece' ? 'selected' : '' }}>Chiếc</option>
                        </select>
                        @error('unit_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="import_method" class="form-label">Phương pháp nhập <span class="text-danger">*</span></label>
                        <select class="form-select choices" id="import_method" name="import_method" disabled>
                            <option value="single_item" {{ $equipment->import_method == 'single_item' ? 'selected' : '' }}>Đơn chiếc</option>
                            <option value="batch_series" {{ $equipment->import_method == 'batch_series' ? 'selected' : '' }}>Hàng Loạt</option>
                        </select>
                        @error('import_method') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    @if ($equipment->import_method === 'batch_series')
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $equipment->quantity }}" readonly>
                        @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thiết bị <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $equipment->name }}">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <input type="file" class="form-control" id="image" name="image">
                        @if ($equipment->image_path)
                            <img src="{{ $equipment->image_path }}" alt="Hình ảnh hiện tại" class="img-thumbnail mt-2" style="max-width: 200px;">
                        @endif
                        @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="import_date" class="form-label">Ngày nhập thiết bị</label>
                        <input type="date" class="form-control" id="import_date" name="import_date" value="{{ $equipment->import_date ? $equipment->import_date->format('Y-m-d') : now()->format('Y-m-d') }}">
                        @error('import_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="country_id" class="form-label">Xuất xứ</label>
                        <select class="form-select choices" id="country_id" name="country_id">
                            <option value="">Chọn xuất xứ</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ $equipment->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Đơn vị sử dụng thiết bị <span class="text-danger">*</span></label>
                        <select class="form-select choices" id="unit_id" name="unit_id">
                            <option value="">Chọn đơn vị</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ $equipment->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="attachment" class="form-label">File đính kèm</label>
                        <div class="suneditor-block border rounded" id="attachment-editor" data-input="attachment-input" data-index="1" data-height="200px"></div>
                        <textarea id="attachment-input" class="d-none" name="attachment">{{ $equipment->attachment }}</textarea>
                        @error('attachment') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="additional_info" class="form-label">Thông tin bổ sung</label>
                        <div class="suneditor-block border rounded" id="additional_info-editor" data-input="additional_info-input" data-index="2" data-height="200px"></div>
                        <textarea id="additional_info-input" class="d-none" name="additional_info">{{ $equipment->additional_info }}</textarea>
                        @error('additional_info') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật thiết bị</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/suneditor.css', 'resources/css/flatpickr.css', 'resources/css/choices.css'], 'build/backend')
@endpush

@push('scripts')
    @vite(['resources/js/modules/suneditor.js', 'resources/js/modules/flatpickr.js', 'resources/js/modules/choices.js'], 'build/backend')
    <script>
        window.addEventListener('load', function() {
            $(document).ready(function() {
                // Init suneditor for attachment and additional_info with existing content
                // Assuming suneditor.js handles initialization and sets value from textarea

                // Init flatpickr for date
                flatpickr('#import_date', {
                    dateFormat: 'Y-m-d',
                    defaultDate: '{{ $equipment->import_date ? $equipment->import_date->format('Y-m-d') : now()->format('Y-m-d') }}',
                });

                // Init Choices for selects with class 'choices'
                const choicesElements = document.querySelectorAll('.choices');
                choicesElements.forEach(element => {
                    new Choices(element, {
                        searchEnabled: true,
                        itemSelectText: '',
                        shouldSort: false,
                    });
                });

                // No toggle for quantity since it's readonly or hidden

                $('#equipment-form').on('submit', function(e) {
                    let valid = true;
                    const requiredFields = ['sku', 'name', 'unit_id']; // Required fields
                    const errors = []; // Collect errors

                    requiredFields.forEach(field => {
                        const input = $('#' + field);
                        if (!input.val().trim()) {
                            valid = false;
                            errors.push(`${input.prev('label').text()} là bắt buộc!`);
                            input.addClass('is-invalid');
                        } else {
                            input.removeClass('is-invalid');
                        }
                    });

                    if (!valid) {
                        e.preventDefault();
                        Toastify({
                            text: errors.join('\n'),
                            duration: 5000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: { background: "#dc3545" },
                        }).showToast();
                        $('#' + requiredFields[0]).focus();
                    }
                });
            });
        });
    </script>
@endpush