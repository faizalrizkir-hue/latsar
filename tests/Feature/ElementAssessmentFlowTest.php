<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use App\Models\DmsDocument;
use App\Models\DmsFile;
use App\Models\ElementTeamAssignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class ElementAssessmentFlowTest extends TestCase
{
    use BootstrapsCoreTables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapCoreTables();
        $this->resetCoreTables();
        $this->withoutMiddleware(EnsureDatabaseServerLock::class);

        foreach ([
            ['username' => 'admin', 'display_name' => 'Administrator', 'role' => 'administrator'],
            ['username' => 'koor1', 'display_name' => 'Koordinator 1', 'role' => 'koordinator'],
            ['username' => 'qa1', 'display_name' => 'QA BPKP 1', 'role' => 'qa'],
        ] as $account) {
            Account::query()->create([
                'username' => $account['username'],
                'password_hash' => Hash::make('rahasia123'),
                'display_name' => $account['display_name'],
                'role' => $account['role'],
                'active' => true,
            ]);
        }

        ElementTeamAssignment::query()->create([
            'element_slug' => 'element1',
            'coordinator_username' => 'koor1',
            'member_usernames' => ['qa1'],
        ]);
    }

    public function test_element_row_save_updates_data_and_creates_notification(): void
    {
        $file = $this->createActiveDmsFile();

        $response = $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->post('/elements/element1_kegiatan_asurans', [
                'action' => 'save',
                'row_id' => 1,
                'pernyataan' => 'Ruang Lingkup dan Fokus',
                'analisis_bukti' => 'Bukti cukup',
                'analisis_nilai' => 'Nilai memadai',
                'evidence' => 'Lampiran pengujian',
                'grad_l1_catatan' => 'Catatan L1',
                'grad_l2_catatan' => 'Catatan L2',
                'grad_l3_catatan' => '',
                'grad_l4_catatan' => '',
                'grad_l5_catatan' => '',
                'doc_file_ids' => [(int) $file->id],
            ]);

        $response->assertRedirect('/elements/element1_kegiatan_asurans');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('element1_kegiatan_asurans', [
            'id' => 1,
            'dokumen_path' => '/uploads/dms/lampiran.pdf',
        ]);

        $this->assertDatabaseHas('notifications', [
            'element_slug' => 'element1',
            'subtopic_slug' => 'element1_kegiatan_asurans',
            'coordinator_username' => 'koor1',
        ]);
    }

    public function test_verifier_can_set_level_chain_and_score_is_calculated(): void
    {
        $file = $this->createActiveDmsFile();

        $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->post('/elements/element1_kegiatan_asurans', [
                'action' => 'save',
                'row_id' => 1,
                'pernyataan' => 'Ruang Lingkup dan Fokus',
                'analisis_bukti' => 'Bukti cukup',
                'analisis_nilai' => 'Nilai memadai',
                'evidence' => 'Lampiran pengujian',
                'grad_l1_catatan' => 'Catatan L1',
                'grad_l2_catatan' => 'Catatan L2',
                'grad_l3_catatan' => '',
                'grad_l4_catatan' => '',
                'grad_l5_catatan' => '',
                'doc_file_ids' => [(int) $file->id],
            ])
            ->assertRedirect('/elements/element1_kegiatan_asurans');

        $response = $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->post('/elements/element1_kegiatan_asurans', [
                'action' => 'verify',
                'row_id' => 1,
                'verified' => 1,
                'verify_note' => 'Sudah diverifikasi koordinator.',
                'level_validation' => [
                    1 => 1,
                    2 => 1,
                    3 => 1,
                ],
            ]);

        $response->assertRedirect('/elements/element1_kegiatan_asurans');
        $response->assertSessionHas('status');

        $row = DB::table('element1_kegiatan_asurans')
            ->where('id', 1)
            ->first();

        $this->assertNotNull($row);
        $this->assertSame(1, (int) ($row->verified ?? 0));
        $this->assertSame('3', (string) ($row->level ?? ''));
        $this->assertEqualsWithDelta(0.60, (float) ($row->skor ?? 0), 0.001);
        $this->assertSame('Sudah diverifikasi koordinator.', (string) ($row->verify_note ?? ''));
        $this->assertStringContainsString('"3":1', (string) ($row->level_validation_state ?? ''));
    }

    public function test_qa_final_verification_requires_verifier_status_first(): void
    {
        $file = $this->createActiveDmsFile();

        $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->post('/elements/element1_kegiatan_asurans', [
                'action' => 'save',
                'row_id' => 1,
                'pernyataan' => 'Ruang Lingkup dan Fokus',
                'analisis_bukti' => 'Bukti cukup',
                'analisis_nilai' => 'Nilai memadai',
                'evidence' => 'Lampiran pengujian',
                'grad_l1_catatan' => 'Catatan L1',
                'grad_l2_catatan' => 'Catatan L2',
                'grad_l3_catatan' => '',
                'grad_l4_catatan' => '',
                'grad_l5_catatan' => '',
                'doc_file_ids' => [(int) $file->id],
            ])
            ->assertRedirect('/elements/element1_kegiatan_asurans');

        $response = $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser('qa1', 'qa'), 'last_activity_at' => time()])
            ->post('/elements/element1_kegiatan_asurans', [
                'action' => 'qa_verify',
                'row_id' => 1,
                'qa_verified' => 1,
                'qa_verify_note' => 'Cek final QA',
                'qa_level_validation' => [
                    1 => 1,
                ],
            ]);

        $response->assertRedirect('/elements/element1_kegiatan_asurans');
        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('element1_kegiatan_asurans', [
            'id' => 1,
            'qa_verified' => 1,
        ]);
    }

    public function test_qa_final_verification_is_disabled_while_feature_is_off(): void
    {
        $file = $this->createActiveDmsFile();

        $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->post('/elements/element1_kegiatan_asurans', [
                'action' => 'save',
                'row_id' => 1,
                'pernyataan' => 'Ruang Lingkup dan Fokus',
                'analisis_bukti' => 'Bukti cukup',
                'analisis_nilai' => 'Nilai memadai',
                'evidence' => 'Lampiran pengujian',
                'grad_l1_catatan' => 'Catatan L1',
                'grad_l2_catatan' => 'Catatan L2',
                'grad_l3_catatan' => '',
                'grad_l4_catatan' => '',
                'grad_l5_catatan' => '',
                'doc_file_ids' => [(int) $file->id],
            ])
            ->assertRedirect('/elements/element1_kegiatan_asurans');

        $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->post('/elements/element1_kegiatan_asurans', [
                'action' => 'verify',
                'row_id' => 1,
                'verified' => 1,
                'verify_note' => 'Sudah diverifikasi koordinator.',
                'level_validation' => [
                    1 => 1,
                    2 => 1,
                    3 => 1,
                ],
            ])
            ->assertRedirect('/elements/element1_kegiatan_asurans');

        $qaVerifyResponse = $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser('qa1', 'qa'), 'last_activity_at' => time()])
            ->post('/elements/element1_kegiatan_asurans', [
                'action' => 'qa_verify',
                'row_id' => 1,
                'qa_verified' => 1,
                'qa_verify_note' => 'Final QA valid.',
                'qa_follow_up_recommendation' => 'Lanjutkan perbaikan level 3.',
                'qa_level_validation' => [
                    1 => 1,
                    2 => 1,
                ],
            ]);

        $qaVerifyResponse->assertRedirect('/elements/element1_kegiatan_asurans');
        $qaVerifyResponse->assertSessionHasErrors();

        $row = DB::table('element1_kegiatan_asurans')
            ->where('id', 1)
            ->first();

        $this->assertNotNull($row);
        $this->assertSame(0, (int) ($row->qa_verified ?? 0));
        $this->assertSame('', (string) ($row->qa_verified_by ?? ''));
        $this->assertSame('', (string) ($row->qa_verify_note ?? ''));
        $this->assertSame('', (string) ($row->qa_level_validation_state ?? ''));
    }

    private function createActiveDmsFile(): DmsFile
    {
        $document = DmsDocument::query()->create([
            'year' => (int) date('Y'),
            'type' => 'Manajemen Pengawasan',
            'doc_no' => 'DOC-ELEM-001',
            'title' => 'Dokumen Element',
            'tag' => 'Surat Tugas',
            'status' => 'Aktif',
            'uploader' => 'Administrator',
            'updated_by' => 'Administrator',
        ]);

        return DmsFile::query()->create([
            'document_id' => $document->id,
            'doc_no' => 'DOC-ELEM-001',
            'doc_name' => 'Lampiran Element',
            'file_name' => 'lampiran.pdf',
            'file_path' => 'dms/lampiran.pdf',
            'file_size' => 12000,
            'size_bytes' => 12000,
            'storage_driver' => 'public',
            'mime_type' => 'application/pdf',
            'uploaded_at' => now(),
        ]);
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
