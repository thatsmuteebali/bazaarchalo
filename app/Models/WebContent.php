<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WebContent extends Model
{
    protected $fillable = [
        'page',
        'field',
        'value',
        'type',
    ];

    public function getImageUrlAttribute(): string
    {
        if (
            $this->type === 'image'
            && $this->value
            && $this->value !== 'no_image'
            && Storage::disk('public')->exists($this->value)
        ) {
            return asset('storage/'.ltrim($this->value, '/'));
        }

        return asset('assets/images/no_image.png');
    }
}
