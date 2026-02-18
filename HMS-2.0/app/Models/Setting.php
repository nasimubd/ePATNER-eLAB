<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? self::castValue($setting->value, $setting->type) : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("setting_{$key}");
    }

    /**
     * Cast value based on type
     */
    private static function castValue($value, string $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get monthly fee setting
     */
    public static function getMonthlyFee(): float
    {
        return static::get('monthly_fee', 500.00);
    }

    /**
     * Get payment QR path
     */
    public static function getPaymentQrPath(): string
    {
        return static::get('payment_qr_path', 'images/Payment-QR.png');
    }
}
