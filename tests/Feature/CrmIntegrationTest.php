<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CrmIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    }

    public function test_client_show_page_shows_loyalty_stats(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $client = Client::factory()->create([
            'points' => 500,
            'total_spent' => 5000,
            'last_purchase_at' => now(),
        ]);
        Sale::factory()->for($client)->create(['total' => 1000]);

        $response = $this->actingAs($user)->get(route('clients.show', $client));
        $response->assertStatus(200);
        $response->assertSee('500');
        $response->assertSee('5,000');
    }

    public function test_client_can_be_assigned_to_group(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $group = CustomerGroup::factory()->create(['name' => 'VIP']);
        $client = Client::factory()->create();

        $this->actingAs($user)->put(route('clients.update', $client), [
            'name' => $client->name,
            'customer_group_id' => $group->id,
        ]);

        $this->assertEquals($group->id, $client->fresh()->customer_group_id);
    }

    public function test_sale_earns_points_for_client(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $client = Client::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'selling_price' => 100]);

        $response = $this->actingAs($user)->post(route('sales.store'), [
            'client_id' => $client->id,
            'payment_method' => 'cash',
            'amount_paid' => 100,
            'details' => [['product_id' => $product->id, 'quantity' => 1, 'price' => 100]],
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertGreaterThan(0, $client->fresh()->points);
        $this->assertGreaterThan(0, $client->fresh()->total_spent);
    }

    public function test_sale_applies_promotion(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $client = Client::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'selling_price' => 200]);

        $promotion = Promotion::factory()->create([
            'type' => 'percentage',
            'value' => 10,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);

        $response = $this->actingAs($user)->post(route('sales.store'), [
            'client_id' => $client->id,
            'payment_method' => 'cash',
            'amount_paid' => 200,
            'details' => [['product_id' => $product->id, 'quantity' => 1, 'price' => 200]],
        ]);
        $response->assertSessionHasNoErrors();

        $sale = Sale::latest()->first();
        $this->assertEquals(180, $sale->total);
        $this->assertEquals($promotion->id, $sale->promotion_id);
        $this->assertEquals(20, $sale->discount_amount);
    }
}
