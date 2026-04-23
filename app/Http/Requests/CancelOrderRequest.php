<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancelled_reason' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'cancelled_reason.required' => 'يجب كتابة سبب الإلغاء.',
        ];
    }
}
