<?php

namespace App\Services;

use App\Exceptions\CartException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderPlacementService
{
    public function __construct(
        private CheckoutService $checkout,
        private StockService $stock
    ) {}

    public function place(User $customer, array $cart, array $details): Order
    {
        $items = $cart['items'] ?? [];
        if ($items === []) {
            throw new CartException('Your cart is empty. Add an item before placing your order.');
        }

        usort($items, function (array $left, array $right): int {
            return [$left['product_id'], $left['variant_id'] ?? 0] <=> [$right['product_id'], $right['variant_id'] ?? 0];
        });

        return DB::transaction(function () use ($customer, $items, $details): Order {
            $prepared = [];
            $subtotal = 0.0;
            $count = 0;

            foreach ($items as $item) {
                $quantity = (int) ($item['qty'] ?? 0);
                if ($quantity < 1 || $quantity > CartService::MAX_PER_LINE) {
                    throw new CartException('An item quantity in your cart is invalid. Please review your cart.');
                }

                $product = Product::query()
                    ->whereKey($item['product_id'] ?? 0)
                    ->where('status', 'active')
                    ->whereHas('shop', fn ($query) => $query->where('status', 'active'))
                    ->whereHas('category', fn ($query) => $query->where('status', 'active'))
                    ->with(['coverImage', 'shop'])
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw new CartException('A product in your cart is no longer available. Please review your cart.');
                }

                $variant = null;
                if ($product->has_variants) {
                    $variant = ProductVariant::query()
                        ->where('product_id', $product->id)
                        ->whereKey($item['variant_id'] ?? 0)
                        ->lockForUpdate()
                        ->first();

                    if (! $variant) {
                        throw new CartException('A product option in your cart is no longer available. Please review your cart.');
                    }
                } elseif (! empty($item['variant_id'])) {
                    throw new CartException('A product in your cart has changed. Please review your cart.');
                }

                $available = (int) ($variant ? $variant->stock : $product->stock);
                if ($available < $quantity) {
                    throw new CartException("Only {$available} of {$product->name} are currently available. Please update your cart.");
                }

                $price = round((float) ($variant ? $variant->price : $product->price), 2);
                $lineTotal = round($price * $quantity, 2);
                $prepared[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $price,
                    'line_total' => $lineTotal,
                ];
                $subtotal += $lineTotal;
                $count += $quantity;
            }

            $totals = $this->checkout->totals(['count' => $count, 'subtotal' => round($subtotal, 2)]);
            $paymentMethod = $details['payment_method'];

            $order = Order::create([
                'user_id' => $customer->id,
                'customer_name' => $details['name'],
                'customer_phone' => $details['phone'],
                'customer_email' => $details['email'] ?? null,
                'delivery_address' => $details['address'],
                'latitude' => $details['latitude'] ?? null,
                'longitude' => $details['longitude'] ?? null,
                'notes' => $details['notes'] ?? null,
                'payment_method' => $paymentMethod,
                'online_payment_method' => $paymentMethod === Order::METHOD_ONLINE
                    ? $details['online_payment_method']
                    : null,
                'payment_status' => Order::PAYMENT_UNPAID,
                'subtotal' => $totals['subtotal'],
                'shipping_total' => $totals['shipping'],
                'total' => $totals['total'],
                'payable_total' => $totals['total'],
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($prepared as $row) {
                /** @var Product $product */
                $product = $row['product'];
                /** @var ProductVariant|null $variant */
                $variant = $row['variant'];

                $orderItem = $order->items()->create([
                    'seller_id' => $product->seller_id,
                    'shop_id' => $product->shop_id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_title' => $variant?->title,
                    'sku' => $variant?->sku,
                    'image_path' => $product->coverImage?->path,
                    'shop_name' => $product->shop?->name,
                    'unit_price' => $row['price'],
                    'quantity' => $row['quantity'],
                    'line_total' => $row['line_total'],
                    'status' => OrderItem::STATUS_PENDING,
                ]);

                $this->stock->adjust(
                    $product,
                    $variant,
                    -$row['quantity'],
                    'sale',
                    'Order ' . $order->order_number,
                    $customer->id,
                    $orderItem
                );
            }

            $order->payments()->create([
                'method' => $paymentMethod,
                'gateway' => $paymentMethod === Order::METHOD_ONLINE ? $details['online_payment_method'] : null,
                'amount' => $totals['total'],
                'status' => OrderPayment::STATUS_PENDING,
            ]);

            $order->tracking()->create([
                'status' => 'placed',
                'title' => 'Order placed',
                'description' => 'Your order has been received and is waiting for seller confirmation.',
                'is_public' => true,
                'created_by' => $customer->id,
            ]);

            return $order;
        });
    }
}
