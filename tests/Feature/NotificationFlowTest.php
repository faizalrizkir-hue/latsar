<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class NotificationFlowTest extends TestCase
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
        $this->resetCoreTables();
        $this->withoutMiddleware(EnsureDatabaseServerLock::class);

        Account::query()->create([
            'username' => 'admin',
            'password_hash' => Hash::make('rahasia123'),
            'display_name' => 'Administrator',
            'role' => 'administrator',
            'active' => true,
        ]);
        Account::query()->create([
            'username' => 'koor1',
            'password_hash' => Hash::make('rahasia123'),
            'display_name' => 'Koordinator 1',
            'role' => 'koordinator',
            'active' => true,
        ]);
    }

    public function test_notification_feed_and_mark_read_flow(): void
    {
        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Sub Topik 1 - Kegiatan Asurans',
            'statement' => 'Isi Data Â· Pernyataan A',
            'row_id' => 1,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now()->subMinute(),
        ]);
        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Sub Topik 1 - Kegiatan Asurans',
            'statement' => 'Verifikasi Â· Pernyataan B',
            'row_id' => 2,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now(),
        ]);

        $feedResponse = $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->getJson('/notifications/feed?scope=element1');

        $feedResponse
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('unread_count', 2);

        $markReadResponse = $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->postJson('/notifications/mark-read', ['scope' => 'element1']);

        $markReadResponse
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('unread_count', 0);

        $this->assertDatabaseCount('notification_reads', 2);
        $this->assertDatabaseHas('notification_reads', [
            'username' => 'admin',
        ]);
    }

    public function test_notification_feed_rejects_invalid_scope_payload(): void
    {
        $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->getJson('/notifications/feed?scope=../../etc/passwd')
            ->assertStatus(422);
    }
}
