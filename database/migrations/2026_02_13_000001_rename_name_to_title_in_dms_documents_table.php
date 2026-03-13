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
        Schema::table('dms_documents', function (Blueprint $table) {
            if (Schema::hasColumn('dms_documents', 'name') && !Schema::hasColumn('dms_documents', 'title')) {
                $table->renameColumn('name', 'title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dms_documents', function (Blueprint $table) {
            if (Schema::hasColumn('dms_documents', 'title') && !Schema::hasColumn('dms_documents', 'name')) {
                $table->renameColumn('title', 'name');
            }
        });
    }
};
