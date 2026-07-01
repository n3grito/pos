<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use App\Services\PromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PromotionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    }

    public function test_finds_best_percentage_promotion(): void
    {
        Promotion::factory()->create([
            'name' => '10% Off',
            'type' => 'percentage',
            'value' => 10,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);
        Promotion::factory()->create([
            'name' => '20% Off',
            'type' => 'percentage',
            'value' => 20,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);

        $service = new PromotionService();
        $best = $service->findBestPromotion(100, [['product_id' => 1, 'quantity' => 2]]);

        $this->assertNotNull($best);
        $this->assertEquals('20% Off', $best->name);
    }

    public function test_respects_minimum_amount(): void
    {
        Promotion::factory()->create([
            'name' => '10% Off',
            'type' => 'percentage',
            'value' => 10,
            'min_amount' => 200,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);

        $service = new PromotionService();
        $best = $service->findBestPromotion(100, [['product_id' => 1, 'quantity' => 1]]);

        $this->assertNull($best);
    }

    public function test_respects_minimum_quantity(): void
    {
        Promotion::factory()->create([
            'name' => '10% Off',
            'type' => 'percentage',
            'value' => 10,
            'min_quantity' => 5,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);

        $service = new PromotionService();
        $best = $service->findBestPromotion(100, [['product_id' => 1, 'quantity' => 2]]);

        $this->assertNull($best);
    }

    public function test_respects_usage_limit(): void
    {
        Promotion::factory()->create([
            'name' => '10% Off',
            'type' => 'percentage',
            'value' => 10,
            'usage_limit' => 10,
            'used_count' => 10,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);

        $service = new PromotionService();
        $best = $service->findBestPromotion(100, [['product_id' => 1, 'quantity' => 1]]);

        $this->assertNull($best);
    }

    public function test_applies_to_specific_products(): void
    {
        $promotion = Promotion::factory()->create([
            'name' => 'Product Promo',
            'type' => 'percentage',
            'value' => 15,
            'applies_to' => 'products',
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);
        $product = Product::factory()->create();
        $otherProduct = Product::factory()->create();
        $promotion->products()->attach($product->id);

        $service = new PromotionService();

        $best = $service->findBestPromotion(100, [
            ['product_id' => $product->id, 'quantity' => 1],
        ]);
        $this->assertNotNull($best);
        $this->assertEquals('Product Promo', $best->name);

        $best = $service->findBestPromotion(100, [
            ['product_id' => $otherProduct->id, 'quantity' => 1],
        ]);
        $this->assertNull($best);
    }

    public function test_applies_to_customer_group(): void
    {
        $group = CustomerGroup::factory()->create();
        $promotion = Promotion::factory()->create([
            'name' => 'Group Promo',
            'type' => 'percentage',
            'value' => 10,
            'applies_to' => 'groups',
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);
        $promotion->customerGroups()->attach($group->id);

        $client = Client::factory()->create(['customer_group_id' => $group->id]);
        $otherClient = Client::factory()->create();

        $service = new PromotionService();

        $best = $service->findBestPromotion(100, [['product_id' => 1, 'quantity' => 1]], $client->id);
        $this->assertNotNull($best);

        $best = $service->findBestPromotion(100, [['product_id' => 1, 'quantity' => 1]], $otherClient->id);
        $this->assertNull($best);
    }

    public function test_skips_expired_promotions(): void
    {
        Promotion::factory()->create([
            'name' => 'Expired',
            'type' => 'percentage',
            'value' => 50,
            'start_date' => today()->subDays(10),
            'end_date' => today()->subDay(),
        ]);

        $service = new PromotionService();
        $best = $service->findBestPromotion(100, [['product_id' => 1, 'quantity' => 1]]);

        $this->assertNull($best);
    }
}
