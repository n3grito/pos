<?php

namespace Database\Seeders;

use App\Models\PriceList;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        $base = PriceList::create(['name' => 'Base', 'is_default' => true]);
        PriceList::create(['name' => 'Mayor']);
        $vip = PriceList::create(['name' => 'VIP']);

        $products = Product::all();

        foreach ($products as $product) {
            // Base list uses selling_price (no entry needed, just the default logic)
            // VIP list: 20% discount
            $vipPrice = round($product->selling_price * 0.8, 2);
            $product->priceLists()->attach($vip->id, ['price' => $vipPrice]);
        }
    }
}
