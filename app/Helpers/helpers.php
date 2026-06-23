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
