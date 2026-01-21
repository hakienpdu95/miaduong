<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MaintenanceLogRequest extends FormRequest
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
            'maintenance_type_id' => ['required', 'exists:maintenance_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'performer' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['operating_active', 'under_repair', 'broken_damaged'])],
            'setup_time' => ['nullable', 'date'],
        ];

        return $rules;
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'maintenance_type_id.required' => 'Loại bảo trì là bắt buộc.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'performer.required' => 'Người thực hiện là bắt buộc.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ];
    }

    /**
     * Handle failed validation (custom redirect with error flash).
     */
    protected function failedValidation(Validator $validator): void
    {
        session()->flash('error', 'Vui lòng kiểm tra lỗi nhập liệu.');
        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }
}