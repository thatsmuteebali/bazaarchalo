<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTracking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderTrackingService
{
    public function __construct(private readonly OrderItemDecisionService $decisions)
    {
    }

    public function addUpdate(Order $order, array $data, User $actor): OrderTracking
    {
        return DB::transaction(function () use ($order, $data, $actor): OrderTracking {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $item = null;

            if (! empty($data['order_item_id'])) {
                $item = OrderItem::query()
                    ->where('order_id', $order->id)
                    ->whereKey($data['order_item_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $status = $data['status'];
            $now = now();

            if (in_array($status, ['shipped', 'delivered'], true) && ! $item) {
                throw ValidationException::withMessages([
                    'order_item_id' => 'Select the order item being shipped or delivered.',
                ]);
            }

            if ($status === 'shipped') {
                if (! in_array($item->status, [OrderItem::STATUS_ACCEPTED, OrderItem::STATUS_SHIPPED], true)) {
                    throw ValidationException::withMessages([
                        'status' => 'Only accepted items can be marked as shipped.',
                    ]);
                }

                $item->update([
                    'status' => OrderItem::STATUS_SHIPPED,
                    'shipped_at' => $item->shipped_at ?? $now,
                    'courier_name' => ($data['courier_name'] ?? null) ?: $item->courier_name,
                    'tracking_number' => ($data['tracking_number'] ?? null) ?: $item->tracking_number,
                ]);
            } elseif ($status === 'delivered') {
                if (! in_array($item->status, [OrderItem::STATUS_SHIPPED, OrderItem::STATUS_DELIVERED], true)) {
                    throw ValidationException::withMessages([
                        'status' => 'Only shipped items can be marked as delivered.',
                    ]);
                }

                $item->update([
                    'status' => OrderItem::STATUS_DELIVERED,
                    'shipped_at' => $item->shipped_at ?? $now,
                    'delivered_at' => $item->delivered_at ?? $now,
                    'courier_name' => ($data['courier_name'] ?? null) ?: $item->courier_name,
                    'tracking_number' => ($data['tracking_number'] ?? null) ?: $item->tracking_number,
                ]);
            }

            $tracking = $order->tracking()->create([
                'order_item_id' => $item?->id,
                'seller_id' => $item?->seller_id,
                'status' => $status,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'courier_name' => $data['courier_name'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'location' => $data['location'] ?? null,
                'is_public' => $data['is_public'],
                'created_by' => $actor->id,
            ]);

            if (in_array($status, ['shipped', 'delivered'], true)) {
                $this->decisions->refreshOrderStatus($order, $now);
            }

            return $tracking;
        });
    }
}
