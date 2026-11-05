<?php

namespace App\Models;

use App\Models\Concerns\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasEncryptedRouteKey, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'status',
        'description',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if ($category->isDirty('name') || $category->isDirty('slug') || blank($category->slug)) {
                $category->slug = $category->generateUniqueSlug();
            }
        });
    }

    private function generateUniqueSlug(): string
    {
        $baseSlug = Str::slug((string) $this->name) ?: 'category';
        $baseSlug = substr($baseSlug, 0, 255);
        $suffix = 1;

        do {
            $candidate = $suffix === 1
                ? $baseSlug
                : substr($baseSlug, 0, 255 - strlen('-' . $suffix)) . '-' . $suffix;

            $query = static::withTrashed()->where('slug', $candidate);
            if ($this->exists) {
                $query->where($this->getKeyName(), '!=', $this->getKey());
            }

            $suffix++;
        } while ($query->exists());

        return $candidate;
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . ltrim($this->image, '/'));
    }

    public function products()   { return $this->hasMany(Product::class); }
}
