<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'image',
        'status',
        'description',
    ];

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . ltrim($this->image, '/'));
    }
    public function products()   { return $this->hasMany(Product::class); }
}
