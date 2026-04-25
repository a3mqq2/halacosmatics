<?php

namespace App\Services;

use App\Models\DeliveryArea;
use Illuminate\Database\Eloquent\Collection;

class DeliveryAreaService
{
    public function list(): Collection
    {
        return DeliveryArea::orderBy('price')->orderBy('name')->get();
    }

    public function create(array $data): DeliveryArea
    {
        return DeliveryArea::create($data);
    }

    public function update(DeliveryArea $area, array $data): void
    {
        $area->update($data);
    }

    public function delete(DeliveryArea $area): void
    {
        $area->delete();
    }
}
