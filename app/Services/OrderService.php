<?php

namespace App\Services;

use App\DTOs\FailDeliveryData;
use Illuminate\Http\UploadedFile;
use App\Models\AgentTransaction;
use App\Models\Marketer;
use App\Models\MarketerTransaction;
use App\Models\Order;
use App\Models\ProductQuantityLog;
use App\Models\Vault;
use App\Models\VaultTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create(Marketer $marketer, array $data, Collection $cartItems): Order
    {
        return DB::transaction(function () use ($marketer, $data, $cartItems) {
            $productsTotal = collect($cartItems)->sum(fn($item) => $item->quantity * (float) $item->selling_price);
            $deliveryCost  = (float) ($data['delivery_cost'] ?? 0);

            $hasDeposit   = !empty($data['has_deposit']);
            $depositProof = null;
            if ($hasDeposit && isset($data['deposit_proof']) && $data['deposit_proof'] instanceof UploadedFile) {
                $depositProof = $data['deposit_proof']->store('deposits', 'public');
            }

            $order = Order::create([
                'marketer_id'    => $marketer->id,
                'customer_name'  => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_phone2'=> $data['customer_phone2'] ?? null,
                'address'        => $data['address'],
                'notes'          => $data['notes'] ?? null,
                'city_id'        => $data['city_id'],
                'city_name'      => $data['city_name'],
                'sub_city_id'    => $data['sub_city_id'] ?? null,
                'sub_city_name'  => $data['sub_city_name'] ?? null,
                'delivery_cost'  => $deliveryCost,
                'products_total' => $productsTotal,
                'grand_total'    => $productsTotal + $deliveryCost,
                'status'         => 'pending',
                'has_deposit'    => $hasDeposit,
                'deposit_amount' => $hasDeposit ? ($data['deposit_amount'] ?? null) : null,
                'deposit_payer'  => $hasDeposit ? ($data['deposit_payer'] ?? null) : null,
                'deposit_proof'  => $depositProof,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id'             => $item->product_id,
                    'product_name'           => $item->product->name,
                    'product_price'          => (float) $item->selling_price,
                    'product_cost'           => (float) $item->product->price,
                    'product_supplier_cost'  => (float) $item->product->cost_price,
                    'quantity'               => $item->quantity,
                    'total'                  => $item->quantity * (float) $item->selling_price,
                ]);
            }

            $productNames = collect($cartItems)->map(fn($i) => $i->product->name)->implode('، ');
            $order->logs()->create([
                'action'      => 'created',
                'description' => "تم إنشاء الطلب #{$order->id} — {$productNames}",
            ]);

            return $order;
        });
    }

    public function approve(Order $order, ?int $vaultId = null): void
    {
        DB::transaction(function () use ($order, $vaultId) {
            $admin = Auth::guard('web')->user();

            $order->update([
                'status'      => 'processing',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);

            $order->logs()->create([
                'action'      => 'approved',
                'description' => "تمت الموافقة على الطلب بواسطة {$admin->name} — الطلب قيد التجهيز.",
            ]);

            if ($order->has_deposit && $order->deposit_payer === 'company' && $vaultId && $order->deposit_amount > 0) {
                $vault        = Vault::findOrFail($vaultId);
                $depositAmt   = (float) $order->deposit_amount;
                $balanceAfter = (float) $vault->current_balance + $depositAmt;

                $vault->update(['current_balance' => $balanceAfter]);

                VaultTransaction::create([
                    'vault_id'       => $vault->id,
                    'user_id'        => $admin->id,
                    'type'           => 'deposit',
                    'recipient_name' => trim($order->marketer->first_name . ' ' . $order->marketer->last_name),
                    'description'    => "عربون طلب #{$order->id}",
                    'amount'         => $depositAmt,
                    'date'           => now()->toDateString(),
                    'balance_after'  => $balanceAfter,
                ]);
            }

            $order->load('items.product');

            foreach ($order->items as $item) {
                $product = $item->product;
                if (! $product) {
                    continue;
                }
                $quantityAfter = max(0, $product->quantity - $item->quantity);
                $product->update(['quantity' => $quantityAfter]);

                ProductQuantityLog::create([
                    'product_id'     => $product->id,
                    'user_id'        => $admin->id,
                    'type'           => 'subtract',
                    'quantity'       => $item->quantity,
                    'quantity_after' => $quantityAfter,
                    'notes'          => "خصم عند الموافقة على طلب #{$order->id}",
                ]);
            }

            $costTotal = $order->items->sum(fn($i) => $i->quantity * (float) $i->product_cost);

            if ($costTotal > 0) {
                $marketer      = $order->marketer;
                $balanceAfter  = (float) $marketer->balance - $costTotal;
                $productNames  = $order->items->map(fn($i) => $i->product_name)->implode('، ');

                $marketer->update(['balance' => $balanceAfter]);

                MarketerTransaction::create([
                    'marketer_id'    => $marketer->id,
                    'user_id'        => $admin->id,
                    'type'           => 'withdrawal',
                    'recipient_name' => $marketer->first_name . ' ' . $marketer->last_name,
                    'description'    => "خصم تكلفة منتجات طلب #{$order->id} — {$productNames}",
                    'amount'         => $costTotal,
                    'date'           => now()->toDateString(),
                    'balance_after'  => $balanceAfter,
                ]);
            }
        });
    }

    public function markDelivered(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update(['status' => 'delivered']);

            $order->logs()->create([
                'action'      => 'delivered',
                'description' => 'تم تسليم الطلب للزبون بنجاح.',
            ]);

            $marketer          = $order->marketer;
            $marketerAmount    = (float) $order->products_total;
            $marketerBalance   = (float) $marketer->balance + $marketerAmount;

            $marketer->update(['balance' => $marketerBalance]);

            MarketerTransaction::create([
                'marketer_id'    => $marketer->id,
                'user_id'        => Auth::guard('web')->id(),
                'type'           => 'deposit',
                'recipient_name' => $marketer->first_name . ' ' . $marketer->last_name,
                'description'    => "إيداع إجمالي مبيعات طلب #{$order->id}",
                'amount'         => $marketerAmount,
                'date'           => now()->toDateString(),
                'balance_after'  => $marketerBalance,
            ]);

            if ($order->agent_id) {
                $agent        = $order->agent;
                $agentAmount  = (float) $order->products_total;
                $agentBalance = (float) $agent->balance + $agentAmount;

                $agent->update(['balance' => $agentBalance]);

                AgentTransaction::create([
                    'agent_id'      => $agent->id,
                    'user_id'       => Auth::guard('web')->id(),
                    'vault_id'      => null,
                    'type'          => 'deposit',
                    'description'   => "استلام قيمة طلب #{$order->id} من الزبون {$order->customer_name}",
                    'amount'        => $agentAmount,
                    'date'          => now()->toDateString(),
                    'balance_after' => $agentBalance,
                ]);
            }
        });
    }

    public function markFailedDelivery(Order $order, FailDeliveryData $data): void
    {
        $order->update([
            'status'                   => 'returning',
            'delivery_failure_reason'  => $data->label,
        ]);

        $order->logs()->create([
            'action'      => 'delivery_failed',
            'description' => "تعذر تسليم الطلب — السبب: {$data->label}",
        ]);
    }

    public function markReturned(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load('items.product');

            $order->update(['status' => 'returned']);

            $adminId = Auth::guard('web')->id();

            foreach ($order->items as $item) {
                $product = $item->product;

                if (! $product) {
                    continue;
                }

                $quantityAfter = $product->quantity + $item->quantity;
                $product->update(['quantity' => $quantityAfter]);

                ProductQuantityLog::create([
                    'product_id'     => $product->id,
                    'user_id'        => $adminId,
                    'type'           => 'add',
                    'quantity'       => $item->quantity,
                    'quantity_after' => $quantityAfter,
                    'notes'          => "استرداد من طلب #{$order->id}",
                ]);
            }

            $order->logs()->create([
                'action'      => 'returned',
                'description' => "تم استلام الطلب المسترد وإعادة الكميات للمخزون.",
            ]);

            $marketer  = $order->marketer;
            $adminId   = Auth::guard('web')->id();
            $today     = now()->toDateString();

            $costTotal = $order->items->sum(
                fn($i) => $i->quantity * ((float) $i->product_cost ?: (float) ($i->product?->price ?? 0))
            );

            if ($costTotal > 0) {
                $balanceAfterRefund = (float) $marketer->balance + $costTotal;
                $marketer->update(['balance' => $balanceAfterRefund]);

                MarketerTransaction::create([
                    'marketer_id'    => $marketer->id,
                    'user_id'        => $adminId,
                    'type'           => 'deposit',
                    'recipient_name' => $marketer->first_name . ' ' . $marketer->last_name,
                    'description'    => "استرداد تكلفة منتجات طلب #{$order->id} المسترد",
                    'amount'         => $costTotal,
                    'date'           => $today,
                    'balance_after'  => $balanceAfterRefund,
                ]);

                $marketer->refresh();
            }

            $returnFee          = 15.0;
            $balanceAfterFee    = (float) $marketer->balance - $returnFee;
            $marketer->update(['balance' => $balanceAfterFee]);

            MarketerTransaction::create([
                'marketer_id'    => $marketer->id,
                'user_id'        => $adminId,
                'type'           => 'withdrawal',
                'recipient_name' => $marketer->first_name . ' ' . $marketer->last_name,
                'description'    => "عمولة تجهيز طلب مسترد #{$order->id}",
                'amount'         => $returnFee,
                'date'           => $today,
                'balance_after'  => $balanceAfterFee,
            ]);
        });
    }
}
