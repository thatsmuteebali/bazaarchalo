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
}
