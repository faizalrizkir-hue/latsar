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
        Schema::table('dms_files', function (Blueprint $table) {
            if (!Schema::hasColumn('dms_files', 'storage_driver')) {
                $table->string('storage_driver', 50)->default('public')->after('file_path');
            }

            if (!Schema::hasColumn('dms_files', 'size_bytes')) {
                $table->unsignedBigInteger('size_bytes')->nullable()->after('file_size');
            }

            if (!Schema::hasColumn('dms_files', 'uploaded_at')) {
                $table->timestamp('uploaded_at')->nullable()->useCurrent()->after('size_bytes');
            }

            if (!Schema::hasColumn('dms_files', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate()->after('uploaded_at');
            }
        });

        // Backfill new timestamps for legacy rows to avoid null ordering issues.
        DB::table('dms_files')
            ->whereNull('uploaded_at')
            ->update(['uploaded_at' => DB::raw('created_at')]);

        DB::table('dms_files')
            ->whereNull('updated_at')
            ->update(['updated_at' => DB::raw('created_at')]);

        // Ensure storage_driver is set for existing rows.
        DB::table('dms_files')
            ->whereNull('storage_driver')
            ->orWhere('storage_driver', '')
            ->update(['storage_driver' => 'public']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dms_files', function (Blueprint $table) {
            if (Schema::hasColumn('dms_files', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('dms_files', 'uploaded_at')) {
                $table->dropColumn('uploaded_at');
            }
            if (Schema::hasColumn('dms_files', 'size_bytes')) {
                $table->dropColumn('size_bytes');
            }
            if (Schema::hasColumn('dms_files', 'storage_driver')) {
                $table->dropColumn('storage_driver');
            }
        });
    }
};
