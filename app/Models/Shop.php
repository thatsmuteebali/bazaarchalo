<?php

namespace App\Models;

use App\Models\Concerns\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasEncryptedRouteKey, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'banner',
        'address',
        'status',
        'is_primary',
        'slug',
        'is_feature_shop',
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
        if ($this->banner) {
            return asset('storage/'.ltrim($this->banner, '/'));
        }

        return asset('img/baner-1.png');
    }
}
