<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\CustomerGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerGroupTest extends TestCase
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
        CustomerGroup::factory()->create(['name' => 'VIP']);

        $response = $this->actingAs($user)->get(route('customer-groups.index'));
        $response->assertStatus(200);
        $response->assertSee('VIP');
    }

    public function test_create_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get(route('customer-groups.create'));
        $response->assertStatus(200);
    }

    public function test_group_can_be_created(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->post(route('customer-groups.store'), [
            'name' => 'Estudiantes',
            'discount_percentage' => 10,
            'color' => '#3b82f6',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customer-groups.index'));

        $this->assertDatabaseHas('customer_groups', ['name' => 'Estudiantes']);
    }

    public function test_group_can_be_updated(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $group = CustomerGroup::factory()->create(['name' => 'Viejos', 'discount_percentage' => 5]);

        $response = $this->actingAs($user)->put(route('customer-groups.update', $group), [
            'name' => 'VIP',
            'discount_percentage' => 15,
            'color' => '#f59e0b',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customer-groups.index'));

        $group->refresh();
        $this->assertEquals('VIP', $group->name);
        $this->assertEquals(15, $group->discount_percentage);
    }

    public function test_group_with_clients_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $group = CustomerGroup::factory()->create();
        Client::factory()->create(['customer_group_id' => $group->id]);

        $response = $this->actingAs($user)->delete(route('customer-groups.destroy', $group));
        $response->assertRedirect();
        $this->assertDatabaseHas('customer_groups', ['id' => $group->id]);
    }

    public function test_group_without_clients_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $group = CustomerGroup::factory()->create();

        $response = $this->actingAs($user)->delete(route('customer-groups.destroy', $group));
        $response->assertRedirect(route('customer-groups.index'));
        $this->assertDatabaseMissing('customer_groups', ['id' => $group->id]);
    }

    public function test_default_group_flag_is_unique(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->actingAs($user)->post(route('customer-groups.store'), [
            'name' => 'General',
            'discount_percentage' => 0,
            'color' => '#6366f1',
            'is_default' => '1',
        ]);

        $this->actingAs($user)->post(route('customer-groups.store'), [
            'name' => 'Default New',
            'discount_percentage' => 5,
            'color' => '#10b981',
            'is_default' => '1',
        ]);

        $defaults = CustomerGroup::where('is_default', true)->count();
        $this->assertEquals(1, $defaults);
    }
}
