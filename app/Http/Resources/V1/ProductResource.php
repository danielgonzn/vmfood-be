<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    private function resolveImageUrl(): ?string
    {
        $image = $this->image_url;

        if ($image === null || $image === '') {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://', '/'])) {
            return $image;
        }

        return Storage::disk('public')->url($image);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'category' => $this->category?->name,
            'subcategory' => $this->category?->name,
            'category_slug' => $this->category?->slug,
            'brand' => $this->brand?->name,
            'brand_slug' => $this->brand?->slug,
            'origin' => $this->origin,
            'condition' => $this->condition,
            'description' => $this->description ?: $this->short_description,
            'short_description' => $this->short_description,
            'image' => $this->resolveImageUrl(),
            'gallery_images' => $this->gallery_images ?? [],
            'available' => $this->available,
            'capacity' => $this->capacity,
            'voltage' => $this->voltage,
            'power' => $this->power,
            'tags' => $this->tags ?? [],
            'published_at' => optional($this->published_at)->toIso8601String(),
        ];
    }
}
