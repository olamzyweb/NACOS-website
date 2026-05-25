<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key with caching.
     */
    public static function get(string $key, $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 300, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Get vote price in kobo.
     */
    public static function getVotePriceKobo(): int
    {
        return (int) static::get('vote_price', 5000); // Default ₦50 = 5000 kobo
    }

    /**
     * Get vote price in Naira.
     */
    public static function getVotePriceNaira(): float
    {
        return static::getVotePriceKobo() / 100;
    }

    /**
     * Check if voting is currently enabled.
     */
    public static function isVotingEnabled(): bool
    {
        return (bool) static::get('voting_enabled', true);
    }

    /**
     * Get event end date.
     */
    public static function getEventDate(): ?string
    {
        return static::get('event_date');
    }

    /**
     * Get site title.
     */
    public static function getSiteTitle(): string
    {
        return static::get('site_title', 'NACOS Day Awards');
    }

    /**
     * Get selected online payment provider.
     */
    public static function getPaymentProvider(): string
    {
        return static::get('payment_provider', 'korapay');
    }
}
