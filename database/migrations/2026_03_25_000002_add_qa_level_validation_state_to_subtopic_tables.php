<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->moduleTables() as $table) {
            if (!Schema::hasTable($table) || Schema::hasColumn($table, 'qa_level_validation_state')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->longText('qa_level_validation_state')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->moduleTables() as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'qa_level_validation_state')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('qa_level_validation_state');
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

