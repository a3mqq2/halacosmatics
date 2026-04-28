<?php

namespace App\Services;

use App\DTOs\FailDeliveryData;
use Illuminate\Http\UploadedFile;
use App\Models\Agent;
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

            $paymentMethod    = $data['payment_method'] ?? 'cash';
            $deliveryIncluded = $paymentMethod === 'bank_transfer' && ! empty($data['delivery_included']);
            $deliveryType     = $data['delivery_type'] ?? 'mosafir';
            $localAreaId      = $deliveryType === 'local' ? ($data['local_area_id'] ?? null) : null;

            $paymentProof = null;
            if ($paymentMethod === 'bank_transfer' && isset($data['payment_proof']) && $data['payment_proof'] instanceof UploadedFile) {
                $paymentProof = $data['payment_proof']->store('payment_proofs', 'public');
            }

            $hasDeposit   = $paymentMethod === 'cash' && ! empty($data['has_deposit']);
            $depositProof = null;
            if ($hasDeposit && isset($data['deposit_proof']) && $data['deposit_proof'] instanceof UploadedFile) {
                $depositProof = $data['deposit_proof']->store('deposits', 'public');
            }

            $order = Order::create([
                'marketer_id'      => $marketer->id,
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'customer_phone2'  => $data['customer_phone2'] ?? null,
                'address'          => $data['address'],
                'notes'            => $data['notes'] ?? null,
                'city_id'          => $data['city_id'],
                'city_name'        => $data['city_name'],
                'sub_city_id'      => $data['sub_city_id'] ?? null,
                'sub_city_name'    => $data['sub_city_name'] ?? null,
                'delivery_cost'    => $deliveryCost,
                'products_total'   => $productsTotal,
                'grand_total'      => $productsTotal + $deliveryCost,
                'status'           => 'pending',
                'payment_method'   => $paymentMethod,
                'payment_proof'    => $paymentProof,
                'delivery_included'=> $deliveryIncluded,
                'delivery_type'    => $deliveryType,
                'local_area_id'    => $localAreaId,
                'has_deposit'      => $hasDeposit,
                'deposit_amount'   => $hasDeposit ? ($data['deposit_amount'] ?? null) : null,
                'deposit_payer'    => $hasDeposit ? ($data['deposit_payer'] ?? null) : null,
                'deposit_proof'    => $depositProof,
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

            if ($order->payment_method === 'bank_transfer') {
                $bankVault = Vault::where('code', 'BNK')->first();

                if (! $bankVault) {
                    throw new \RuntimeException('الخزينة المصرفية (BNK) غير موجودة. يرجى إنشاؤها أولاً من إعدادات الخزائن.');
                }

                $vaultAmount     = $order->delivery_included
                    ? (float) $order->grand_total
                    : (float) $order->products_total;
                $newVaultBalance = (float) $bankVault->current_balance + $vaultAmount;

                $bankVault->update(['current_balance' => $newVaultBalance]);

                VaultTransaction::create([
                    'vault_id'       => $bankVault->id,
                    'user_id'        => $admin->id,
                    'type'           => 'deposit',
                    'recipient_name' => trim($order->marketer->first_name . ' ' . $order->marketer->last_name),
                    'description'    => "دفع مصرفي — طلب #{$order->id}" . ($order->delivery_included ? ' (شامل التوصيل)' : ' (منتجات فقط)'),
                    'amount'         => $vaultAmount,
                    'date'           => now()->toDateString(),
                    'balance_after'  => $newVaultBalance,
                ]);

                $marketer         = $order->marketer;
                $marketerBalance  = (float) $marketer->balance + (float) $order->products_total;
                $marketer->update(['balance' => $marketerBalance]);

                MarketerTransaction::create([
                    'marketer_id'    => $marketer->id,
                    'user_id'        => $admin->id,
                    'type'           => 'deposit',
                    'recipient_name' => trim($marketer->first_name . ' ' . $marketer->last_name),
                    'description'    => "إيداع دفعة مصرفية — طلب #{$order->id}",
                    'amount'         => (float) $order->products_total,
                    'date'           => now()->toDateString(),
                    'balance_after'  => $marketerBalance,
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

            $isBankTransfer = $order->payment_method === 'bank_transfer';
            $adminId        = Auth::guard('web')->id();

            if (! $isBankTransfer && ! $order->agent_id) {
                $marketer        = $order->marketer;
                $marketerAmount  = (float) $order->products_total;
                $marketerBalance = (float) $marketer->balance + $marketerAmount;

                $marketer->update(['balance' => $marketerBalance]);

                MarketerTransaction::create([
                    'marketer_id'    => $marketer->id,
                    'user_id'        => $adminId,
                    'type'           => 'deposit',
                    'recipient_name' => $marketer->first_name . ' ' . $marketer->last_name,
                    'description'    => "إيداع إجمالي مبيعات طلب #{$order->id}",
                    'amount'         => $marketerAmount,
                    'date'           => now()->toDateString(),
                    'balance_after'  => $marketerBalance,
                ]);
            }
        });
    }

    public function revertDelivery(Order $order, string $reason): void
    {
        DB::transaction(function () use ($order, $reason) {
            $order->load('items.product');

            $admin           = Auth::guard('web')->user();
            $adminId         = $admin->id;
            $isBankTransfer  = $order->payment_method === 'bank_transfer';
            $previousStatus  = $order->status;
            $newStatus       = ($order->agent_id || $order->mosafir_parcel_id) ? 'with_agent' : 'processing';
            $today           = now()->toDateString();

            $order->update(['status' => $newStatus, 'delivery_failure_reason' => null]);

            if ($previousStatus === 'delivered' && ! $isBankTransfer && ! $order->agent_id) {
                $marketer        = $order->marketer;
                $marketerAmount  = (float) $order->products_total;
                $marketerBalance = (float) $marketer->balance - $marketerAmount;

                $marketer->update(['balance' => $marketerBalance]);

                MarketerTransaction::create([
                    'marketer_id'    => $marketer->id,
                    'user_id'        => $adminId,
                    'type'           => 'withdrawal',
                    'recipient_name' => trim($marketer->first_name . ' ' . $marketer->last_name),
                    'description'    => "تراجع عن تسليم طلب #{$order->id} — تعديل إداري — السبب: {$reason}",
                    'amount'         => $marketerAmount,
                    'date'           => $today,
                    'balance_after'  => $marketerBalance,
                ]);
            }

            if ($previousStatus === 'returned') {
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if (! $product) {
                        continue;
                    }
                    $quantityAfter = max(0, $product->quantity - $item->quantity);
                    $product->update(['quantity' => $quantityAfter]);

                    ProductQuantityLog::create([
                        'product_id'     => $product->id,
                        'user_id'        => $adminId,
                        'type'           => 'subtract',
                        'quantity'       => $item->quantity,
                        'quantity_after' => $quantityAfter,
                        'notes'          => "تراجع عن استرداد طلب #{$order->id} — تعديل إداري",
                    ]);
                }

                $marketer  = $order->marketer;
                $costTotal = $order->items->sum(
                    fn($i) => $i->quantity * ((float) $i->product_cost ?: (float) ($i->product?->price ?? 0))
                );

                if ($costTotal > 0) {
                    $balanceAfter = (float) $marketer->balance - $costTotal;
                    $marketer->update(['balance' => $balanceAfter]);

                    MarketerTransaction::create([
                        'marketer_id'    => $marketer->id,
                        'user_id'        => $adminId,
                        'type'           => 'withdrawal',
                        'recipient_name' => trim($marketer->first_name . ' ' . $marketer->last_name),
                        'description'    => "تراجع — عكس استرداد تكلفة منتجات طلب #{$order->id}",
                        'amount'         => $costTotal,
                        'date'           => $today,
                        'balance_after'  => $balanceAfter,
                    ]);

                    $marketer->refresh();
                }

                $returnFee    = 15.0;
                $balanceAfter = (float) $marketer->balance + $returnFee;
                $marketer->update(['balance' => $balanceAfter]);

                MarketerTransaction::create([
                    'marketer_id'    => $marketer->id,
                    'user_id'        => $adminId,
                    'type'           => 'deposit',
                    'recipient_name' => trim($marketer->first_name . ' ' . $marketer->last_name),
                    'description'    => "تراجع — عكس عمولة تجهيز طلب مسترد #{$order->id}",
                    'amount'         => $returnFee,
                    'date'           => $today,
                    'balance_after'  => $balanceAfter,
                ]);
            }

            $statusLabels = [
                'delivered' => 'التسليم',
                'returning' => 'حالة قيد الاسترداد',
                'returned'  => 'استرداد الطلب',
            ];
            $label = $statusLabels[$previousStatus] ?? $previousStatus;

            $order->logs()->create([
                'action'      => 'revert_delivery',
                'description' => "تم بواسطة تعديل إداري — تراجع عن {$label} وإرجاع الطلب لحالة قيد التوصيل بواسطة {$admin->name} — السبب: {$reason}",
            ]);
        });
    }

    public function dispatchToAgent(Order $order, Agent $agent): void
    {
        DB::transaction(function () use ($order, $agent) {
            $isBankTransfer = $order->payment_method === 'bank_transfer';
            $adminId        = Auth::guard('web')->id();

            $agentAmount = ($isBankTransfer && $order->delivery_included)
                ? 0.0
                : ($isBankTransfer ? (float) $order->delivery_cost : (float) $order->products_total);

            if ($agentAmount > 0) {
                $agentBalance = (float) $agent->balance + $agentAmount;
                $agent->update(['balance' => $agentBalance]);

                AgentTransaction::create([
                    'agent_id'      => $agent->id,
                    'user_id'       => $adminId,
                    'vault_id'      => null,
                    'type'          => 'deposit',
                    'description'   => "عهدة طلب #{$order->id} — {$order->customer_name}",
                    'amount'        => $agentAmount,
                    'date'          => now()->toDateString(),
                    'balance_after' => $agentBalance,
                ]);
            }

            if (! $isBankTransfer) {
                $marketer        = $order->marketer;
                $marketerAmount  = (float) $order->products_total;
                $marketerBalance = (float) $marketer->balance + $marketerAmount;

                $marketer->update(['balance' => $marketerBalance]);

                MarketerTransaction::create([
                    'marketer_id'    => $marketer->id,
                    'user_id'        => $adminId,
                    'type'           => 'deposit',
                    'recipient_name' => trim($marketer->first_name . ' ' . $marketer->last_name),
                    'description'    => "إيداع مبيعات طلب #{$order->id} — تسليم للمندوب",
                    'amount'         => $marketerAmount,
                    'date'           => now()->toDateString(),
                    'balance_after'  => $marketerBalance,
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

    public function cancelByMarketer(Order $order): void
    {
        $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        $order->logs()->create([
            'action'      => 'cancelled',
            'description' => 'تم إلغاء الطلب من قِبَل المسوقة.',
        ]);
    }

    public function cancelByAdmin(Order $order, string $reason): void
    {
        DB::transaction(function () use ($order, $reason) {
            $order->load('items.product');

            $admin   = Auth::guard('web')->user();
            $adminId = $admin->id;

            $order->update([
                'status'           => 'cancelled',
                'cancelled_reason' => $reason,
                'cancelled_by'     => $adminId,
                'cancelled_at'     => now(),
            ]);

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
                    'notes'          => "استرداد عند إلغاء طلب #{$order->id}",
                ]);
            }

            $costTotal = $order->items->sum(fn($i) => $i->quantity * (float) $i->product_cost);

            if ($costTotal > 0) {
                $marketer     = $order->marketer;
                $balanceAfter = (float) $marketer->balance + $costTotal;
                $marketer->update(['balance' => $balanceAfter]);

                MarketerTransaction::create([
                    'marketer_id'    => $marketer->id,
                    'user_id'        => $adminId,
                    'type'           => 'deposit',
                    'recipient_name' => $marketer->first_name . ' ' . $marketer->last_name,
                    'description'    => "استرداد تكلفة منتجات طلب #{$order->id} (إلغاء)",
                    'amount'         => $costTotal,
                    'date'           => now()->toDateString(),
                    'balance_after'  => $balanceAfter,
                ]);
            }

            $order->logs()->create([
                'action'      => 'cancelled',
                'description' => "تم إلغاء الطلب بواسطة {$admin->name} — السبب: {$reason}",
            ]);
        });
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
