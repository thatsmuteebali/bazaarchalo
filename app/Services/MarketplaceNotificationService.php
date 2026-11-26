<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use Illuminate\Support\Facades\Notification;

class MarketplaceNotificationService
{
    public function notifyOrderPlaced(Order $order): void
    {
        $order->loadMissing('items');
        $customerName = $order->customer_name;

        $this->notifyActiveAdmins(new MarketplaceNotification(
            'New order received',
            'Order '.$order->order_number.' was placed by '.$customerName.' and is waiting for item decisions.',
            route('admin.orders.show', $order, false),
            'bi bi-bag-check',
        ));

        User::query()
            ->whereIn('id', $order->items->pluck('seller_id')->unique())
            ->where('role', 'seller')
            ->where('status', 'Active')
            ->get()
            ->each(function (User $seller) use ($order): void {
                $seller->notify(new MarketplaceNotification(
                    'New order received',
                    'Order '.$order->order_number.' includes items for your shop. Please review and accept or reject each item.',
                    route('seller.orders.show', $order, false),
                    'bi bi-bag-check',
                ));
            });
    }

    public function notifyOrderItemDecision(
        Order $order,
        OrderItem $item,
        string $decision,
        User $actor,
        bool $wasAdmin,
    ): void {
        $status = $decision === OrderItem::STATUS_ACCEPTED ? 'accepted' : 'rejected';
        $actorName = $wasAdmin ? 'An administrator' : 'The seller';
        $reason = $decision === OrderItem::STATUS_REJECTED && $item->status_reason
            ? ' Reason: '.$item->status_reason
            : '';

        $customer = $order->customer;
        if ($customer) {
            $customer->notify(new MarketplaceNotification(
                'Order item '.$status,
                $actorName.' '.$status.' "'.$item->product_name.'" in order '.$order->order_number.'.'.$reason,
                route('customer.order-tracking', ['order' => $order->order_number], false),
                $decision === OrderItem::STATUS_ACCEPTED ? 'bi bi-check-circle' : 'bi bi-x-circle',
            ));
        }

        if ($wasAdmin) {
            $item->seller?->notify(new MarketplaceNotification(
                'Order item '.$status,
                'An administrator '.$status.' "'.$item->product_name.'" in order '.$order->order_number.'.'.$reason,
                route('seller.orders.show', $order, false),
                $decision === OrderItem::STATUS_ACCEPTED ? 'bi bi-check-circle' : 'bi bi-x-circle',
            ));

            return;
        }

        $this->notifyActiveAdmins(new MarketplaceNotification(
            'Seller '.$status.' an order item',
            $actor->name.' '.$status.' "'.$item->product_name.'" in order '.$order->order_number.'.'.$reason,
            route('admin.orders.show', $order, false),
            $decision === OrderItem::STATUS_ACCEPTED ? 'bi bi-check-circle' : 'bi bi-x-circle',
        ));
    }

    public function notifyRiderAssigned(Order $order, User $rider, iterable $acceptedItems): void
    {
        $items = collect($acceptedItems);
        $items->pluck('seller')->filter()->unique('id')->each(function (User $seller) use ($order, $items): void {
            $sellerItems = $items->where('seller_id', $seller->id)->pluck('product_name')->unique()->implode(', ');
            $seller->notify(new MarketplaceNotification(
                'Rider assigned to your order',
                'Please prepare '.$sellerItems.' for order '.$order->order_number.'. A rider will pick them up from you soon.',
                route('seller.orders.show', $order, false),
                'bi bi-truck',
            ));
        });

        $order->customer?->notify(new MarketplaceNotification(
            'Your order is ready for delivery',
            'A rider has been assigned to order '.$order->order_number.'. Your accepted items will be delivered soon.',
            route('customer.order-tracking', ['order' => $order->order_number], false),
            'bi bi-truck',
        ));

        $rider->notify(new MarketplaceNotification(
            'New delivery assigned',
            'Order '.$order->order_number.' has been assigned to you. Please collect the accepted items from the sellers and deliver them to the customer.',
            route('rider.orders.show', $order, false),
            'bi bi-truck',
        ));
    }

    public function notifyRiderItemStatus(Order $order, OrderItem $item, string $status): void
    {
        $isDelivered = $status === OrderItem::STATUS_DELIVERED;
        $message = $isDelivered
            ? '"'.$item->product_name.'" from order '.$order->order_number.' has been delivered. Please leave a product review.'
            : 'The rider picked up "'.$item->product_name.'" from the seller for order '.$order->order_number.'.';

        $order->customer?->notify(new MarketplaceNotification(
            $isDelivered ? 'Order item delivered' : 'Order item picked up',
            $message,
            route(
                $isDelivered ? 'customer.order-detail' : 'customer.order-tracking',
                ['order' => $order->order_number],
                false,
            ),
            $isDelivered ? 'bi bi-house-check' : 'bi bi-truck',
        ));

        if (! $isDelivered) {
            $item->seller?->notify(new MarketplaceNotification(
                'Rider picked up your order item',
                'The rider collected "'.$item->product_name.'" for order '.$order->order_number.'.',
                route('seller.orders.show', $order, false),
                'bi bi-truck',
            ));
        }
    }

    public function notifyActiveSellersOfCategory(Category $category): void
    {
        User::query()
            ->where('role', 'seller')
            ->where('status', 'Active')
            ->chunkById(100, function ($sellers) use ($category) {
                Notification::send($sellers, new MarketplaceNotification(
                    'New category available',
                    'The '.$category->name.' category is now active.',
                    route('seller.products.create', [], false),
                    'bi bi-tags',
                ));
            });
    }

    public function notifyAdminsOfPendingSubmission(
        string $entity,
        string $name,
        User $seller,
        string $url,
        string $icon,
    ): void {
        $this->notifyAdmins(new MarketplaceNotification(
            'New '.$entity.' pending review',
            $seller->name.' submitted '.$name.'. It is awaiting your review.',
            $url,
            $icon,
        ));
    }

    public function notifyAdminsOfVerifiedCustomer(User $customer): void
    {
        $this->notifyAdmins(new MarketplaceNotification(
            'New customer register',
            $customer->name.' ('.$customer->email.') verified their email address.',
            route('admin.customers.index', [], false),
            'bi bi-person-check',
        ));
    }

    public function notifyAdminsOfVerifiedSeller(User $seller): void
    {
        $this->notifyAdmins(new MarketplaceNotification(
            'New seller register',
            $seller->name.' ('.$seller->email.') verified their email address.',
            route('admin.sellers.index', [], false),
            'bi bi-shop',
        ));
    }

    public function notifySellerOfStatus(
        User $seller,
        string $entity,
        string $name,
        string $status,
        string $url,
        string $icon,
    ): void {
        $seller->notify(new MarketplaceNotification(
            'Your '.$entity.' is '.strtolower($status),
            'Your '.$entity.' "'.$name.'" is now '.strtolower($status).'.',
            $url,
            $icon,
        ));
    }

    private function notifyAdmins(MarketplaceNotification $notification): void
    {
        User::query()
            ->where('role', 'admin')
            ->chunkById(100, function ($admins) use ($notification) {
                Notification::send($admins, $notification);
            });
    }

    private function notifyActiveAdmins(MarketplaceNotification $notification): void
    {
        User::query()
            ->where('role', 'admin')
            ->where('status', 'Active')
            ->chunkById(100, function ($admins) use ($notification) {
                Notification::send($admins, $notification);
            });
    }
}
