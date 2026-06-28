<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;
        if (in_array($value, ['1', 'true', 'yes'], true)) return true;
        if (in_array($value, ['0', 'false', 'no', ''], true)) return false;
        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function defaultTaxRate(): float
    {
        return (float) (static::get('default_tax_rate', '0'));
    }
}
