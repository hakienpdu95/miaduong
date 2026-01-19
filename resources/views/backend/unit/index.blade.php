@extends('layouts.backend') 

@section('content') 
<div class="row"> 
    <div class="col-md-12"> 
        <div class="card mb-4"> 
            <div class="card-body"> 
                <form id="filter-form" class="mb-3"> 
                    <div class="row mb-3"> 
                        <div class="col-md-3"> 
                            <label for="filter_code">Mã đơn vị</label> 
                            <input type="text" class="form-control" id="filter_code" name="filter_code"> 
                        </div> 
                        <div class="col-md-3"> 
                            <label for="filter_name">Tên đơn vị</label> 
                            <input type="text" class="form-control" id="filter_name" name="filter_name"> 
                        </div>  
                    </div> 
                    <div class="row">
                        <!-- Add more filters here --> 
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
                <table id="units-table" class="table table-bordered table-striped"> 
                    <thead> 
                        <tr> 
                            <th>ID</th> 
                            <th>Mã đơn vị</th> 
                            <th>Tên đơn vị</th> 
                            <th>Tên quản đốc</th> 
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
                let table = $('#units-table').DataTable({ 
                    processing: true, 
                    serverSide: true, 
                    responsive: true, // Thêm: Auto responsive mobile 
                    ajax: { 
                        url: '{{ route('api.units.datatable') }}', 
                        data: function(d) { 
                            d.filter_code = $('#filter_code').val(); 
                            d.filter_name = $('#filter_name').val(); 
                        } 
                    }, 
                    columns: [ 
                        { data: 'id', name: 'id' }, 
                        { data: 'code', name: 'code' }, 
                        { data: 'name', name: 'name' }, 
                        { data: 'supervisor_name', name: 'supervisor_name' }, 
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
                        // Debounce global search (thêm: Delay 300ms để giảm request khi type nhanh) 
                        let searchInput = $('.dataTables_filter input'); 
                        let searchWait = 0; 
                        searchInput.unbind().bind('input', function(e) { 
                            let term = this.value; 
                            clearTimeout(searchWait); 
                            searchWait = setTimeout(function() { table.search(term).draw(); }, 300); 
                        }); 
                    } 
                }); 

                // Handle filter form submit 
                $('#filter-form').on('submit', function(e) { 
                    e.preventDefault(); 
                    table.draw(); 
                }); 

                // Reset filters 
                $('#reset-filter').on('click', function() { 
                    $('#filter-form')[0].reset(); 
                    table.draw(); 
                }); 

                // Bổ sung delete action (AJAX + confirm) 
                $('#units-table').on('click', '.delete-unit', function() { 
                    let id = $(this).data('id'); 
                    if (confirm('Bạn có chắc muốn xóa đơn vị này?')) { // Hoặc dùng sweetalert nếu có 
                        $.ajax({ 
                            url: '{{ route('api.units.destroy', '_id_') }}'.replace('_id_', id),
                            type: 'DELETE', 
                            data: { _token: '{{ csrf_token() }}' }, // CSRF for security 
                            success: function(response) { 
                                if (response.success) { 
                                    table.draw(false); // Reload without reset page/sort 
                                    // Toast success nếu có toastify 
                                    console.log('Deleted successfully'); 
                                } 
                            }, 
                            error: function(xhr) { 
                                alert('Lỗi khi xóa: ' + xhr.responseJSON.message); 
                            } 
                        }); 
                    } 
                }); 
            }); 
        }); 
    </script> 
@endpush 