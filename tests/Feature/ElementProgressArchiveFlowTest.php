<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use App\Models\ElementProgressArchive;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class ElementProgressArchiveFlowTest extends TestCase
{
    use BootstrapsCoreTables;

    private array $sessionUser = [
        'id' => 1,
        'username' => 'admin',
        'display_name' => 'Administrator',
        'role' => 'administrator',
        'role_label' => 'Administrator',
        'profile_photo' => null,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapCoreTables();
        $this->createElementPreferencesTable();
        $this->createElementProgressArchiveTables();
        $this->resetCoreTables();
        $this->resetArchiveTables();
        $this->withoutMiddleware(EnsureDatabaseServerLock::class);

        Account::query()->create([
            'username' => 'admin',
            'password_hash' => Hash::make('rahasia123'),
            'display_name' => 'Administrator',
            'role' => 'administrator',
            'active' => true,
        ]);
    }

    public function test_archive_progress_snapshots_current_element_data(): void
    {
        DB::table('element1_kegiatan_asurans')->insert([
            'id' => 1,
            'pernyataan' => 'Ruang Lingkup dan Fokus',
            'analisis_bukti' => 'Bukti awal',
            'verified' => 1,
            'level' => '3',
            'skor' => 0.60,
        ]);

        $response = $this
            ->from('/element-preferences')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/element-preferences/archive-progress', [
                'budget_year' => 2026,
            ]);

        $response->assertRedirect('/element-preferences');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('element_progress_archives', [
            'budget_year' => 2026,
            'archived_by' => 'admin',
        ]);

        $archive = ElementProgressArchive::query()->where('budget_year', 2026)->first();
        $this->assertNotNull($archive);
        $this->assertGreaterThan(0, (int) ($archive->total_rows ?? 0));

        $snapshotTables = (array) data_get($archive->snapshot, 'tables', []);
        $this->assertArrayHasKey('element1_kegiatan_asurans', $snapshotTables);

        $rows = (array) data_get($snapshotTables, 'element1_kegiatan_asurans.rows', []);
        $firstRow = (array) ($rows[0] ?? []);
        $this->assertSame('Ruang Lingkup dan Fokus', (string) ($firstRow['pernyataan'] ?? ''));
    }

    public function test_load_archive_restores_snapshot_and_writes_load_log(): void
    {
        DB::table('element1_kegiatan_asurans')->insert([
            'id' => 1,
            'pernyataan' => 'Ruang Lingkup dan Fokus',
            'analisis_bukti' => 'Snapshot value',
            'verified' => 1,
            'level' => '3',
            'skor' => 0.60,
        ]);

        $this
            ->from('/element-preferences')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/element-preferences/archive-progress', [
                'budget_year' => 2026,
            ])
            ->assertRedirect('/element-preferences');

        DB::table('element1_kegiatan_asurans')
            ->where('id', 1)
            ->update([
                'analisis_bukti' => 'Changed value',
                'verified' => 0,
                'level' => '-',
                'skor' => null,
            ]);

        $archive = ElementProgressArchive::query()->where('budget_year', 2026)->firstOrFail();

        $response = $this
            ->from('/element-preferences')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/element-preferences/load-archive', [
                'archive_id' => (int) $archive->id,
            ]);

        $response->assertRedirect('/element-preferences');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('element1_kegiatan_asurans', [
            'id' => 1,
            'analisis_bukti' => 'Snapshot value',
            'verified' => 1,
            'level' => '3',
        ]);

        $this->assertDatabaseHas('element_progress_archive_load_logs', [
            'archive_id' => (int) $archive->id,
            'budget_year' => 2026,
            'loaded_by' => 'admin',
        ]);
    }

    public function test_archive_progress_requires_valid_budget_year(): void
    {
        $response = $this
            ->from('/element-preferences')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/element-preferences/archive-progress', [
                'budget_year' => 1800,
            ]);

        $response->assertRedirect('/element-preferences');
        $response->assertSessionHasErrors(['budget_year']);
    }

    public function test_load_archive_requires_valid_archive_id(): void
    {
        $response = $this
            ->from('/element-preferences')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/element-preferences/load-archive', [
                'archive_id' => 0,
            ]);

        $response->assertRedirect('/element-preferences');
        $response->assertSessionHasErrors(['archive_id']);
    }

    private function createElementPreferencesTable(): void
    {
        if (Schema::hasTable('element_preferences')) {
            return;
        }

        Schema::create('element_preferences', function (Blueprint $table): void {
            $table->id();
            $table->json('payload');
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    private function createElementProgressArchiveTables(): void
    {
        if (!Schema::hasTable('element_progress_archives')) {
            Schema::create('element_progress_archives', function (Blueprint $table): void {
                $table->id();
                $table->unsignedSmallInteger('budget_year')->unique();
                $table->json('snapshot');
                $table->unsignedInteger('total_rows')->default(0);
                $table->string('archived_by', 100)->nullable();
                $table->string('loaded_by', 100)->nullable();
                $table->timestamp('last_loaded_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('element_progress_archive_load_logs')) {
            return;
        }

        Schema::create('element_progress_archive_load_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('archive_id');
            $table->unsignedSmallInteger('budget_year');
            $table->unsignedSmallInteger('restored_tables')->default(0);
            $table->unsignedInteger('restored_total')->default(0);
            $table->json('restored_by_table')->nullable();
            $table->string('loaded_by', 100)->nullable();
            $table->timestamps();
            $table->index(['budget_year', 'created_at']);
        });
    }

    private function resetArchiveTables(): void
    {
        foreach ([
            'element_progress_archive_load_logs',
            'element_progress_archives',
            'element_preferences',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }
    }
}

