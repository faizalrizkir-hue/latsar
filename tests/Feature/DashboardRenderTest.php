<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class DashboardRenderTest extends TestCase
{
    use BootstrapsCoreTables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapCoreTables();
        $this->resetCoreTables();
        $this->withoutMiddleware(EnsureDatabaseServerLock::class);
        $this->createElementAssessmentsTable();

        Account::query()->create([
            'username' => 'admin',
            'password_hash' => Hash::make('rahasia123'),
            'display_name' => 'Administrator',
            'role' => 'administrator',
            'active' => true,
        ]);
    }

    public function test_dashboard_page_renders_for_authenticated_user(): void
    {
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
            ->get('/');

        $response
            ->assertOk()
            ->assertViewIs('dashboard')
            ->assertSee('Skor dan Level Kapabilitas APIP');
    }

    private function createElementAssessmentsTable(): void
    {
        if (Schema::hasTable('element_assessments')) {
            return;
        }

        Schema::create('element_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('subtopic_slug', 120);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
}
