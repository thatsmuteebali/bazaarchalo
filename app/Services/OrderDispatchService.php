<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderDispatchService
{
    public function __construct(
        private readonly OrderItemDecisionService $decisions,
        private readonly MarketplaceNotificationService $notifications,
    ) {}

    public function assignRider(Order $order, int $riderId, User $admin): Order
    {
        return DB::transaction(function () use ($order, $riderId, $admin): Order {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $rider = User::query()
                ->whereKey($riderId)
                ->where('role', 'rider')
                ->where('status', 'Active')
                ->lockForUpdate()
                ->first();

            if (! $rider) {
                throw ValidationException::withMessages([
                    'rider_id' => 'Select an active rider.',
                ]);
            }

            if ($order->rider_id !== null) {
                throw ValidationException::withMessages([
                    'rider_id' => 'A rider has already been assigned to this order.',
                ]);
            }

            /** @var \Illuminate\Database\Eloquent\Collection<int, OrderItem> $items */
            $items = $order->items()->lockForUpdate()->get();
            if ($items->isEmpty() || $items->contains(fn (OrderItem $item) => $item->canBeDecided())) {
                throw ValidationException::withMessages([
                    'rider_id' => 'Decide every order item before assigning a rider.',
                ]);
            }

            $acceptedItems = $items->where('status', OrderItem::STATUS_ACCEPTED);
            if ($acceptedItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'rider_id' => 'A rider can only be assigned when the order has accepted items.',
                ]);
            }

            $order->rider()->associate($rider);
            $order->save();
            $order->tracking()->create([
                'status' => 'rider_assigned',
                'title' => 'Rider assigned',
                'description' => $rider->name.' has been assigned to deliver this order.',
                'is_public' => true,
                'created_by' => $admin->id,
            ]);

            $acceptedItems->load('seller');
            $order->setRelation('items', $items);
            $order->setRelation('customer', $order->customer()->first());
            $this->notifications->notifyRiderAssigned($order, $rider, $acceptedItems);

            return $order;
        });
    }

    public function updateItemStatus(
        Order $order,
        OrderItem $orderItem,
        string $status,
        User $rider,
    ): Order {
        return DB::transaction(function () use ($order, $orderItem, $status, $rider): Order {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $order->rider_id === (int) $rider->id, 404);

            /** @var OrderItem $item */
            $item = $order->items()
                ->whereKey($orderItem->id)
                ->lockForUpdate()
                ->firstOrFail();

            $now = now();
            if ($status === OrderItem::STATUS_SHIPPED) {
                if ($item->status !== OrderItem::STATUS_ACCEPTED) {
                    throw ValidationException::withMessages([
                        'status' => 'Only accepted items can be marked as picked up.',
                    ]);
                }

                $item->update([
                    'status' => OrderItem::STATUS_SHIPPED,
                    'shipped_at' => $now,
                ]);
                $title = 'Rider picked up '.$item->product_name;
                $description = 'The rider collected this item from '
                    .($item->shop_name ?: 'the seller')
                    .' and is delivering it to the customer.';
            } elseif ($status === OrderItem::STATUS_DELIVERED) {
                if ($item->status !== OrderItem::STATUS_SHIPPED) {
                    throw ValidationException::withMessages([
                        'status' => 'Only picked-up items can be marked as delivered.',
                    ]);
                }

                $item->update(['status' => OrderItem::STATUS_DELIVERED, 'delivered_at' => $now]);
                $title = 'Delivered '.$item->product_name;
                $description = 'The rider delivered this item to the customer.';
            } else {
                throw ValidationException::withMessages([
                    'status' => 'Choose a valid delivery status.',
                ]);
            }

            $item->loadMissing('seller');
            $order->setRelation('customer', $order->customer()->first());
            $order->tracking()->create([
                'order_item_id' => $item->id,
                'seller_id' => $item->seller_id,
                'status' => $status,
                'title' => $title,
                'description' => $description,
                'is_public' => true,
                'created_by' => $rider->id,
            ]);

            $this->decisions->refreshOrderStatus($order, $now);
            $this->notifications->notifyRiderItemStatus($order, $item, $status);

            return $order;
        });
    }
}
