<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevertDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'revert_reason' => 'required|string|min:5|max:500',
        ];
    }

    public function attributes(): array
    {
        return [
            'revert_reason' => 'سبب التراجع',
        ];
    }
}
