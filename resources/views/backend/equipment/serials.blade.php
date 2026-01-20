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
                <h5 class="card-title">Danh sách mã Serial của thiết bị: {{ $equipment->name }}</h5>
                <form id="filter-form">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filter_serial">Mã Serial</label>
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
                <table id="serials-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Mã QR Code</th>
                            <th>Mã Serial</th>
                            <th>Tên thiết bị</th>
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
@endpush

@push('scripts')
    @vite(['resources/js/modules/dataTables.js', 'resources/js/modules/qrcode-generator.js'], 'build/backend')
    <script>
        window.addEventListener('load', function() {
            $(document).ready(function() {
                let table = $('#serials-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: '{{ route('api.equipment-qr-codes.datatable', $equipment->id) }}',
                        data: function(d) {
                            d.filter_serial = $('#filter_serial').val();
                        }
                    },
                    columns: [
                        { data: 'qr_code', name: 'qr_code' },
                        { data: 'serial_number', name: 'serial_number' },
                        { data: 'equipment_name', name: 'equipment.name' },
                        { data: 'formatted_created_at', name: 'created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    order: [[1, 'asc']], // Order by serial
                    pageLength: 25, // Giới hạn page size để tối ưu, user có thể adjust
                    language: {
                        processing: 'Đang tải...',
                        search: 'Tìm kiếm:',
                        lengthMenu: 'Hiển thị _MENU_ dòng',
                        paginate: {
                            first: 'Đầu',
                            last: 'Cuối',
                            next: 'Tiếp',
                            previous: 'Trước'
                        }
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
                    },
                    drawCallback: function(settings) {
                        // Generate QR codes client-side sau khi draw table để tối ưu (chỉ generate cho rows visible)
                        $('.qr-code-placeholder').each(function() {
                            let url = $(this).data('url');
                            let typeNumber = 0; // Auto detect
                            let errorCorrectionLevel = 'L'; // Low
                            let qr = qrcode(typeNumber, errorCorrectionLevel);
                            qr.addData(url);
                            qr.make();
                            $(this).html(qr.createImgTag(2, 0)); // cellSize=2, margin=0 để small image
                        });
                    }
                });

                // Handle filter form submit with debounce
                let filterTimeout;
                $('#filter-form input').on('input', function() {
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
                    table.draw();
                });
            });
        });
    </script>
@endpush