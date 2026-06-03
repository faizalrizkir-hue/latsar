<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class GuidePageTest extends TestCase
{
    use BootstrapsCoreTables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapCoreTables();
        $this->resetCoreTables();
        $this->withoutMiddleware(EnsureDatabaseServerLock::class);
    }

    public function test_guide_page_renders_for_authenticated_user(): void
    {
        $response = $this
            ->withSession([
                'user' => $this->sessionUser('admin', 'administrator'),
                'last_activity_at' => time(),
            ])
            ->get('/panduan');

        $response
            ->assertOk()
            ->assertViewIs('guides.index')
            ->assertSee('Panduan')
            ->assertSee('Administrator')
            ->assertSee('Koordinator / Anggota Tim')
            ->assertSee('https://online.fliphtml5.com/bpnya/bdaj/')
            ->assertSee('https://online.fliphtml5.com/bpnya/oejy/')
            ->assertSee('Online FlipHTML5')
            ->assertSee('Gambar Proses Bisnis')
            ->assertSee('/panduan');
    }

    private function sessionUser(string $username, string $role): array
    {
        return [
            'id' => 1,
            'username' => $username,
            'display_name' => $username,
            'role' => $role,
            'role_label' => Account::roleLabel($role),
            'profile_photo' => null,
        ];
    }
}
