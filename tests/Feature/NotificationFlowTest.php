<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        $legacySeparator = "\u{00C3}\u{201A}\u{00C2}\u{00B7}";

        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Isi Data '.$legacySeparator.' Pernyataan A',
            'row_id' => 1,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now()->subMinute(),
        ]);
        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Verifikasi '.$legacySeparator.' Pernyataan B',
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
            ->assertJsonPath('unread_count', 2)
            ->assertJsonPath('items.0.action_text', 'Verifikasi')
            ->assertJsonPath('items.0.detail_text', 'Pernyataan B')
            ->assertJsonPath('items.1.action_text', 'Isi Data')
            ->assertJsonPath('items.1.detail_text', 'Pernyataan A');

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

    public function test_notification_feed_normalizes_legacy_statement_format(): void
    {
        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Koordinator melakukan verifikasi final QA pada elemen 1: Pernyataan C',
            'row_id' => 3,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now(),
        ]);

        $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->getJson('/notifications/feed?scope=element1')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('items.0.action_text', 'Verifikasi QA')
            ->assertJsonPath('items.0.detail_text', 'Pernyataan C');
    }

    public function test_notification_feed_keeps_full_statement_detail(): void
    {
        $statementDetail = 'Melaksanakan Reviu Berjenjang pada Setiap Tahapan Penugasan Pengawasan';

        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Isi Data | '.$statementDetail,
            'row_id' => 4,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now(),
        ]);

        $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->getJson('/notifications/feed?scope=element1')
            ->assertOk()
            ->assertJsonPath('items.0.action_text', 'Isi Data')
            ->assertJsonPath('items.0.detail_text', $statementDetail);
    }

    public function test_notification_feed_formats_combined_note_actions(): void
    {
        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Isi Data + Catatan | Ruang Lingkup dan Fokus - Catatan: Mohon cek bukti utama.',
            'row_id' => 1,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now()->subSecond(),
        ]);
        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Verifikasi + Catatan | Ruang Lingkup dan Fokus - Catatan: Sudah diverifikasi.',
            'row_id' => 1,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now(),
        ]);

        $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->getJson('/notifications/feed?scope=element1')
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('items.0.action_text', 'Verifikasi + Catatan')
            ->assertJsonPath('items.0.action_class', 'is-note')
            ->assertJsonPath('items.0.detail_text', 'Ruang Lingkup dan Fokus')
            ->assertJsonPath('items.0.note_text', 'Sudah diverifikasi.')
            ->assertJsonPath('items.1.action_text', 'Isi Data + Catatan')
            ->assertJsonPath('items.1.action_class', 'is-fill')
            ->assertJsonPath('items.1.detail_text', 'Ruang Lingkup dan Fokus')
            ->assertJsonPath('items.1.note_text', 'Mohon cek bukti utama.');
    }

    public function test_notification_feed_collapses_legacy_split_note_pairs(): void
    {
        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Isi Data | Ruang Lingkup dan Fokus',
            'row_id' => 1,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now()->subSecond(),
        ]);
        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Catatan Anggota | Ruang Lingkup dan Fokus - Catatan: Mohon cek bukti utama.',
            'row_id' => 1,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now(),
        ]);

        $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->getJson('/notifications/feed?scope=element1')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('items.0.action_text', 'Isi Data + Catatan')
            ->assertJsonPath('items.0.detail_text', 'Ruang Lingkup dan Fokus')
            ->assertJsonPath('items.0.note_text', 'Mohon cek bukti utama.');
    }

    public function test_notification_feed_recovers_truncated_legacy_statement_detail(): void
    {
        $statementDetail = 'Pelaksanaan Pengembangan Informasi Awal Penugasan Pengawasan Secara Lengkap';

        DB::table('element1_kegiatan_asurans')->insert([
            'id' => 4,
            'pernyataan' => $statementDetail,
        ]);

        Notification::query()->create([
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'subtopic_title' => 'Topik 1 - Kegiatan Asurans',
            'statement' => 'Isi Data | Pelaksanaan Pengembangan Informasi Awal P...',
            'row_id' => 4,
            'coordinator_name' => 'Koordinator 1',
            'coordinator_username' => 'koor1',
            'created_at' => now(),
        ]);

        $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->getJson('/notifications/feed?scope=element1')
            ->assertOk()
            ->assertJsonPath('items.0.action_text', 'Isi Data')
            ->assertJsonPath('items.0.detail_text', $statementDetail);
    }

    public function test_notification_feed_rejects_invalid_scope_payload(): void
    {
        $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->getJson('/notifications/feed?scope=../../etc/passwd')
            ->assertStatus(422);
    }
}

