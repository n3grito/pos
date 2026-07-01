<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DarkModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_dark_mode_default(): void
    {
        $user = User::factory()->create();

        $this->assertContains($user->dark_mode, ['system', null]);
    }

    public function test_dark_mode_can_be_set_via_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'dark_mode' => 'dark',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('dark', $user->fresh()->dark_mode);
    }

    public function test_dark_mode_accepts_valid_values(): void
    {
        $user = User::factory()->create();

        foreach (['light', 'dark', 'system'] as $mode) {
            $response = $this->actingAs($user)->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'dark_mode' => $mode,
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertEquals($mode, $user->fresh()->dark_mode);
        }
    }
}
