<?php

namespace App\Services;

/**
 * Order totals. The checkout page uses it to show the totals and the
 * "Place Order" code must use it again on the server (never trust amounts sent by the browser).
 */
class CheckoutService
{
    /** @param array $cart the array returned by CartService::summary() */
    public function totals(array $cart): array
    {
        $count    = (int) ($cart['count'] ?? 0);
        $subtotal = round((float) ($cart['subtotal'] ?? 0), 2);

        $flat     = max(0.0, (float) config('shop.shipping.flat_rate', 0));
        $freeOver = config('shop.shipping.free_over');
        $freeOver = $freeOver === null ? null : (float) $freeOver;

        $isFree = $count === 0 || $flat <= 0 || ($freeOver !== null && $subtotal >= $freeOver);

        $shipping = $isFree ? 0.0 : $flat;

        $remaining = ($count > 0 && $flat > 0 && $freeOver !== null && $subtotal < $freeOver)
            ? round($freeOver - $subtotal, 2)
            : null;

        return [
            'subtotal'           => $subtotal,
            'shipping'           => round($shipping, 2),
            'total'              => round($subtotal + $shipping, 2),
            'free_over'          => $freeOver,
            'remaining_for_free' => $remaining,
            'progress'           => $freeOver && $freeOver > 0 ? (int) min(100, round($subtotal / $freeOver * 100)) : 100,
        ];
    }
}
