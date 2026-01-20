<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class EquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'sku' => ['required', 'string', 'max:100', Rule::unique('equipments', 'sku')],
            'unit_type' => ['required', Rule::in(['box', 'set_kit', 'device_equipment', 'piece_item', 'unit_piece'])],
            'import_method' => ['required', Rule::in(['single_item', 'batch_series'])],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'file', 'image', 'max:2048'], // Assuming 2MB max
            'import_date' => ['nullable', 'date'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'attachment' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
        ];

        if ($this->input('import_method') === 'batch_series') {
            $rules['quantity'] = ['required', 'integer', 'min:1'];
        }

        return $rules;
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'Mã SKU là bắt buộc.',
            'sku.unique' => 'Mã SKU đã tồn tại.',
            'unit_type.required' => 'Đơn vị tính là bắt buộc.',
            'import_method.required' => 'Phương pháp nhập là bắt buộc.',
            'name.required' => 'Tên thiết bị là bắt buộc.',
            'unit_id.required' => 'Đơn vị sử dụng là bắt buộc.',
            'quantity.required' => 'Số lượng là bắt buộc khi chọn Hàng Loạt.',
        ];
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator): void
    {
        Log::error('Validation failed: ' . json_encode($validator->errors()));
        session()->flash('error', 'Vui lòng kiểm tra lỗi nhập liệu.');
        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }
}