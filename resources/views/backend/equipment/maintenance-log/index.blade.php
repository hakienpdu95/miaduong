@extends('layouts.backend')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card mb-4">
            <div class="card-body">
                <form id="filter-form">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filter_maintenance_type_id">Loại bảo trì</label>
                            <select class="form-select choices" id="filter_maintenance_type_id" name="filter_maintenance_type_id">
                                <option value="">Tất cả</option>
                                @foreach($maintenanceTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name ?? $type->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_start_date">Ngày bắt đầu</label>
                            <input type="text" class="form-control flatpickr" id="filter_start_date" name="filter_start_date">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_status">Trạng thái</label>
                            <select class="form-select choices" id="filter_status" name="filter_status">
                                <option value="">Tất cả</option>
                                <option value="operating_active">Đang hoạt động</option>
                                <option value="under_repair">Đang sửa chữa</option>
                                <option value="broken_damaged">Đã hỏng</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                            <button type="button" id="reset-filter" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table id="maintenance-logs-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Loại bảo trì</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Người thực hiện</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/dataTables.css', 'resources/css/flatpickr.css', 'resources/css/choices.css'], 'build/backend')
@endpush

@push('scripts')
    @vite(['resources/js/modules/dataTables.js', 'resources/js/modules/flatpickr.js', 'resources/js/modules/choices.js'], 'build/backend')
    <script>
        window.addEventListener('load', function() {
            $(document).ready(function() {
                // Init flatpickr for filter date
                $('#filter_start_date').flatpickr({
                    dateFormat: 'Y-m-d',
                });

                // Init Choices for selects
                const choicesElements = document.querySelectorAll('.choices');
                choicesElements.forEach(element => {
                    new Choices(element, {
                        searchEnabled: true,
                        itemSelectText: '',
                        shouldSort: false,
                    });
                });

                let table = $('#maintenance-logs-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: '{{ route('api.maintenance-log.datatable', ['equipmentQrCodeId' => $equipment_qr_code->id]) }}',
                        data: function(d) {
                            d.filter_maintenance_type_id = $('#filter_maintenance_type_id').val();
                            d.filter_start_date = $('#filter_start_date').val();
                            d.filter_status = $('#filter_status').val();
                        }
                    },
                    columns: [
                        { data: 'maintenance_type_name', name: 'maintenance_type_id' },
                        { data: 'start_date', name: 'start_date' },
                        { data: 'end_date', name: 'end_date' },
                        { data: 'performer', name: 'performer' },
                        { data: 'status_label', name: 'status' },
                        { data: 'formatted_created_at', name: 'created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    order: [[5, 'desc']], // Order by created_at desc
                    pageLength: 25,
                    language: {
                        processing: 'Đang tải...',
                        search: 'Tìm kiếm:',
                        lengthMenu: 'Hiển thị _MENU_ dòng',
                        paginate: { first: 'Đầu', last: 'Cuối', next: 'Tiếp', previous: 'Trước' }
                    },
                    initComplete: function() {
                        // Debounce global search
                        let searchInput = $('.dataTables_filter input');
                        let searchWait = 0;
                        searchInput.unbind().bind('input', function(e) {
                            let term = this.value;
                            clearTimeout(searchWait);
                            searchWait = setTimeout(function() {
                                table.search(term).draw();
                            }, 300);
                        });
                    }
                });

                // Handle filter form submit with debounce
                let filterTimeout;
                $('#filter-form input, #filter-form select').on('input change', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(function() {
                        table.draw();
                    }, 300);
                });
                $('#filter-form').on('submit', function(e) {
                    e.preventDefault();
                    table.draw();
                });

                // Reset filters
                $('#reset-filter').on('click', function() {
                    $('#filter-form')[0].reset();
                    // Reset choices if needed
                    choicesElements.forEach(element => {
                        element.setChoiceByValue('');
                    });
                    table.draw();
                });

                // Delete action
                $('#maintenance-logs-table').on('click', '.delete-maintenance-log', function() {
                    let id = $(this).data('id');
                    if (confirm('Bạn có chắc muốn xóa nhật ký này?')) {
                        $.ajax({
                            url: '{{ route('api.maintenance-log.destroy', ['equipmentQrCodeId' => $equipment_qr_code->id, 'id' => '_id_']) }}'.replace('_id_', id),
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                if (response.success) {
                                    table.draw(false);
                                    Toastify({ text: response.message, duration: 3000, style: { background: "green" } }).showToast();
                                }
                            },
                            error: function(xhr) {
                                Toastify({ text: 'Lỗi khi xóa: ' + xhr.responseJSON.message, duration: 3000, style: { background: "red" } }).showToast();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush