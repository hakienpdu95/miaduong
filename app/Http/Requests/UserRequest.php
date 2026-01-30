<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
        $userId = $this->route('id') ?? null; // Lấy ID từ route parameter (cho update)

        $rules = [
            'account_type' => ['required', Rule::in(['warehouse_management', 'foreman'])],
            'unit_id' => ['required_if:account_type,foreman', 'exists:units,id'], // Chỉ required khi account_type là foreman
            'username' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'required_if:account_type,warehouse_management', 'string', 'max:255'],
            'email' => ['nullable'],
        ];

        // Rules khác nhau cho create (POST) và update (PATCH/PUT)
        if ($this->isMethod('post')) {
            // Create: Password required, username unique full
            $rules['password'] = ['required', 'confirmed', 'min:8'];
            $rules['username'][] = 'unique:users,username';
        } elseif ($this->isMethod('patch') || $this->isMethod('put')) {
            // Update: Password optional (nullable), username unique ignore current ID
            $rules['password'] = ['nullable', 'confirmed', 'min:8'];
            $rules['username'][] = Rule::unique('users')->ignore($userId);
        }

        return $rules;
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'account_type.required' => 'Loại tài khoản là bắt buộc.',
            'account_type.in' => 'Loại tài khoản không hợp lệ.',
            'unit_id.required_if' => 'Đơn vị là bắt buộc khi loại tài khoản là Quản đốc.',
            'unit_id.exists' => 'Đơn vị không tồn tại.',
            'username.required' => 'Tên tài khoản là bắt buộc.',
            'username.unique' => 'Tên tài khoản đã tồn tại.',
            'name.required_if' => 'Tên nhân viên là bắt buộc khi loại là Quản lý kho.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.confirmed' => 'Mật khẩu và nhập lại không khớp.',
            'password.min' => 'Mật khẩu phải ít nhất 8 ký tự.',
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