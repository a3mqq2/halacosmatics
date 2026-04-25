<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketer\CheckoutRequest;
use App\Models\DeliveryArea;
use App\Services\CartService;
use App\Services\MosafirClient;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private MosafirClient $mosafirClient,
        private OrderService $orderService,
    ) {}

    public function index()
    {
        $marketer = Auth::guard('marketer')->user();
        $cart     = $this->cartService->getCartWithTotal($marketer);

        if ($cart['items']->isEmpty()) {
            return redirect()->route('marketer.cart');
        }

        $cities     = $this->mosafirClient->getPrices() ?? [];
        $localAreas = DeliveryArea::orderBy('price')->orderBy('name')->get();

        return view('marketer.checkout', compact('cart', 'cities', 'localAreas'));
    }

    public function store(CheckoutRequest $request)
    {
        $marketer = Auth::guard('marketer')->user();
        $cart     = $this->cartService->getCartWithTotal($marketer);

        if ($cart['items']->isEmpty()) {
            return redirect()->route('marketer.cart');
        }

        $data = $request->validated();
        if ($request->hasFile('payment_proof')) {
            $data['payment_proof'] = $request->file('payment_proof');
        }
        if ($request->hasFile('deposit_proof')) {
            $data['deposit_proof'] = $request->file('deposit_proof');
        }

        $order = $this->orderService->create($marketer, $data, $cart['items']);

        $this->cartService->clear($marketer);

        return redirect()->route('marketer.orders.show', $order)->with('success', 'تم إرسال طلبك بنجاح وهو قيد الموافقة.');
    }
}
