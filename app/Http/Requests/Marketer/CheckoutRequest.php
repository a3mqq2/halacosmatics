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
            'customer_name'   => 'required|string|max:255',
            'customer_phone'  => ['required', 'regex:/^09[1-4]\d{7}$/'],
            'customer_phone2' => ['nullable', 'regex:/^09[1-4]\d{7}$/'],
            'address'         => 'required|string|max:500',
            'notes'           => 'nullable|string|max:1000',

            'delivery_type'   => 'required|in:local,mosafir',
            'delivery_cost'   => 'required|numeric|min:0',

            'local_area_id'   => 'required_if:delivery_type,local|nullable|integer|exists:delivery_areas,id',

            'city_id'         => 'required_if:delivery_type,mosafir|nullable',
            'city_name'       => 'required|string|max:255',
            'sub_city_id'     => 'nullable|integer',
            'sub_city_name'   => 'nullable|string|max:255',

            'has_deposit'      => 'nullable|boolean',
            'deposit_amount'   => 'nullable|required_if:has_deposit,1|integer|in:5,10,20,30',
            'deposit_payer'    => 'nullable|required_if:has_deposit,1|in:marketer,company',
            'deposit_proof'    => 'nullable|required_if:deposit_payer,company|file|mimes:jpg,jpeg,png|max:5120',
            'payment_method'   => 'required|in:cash,bank_transfer',
            'payment_proof'    => 'nullable|required_if:payment_method,bank_transfer|file|mimes:jpg,jpeg,png|max:5120',
            'delivery_included'=> 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex'  => 'رقم الهاتف يجب أن يتكون من 10 أرقام ويبدأ بـ 091 أو 092 أو 093 أو 094',
            'customer_phone2.regex' => 'رقم الهاتف الاحتياطي يجب أن يتكون من 10 أرقام ويبدأ بـ 091 أو 092 أو 093 أو 094',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name'  => 'اسم الزبون',
            'customer_phone' => 'رقم الهاتف',
            'address'        => 'العنوان',
            'delivery_type'  => 'نوع التوصيل',
            'local_area_id'  => 'منطقة التوصيل',
            'city_id'        => 'المدينة',
            'city_name'      => 'المدينة',
            'delivery_cost'  => 'رسوم التوصيل',
            'payment_method' => 'طريقة الدفع',
            'payment_proof'  => 'إيصال التحويل',
        ];
    }
}
