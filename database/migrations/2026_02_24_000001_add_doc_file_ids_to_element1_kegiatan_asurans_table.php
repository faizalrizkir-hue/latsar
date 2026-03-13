<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('element1_kegiatan_asurans')) {
            return;
        }

        if (!Schema::hasColumn('element1_kegiatan_asurans', 'doc_file_ids')) {
            Schema::table('element1_kegiatan_asurans', function (Blueprint $table) {
                $table->longText('doc_file_ids')->nullable()->after('dokumen_path');
            });
        }

        // Backfill data lama dari tabel relasi (jika ada) ke kolom utama.
        if (!Schema::hasTable('element1_kegiatan_asurans_row_doc_selections')) {
            return;
        }

        $rows = DB::table('element1_kegiatan_asurans_row_doc_selections')
            ->orderBy('row_id')
            ->orderBy('doc_file_id')
            ->get(['row_id', 'doc_file_id'])
            ->groupBy('row_id');

        foreach ($rows as $rowId => $items) {
            $docIds = collect($items)
                ->pluck('doc_file_id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            DB::table('element1_kegiatan_asurans')
                ->where('id', (int) $rowId)
                ->update([
                    'doc_file_ids' => empty($docIds) ? null : json_encode($docIds, JSON_UNESCAPED_UNICODE),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('element1_kegiatan_asurans')) {
            return;
        }

        if (Schema::hasColumn('element1_kegiatan_asurans', 'doc_file_ids')) {
            Schema::table('element1_kegiatan_asurans', function (Blueprint $table) {
                $table->dropColumn('doc_file_ids');
            });
        }
    }
};
