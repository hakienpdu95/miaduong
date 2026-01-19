<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Unit;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Hoặc check permission
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('unit'); // ID cho ignore unique nếu edit

        return [
            'code' => [ 
                'nullable',
                'string',
                'max:255',
                Rule::unique('units', 'code')->ignore($id), // Unique, ignore nếu edit (PUT/PATCH)
            ],
            'name' => 'required|string|max:255',
            'supervisor_name' => 'required|string|max:255',
            'supervisor_phone' => 'nullable|string|max:20',
            'quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $code = $this->code;
        if (empty($code)) {
            // Tự sinh code: DV-0000x (x = max id +1, efficient)
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
}