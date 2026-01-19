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
                            <label for="filter_username">Tên tài khoản</label>
                            <input type="text" class="form-control" id="filter_username" name="filter_username">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_name">Tên nhân viên</label>
                            <input type="text" class="form-control" id="filter_name" name="filter_name">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_account_type">Loại tài khoản</label>
                            <select class="form-control" id="filter_account_type" name="filter_account_type">
                                <option value="">Tất cả</option>
                                <option value="warehouse_management">Quản lý kho</option>
                                <option value="foreman">Quản đốc</option>
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
                <table id="users-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Loại tài khoản</th>
                            <th>Đơn vị</th>
                            <th>Tên tài khoản</th>
                            <th>Tên nhân viên</th>
                            <th>Email</th>
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
@vite([ 'resources/css/dataTables.css' ], 'build/backend')
@endpush

@push('scripts')
@vite([ 'resources/js/modules/dataTables.js'], 'build/backend')
<script>
    window.addEventListener('load', function() {
        $(document).ready(function() {
            let table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('api.user.datatable') }}',
                    data: function(d) {
                        d.filter_username = $('#filter_username').val();
                        d.filter_name = $('#filter_name').val();
                        d.filter_account_type = $('#filter_account_type').val();
                    }
                },
                columns: [
                    { data: 'account_type', name: 'profile.account_type' },
                    { data: 'unit_name', name: 'profile.unit.name' },
                    { data: 'username', name: 'username' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'formatted_created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
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
                table.draw();
            });

            // Toggle active action
            $('#users-table').on('click', '.toggle-active', function() {
                let id = $(this).data('id');
                let currentActive = $(this).data('active');
                let confirmMsg = currentActive ? 'Bạn có chắc muốn khóa tài khoản này?' : 'Bạn có chắc muốn mở khóa tài khoản này?';
                if (confirm(confirmMsg)) {
                    $.ajax({
                        url: '{{ route('api.user.toggle-active', '_id_') }}'.replace('_id_', id),
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.draw(false);
                                Toastify({
                                    text: response.message,
                                    duration: 3000,
                                    style: { background: "green" }
                                }).showToast();
                            }
                        },
                        error: function(xhr) {
                            Toastify({
                                text: 'Lỗi khi cập nhật: ' + xhr.responseJSON.message,
                                duration: 3000,
                                style: { background: "red" }
                            }).showToast();
                        }
                    });
                }
            });

            // Reset password action
            $('#users-table').on('click', '.reset-password', function() {
                let id = $(this).data('id');
                if (confirm('Bạn có chắc muốn reset mật khẩu tài khoản này về mặc định?')) {
                    $.ajax({
                        url: '{{ route('api.user.reset-password', '_id_') }}'.replace('_id_', id),
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                Toastify({
                                    text: response.message,
                                    duration: 3000,
                                    style: { background: "green" }
                                }).showToast();
                            }
                        },
                        error: function(xhr) {
                            Toastify({
                                text: 'Lỗi khi reset: ' + xhr.responseJSON.message,
                                duration: 3000,
                                style: { background: "red" }
                            }).showToast();
                        }
                    });
                }
            });

            // Delete action
            $('#users-table').on('click', '.delete-user', function() {
                let id = $(this).data('id');
                if (confirm('Bạn có chắc muốn xóa tài khoản này?')) {
                    $.ajax({
                        url: '{{ route('api.user.destroy', '_id_') }}'.replace('_id_', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.draw(false);
                                Toastify({
                                    text: response.message,
                                    duration: 3000,
                                    style: { background: "green" }
                                }).showToast();
                            }
                        },
                        error: function(xhr) {
                            Toastify({
                                text: 'Lỗi khi xóa: ' + xhr.responseJSON.message,
                                duration: 3000,
                                style: { background: "red" }
                            }).showToast();
                        }
                    });
                }
            });
        });
    });
</script>
@endpush