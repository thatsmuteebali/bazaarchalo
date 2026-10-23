<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderItem extends Model
{
    public const STATUS_PENDING   = 'pending';    // waiting for the seller
    public const STATUS_ACCEPTED  = 'accepted';   // seller will send it
    public const STATUS_REJECTED  = 'rejected';   // seller cannot send it
    public const STATUS_SHIPPED   = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_id', 'seller_id', 'shop_id', 'product_id', 'product_variant_id',
        'product_name', 'variant_title', 'sku', 'image_path', 'shop_name',
        'unit_price', 'quantity', 'line_total',
        'status', 'status_reason', 'accepted_at', 'rejected_at', 'cancelled_at',
        'courier_name', 'tracking_number', 'shipped_at', 'delivered_at',
        'stock_released_at',
    ];

    protected $casts = [
        'unit_price'        => 'decimal:2',
        'line_total'        => 'decimal:2',
        'quantity'          => 'integer',
        'accepted_at'       => 'datetime',
        'rejected_at'       => 'datetime',
        'cancelled_at'      => 'datetime',
        'shipped_at'        => 'datetime',
        'delivered_at'      => 'datetime',
        'stock_released_at' => 'datetime',
    ];

    /* ---------------- relations ---------------- */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function tracking()
    {
        return $this->hasMany(OrderTracking::class)->latest('created_at')->latest('id');
    }

    /* ---------------- scopes ---------------- */

    public function scopeForSeller(Builder $query, int $sellerId): Builder
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /* ---------------- helpers ---------------- */

    /** Rejected and cancelled items do not count towards what the customer pays. */
    public function isActive(): bool
    {
        return ! in_array($this->status, [self::STATUS_REJECTED, self::STATUS_CANCELLED], true);
    }

    /** The seller may still accept or reject it. */
    public function canBeDecided(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
