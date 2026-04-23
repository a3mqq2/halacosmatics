<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVaultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:100',
            'code'            => 'nullable|string|max:20|unique:vaults,code',
            'opening_balance' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'اسم الخزينة مطلوب.',
            'opening_balance.min'  => 'الرصيد الافتتاحي يجب أن يكون 0 أو أكثر.',
        ];
    }
}
