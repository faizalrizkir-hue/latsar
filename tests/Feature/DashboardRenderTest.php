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
            ->assertSee('Kapabilitas APIP Saat Ini');

        $activeBudgetYear = (int) $response->viewData('activeBudgetYear');
        $this->assertSame((int) now('Asia/Jakarta')->year, $activeBudgetYear);
        $response->assertSee('Tahun Anggaran '.$activeBudgetYear);

        $elements = collect($response->viewData('elements'));
        $totalTopicCount = $elements->sum(
            fn (array $element): int => (int) ($element['subtopic_count'] ?? count((array) ($element['subtopics'] ?? [])))
        );
        $assessedTopicCount = $elements->sum(function (array $element): int {
            $topicCount = (int) ($element['subtopic_count'] ?? count((array) ($element['subtopics'] ?? [])));
            $assessedCount = (int) ($element['assessed_subtopic_count'] ?? 0);

            return max(0, min($assessedCount, $topicCount));
        });
        $pendingTopicCount = max(0, $totalTopicCount - $assessedTopicCount);
        $totalTopicPercent = $totalTopicCount > 0 ? 100 : 0;
        $assessedTopicPercent = $totalTopicCount > 0
            ? (int) round(($assessedTopicCount / $totalTopicCount) * 100)
            : 0;
        $pendingTopicPercent = $totalTopicCount > 0
            ? max(0, 100 - $assessedTopicPercent)
            : 0;

        $response->assertSeeInOrder([
            '<span class="apip-recap-insight-label">Total Topik</span>',
            '<strong>'.$totalTopicCount.'</strong>',
            '<span class="apip-recap-insight-percent">'.$totalTopicPercent.'%</span>',
            '<span class="apip-recap-insight-label">Topik Dinilai</span>',
            '<strong>'.$assessedTopicCount.'</strong>',
            '<span class="apip-recap-insight-percent">'.$assessedTopicPercent.'%</span>',
            '<span class="apip-recap-insight-label">Belum Dinilai</span>',
            '<strong>'.$pendingTopicCount.'</strong>',
            '<span class="apip-recap-insight-percent">'.$pendingTopicPercent.'%</span>',
        ], false);
    }

    public function test_element_summary_page_shows_topic_weight_tooltips(): void
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
            ->get('/elements/element1');

        $response
            ->assertOk()
            ->assertDontSee('Bobot Topik')
            ->assertSee('data-hint="Bobot topik: 80%"', false)
            ->assertSee('data-hint="Bobot topik: 20%"', false);
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
