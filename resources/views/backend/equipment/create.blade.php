@extends('layouts.backend')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <form id="equipment-form" action="{{ route('equipment.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="sku" class="form-label">Mã SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sku" name="sku">
                        @error('sku') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_type" class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
                        <select class="form-select choices" id="unit_type" name="unit_type">
                            <option value="box" selected>Hộp</option>
                            <option value="set_kit">Bộ</option>
                            <option value="device_equipment">Thiết bị</option>
                            <option value="piece_item">Cái</option>
                            <option value="unit_piece">Chiếc</option>
                        </select>
                        @error('unit_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="import_method" class="form-label">Phương pháp nhập <span class="text-danger">*</span></label>
                        <select class="form-select choices" id="import_method" name="import_method">
                            <option value="single_item" selected>Đơn chiếc</option>
                            <option value="batch_series">Hàng Loạt</option>
                        </select>
                        @error('import_method') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3 d-none" id="quantity-block">
                        <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1">
                        @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thiết bị <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <input type="file" class="form-control" id="image" name="image">
                        @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="import_date" class="form-label">Ngày nhập thiết bị</label>
                        <input type="date" class="form-control" id="import_date" name="import_date" value="{{ now()->format('Y-m-d') }}">
                        @error('import_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="country_id" class="form-label">Xuất xứ</label>
                        <select class="form-select choices" id="country_id" name="country_id">
                            <option value="">Chọn xuất xứ</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Đơn vị sử dụng thiết bị <span class="text-danger">*</span></label>
                        <select class="form-select choices" id="unit_id" name="unit_id">
                            <option value="">Chọn đơn vị</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="attachment" class="form-label">File đính kèm</label>
                        <div class="suneditor-block border rounded" id="attachment-editor" data-input="attachment-input" data-index="1" data-height="200px"></div>
                        <textarea id="attachment-input" class="d-none" name="attachment"></textarea>
                        @error('attachment') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="additional_info" class="form-label">Thông tin bổ sung</label>
                        <div class="suneditor-block border rounded" id="additional_info-editor" data-input="additional_info-input" data-index="2" data-height="200px"></div>
                        <textarea id="additional_info-input" class="d-none" name="additional_info"></textarea>
                        @error('additional_info') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Tạo thiết bị</button>
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
                // Init flatpickr for date
                flatpickr('#import_date', {
                    dateFormat: 'Y-m-d',
                    defaultDate: new Date(),
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

                // Toggle quantity input based on import_method
                $('#import_method').on('change', function() {
                    if ($(this).val() === 'batch_series') {
                        $('#quantity-block').removeClass('d-none');
                    } else {
                        $('#quantity-block').addClass('d-none');
                    }
                });

                $('#equipment-form').on('submit', function(e) {
                    let valid = true;
                    const requiredFields = ['sku', 'unit_type', 'import_method', 'name', 'unit_id'];
                    const errors = [];

                    requiredFields.forEach(field => {
                        const input = $('#' + field);
                        const value = input.val() || '';
                        const labelText = $('label[for="' + field + '"]').text().replace(/\s*\*\s*$/, '').trim();
                        if (!value.trim()) {
                            valid = false;
                            errors.push(`${labelText} là bắt buộc!`);
                            input.closest('.choices')?.addClass('is-invalid') || input.addClass('is-invalid');
                        } else {
                            input.closest('.choices')?.removeClass('is-invalid') || input.removeClass('is-invalid');
                        }
                    });

                    // Conditional check for quantity
                    const importMethodVal = $('#import_method').val() || '';
                    if (importMethodVal === 'batch_series' && !$('#quantity').val()) {
                        valid = false;
                        const quantityLabel = $('label[for="quantity"]').text().replace(/\s*\*\s*$/, '').trim();
                        errors.push(`${quantityLabel} là bắt buộc khi chọn Hàng Loạt!`);
                        $('#quantity').addClass('is-invalid');
                    } else {
                        $('#quantity').removeClass('is-invalid');
                    }

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