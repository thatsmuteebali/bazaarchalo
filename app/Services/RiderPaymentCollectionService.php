<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RiderPaymentCollectionService
{
    public function collectCash(Order $order, User $rider): Order
    {
        return DB::transaction(function () use ($order, $rider): Order {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $order->rider_id === (int) $rider->id, 404);

            if (! $order->isCashOnDelivery()) {
                throw ValidationException::withMessages([
                    'payment' => 'Only cash-on-delivery orders can be paid to the rider.',
                ]);
            }

            if ($order->payment_status !== Order::PAYMENT_UNPAID) {
                throw ValidationException::withMessages([
                    'payment' => 'This order is not awaiting cash payment.',
                ]);
            }

            $itemStatuses = $order->items()->pluck('status');
            $deliverableStatuses = $itemStatuses->reject(fn ($status) => in_array($status, [
                OrderItem::STATUS_REJECTED,
                OrderItem::STATUS_CANCELLED,
            ], true));

            if ($deliverableStatuses->isEmpty()
                || $deliverableStatuses->contains(fn ($status) => $status !== OrderItem::STATUS_DELIVERED)) {
                throw ValidationException::withMessages([
                    'payment' => 'All accepted order items must be delivered before recording cash payment.',
                ]);
            }

            $now = now();
            $updatedPayments = $order->payments()
                ->where('status', OrderPayment::STATUS_PENDING)
                ->update([
                    'status' => OrderPayment::STATUS_PAID,
                    'amount' => $order->payable_total,
                    'paid_at' => $now,
                    'updated_at' => $now,
                ]);

            if ($updatedPayments === 0) {
                throw ValidationException::withMessages([
                    'payment' => 'No pending payment record was found for this order.',
                ]);
            }

            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
                'paid_at' => $now,
            ]);
            $order->tracking()->create([
                'status' => 'payment_received',
                'title' => 'Cash payment received',
                'description' => 'The rider collected '.\App\Support\Money::format($order->payable_total).' from the customer.',
                'is_public' => true,
                'created_by' => $rider->id,
            ]);

            return $order;
        });
    }
}
