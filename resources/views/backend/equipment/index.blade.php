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
                            <label for="filter_sku">Mã SKU</label>
                            <input type="text" class="form-control" id="filter_sku" name="filter_sku">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_name">Tên thiết bị</label>
                            <input type="text" class="form-control" id="filter_name" name="filter_name">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_unit_type">Đơn vị tính</label>
                            <select class="form-select choices" id="filter_unit_type" name="filter_unit_type">
                                <option value="">Tất cả</option>
                                <option value="box">Hộp</option>
                                <option value="set_kit">Bộ</option>
                                <option value="device_equipment">Thiết bị</option>
                                <option value="piece_item">Cái</option>
                                <option value="unit_piece">Chiếc</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_import_method">Phương pháp nhập</label>
                            <select class="form-select choices" id="filter_import_method" name="filter_import_method">
                                <option value="">Tất cả</option>
                                <option value="single_item">Đơn chiếc</option>
                                <option value="batch_series">Hàng Loạt</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3"> <!-- Row cho filter serial -->
                        <div class="col-md-3">
                            <label for="filter_serial">Số Serial</label>
                            <input type="text" class="form-control" id="filter_serial" name="filter_serial">
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
                <div class="mb-3"> <!-- Nút xuất Excel với id -->
                    <a id="export-all-btn" href="{{ route('admin.equipment.export-all') }}" class="btn btn-success">
                        <i class="fa fa-download"></i> Xuất Excel Danh Sách Thiết Bị
                    </a>
                </div>
                <table id="equipments-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Mã SKU</th>
                            <th>Tên thiết bị</th>
                            <th>Đơn vị tính</th>
                            <th>Phương pháp nhập</th>
                            <th>Ngày nhập</th>
                            <th>Đơn vị sử dụng</th>
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
    @vite(['resources/css/dataTables.css'], 'build/backend')
    @vite(['resources/css/choices.css'], 'build/backend')
@endpush

@push('scripts')
    @vite(['resources/js/modules/dataTables.js'], 'build/backend')
    @vite(['resources/js/modules/choices.js'], 'build/backend')
    <script>
        window.addEventListener('load', function() {
            $(document).ready(function() {
                // Init Choices for filter selects
                const choicesElements = document.querySelectorAll('.choices');
                choicesElements.forEach(element => {
                    new Choices(element, { searchEnabled: true, itemSelectText: '', shouldSort: false, });
                });

                let table = $('#equipments-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: '{{ route('api.equipments.datatable') }}',
                        data: function(d) {
                            d.filter_sku = $('#filter_sku').val();
                            d.filter_name = $('#filter_name').val();
                            d.filter_unit_type = $('#filter_unit_type').val();
                            d.filter_import_method = $('#filter_import_method').val();
                            d.filter_serial = $('#filter_serial').val(); // Nếu có filter serial
                        }
                    },
                    columns: [
                        { data: 'sku', name: 'sku' },
                        { data: 'name', name: 'name' },
                        { data: 'unit_type_label', name: 'unit_type' },
                        { data: 'import_method_label', name: 'import_method' },
                        { data: 'formatted_import_date', name: 'import_date' },
                        { data: 'unit_name', name: 'unit.name' },
                        { data: 'formatted_created_at', name: 'created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    order: [[0, 'desc']],
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
                            searchWait = setTimeout(function() { table.search(term).draw(); }, 300);
                        });
                    }
                });

                // Handle filter form submit with debounce
                let filterTimeout;
                $('#filter-form input, #filter-form select').on('input change', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(function() { table.draw(); }, 300);
                });

                $('#filter-form').on('submit', function(e) {
                    e.preventDefault();
                    table.draw();
                });

                // Reset filters
                $('#reset-filter').on('click', function() {
                    $('#filter-form')[0].reset();
                    // Reset Choices selects
                    choicesElements.forEach(element => { element.setChoiceByValue(''); });
                    table.draw();
                });

                // Delete action
                $('#equipments-table').on('click', '.delete-equipment', function() {
                    let id = $(this).data('id');
                    if (confirm('Bạn có chắc muốn xóa thiết bị này?')) {
                        $.ajax({
                            url: '{{ route('api.equipments.destroy', '_id_') }}'.replace('_id_', id),
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

                // Bổ sung: Xử lý click nút export với filters (append params vào URL)
                $('#export-all-btn').on('click', function(e) {
                    e.preventDefault();
                    let url = '{{ route('admin.equipment.export-all') }}?';
                    url += 'filter_sku=' + encodeURIComponent($('#filter_sku').val()) + '&';
                    url += 'filter_name=' + encodeURIComponent($('#filter_name').val()) + '&';
                    url += 'filter_unit_type=' + encodeURIComponent($('#filter_unit_type').val()) + '&';
                    url += 'filter_import_method=' + encodeURIComponent($('#filter_import_method').val()) + '&';
                    url += 'filter_serial=' + encodeURIComponent($('#filter_serial').val());
                    window.location.href = url;
                });
            });
        });
    </script>
@endpush