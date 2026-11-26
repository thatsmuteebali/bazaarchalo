<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderItemDecisionService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly MarketplaceNotificationService $notifications,
    ) {}

    public function decide(
        int $orderItemId,
        string $decision,
        ?string $reason,
        User $actor,
        string $actorLabel,
        ?int $sellerId = null,
    ): Order {
        return DB::transaction(function () use ($orderItemId, $decision, $reason, $actor, $actorLabel, $sellerId): Order {
            $initialItem = OrderItem::query()->findOrFail($orderItemId);
            $order = Order::query()->whereKey($initialItem->order_id)->lockForUpdate()->firstOrFail();
            $itemQuery = OrderItem::query()->whereKey($orderItemId);
            $itemQuery->where('order_id', $order->id);

            if ($sellerId !== null) {
                $itemQuery->where('seller_id', $sellerId);
            }

            $item = $itemQuery->lockForUpdate()->firstOrFail();

            if (! $item->canBeDecided()) {
                throw ValidationException::withMessages([
                    'decision' => 'This item has already been decided.',
                ]);
            }

            $now = now();

            if ($decision === OrderItem::STATUS_REJECTED) {
                $product = $item->product_id ? Product::query()->find($item->product_id) : null;
                $variant = $item->product_variant_id
                    ? ProductVariant::query()->find($item->product_variant_id)
                    : null;

                if ($product && (! $product->has_variants || $variant)) {
                    $this->stock->adjust(
                        $product,
                        $variant,
                        $item->quantity,
                        'return',
                        'Rejected item from order '.$order->order_number,
                        $actor->id,
                        $item
                    );
                }

                $item->update([
                    'status' => OrderItem::STATUS_REJECTED,
                    'status_reason' => $reason,
                    'rejected_at' => $now,
                    'stock_released_at' => $now,
                ]);

                $order->payable_total = max(0, round((float) $order->payable_total - (float) $item->line_total, 2));
            } else {
                $item->update([
                    'status' => OrderItem::STATUS_ACCEPTED,
                    'status_reason' => null,
                    'accepted_at' => $now,
                ]);
            }

            $order->tracking()->create([
                'order_item_id' => $item->id,
                'seller_id' => $item->seller_id,
                'status' => $decision,
                'title' => $actorLabel.($decision === OrderItem::STATUS_ACCEPTED
                    ? ' accepted '.$item->product_name
                    : ' rejected '.$item->product_name),
                'description' => $decision === OrderItem::STATUS_REJECTED
                    ? $reason
                    : $item->product_name.' is being prepared by the seller.',
                'is_public' => true,
                'created_by' => $actor->id,
            ]);

            $this->refreshOrderStatus($order, $now);

            $order->payments()
                ->where('status', OrderPayment::STATUS_PENDING)
                ->update(['amount' => $order->payable_total]);

            $order->loadMissing('customer');
            $item->loadMissing('seller');
            $this->notifications->notifyOrderItemDecision(
                $order,
                $item,
                $decision,
                $actor,
                $actorLabel === 'Admin',
            );

            return $order;
        });
    }

    public function refreshOrderStatus(Order $order, \DateTimeInterface $now): void
    {
        $statuses = $order->items()->pluck('status');
        $activeStatuses = $statuses->reject(fn ($status) => in_array($status, [
            OrderItem::STATUS_REJECTED,
            OrderItem::STATUS_CANCELLED,
        ], true));

        if ($activeStatuses->isEmpty()) {
            $orderStatus = Order::STATUS_CANCELLED;
        } elseif ($activeStatuses->every(fn ($status) => $status === OrderItem::STATUS_DELIVERED)) {
            $orderStatus = Order::STATUS_COMPLETED;
        } elseif ($activeStatuses->contains(fn ($status) => in_array($status, [
            OrderItem::STATUS_ACCEPTED,
            OrderItem::STATUS_SHIPPED,
            OrderItem::STATUS_DELIVERED,
        ], true))) {
            $orderStatus = Order::STATUS_PROCESSING;
        } else {
            $orderStatus = Order::STATUS_PENDING;
        }

        $order->update([
            'status' => $orderStatus,
            'payable_total' => $orderStatus === Order::STATUS_CANCELLED ? '0.00' : $order->payable_total,
            'completed_at' => $orderStatus === Order::STATUS_COMPLETED ? ($order->completed_at ?? $now) : null,
            'cancelled_at' => $orderStatus === Order::STATUS_CANCELLED ? ($order->cancelled_at ?? $now) : null,
        ]);
    }
}
