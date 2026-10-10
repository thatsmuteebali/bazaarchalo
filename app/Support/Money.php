<?php

namespace App\Support;

/** One place that decides how a price looks: "Rs. 2,500" or "Rs. 2,499.50". */
class Money
{
    public static function format($amount): string
    {
        $value    = round((float) $amount, 2);
        $decimals = abs($value - round($value)) < 0.005 ? 0 : 2;

        return config('shop.currency_symbol', 'Rs.') . ' ' . number_format($value, $decimals);
    }
}
