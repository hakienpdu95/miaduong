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

                <form id="user-form" action="{{ route('user.update', $user->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="account_type" class="form-label">Loại tài khoản <span class="text-danger">*</span></label>
                        @php
                            $accountTypeLabels = [
                                'warehouse_management' => 'Quản lý kho',
                                'foreman' => 'Quản đốc',
                            ];
                            $displayAccountType = $accountTypeLabels[$profile->account_type] ?? $profile->account_type;
                        @endphp
                        <input type="text" class="form-control" id="account_type_display" value="{{ $displayAccountType }}" disabled>
                        <input type="hidden" name="account_type" value="{{ $profile->account_type }}"> <!-- Giữ giá trị gốc để submit -->
                        @error('account_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Đơn vị <span class="text-danger">*</span></label>
                        <select class="choices-select form-control" id="unit_id" name="unit_id">
                            <option disabled>Chọn đơn vị</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ $profile->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Tên tài khoản <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" value="{{ $user->username }}" disabled>
                        <input type="hidden" name="username" value="{{ $user->username }}"> <!-- Giữ giá trị gốc để submit -->
                        @error('username')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3" id="name-field" style="display: {{ $profile->account_type === 'warehouse_management' ? 'block' : 'none' }};">
                        <label for="name" class="form-label">Tên nhân viên</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật tài khoản</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/choices.css'], 'build/backend')
@endpush

@push('scripts')
    @vite(['resources/js/modules/choices.js'], 'build/backend')
    <script>
        window.addEventListener('load', function() {
            $(document).ready(function() {
                // Vì account_type disabled, không cần toggle on change, chỉ initial display dựa trên style inline

                $('#user-form').on('submit', function(e) {
                    let valid = true;
                    const requiredFields = ['unit_id']; // Loại bỏ account_type và username vì disabled
                    const errors = [];

                    requiredFields.forEach(field => {
                        const input = $('#' + field);
                        const value = input.val() || '';
                        const labelText = $('label[for="' + field + '"]').text().replace(/\s*\*\s*$/, '').trim(); // Lấy text label, loại bỏ * nếu có
                        if (!value.trim()) {
                            valid = false;
                            errors.push(`${labelText} là bắt buộc!`);
                            input.closest('.choices')?.addClass('is-invalid') || input.addClass('is-invalid'); // Thêm class cho Choices hoặc input
                        } else {
                            input.closest('.choices')?.removeClass('is-invalid') || input.removeClass('is-invalid');
                        }
                    });

                    // Conditional check for name
                    const accountTypeVal = '{{ $profile->account_type }}'; // Sử dụng giá trị fixed từ server
                    if (accountTypeVal === 'warehouse_management' && !$('#name').val().trim()) {
                        valid = false;
                        const nameLabel = $('label[for="name"]').text().trim();
                        errors.push(`${nameLabel} là bắt buộc!`);
                        $('#name').addClass('is-invalid');
                    } else {
                        $('#name').removeClass('is-invalid');
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