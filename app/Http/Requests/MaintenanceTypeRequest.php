<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\MaintenanceType;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MaintenanceTypeRequest extends FormRequest
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
        $id = $this->route('id'); // Match param {id}
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
        // Append unique chỉ ở create (POST), ignore ở edit (PUT/PATCH)
        if ($this->method() === 'POST') {
            $rules['name'][] = 'unique:maintenance_types,name';
        } else {
            $rules['name'][] = Rule::unique('maintenance_types', 'name')->ignore($id);
        }
        return $rules;
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên loại bảo trì là bắt buộc.',
            'name.unique' => 'Tên loại bảo trì đã tồn tại.',
        ];
    }

    /**
     * Handle failed validation (custom redirect with error flash).
     */
    protected function failedValidation(Validator $validator): void
    {
        // Log error để debug (optional, remove in production nếu không cần)
        Log::error('Validation failed: ' . json_encode($validator->errors()));
        // Flash error message chung nếu cần (hoặc để @error in view handle per field)
        session()->flash('error', 'Vui lòng kiểm tra lỗi nhập liệu.');
        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }
}