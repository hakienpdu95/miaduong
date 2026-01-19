@extends('layouts.backend')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="unit-form" action="{{ route('unit.store') }}" method="POST" novalidate> <!-- Thêm novalidate để disable browser validate -->
                        @csrf

                        <div class="mb-3">
                            <label for="codeunit" class="form-label">Mã đơn vị (tự sinh nếu không nhập)</label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="DV-00001">
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên đơn vị <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>

                        <div class="mb-3">
                            <label for="supervisor_name" class="form-label">Quản đốc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="supervisor_name" name="supervisor_name"> 
                        </div>

                        <div class="mb-3">
                            <label for="supervisor_phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="supervisor_phone" name="supervisor_phone">
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Số lượng thiết bị</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0">
                        </div>

                        <div class="mb-3"> 
	                        <label for="description" class="form-label">Mô tả</label> 
	                        <div class="suneditor-block border rounded" id="description-editor" data-input="description-input" data-index="1" data-height="200px"></div> 
	                        <textarea id="description-input" class="d-none" name="description"></textarea>
	                        @error('description') 
	                            <span class="text-danger">{{ $message }}</span> 
	                        @enderror 
	                    </div>

                        <button type="submit" class="btn btn-primary">Tạo đơn vị</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

	@push('styles')
	    @vite([ 'resources/css/suneditor.css' ], 'build/backend')
	@endpush

	@push('scripts')
	    @vite([ 'resources/js/modules/suneditor.js'], 'build/backend')
	    <script>
	    window.addEventListener('load', function() {
			$(document).ready(function() {
			    // Init suneditor nếu cần...

			    $('#unit-form').on('submit', function(e) {
			        let valid = true;
			        const requiredFields = ['name', 'supervisor_name']; // Required fields
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
			            // Valid → submit to server
			        }
			    });
			});	    	
	    });

		</script>
	@endpush
@endsection