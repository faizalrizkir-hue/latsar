<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'element1_kegiatan_asurans',
        'element1_jasa_konsultansi',
        'element2_komunikasi_hasil',
        'element2_pelaksanaan_penugasan',
        'element2_pemantauan_tindak_lanjut',
        'element2_pengembangan_informasi',
        'element2_pengendalian_kualitas',
        'element2_perencanaan_penugasan',
        'element3_pelaporan_manajemen_kld',
        'element3_perencanaan_pengawasan',
        'element4_dukungan_tik',
        'element4_manajemen_kinerja',
        'element4_mekanisme_pendanaan',
        'element4_pengembangan_sdm_profesional_apip',
        'element4_perencanaan_sdm_apip',
        'element5_akses_informasi_sumberdaya',
        'element5_hubungan_apip_manajemen',
        'element5_koordinasi_pengawasan',
        'element5_pembangunan_budaya_integritas',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'member_note_to_coordinator')) {
                continue;
            }

            $hasVerifyNote = Schema::hasColumn($tableName, 'verify_note');
            Schema::table($tableName, function (Blueprint $table) use ($hasVerifyNote) {
                $column = $table->text('member_note_to_coordinator')->nullable();
                if ($hasVerifyNote) {
                    $column->after('verify_note');
                }
            });
        }

        $this->bumpSchemaMetadataCacheVersion();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'member_note_to_coordinator')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('member_note_to_coordinator');
            });
        }

        $this->bumpSchemaMetadataCacheVersion();
    }

    private function bumpSchemaMetadataCacheVersion(): void
    {
        Cache::forever('schema:metadata:version', (int) Cache::get('schema:metadata:version', 1) + 1);
    }
};
