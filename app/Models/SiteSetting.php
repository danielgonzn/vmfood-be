<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    public const MAINTENANCE_KEY = 'site.maintenance';

    protected $fillable = [
        'key',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            static::bumpSettingsVersion();
        });

        static::deleted(function (): void {
            static::bumpSettingsVersion();
        });
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (!Schema::hasTable((new static())->getTable())) {
            return $default;
        }

        try {
            return static::query()->where('key', $key)->value('value') ?? $default;
        } catch (QueryException) {
            return $default;
        }
    }

    public static function setValue(string $key, mixed $value): void
    {
        if (!Schema::hasTable((new static())->getTable())) {
            return;
        }

        try {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        } catch (QueryException) {
            // Silently ignore writes when schema is not ready.
        }
    }

    public static function defaultMaintenanceConfig(): array
    {
        return [
            'enabled' => false,
            'title' => 'Estamos construyendo algo extraordinario',
            'subtitle' => 'Nuestra nueva plataforma digital está en desarrollo. Pronto podrás explorar el catálogo más avanzado de maquinaria industrial para alimentos.',
            'email' => 'contacto@vmfoodimport.com',
            'phone' => '+58 412-7212203',
            'address' => 'Av. Pedro Russo Ferrer Local Nº23 Galpón B',
            'whatsapp' => '+58 412-7212203',
            'logo_url' => '/images/logo.png',
        ];
    }

    public static function maintenanceConfig(): array
    {
        $stored = static::getValue(static::MAINTENANCE_KEY, []);
        if (!is_array($stored)) {
            $stored = [];
        }

        return array_replace(static::defaultMaintenanceConfig(), $stored);
    }

    public static function setMaintenanceConfig(array $config): void
    {
        $payload = array_replace(static::defaultMaintenanceConfig(), $config);
        $payload['enabled'] = filter_var($payload['enabled'] ?? false, FILTER_VALIDATE_BOOL);
        static::setValue(static::MAINTENANCE_KEY, $payload);
    }

    public static function settingsVersion(): int
    {
        return (int) Cache::get('site_settings_version', 1);
    }

    private static function bumpSettingsVersion(): void
    {
        if (!Cache::has('site_settings_version')) {
            Cache::forever('site_settings_version', 1);
        }

        Cache::increment('site_settings_version');
    }
}
