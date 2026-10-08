<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'banner',
        'address',
        'status',
        'is_primary',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getBannerUrlAttribute(): string
    {
        if($this->banner){
            return asset('storage/' . ltrim($this->banner, '/'));
        }
        return asset('img/baner-1.png');
    }
}
