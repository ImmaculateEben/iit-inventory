<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value', 'value_type'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('setting_key', $key)->first();
        if (!$setting) {
            return $default;
        }

        return match ($setting->value_type) {
            'integer' => (int) $setting->setting_value,
            'boolean' => filter_var($setting->setting_value, FILTER_VALIDATE_BOOLEAN),
            'float' => (float) $setting->setting_value,
            default => $setting->setting_value,
        };
    }

    public static function setValue(string $key, mixed $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => (string) $value, 'value_type' => $type]
        );
    }
}
