<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebContent extends Model
{
    protected $fillable = [
        'section',
        'key',
        'name',
        'title',
        'subtitle',
        'body',
        'image_path',
        'banner_path',
        'cta_text',
        'cta_url',
        'meta',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            static::bumpContentVersion();
        });

        static::deleted(function (): void {
            static::bumpContentVersion();
        });
    }

    private static function bumpContentVersion(): void
    {
        if (!Cache::has('web_content_version')) {
            Cache::forever('web_content_version', 1);
        }

        Cache::increment('web_content_version');
    }
}
