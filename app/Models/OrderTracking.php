<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $table = 'order_tracking';

    const UPDATED_AT = null; // events are never edited

    protected $fillable = [
        'order_id', 'order_item_id', 'seller_id',
        'status', 'title', 'description',
        'courier_name', 'tracking_number', 'location',
        'is_public', 'created_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
