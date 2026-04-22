<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketerRequest extends FormRequest
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
            'phone'        => 'required|string|max:20',
            'backup_phone' => 'nullable|string|max:20',
            'email'        => 'nullable|email|unique:marketers,email|max:255',
            'password'     => 'required|string|min:8|confirmed',
        ];
    }
}
