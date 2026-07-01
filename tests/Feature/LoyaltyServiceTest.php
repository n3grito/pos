<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoyaltyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    }

    public function test_earns_points_on_purchase(): void
    {
        $client = Client::factory()->create();
        $service = new LoyaltyService();

        $points = $service->earnPoints($client, 100);

        $this->assertEquals(10, $points);
        $this->assertEquals(10, $client->fresh()->points);
        $this->assertEquals(100, $client->fresh()->total_spent);
    }

    public function test_earns_no_points_for_small_amount(): void
    {
        $client = Client::factory()->create();
        $service = new LoyaltyService();

        $points = $service->earnPoints($client, 5);

        $this->assertEquals(0, $points);
        $this->assertEquals(0, $client->fresh()->points);
    }

    public function test_redeems_points_for_discount(): void
    {
        $client = Client::factory()->create(['points' => 500]);
        $service = new LoyaltyService();

        $discount = $service->redeemPoints($client, 200);

        $this->assertEquals(2.0, $discount);
        $this->assertEquals(300, $client->fresh()->points);
    }

    public function test_cannot_redeem_more_than_available(): void
    {
        $client = Client::factory()->create(['points' => 100]);
        $service = new LoyaltyService();

        $discount = $service->redeemPoints($client, 500);

        $this->assertEquals(1.0, $discount);
        $this->assertEquals(0, $client->fresh()->points);
    }

    public function test_creates_transaction_on_earn(): void
    {
        $client = Client::factory()->create();
        $service = new LoyaltyService();

        $service->earnPoints($client, 100, 'sale', 1, 'Test purchase');

        $this->assertDatabaseHas('loyalty_transactions', [
            'client_id' => $client->id,
            'points' => 10,
            'type' => 'earn',
            'reference_type' => 'sale',
            'reference_id' => 1,
        ]);
    }

    public function test_creates_transaction_on_redeem(): void
    {
        $client = Client::factory()->create(['points' => 500]);
        $service = new LoyaltyService();

        $service->redeemPoints($client, 200, 'sale', 1);

        $this->assertDatabaseHas('loyalty_transactions', [
            'client_id' => $client->id,
            'points' => -200,
            'type' => 'redeem',
        ]);
    }

    public function test_calculate_discount_value(): void
    {
        $service = new LoyaltyService();

        $this->assertEquals(5.0, $service->calculateDiscountValue(500));
        $this->assertEquals(0.5, $service->calculateDiscountValue(50));
    }

    public function test_can_redeem_checks_balance(): void
    {
        $service = new LoyaltyService();
        $client = Client::factory()->create(['points' => 300]);

        $this->assertTrue($service->canRedeem($client, 300));
        $this->assertTrue($service->canRedeem($client, 200));
        $this->assertFalse($service->canRedeem($client, 500));
    }
}
