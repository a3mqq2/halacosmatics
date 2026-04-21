<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('marketer')?->id;

        return [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'backup_phone' => 'nullable|string|max:20',
            'email'        => "nullable|email|unique:marketers,email,{$id}|max:255",
            'username'     => "required|string|unique:marketers,username,{$id}|max:255",
            'password'     => 'nullable|string|min:8|confirmed',
            'passport'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }
}
