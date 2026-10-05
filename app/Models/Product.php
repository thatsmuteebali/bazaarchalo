<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'seller_id', 'shop_id', 'category_id', 'collection_id',
        'name', 'short_description', 'description',
        'price', 'compare_price', 'stock',
        'status', 'is_featured', 'has_variants',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock'         => 'integer',
        'is_featured'   => 'boolean',
        'has_variants'  => 'boolean',
    ];

    public function seller()     { return $this->belongsTo(User::class, 'seller_id'); }
    public function shop()       { return $this->belongsTo(Shop::class); }
    public function category()   { return $this->belongsTo(Category::class); }
    public function collection() { return $this->belongsTo(Collection::class); }

    public function images()     { return $this->hasMany(ProductImage::class); }
    public function options()    { return $this->hasMany(ProductOption::class); }
    public function variants()   { return $this->hasMany(ProductVariant::class); }
    public function movements()  { return $this->hasMany(InventoryMovement::class); }

    // first image by sort_order (used as thumbnail in the list)
    public function coverImage()
    {
        return $this->hasOne(ProductImage::class)->ofMany('sort_order', 'min');
    }
}
