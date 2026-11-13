<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSectionSliderImage extends Model
{
    protected $fillable = ['name', 'slider_image'];

    public function getImageUrlAttribute(): string
    {
        if ($this->slider_image && Storage::disk('public')->exists($this->slider_image)) {
            return asset('storage/'.ltrim($this->slider_image, '/'));
        }

        return asset('assets/images/no_image.png');
    }
}