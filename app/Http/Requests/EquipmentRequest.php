<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ImportBatch;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'sku' => ['required', 'string', 'max:255', 'unique:import_batches,sku'],
            'unit_type' => ['required', 'string', Rule::in(['box', 'set_kit', 'device_equipment', 'piece_item', 'unit_piece'])],
            'import_method' => ['required', 'string', Rule::in(['single_item', 'batch_series'])],
        ];

        if ($this->import_method === 'single_item') {
            $rules += [
                'name' => ['required', 'string', 'max:255'],
                'image' => ['nullable', 'file', 'image', 'max:2048'],
                'import_date' => ['nullable', 'date'],
                'country_id' => ['nullable', 'integer', 'exists:country,id'],
                'unit_id' => ['required', 'integer', 'exists:units,id'],
                'attachment' => ['nullable', 'string'],
                'additional_info' => ['nullable', 'string'],
            ];
        } else {
            $rules += [
                'quantity' => ['required', 'integer', 'min:1'],
                'equipments' => ['required', 'array', 'size:' . $this->quantity],
                'equipments.*.name' => ['required', 'string', 'max:255'],
                'equipments.*.image' => ['nullable', 'file', 'image', 'max:2048'],
                'equipments.*.import_date' => ['nullable', 'date'],
                'equipments.*.country_id' => ['nullable', 'integer', 'exists:country,id'],
                'equipments.*.unit_id' => ['required', 'integer', 'exists:units,id'],
                'equipments.*.attachment' => ['nullable', 'string'],
                'equipments.*.additional_info' => ['nullable', 'string'],
            ];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if (empty($this->sku)) {
            $maxId = ImportBatch::max('id') ?? 0;
            $generatedSku = 'SKU-' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
            $this->merge(['sku' => $generatedSku]);
        }
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'Mã SKU là bắt buộc.',
            'sku.unique' => 'Mã SKU đã tồn tại.',
            'unit_type.required' => 'Đơn vị tính là bắt buộc.',
            'import_method.required' => 'Phương pháp nhập là bắt buộc.',
            'name.required' => 'Tên thiết bị là bắt buộc.',
            'unit_id.required' => 'Đơn vị sử dụng là bắt buộc.',
            'equipments.*.name.required' => 'Tên thiết bị là bắt buộc.',
            'equipments.*.unit_id.required' => 'Đơn vị sử dụng là bắt buộc.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        dd($validator->errors());
        session()->flash('error', 'Vui lòng kiểm tra lỗi nhập liệu.');
        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }
}