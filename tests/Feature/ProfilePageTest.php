<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use BootstrapsCoreTables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapCoreTables();
        $this->resetCoreTables();
        $this->withoutMiddleware(EnsureDatabaseServerLock::class);
    }

    public function test_profile_page_shows_browser_summary_for_last_login_device(): void
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36';

        Account::query()->create([
            'username' => 'admin',
            'password_hash' => Hash::make('rahasia123'),
            'display_name' => 'Administrator',
            'role' => 'administrator',
            'active' => true,
            'last_login_ip' => '127.0.0.1',
            'last_login_device' => $userAgent,
        ]);

        $response = $this
            ->withSession([
                'user' => [
                    'id' => 1,
                    'username' => 'admin',
                    'display_name' => 'Administrator',
                    'role' => 'administrator',
                    'role_label' => 'Administrator',
                    'profile_photo' => null,
                ],
                'last_activity_at' => time(),
            ])
            ->get('/profile');

        $response
            ->assertOk()
            ->assertViewIs('profile.edit')
            ->assertSee('Browser terakhir')
            ->assertSee('Google Chrome 125')
            ->assertSee('Desktop - Windows 10/11')
            ->assertDontSee('Mozilla/5.0');
    }
}
