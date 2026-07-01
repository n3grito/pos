<?php

namespace Tests\Feature;

use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    }

    public function test_index_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        Promotion::factory()->create(['name' => 'Verano 2026']);

        $response = $this->actingAs($user)->get(route('promotions.index'));
        $response->assertStatus(200);
        $response->assertSee('Verano 2026');
    }

    public function test_create_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get(route('promotions.create'));
        $response->assertStatus(200);
    }

    public function test_percentage_promotion_can_be_created(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->post(route('promotions.store'), [
            'name' => '10% Off',
            'type' => 'percentage',
            'value' => 10,
            'applies_to' => 'all',
            'start_date' => today()->format('Y-m-d'),
            'end_date' => today()->addDays(30)->format('Y-m-d'),
            'min_amount' => 0,
            'min_quantity' => 0,
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('promotions.index'));

        $this->assertDatabaseHas('promotions', ['name' => '10% Off', 'type' => 'percentage']);
    }

    public function test_fixed_promotion_can_be_created(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->post(route('promotions.store'), [
            'name' => '$50 Off',
            'type' => 'fixed',
            'value' => 50,
            'applies_to' => 'all',
            'start_date' => today()->format('Y-m-d'),
            'end_date' => today()->addDays(30)->format('Y-m-d'),
            'min_amount' => 200,
            'min_quantity' => 2,
            'usage_limit' => 100,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('promotions', ['name' => '$50 Off', 'min_amount' => 200]);
    }

    public function test_promotion_applies_to_specific_products(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('promotions.store'), [
            'name' => 'Product Promo',
            'type' => 'percentage',
            'value' => 20,
            'applies_to' => 'products',
            'product_ids' => [$product->id],
            'start_date' => today()->format('Y-m-d'),
            'end_date' => today()->addDays(30)->format('Y-m-d'),
        ]);
        $response->assertSessionHasNoErrors();

        $promotion = Promotion::where('name', 'Product Promo')->first();
        $this->assertTrue($promotion->products->contains($product));
    }

    public function test_promotion_applies_to_customer_groups(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $group = CustomerGroup::factory()->create();

        $response = $this->actingAs($user)->post(route('promotions.store'), [
            'name' => 'Group Promo',
            'type' => 'percentage',
            'value' => 15,
            'applies_to' => 'groups',
            'group_ids' => [$group->id],
            'start_date' => today()->format('Y-m-d'),
            'end_date' => today()->addDays(30)->format('Y-m-d'),
        ]);
        $response->assertSessionHasNoErrors();

        $promotion = Promotion::where('name', 'Group Promo')->first();
        $this->assertTrue($promotion->customerGroups->contains($group));
    }

    public function test_promotion_can_be_updated(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $promotion = Promotion::factory()->create(['value' => 5]);

        $response = $this->actingAs($user)->put(route('promotions.update', $promotion), [
            'name' => $promotion->name,
            'type' => 'percentage',
            'value' => 15,
            'applies_to' => 'all',
            'start_date' => $promotion->start_date->format('Y-m-d'),
            'end_date' => $promotion->end_date->format('Y-m-d'),
        ]);
        $response->assertSessionHasNoErrors();

        $promotion->refresh();
        $this->assertEquals(15, $promotion->value);
    }

    public function test_promotion_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $promotion = Promotion::factory()->create();

        $response = $this->actingAs($user)->delete(route('promotions.destroy', $promotion));
        $response->assertRedirect(route('promotions.index'));
        $this->assertDatabaseMissing('promotions', ['id' => $promotion->id]);
    }

    public function test_active_scope_filters_correctly(): void
    {
        $active = Promotion::factory()->create([
            'is_active' => true,
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);
        $inactive = Promotion::factory()->create(['is_active' => false]);
        $expired = Promotion::factory()->create([
            'is_active' => true,
            'start_date' => today()->subDays(10),
            'end_date' => today()->subDay(),
        ]);

        $activePromotions = Promotion::active()->get();
        $this->assertTrue($activePromotions->contains($active));
        $this->assertFalse($activePromotions->contains($inactive));
        $this->assertFalse($activePromotions->contains($expired));
    }

    public function test_calculate_percentage_discount(): void
    {
        $promotion = Promotion::factory()->create([
            'type' => 'percentage',
            'value' => 10,
        ]);
        $this->assertEquals(10.0, $promotion->calculateDiscount(100));
        $this->assertEquals(0.5, $promotion->calculateDiscount(5));
    }

    public function test_calculate_fixed_discount(): void
    {
        $promotion = Promotion::factory()->create([
            'type' => 'fixed',
            'value' => 50,
        ]);
        $this->assertEquals(50.0, $promotion->calculateDiscount(100));
        $this->assertEquals(30.0, $promotion->calculateDiscount(30));
    }

    public function test_calculate_discount_with_max(): void
    {
        $promotion = Promotion::factory()->create([
            'type' => 'percentage',
            'value' => 20,
            'max_discount' => 15,
        ]);
        $this->assertEquals(15.0, $promotion->calculateDiscount(200));
    }
}
