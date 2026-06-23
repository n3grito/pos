<?php

namespace App\Helpers;

use App\Models\Currency;

class CurrencyHelper
{
    protected static ?Currency $default = null;

    public static function default(): Currency
    {
        if (self::$default === null) {
            self::$default = Currency::where('is_active', true)->first();
        }
        return self::$default ?? new Currency(['symbol' => '$', 'code' => 'CUP']);
    }

    public static function format(float $amount, ?Currency $currency = null): string
    {
        $c = $currency ?? self::default();
        return $c->symbol . number_format($amount, 2);
    }

    public static function symbol(): string
    {
        return self::default()->symbol;
    }
}
