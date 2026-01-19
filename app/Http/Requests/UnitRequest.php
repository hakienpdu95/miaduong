<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Unit;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UnitRequest extends FormRequest
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
            'code' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'supervisor_name' => ['required', 'string', 'max:255'],
            'supervisor_phone' => ['nullable', 'string', 'max:20'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ];

        // Append unique chỉ ở create (POST), ignore ở edit (PUT/PATCH)
        if ($this->method() === 'POST') {
            $rules['code'][] = 'unique:units,code';
        } else {
            $rules['code'][] = Rule::unique('units', 'code')->ignore($id); 
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $code = $this->code;
        if (empty($code) && $this->method() === 'POST') { // Chỉ generate ở create
            $maxId = Unit::max('id') ?? 0;
            $generatedCode = 'DV-' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
            $this->merge(['code' => $generatedCode]);
        }
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên đơn vị là bắt buộc.',
            'supervisor_name.required' => 'Tên quản đốc là bắt buộc.',
            'code.unique' => 'Mã đơn vị đã tồn tại.',
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