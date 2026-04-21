<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FailDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|in:phone_off,wrong_number,forwarded,didnt_order,busy,other',
            'notes'  => 'nullable|string|max:300|required_if:reason,other',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required'    => 'يجب اختيار سبب التعذر.',
            'reason.in'          => 'السبب المختار غير صالح.',
            'notes.required_if'  => 'يجب إدخال التفاصيل عند اختيار "أخرى".',
            'notes.max'          => 'الملاحظات يجب ألا تتجاوز 300 حرف.',
        ];
    }
}
