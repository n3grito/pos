<?php

namespace Database\Seeders;

use App\Models\PriceList;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        $base = PriceList::firstOrCreate(['name' => 'Base'], ['is_default' => true]);
        PriceList::firstOrCreate(['name' => 'Mayor']);
        $vip = PriceList::firstOrCreate(['name' => 'VIP']);

        $products = Product::all();

        foreach ($products as $product) {
            $vipPrice = round($product->selling_price * 0.8, 2);
            $product->priceLists()->syncWithoutDetaching([$vip->id => ['price' => $vipPrice]]);
        }
    }
}
