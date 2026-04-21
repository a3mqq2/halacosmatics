<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAgentTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'        => 'required|in:deposit,withdrawal',
            'description' => 'required|string|max:500',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'vault_id'    => 'required_if:type,withdrawal|nullable|exists:vaults,id',
        ];
    }
}
