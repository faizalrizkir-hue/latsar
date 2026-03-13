<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        if (!Schema::hasColumn('element1_kegiatan_asurans', 'level_validation_state')) {
            Schema::table('element1_kegiatan_asurans', function (Blueprint $table) {
                $table->longText('level_validation_state')->nullable()->after('doc_file_ids');
            });
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

        if (Schema::hasColumn('element1_kegiatan_asurans', 'level_validation_state')) {
            Schema::table('element1_kegiatan_asurans', function (Blueprint $table) {
                $table->dropColumn('level_validation_state');
            });
        }
    }
};

