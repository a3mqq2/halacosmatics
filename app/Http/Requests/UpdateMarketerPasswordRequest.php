<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketerPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'أدخلي كلمة المرور الحالية.',
            'password.required'         => 'أدخلي كلمة المرور الجديدة.',
            'password.min'              => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed'        => 'كلمة المرور الجديدة غير متطابقة.',
        ];
    }
}
