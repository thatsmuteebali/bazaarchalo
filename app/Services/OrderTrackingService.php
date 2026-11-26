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
    public function addUpdate(Order $order, array $data, User $actor): OrderTracking
    {
        if ($data['status'] !== 'note') {
            throw ValidationException::withMessages([
                'status' => 'Only the assigned rider can update pickup and delivery statuses.',
            ]);
        }

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

            return $tracking;
        });
    }
}
