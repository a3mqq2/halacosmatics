<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryAreaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:100',
            'price' => 'required|integer|min:0|max:9999',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'اسم المنطقة مطلوب.',
            'price.required' => 'سعر التوصيل مطلوب.',
            'price.integer'  => 'السعر يجب أن يكون رقماً صحيحاً.',
        ];
    }
}
