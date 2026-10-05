<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'title', 'option_values',
        'sku', 'price', 'compare_price', 'stock',
    ];

    protected $casts = [
        'option_values' => 'array',
        'price'         => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock'         => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
