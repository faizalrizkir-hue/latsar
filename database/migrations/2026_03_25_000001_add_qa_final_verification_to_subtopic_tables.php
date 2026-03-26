<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->moduleTables() as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $addQaVerified = !Schema::hasColumn($table, 'qa_verified');
            $addQaVerifiedBy = !Schema::hasColumn($table, 'qa_verified_by');
            $addQaVerifiedAt = !Schema::hasColumn($table, 'qa_verified_at');
            $addQaVerifyNote = !Schema::hasColumn($table, 'qa_verify_note');

            if (!$addQaVerified && !$addQaVerifiedBy && !$addQaVerifiedAt && !$addQaVerifyNote) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($addQaVerified, $addQaVerifiedBy, $addQaVerifiedAt, $addQaVerifyNote) {
                if ($addQaVerified) {
                    $blueprint->boolean('qa_verified')->default(false);
                }
                if ($addQaVerifiedBy) {
                    $blueprint->string('qa_verified_by', 150)->nullable();
                }
                if ($addQaVerifiedAt) {
                    $blueprint->timestamp('qa_verified_at')->nullable();
                }
                if ($addQaVerifyNote) {
                    $blueprint->text('qa_verify_note')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->moduleTables() as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $dropColumns = [];
            foreach (['qa_verified', 'qa_verified_by', 'qa_verified_at', 'qa_verify_note'] as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (count($dropColumns) === 0) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($dropColumns) {
                $blueprint->dropColumn($dropColumns);
            });
        }
    }

    private function moduleTables(): array
    {
        $tables = [];
        $modules = config('element_subtopic_modules.modules', []);

        if (is_array($modules)) {
            foreach ($modules as $module) {
                if (!is_array($module)) {
                    continue;
                }

                $modelClass = trim((string) ($module['model'] ?? ''));
                if ($modelClass === '' || !class_exists($modelClass)) {
                    continue;
                }

                $table = trim((string) ((new $modelClass())->getTable() ?? ''));
                if ($table !== '') {
                    $tables[] = $table;
                }
            }
        }

        if (count($tables) === 0) {
            $tables = [
                'element1_kegiatan_asurans',
                'element1_jasa_konsultansi',
                'element2_pengembangan_informasi',
                'element2_perencanaan_penugasan',
                'element2_pelaksanaan_penugasan',
                'element2_komunikasi_hasil',
                'element2_pengendalian_kualitas',
                'element2_pemantauan_tindak_lanjut',
                'element3_perencanaan_pengawasan',
                'element3_pelaporan_manajemen_kld',
                'element4_manajemen_kinerja',
                'element4_mekanisme_pendanaan',
                'element4_perencanaan_sdm_apip',
                'element4_pengembangan_sdm_profesional_apip',
                'element4_dukungan_tik',
                'element5_pembangunan_budaya_integritas',
                'element5_hubungan_apip_manajemen',
                'element5_koordinasi_pengawasan',
                'element5_akses_informasi_sumberdaya',
            ];
        }

        return array_values(array_unique(array_filter($tables, fn ($table) => trim((string) $table) !== '')));
    }
};

