<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class AdjustQuantityDTO
{
    public function __construct(
        public int $quantity,
        public ?string $notes,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            quantity: (int) $request->quantity,
            notes:    $request->notes,
        );
    }
}
