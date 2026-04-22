<?php

namespace App\Http\Requests;

use App\Models\Marketer;
use Illuminate\Foundation\Http\FormRequest;

class RegisterMarketerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone'        => [
                'required',
                'string',
                'regex:/^218(91|92|93|94)\d{7}$/',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $last9 = substr(preg_replace('/\D/', '', $value), -9);
                    if (Marketer::where('phone', 'like', '%' . $last9)->exists()) {
                        $fail('رقم الهاتف مسجّل مسبقاً.');
                    }
                },
            ],
            'backup_phone' => ['nullable', 'string', 'regex:/^218(91|92|93|94)\d{7}$/'],
            'email'        => 'nullable|email|unique:marketers,email|max:255',
            'password'     => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex'        => 'رقم الهاتف يجب أن يبدأ بـ 91 أو 92 أو 93 أو 94 ويكون 9 أرقام.',
            'backup_phone.regex' => 'رقم الهاتف الاحتياطي يجب أن يبدأ بـ 91 أو 92 أو 93 أو 94 ويكون 9 أرقام.',
        ];
    }
}
