<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class AuthLoginThrottleTest extends TestCase
{
    use BootstrapsCoreTables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapCoreTables();
        $this->resetCoreTables();

        Account::query()->create([
            'username' => 'admin',
            'password_hash' => Hash::make('rahasia123'),
            'display_name' => 'Administrator',
            'role' => 'administrator',
            'active' => true,
        ]);
    }

    public function test_login_is_rate_limited_after_too_many_failed_attempts(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/login', [
                'username' => 'admin',
                'password' => 'salah-total',
            ])->assertStatus(422);
        }

        $this->postJson('/login', [
            'username' => 'admin',
            'password' => 'salah-total',
        ])->assertStatus(429);
    }

    public function test_login_page_is_served_without_cache_to_avoid_stale_csrf_token(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertHeader('Pragma', 'no-cache');

        $cacheControl = (string) $response->headers->get('Cache-Control', '');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }
}
