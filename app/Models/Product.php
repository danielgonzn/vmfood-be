<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'brand_id',
        'title',
        'slug',
        'short_description',
        'description',
        'origin',
        'condition',
        'image_url',
        'gallery_images',
        'capacity',
        'voltage',
        'power',
        'tags',
        'available',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'tags' => 'array',
            'available' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            if ($product->category_id !== null && !Category::query()->whereKey($product->category_id)->exists()) {
                $product->category_id = null;
            }

            if ($product->brand_id !== null && !Brand::query()->whereKey($product->brand_id)->exists()) {
                $product->brand_id = null;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
