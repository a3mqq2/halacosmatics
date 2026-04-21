<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class CreateProductDTO
{
    public function __construct(
        public string  $name,
        public ?string $code,
        public int     $quantity,
        public int     $price,
        public int     $costPrice,
        public array   $images,
        public int     $primaryIndex,
        public ?string $description,
        public bool    $isActive,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            name:         $request->name,
            code:         $request->code,
            quantity:     (int) $request->quantity,
            price:        (int) $request->price,
            costPrice:    (int) $request->input('cost_price', 0),
            images:       $request->file('images', []),
            primaryIndex: (int) $request->input('primary_index', 0),
            description:  $request->description,
            isActive:     $request->boolean('is_active', true),
        );
    }
}
