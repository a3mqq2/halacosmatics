<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('product')?->id;

        return [
            'name'        => 'required|string|max:255',
            'code'        => "nullable|string|max:100|unique:products,code,{$id}",
            'price'       => 'required|integer|min:0',
            'cost_price'  => 'required|integer|min:0',
            'images'          => 'nullable|array|max:10',
            'images.*'        => 'file|mimes:jpg,jpeg,png,webp|max:5120',
            'primary_index'   => 'nullable|integer|min:0',
            'primary_existing'=> 'nullable|integer|exists:product_images,id',
            'delete_images'   => 'nullable|array',
            'delete_images.*' => 'integer|exists:product_images,id',
            'description'     => 'nullable|string',
            'is_active'       => 'boolean',
        ];
    }
}
