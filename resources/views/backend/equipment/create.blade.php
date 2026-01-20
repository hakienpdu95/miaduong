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
                <form id="import-form" action="{{ route('equipment.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="sku" class="form-label">Mã SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sku" name="sku" placeholder="SKU-00001">
                    </div>
                    <div class="mb-3">
                        <label for="unit_type" class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
                        <select class="form-select" id="unit_type" name="unit_type">
                            <option value="box" selected>Hộp</option>
                            <option value="set_kit">Bộ</option>
                            <option value="device_equipment">Thiết bị</option>
                            <option value="piece_item">Cái</option>
                            <option value="unit_piece">Chiếc</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="import_method" class="form-label">Phương pháp nhập <span class="text-danger">*</span></label>
                        <select class="form-select" id="import_method" name="import_method">
                            <option value="single_item" selected>Đơn chiếc</option>
                            <option value="batch_series">Hàng Loạt</option>
                        </select>
                    </div>
                    <div id="quantity-section" class="mb-3 d-none">
                        <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1">
                    </div>
                    <div id="equipments-container"></div>
                    <button type="submit" class="btn btn-primary">Tạo mới</button>
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
            const countries = {!! json_encode($countries->pluck('name', 'id')) !!};
            const units = {!! json_encode($units->pluck('name', 'id')) !!};
            const today = new Date().toISOString().split('T')[0];

            function generateEquipmentGroup(index, isSingle = false) {
                const namePrefix = isSingle ? '' : `equipments[${index}]`;
                const idPrefix = isSingle ? '' : `equipments_${index}_`;
                const nameField = (field) => isSingle ? field : `${namePrefix}[${field}]`;

                return `
                    <div class="equipment-group border p-3 mb-3" data-index="${index}">
                        <h5>Thiết bị ${index + 1}</h5>
                        <div class="mb-3">
                            <label for="${idPrefix}name" class="form-label">Tên thiết bị <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="${nameField('name')}" id="${idPrefix}name">
                        </div>
                        <div class="mb-3">
                            <label for="${idPrefix}image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" name="${nameField('image')}" id="${idPrefix}image">
                        </div>
                        <div class="mb-3">
                            <label for="${idPrefix}import_date" class="form-label">Ngày nhập thiết bị</label>
                            <input type="text" class="form-control flatpickr" name="${nameField('import_date')}" id="${idPrefix}import_date" value="${today}">
                        </div>
                        <div class="mb-3">
                            <label for="${idPrefix}country_id" class="form-label">Xuất xứ</label>
                            <select class="choices-select form-control" name="${nameField('country_id')}" id="${idPrefix}country_id">
                                <option selected="true" disabled="disabled">Chọn xuất xứ</option>
                                ${Object.entries(countries).map(([id, name]) => `<option value="${id}">${name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="${idPrefix}unit_id" class="form-label">Đơn vị sử dụng thiết bị <span class="text-danger">*</span></label>
                            <select class="choices-select form-control" name="${nameField('unit_id')}" id="${idPrefix}unit_id">
                                <option selected="true" disabled="disabled">Chọn đơn vị</option>
                                ${Object.entries(units).map(([id, name]) => `<option value="${id}">${name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="${idPrefix}attachment" class="form-label">File đính kèm</label>
                            <div class="suneditor-block border rounded" id="attachment-editor-${index}" data-input="attachment-input-${index}" data-index="${index}-attachment" data-height="200px"></div>
                            <textarea id="attachment-input-${index}" class="d-none" name="${nameField('attachment')}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="${idPrefix}additional_info" class="form-label">Thông tin bổ sung</label>
                            <div class="suneditor-block border rounded" id="additional-info-editor-${index}" data-input="additional-info-input-${index}" data-index="${index}-additional" data-height="200px"></div>
                            <textarea id="additional-info-input-${index}" class="d-none" name="${nameField('additional_info')}"></textarea>
                        </div>
                    </div>
                `;
            }

            function initEditorsAndPickers(container) {
                // Init suneditor and flatpickr for new elements
                container.find('.suneditor-block').each(function() {
                    // Use the exposed window.initializeSunEditor from suneditor.js
                    window.initializeSunEditor(this);
                });
                container.find('.flatpickr').flatpickr({ dateFormat: 'Y-m-d' });
                // Init Choices for dynamic selects
                container.find('.choices-select').each(function() {
                    // Assuming Choices is exposed globally via choices.js, or adjust if needed
                    if (!this.choices) { // Prevent re-initialization
                        new Choices(this);
                    }
                });
            }

            $('#import_method').on('change', function() {
                const method = $(this).val();
                $('#quantity-section').toggleClass('d-none', method !== 'batch_series');
                $('#equipments-container').empty();
                if (method === 'single_item') {
                    const group = $(generateEquipmentGroup(0, true));
                    $('#equipments-container').append(group);
                    initEditorsAndPickers(group);
                }
            });

            $('#quantity').on('change', function() {
                const qty = parseInt($(this).val()) || 0;
                $('#equipments-container').empty();
                for (let i = 0; i < qty; i++) {
                    const group = $(generateEquipmentGroup(i));
                    $('#equipments-container').append(group);
                    initEditorsAndPickers(group);
                }
            });

            // Initial load for single_item
            $('#import_method').trigger('change');

            $('#import-form').on('submit', function(e) {
                // Client-side validation similar to unit example
                let valid = true;
                const errors = [];
                // Check common fields
                if (!$('#sku').val().trim()) {
                    valid = false;
                    errors.push('Mã SKU là bắt buộc!');
                }
                // Check equipment groups
                $('.equipment-group').each(function() {
                    const group = $(this);
                    const nameInput = group.find('input[id$="name"]');
                    const unitSelect = group.find('select[id$="unit_id"]');
                    if (!nameInput.val().trim()) {
                        valid = false;
                        errors.push(`Tên thiết bị trong group ${parseInt(group.data('index')) + 1} là bắt buộc!`);
                    }
                    if (!unitSelect.val()) {
                        valid = false;
                        errors.push(`Đơn vị sử dụng trong group ${parseInt(group.data('index')) + 1} là bắt buộc!`);
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
                }
            });
        });
    });
</script>
@endpush