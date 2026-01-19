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
                <form id="user-form" action="{{ route('user.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="account_type" class="form-label">Loại tài khoản <span class="text-danger">*</span></label>
                        <select class="choices-select form-control" id="account_type" name="account_type">
                            <option selected="true" disabled="disabled">Chọn loại tài khoản</option>
                            <option value="warehouse_management" selected>Quản lý kho</option>
                            <option value="foreman">Quản đốc</option>
                        </select>
                        @error('account_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Đơn vị <span class="text-danger">*</span></label>
                        <select class="choices-select form-control" id="unit_id" name="unit_id">
                            <option selected="true" disabled="disabled">Chọn đơn vị</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên tài khoản <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username">
                        @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3" id="name-field" style="display: block;">
                        <label for="name" class="form-label">Tên nhân viên</label>
                        <input type="text" class="form-control" id="name" name="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password">
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
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
            // Toggle name field based on account_type
            $('#account_type').on('change', function() {
                const value = $(this).val() || '';
                if (value === 'warehouse_management') {
                    $('#name-field').show();
                } else {
                    $('#name-field').hide();
                    $('#name').val(''); // Clear value if hidden
                }
            });

            // Initial check
            const initialAccountType = $('#account_type').val() || '';
            if (initialAccountType !== 'warehouse_management') {
                $('#name-field').hide();
            }

            $('#user-form').on('submit', function(e) {
                let valid = true;
                const requiredFields = ['account_type', 'unit_id', 'username', 'password', 'password_confirmation'];
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
                const accountTypeVal = $('#account_type').val() || '';
                if (accountTypeVal === 'warehouse_management' && !$('#name').val().trim()) {
                    valid = false;
                    const nameLabel = $('label[for="name"]').text().trim();
                    errors.push(`${nameLabel} là bắt buộc!`);
                    $('#name').addClass('is-invalid');
                } else {
                    $('#name').removeClass('is-invalid');
                }

                // Basic password match check (client-side)
                const password = $('#password').val() || '';
                const confirmation = $('#password_confirmation').val() || '';
                if (password !== confirmation) {
                    valid = false;
                    const confirmLabel = $('label[for="password_confirmation"]').text().replace(/\s*\*\s*$/, '').trim();
                    errors.push(`${confirmLabel} không khớp với mật khẩu!`);
                    $('#password_confirmation').addClass('is-invalid');
                } else {
                    $('#password_confirmation').removeClass('is-invalid');
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