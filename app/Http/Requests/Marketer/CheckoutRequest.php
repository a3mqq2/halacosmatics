<?php

namespace App\Http\Requests\Marketer;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_phone2'=> 'nullable|string|max:20',
            'address'        => 'required|string|max:500',
            'notes'          => 'nullable|string|max:1000',
            'city_id'        => 'required|integer',
            'city_name'      => 'required|string|max:255',
            'sub_city_id'    => 'nullable|integer',
            'sub_city_name'  => 'nullable|string|max:255',
            'delivery_cost'  => 'required|numeric|min:0',
            'has_deposit'    => 'nullable|boolean',
            'deposit_amount' => 'nullable|required_if:has_deposit,1|integer|in:5,10,20,30',
            'deposit_payer'  => 'nullable|required_if:has_deposit,1|in:marketer,company',
            'deposit_proof'  => 'nullable|required_if:deposit_payer,company|file|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name'  => 'اسم الزبون',
            'customer_phone' => 'رقم الهاتف',
            'address'        => 'العنوان',
            'city_id'        => 'المدينة',
            'city_name'      => 'المدينة',
            'delivery_cost'  => 'رسوم التوصيل',
        ];
    }
}
