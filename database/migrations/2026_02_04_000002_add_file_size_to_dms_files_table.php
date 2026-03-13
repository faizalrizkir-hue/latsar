<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dms_files', function (Blueprint $table) {
            if (!Schema::hasColumn('dms_files', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dms_files', function (Blueprint $table) {
            if (Schema::hasColumn('dms_files', 'file_size')) {
                $table->dropColumn('file_size');
            }
        });
    }
};
