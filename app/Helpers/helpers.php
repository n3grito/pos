<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('currency')) {
    function currency(float $amount): string
    {
        return CurrencyHelper::format($amount);
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return CurrencyHelper::symbol();
    }
}

if (!function_exists('logo_url')) {
    function logo_url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        return url('storage/' . $path);
    }
}
