<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\WebContent */
class WebContentItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'section' => $this->section,
            'key' => $this->key,
            'name' => $this->name,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'body' => $this->body,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'banner_url' => $this->banner_path ? asset('storage/'.$this->banner_path) : null,
            'cta_text' => $this->cta_text,
            'cta_url' => $this->cta_url,
            'meta' => $this->meta ?? [],
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
