<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('dms_documents')) {
            return;
        }

        DB::transaction(function () {
            $now = now();

            // Legacy alias: SDM -> Sumber Daya Manusia
            DB::table('dms_documents')
                ->where('type', 'SDM')
                ->update([
                    'type' => 'Sumber Daya Manusia',
                    'updated_at' => $now,
                ]);

            // Legacy alias: Surat Tugas (ST)/Surat Tugas as type -> Manajemen Pengawasan + Surat Tugas
            DB::table('dms_documents')
                ->where('type', 'like', '%Surat Tugas%')
                ->where('type', '!=', 'Manajemen Pengawasan')
                ->update([
                    'type' => 'Manajemen Pengawasan',
                    'tag' => 'Surat Tugas',
                    'updated_at' => $now,
                ]);

            $singleTagByType = [
                'Sumber Daya Manusia' => 'Dokumen SDM',
                'Keuangan' => 'Dokumen Keuangan',
                'Pemanfaatan Sistem Informasi (SI)' => 'Dokumen Sistem Informasi (SI)',
                'Pedoman/Kebijakan' => 'Dokumen Pedoman/Kebijakan',
                'Lainnya' => 'Dokumen Lainnya',
            ];

            foreach ($singleTagByType as $type => $tag) {
                DB::table('dms_documents')
                    ->where('type', $type)
                    ->where(function ($q) {
                        $q->whereNull('tag')
                            ->orWhere('tag', '')
                            ->orWhere('tag', 'Dokumen');
                    })
                    ->update([
                        'tag' => $tag,
                        'updated_at' => $now,
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data normalization is intentionally not reverted.
    }
};

