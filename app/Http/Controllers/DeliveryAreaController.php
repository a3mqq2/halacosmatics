<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryAreaRequest;
use App\Http\Requests\UpdateDeliveryAreaRequest;
use App\Models\DeliveryArea;
use App\Services\DeliveryAreaService;

class DeliveryAreaController extends Controller
{
    public function __construct(private DeliveryAreaService $service) {}

    public function index()
    {
        $areas = $this->service->list();

        return view('delivery_areas.index', compact('areas'));
    }

    public function create()
    {
        return view('delivery_areas.create');
    }

    public function store(StoreDeliveryAreaRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('delivery_areas.index')->with('success', 'تمت إضافة المنطقة بنجاح.');
    }

    public function edit(DeliveryArea $deliveryArea)
    {
        return view('delivery_areas.edit', ['area' => $deliveryArea]);
    }

    public function update(UpdateDeliveryAreaRequest $request, DeliveryArea $deliveryArea)
    {
        $this->service->update($deliveryArea, $request->validated());

        return redirect()->route('delivery_areas.index')->with('success', 'تم تحديث المنطقة بنجاح.');
    }

    public function destroy(DeliveryArea $deliveryArea)
    {
        $this->service->delete($deliveryArea);

        return redirect()->route('delivery_areas.index')->with('success', 'تم حذف المنطقة.');
    }
}
