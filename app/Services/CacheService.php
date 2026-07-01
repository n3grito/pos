<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Client;
use App\Models\GeneralSetting;
use App\Models\MailSetting;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public static function settings(): array
    {
        return self::remember('app.settings', 3600, function () {
            $settings = GeneralSetting::pluck('value', 'key');
            $mail = MailSetting::first();
            return [
                'timezone' => $settings['timezone'] ?? 'America/Havana',
                'registration_enabled' => ($settings['registration_enabled'] ?? 'true') === 'true',
                'currency_symbol' => $settings['currency_symbol'] ?? '$',
                'business_name' => $settings['business_name'] ?? config('app.name'),
                'mail' => $mail ? $mail->toArray() : [],
            ];
        });
    }

    public static function clearSettings(): void
    {
        self::forget('app.settings');
    }

    public static function productsCount(): int
    {
        return self::remember('stats.products_count', 300, fn() => Product::count());
    }

    public static function clientsCount(): int
    {
        return self::remember('stats.clients_count', 300, fn() => Client::count());
    }

    public static function categories(): array
    {
        return self::remember('categories.all', 3600, fn() => Category::select('id', 'name')->where('is_active', true)->orderBy('name')->get()->toArray());
    }

    public static function clearCategories(): void
    {
        self::forget('categories.all');
    }

    public static function salesToday(): float
    {
        return self::remember('stats.sales_today', 60, fn() =>
            Sale::whereDate('date', today())->where('status', 'completed')->sum('total')
        );
    }

    public static function salesMonth(): float
    {
        return self::remember('stats.sales_month', 120, fn() =>
            Sale::whereMonth('date', now()->month)->whereYear('date', now()->year)->where('status', 'completed')->sum('total')
        );
    }

    public static function lowStockCount(): int
    {
        return self::remember('stats.low_stock_count', 120, fn() =>
            Product::where('is_active', true)->whereColumn('stock', '<=', 'min_stock')->count()
        );
    }

    public static function topSaleProducts(int $limit = 6): array
    {
        return self::remember('stats.top_products.' . $limit, 300, function () use ($limit) {
            return Product::where('is_active', true)
                ->where('available_for_sale', true)
                ->orderBy('stock', 'desc')
                ->limit($limit)
                ->get(['id', 'name', 'sku', 'selling_price', 'stock'])
                ->toArray();
        });
    }

    public static function clearDashboard(): void
    {
        foreach (['stats.sales_today', 'stats.sales_month', 'stats.products_count', 'stats.clients_count', 'stats.low_stock_count', 'stats.top_products.6'] as $key) {
            self::forget($key);
        }
    }

    protected static function remember(string $key, int $ttl, callable $callback): mixed
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            return $callback();
        }
    }

    protected static function forget(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (\Exception $e) {
        }
    }
}
