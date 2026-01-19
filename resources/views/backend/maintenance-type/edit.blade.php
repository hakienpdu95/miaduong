@extends('layouts.backend')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="maintenance-type-form" action="{{ route('maintenance-type.update', $maintenanceType->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên loại bảo trì/ nâng cấp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $maintenanceType->name) }}">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <div class="suneditor-block border rounded" id="description-editor" data-input="description-input" data-index="1" data-height="200px"></div>
                        <textarea id="description-input" class="d-none" name="description">{{ old('description', $maintenanceType->description) }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật loại bảo trì</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite(['resources/css/suneditor.css'], 'build/backend')
@endpush

@push('scripts')
@vite(['resources/js/modules/suneditor.js'], 'build/backend')
<script>
    window.addEventListener('load', function() {
        $(document).ready(function() {
            // Init suneditor với nội dung hiện có
            const editor = SUNEDITOR.create('description-editor', {
                // Cấu hình suneditor tương tự create, nhưng set nội dung ban đầu
                value: '{{ old('description', $maintenanceType->description) }}',
                height: 200,
                // Thêm các tùy chọn khác nếu cần từ file suneditor.js
            });

            $('#maintenance-type-form').on('submit', function(e) {
                let valid = true;
                const requiredFields = ['name']; // Required fields
                const errors = []; // Collect errors to show one toast if multiple
                requiredFields.forEach(field => {
                    const input = $('#' + field);
                    if (!input.val().trim()) {
                        valid = false;
                        errors.push(`${input.prev('label').text()} là bắt buộc!`);
                        input.addClass('is-invalid'); // Optional: Add Bootstrap invalid class
                    } else {
                        input.removeClass('is-invalid');
                    }
                });
                if (!valid) {
                    e.preventDefault(); // Prevent submit
                    // Show one toast with all errors (or loop show multiple)
                    Toastify({
                        text: errors.join('\n'), // Join errors for one toast
                        duration: 5000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: { background: "#dc3545" },
                    }).showToast();
                    $('#' + requiredFields[0]).focus(); // Focus first invalid
                } else {
                    // Cập nhật textarea ẩn với nội dung từ editor trước submit
                    $('#description-input').val(editor.getContents());
                    // Valid → submit to server
                }
            });
        });
    });
</script>
@endpush