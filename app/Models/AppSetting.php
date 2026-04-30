<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected static array $defaults = [
        'premium_feature_lock_enabled' => '1',
    ];

    protected static ?array $cachedSettings = null;

    public static function defaults(): array
    {
        return static::$defaults;
    }

    public static function allWithDefaults(): array
    {
        if (static::$cachedSettings !== null) {
            return static::$cachedSettings;
        }

        static::$cachedSettings = array_merge(
            static::defaults(),
            static::query()->pluck('value', 'key')->toArray()
        );

        return static::$cachedSettings;
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = static::allWithDefaults();

        return $settings[$key] ?? $default;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = static::getValue($key, $default ? '1' : '0');

        if (is_bool($value)) {
            return $value;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }

    public static function putMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => $value ? '1' : '0']
            );
        }

        static::$cachedSettings = null;
    }

    public static function isPremiumFeatureLockEnabled(): bool
    {
        return static::getBool('premium_feature_lock_enabled', true);
    }

    public static function premiumFeatureLabels(): array
    {
        return [
            'Member Mapping',
            'Alalayang Agila Help',
            'Find A Kuya',
            'Chat with Kuya',
            'Publish Announcement',
        ];
    }
}
