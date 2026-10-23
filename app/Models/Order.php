<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_PENDING    = 'pending';     // waiting for sellers to accept or reject their items
    public const STATUS_PROCESSING = 'processing';  // sellers are preparing / shipping
    public const STATUS_COMPLETED  = 'completed';   // everything that was accepted has been delivered
    public const STATUS_CANCELLED  = 'cancelled';   // every item was rejected or cancelled

    public const PAYMENT_UNPAID   = 'unpaid';
    public const PAYMENT_PAID     = 'paid';
    public const PAYMENT_FAILED   = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    public const METHOD_COD    = 'cod';
    public const METHOD_ONLINE = 'online';

    protected $fillable = [
        'order_number', 'user_id',
        'customer_name', 'customer_phone', 'customer_email',
        'delivery_address', 'latitude', 'longitude', 'notes',
        'payment_method', 'online_payment_method', 'payment_status', 'paid_at',
        'subtotal', 'shipping_total', 'total', 'payable_total',
        'status', 'completed_at', 'cancelled_at',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'total'          => 'decimal:2',
        'payable_total'  => 'decimal:2',
        'latitude'       => 'decimal:7',
        'longitude'      => 'decimal:7',
        'paid_at'        => 'datetime',
        'completed_at'   => 'datetime',
        'cancelled_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /** BC-261024-48392  (BC + date + 5 random digits), unique. */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'BC-' . now()->format('ymd') . '-' . random_int(10000, 99999);
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    /* ---------------- relations ---------------- */

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tracking()
    {
        return $this->hasMany(OrderTracking::class)->latest('created_at')->latest('id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    /* ---------------- helpers ---------------- */

    public function isCashOnDelivery(): bool
    {
        return $this->payment_method === self::METHOD_COD;
    }

    /** Orders that contain at least one item of this seller. */
    public function scopeForSeller(Builder $query, int $sellerId): Builder
    {
        return $query->whereHas('items', fn ($items) => $items->where('seller_id', $sellerId));
    }
}
