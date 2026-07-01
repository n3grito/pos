<?php

namespace Tests\Feature;

use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    }

    public function test_general_settings_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get('/settings/general');

        $response->assertStatus(200);
    }

    public function test_timezone_can_be_updated(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->post('/settings/general', [
            'timezone' => 'America/Havana',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('America/Havana', GeneralSetting::get('timezone'));
    }

    public function test_invalid_timezone_is_rejected(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->post('/settings/general', [
            'timezone' => 'Invalid/Timezone',
        ]);

        $response->assertSessionHasErrors('timezone');
    }
}
