<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class DmsFlowTest extends TestCase
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
        Storage::fake('public');

        Account::query()->create([
            'username' => 'admin',
            'password_hash' => Hash::make('rahasia123'),
            'display_name' => 'Administrator',
            'role' => 'administrator',
            'active' => true,
        ]);
    }

    public function test_authenticated_user_can_upload_dms_document_and_file(): void
    {
        $response = $this
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/dms', [
                'title' => 'Dokumen Uji DMS',
                'year' => (int) date('Y'),
                'type' => 'Manajemen Pengawasan',
                'tag' => 'Surat Tugas',
                'doc_no' => ['DOC-001'],
                'name' => ['Lampiran Utama'],
                'files' => [UploadedFile::fake()->create('surat-tugas.pdf', 200, 'application/pdf')],
            ]);

        $response->assertRedirect(route('dms.index'));

        $this->assertDatabaseHas('dms_documents', [
            'doc_no' => 'DOC-001',
            'title' => 'Dokumen Uji DMS',
            'status' => 'Aktif',
        ]);

        $this->assertDatabaseHas('dms_files', [
            'doc_no' => 'DOC-001',
            'doc_name' => 'Lampiran Utama',
            'storage_driver' => 'public',
        ]);
    }

    public function test_dms_upload_rejects_blocked_extension(): void
    {
        $response = $this
            ->from('/dms/create')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/dms', [
                'title' => 'Dokumen Uji DMS',
                'year' => (int) date('Y'),
                'type' => 'Manajemen Pengawasan',
                'tag' => 'Surat Tugas',
                'doc_no' => ['DOC-002'],
                'name' => ['Lampiran Berbahaya'],
                'files' => [UploadedFile::fake()->create('shell.php', 10, 'text/x-php')],
            ]);

        $response->assertRedirect('/dms/create');
        $response->assertSessionHasErrors(['files.0']);
        $this->assertDatabaseMissing('dms_documents', ['doc_no' => 'DOC-002']);
    }

    public function test_dms_upload_rejects_blocked_mime_prefix(): void
    {
        $response = $this
            ->from('/dms/create')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/dms', [
                'title' => 'Dokumen Uji DMS',
                'year' => (int) date('Y'),
                'type' => 'Manajemen Pengawasan',
                'tag' => 'Surat Tugas',
                'doc_no' => ['DOC-003'],
                'name' => ['Lampiran MIME Berbahaya'],
                'files' => [UploadedFile::fake()->create('shell.txt', 10, 'application/x-httpd-php')],
            ]);

        $response->assertRedirect('/dms/create');
        $response->assertSessionHasErrors(['files.0']);
        $this->assertDatabaseMissing('dms_documents', ['doc_no' => 'DOC-003']);
    }

    public function test_dms_upload_rejects_file_exceeding_configured_max_size(): void
    {
        config(['dms.upload.max_kilobytes' => 256]);

        $response = $this
            ->from('/dms/create')
            ->withSession(['user' => $this->sessionUser, 'last_activity_at' => time()])
            ->post('/dms', [
                'title' => 'Dokumen Uji DMS',
                'year' => (int) date('Y'),
                'type' => 'Manajemen Pengawasan',
                'tag' => 'Surat Tugas',
                'doc_no' => ['DOC-004'],
                'name' => ['Lampiran Besar'],
                'files' => [UploadedFile::fake()->create('besar.pdf', 512, 'application/pdf')],
            ]);

        $response->assertRedirect('/dms/create');
        $response->assertSessionHasErrors(['files.0']);
        $this->assertDatabaseMissing('dms_documents', ['doc_no' => 'DOC-004']);
    }
}
