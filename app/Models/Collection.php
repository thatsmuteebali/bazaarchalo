<?php

namespace App\Models;

use App\Models\Concerns\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasEncryptedRouteKey, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'image',
        'status',
        'slug',
        'is_featured',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/'.ltrim($this->image, '/'))
            : asset('img/fruite-item-1.jpg');
    }
}
