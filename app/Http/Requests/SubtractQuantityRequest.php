<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubtractQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $max = $this->route('product')?->quantity ?? 0;

        return [
            'quantity' => "required|integer|min:1|max:{$max}",
            'notes'    => 'nullable|string|max:255',
        ];
    }
}
