<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    const CACHE_KEY = 'system_settings';

    protected $fillable = [
        'system_name',
        'system_short_name',
        'system_logo',
        'system_favicon',
        'primary_color',
        'secondary_color',
        'accent_color',
        'login_background',
        'currency',
        'timezone',
        'email',
        'phone',
        'address',
        'footer_text',
        'email_from_name',
        'email_from_address',
        'maintenance_mode',
        // Social media
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'whatsapp_number',
        // About section
        'about_description',
        'about_image',
        // Contact section
        'contact_email',
        'contact_phone',
        'contact_address',
        'map_embed_url',
        // Hero section
        'hero_title',
        'hero_subtitle',
        'hero_image',
        // System description
        'system_description',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
        ];
    }

    /**
     * Get settings with caching. Returns a single record (id=1).
     */
    public static function getSettings(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::firstOrCreate(['id' => 1]);
        });
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get a specific setting value with a fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::getSettings();
        return $settings->$key ?? $default;
    }

    /**
     * Get system name with fallback.
     */
    public static function getSystemName(): string
    {
        return self::get('system_name', 'Chriss Integrated Systems');
    }

    /**
     * Get system short name with fallback.
     */
    public static function getSystemShortName(): string
    {
        return self::get('system_short_name', 'CIS');
    }

    /**
     * Get logo URL with fallback.
     */
    public static function getLogoUrl(): string
    {
        $logo = self::get('system_logo');
        if ($logo && file_exists(storage_path('app/public/' . $logo))) {
            return asset('storage/' . $logo);
        }
        return asset('img/logo.svg');
    }

    /**
     * Get favicon URL with fallback.
     */
    public static function getFaviconUrl(): string
    {
        $favicon = self::get('system_favicon');
        if ($favicon && file_exists(storage_path('app/public/' . $favicon))) {
            return asset('storage/' . $favicon);
        }
        return asset('favicon.svg');
    }

    /**
     * Get primary color with fallback.
     */
    public static function getPrimaryColor(): string
    {
        return self::get('primary_color', '#1a73e8');
    }

    /**
     * Get secondary color with fallback.
     */
    public static function getSecondaryColor(): string
    {
        return self::get('secondary_color', '#6c757d');
    }

    /**
     * Get accent color with fallback.
     */
    public static function getAccentColor(): string
    {
        return self::get('accent_color', '#0d6efd');
    }

    /**
     * Get footer text with fallback.
     */
    public static function getFooterText(): string
    {
        return self::get('footer_text', 'All rights reserved.');
    }

    /**
     * Get contact email with fallback.
     */
    public static function getContactEmail(): string
    {
        return self::get('email', 'info@chrissintegrated.com');
    }

    /**
     * Get contact phone with fallback.
     */
    public static function getContactPhone(): string
    {
        return self::get('phone', '+255 000 000 000');
    }

    /**
     * Get address with fallback.
     */
    public static function getAddress(): string
    {
        return self::get('address', 'Tanzania');
    }

    /**
     * Get currency with fallback.
     */
    public static function getCurrency(): string
    {
        return self::get('currency', 'TZS');
    }

    /**
     * Get timezone with fallback.
     */
    public static function getTimezone(): string
    {
        return self::get('timezone', 'Africa/Dar_es_Salaam');
    }

    /**
     * Bootstrap CSS gradient from primary color.
     */
    public static function getPrimaryGradient(): string
    {
        $primary = self::getPrimaryColor();
        return "linear-gradient(135deg, {$primary}, " . self::adjustBrightness($primary, -20) . ')';
    }

    /**
     * Adjust hex color brightness.
     */
    protected static function adjustBrightness(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = max(0, min(255, hexdec(substr($hex, 0, 2)) + $percent));
        $g = max(0, min(255, hexdec(substr($hex, 2, 2)) + $percent));
        $b = max(0, min(255, hexdec(substr($hex, 4, 2)) + $percent));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Override update to clear cache automatically.
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        $result = parent::update($attributes, $options);
        self::clearCache();
        return $result;
    }

    /**
     * Override delete to clear cache automatically.
     */
    public function delete(): ?bool
    {
        $result = parent::delete();
        self::clearCache();
        return $result;
    }
}