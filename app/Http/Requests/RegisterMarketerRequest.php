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
                'max:20',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $last9 = substr(preg_replace('/\D/', '', $value), -9);
                    if (strlen($last9) < 9) return;
                    if (Marketer::where('phone', 'like', '%' . $last9)->exists()) {
                        $fail('رقم الهاتف مسجّل مسبقاً.');
                    }
                },
            ],
            'backup_phone' => 'nullable|string|max:20',
            'email'        => 'nullable|email|unique:marketers,email|max:255',
            'password'     => 'required|string|min:8|confirmed',
        ];
    }
}
