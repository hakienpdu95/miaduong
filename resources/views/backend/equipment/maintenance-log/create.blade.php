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
                <form id="maintenance-log-form" action="{{ route('maintenance-log.store', ['equipment_qr_code_id' => $equipment_qr_code->id]) }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="maintenance_type_id" class="form-label">Loại bảo trì / nâng cấp <span class="text-danger">*</span></label>
                        <select class="form-select choices" id="maintenance_type_id" name="maintenance_type_id">
                            <option value="">Chọn loại</option>
                            @foreach($maintenanceTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('maintenance_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Ngày bắt đầu bảo trì <span class="text-danger">*</span></label>
                        <input type="text" class="form-control flatpickr" id="start_date" name="start_date">
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Ngày kết thúc bảo trì <span class="text-danger">*</span></label>
                        <input type="text" class="form-control flatpickr" id="end_date" name="end_date">
                        @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="performer" class="form-label">Người thực hiện <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="performer" name="performer">
                        @error('performer') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <div class="suneditor-block border rounded" id="description-editor" data-input="description-input" data-index="1" data-height="200px"></div>
                        <textarea id="description-input" class="d-none" name="description"></textarea>
                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select choices" id="status" name="status">
                            <option value="operating_active" selected>Đang hoạt động</option>
                            <option value="under_repair">Đang sửa chữa</option>
                            <option value="broken_damaged">Đã hỏng</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="setup_time" class="form-label">Cài đặt thời gian</label>
                        <input type="text" class="form-control flatpickr" id="setup_time" name="setup_time">
                        @error('setup_time') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Tạo nhật ký bảo dưỡng</button>
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
                // Init flatpickr for dates
                $('.flatpickr').flatpickr({
                    dateFormat: 'Y-m-d',
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

                // Client-side validation on submit
                $('#maintenance-log-form').on('submit', function(e) {
                    let valid = true;
                    const requiredFields = ['maintenance_type_id', 'start_date', 'end_date', 'performer', 'status'];
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