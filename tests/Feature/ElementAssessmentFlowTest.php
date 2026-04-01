<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use App\Models\DmsDocument;
use App\Models\DmsFile;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class ElementAssessmentFlowTest extends TestCase
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
    }

    public function test_element_row_save_updates_data_and_creates_notification(): void
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

        $file = DmsFile::query()->create([
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

        $response = $this
            ->from('/elements/element1_kegiatan_asurans')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
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
            'coordinator_username' => 'admin',
        ]);
    }
}
