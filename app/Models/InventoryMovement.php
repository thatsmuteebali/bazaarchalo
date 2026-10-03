<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    const UPDATED_AT = null; // only created_at

    public const REASONS = ['initial', 'restock', 'sale', 'return', 'damaged', 'correction'];

    protected $fillable = [
        'product_id', 'product_variant_id', 'variant_title', 'user_id',
        'reason', 'quantity_change', 'stock_before', 'stock_after', 'note',
        'reference_type', 'reference_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}