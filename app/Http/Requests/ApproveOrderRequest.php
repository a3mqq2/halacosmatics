<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $order = $this->route('order');

        if ($order->has_deposit && $order->deposit_payer === 'company') {
            return [
                'vault_id' => 'required|exists:vaults,id',
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'vault_id.required' => 'يجب اختيار الخزينة لتسجيل قيمة العربون.',
            'vault_id.exists'   => 'الخزينة المختارة غير موجودة.',
        ];
    }
}
