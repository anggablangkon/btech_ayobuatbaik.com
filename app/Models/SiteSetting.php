<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key with caching (24 hours).
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("site_setting_{$key}", 86400, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value and clear its cache.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting_{$key}");
    }

    /**
     * Get all settings as key-value array (cached).
     */
    public static function allCached(): array
    {
        return Cache::remember('site_settings_all', 86400, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("site_setting_{$setting->key}");
        }
        Cache::forget('site_settings_all');
    }
}
