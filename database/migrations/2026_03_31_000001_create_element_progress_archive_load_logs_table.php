<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('element_progress_archive_load_logs')) {
            return;
        }

        Schema::create('element_progress_archive_load_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archive_id')
                ->constrained('element_progress_archives')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('budget_year');
            $table->unsignedSmallInteger('restored_tables')->default(0);
            $table->unsignedInteger('restored_total')->default(0);
            $table->json('restored_by_table')->nullable();
            $table->string('loaded_by', 100)->nullable();
            $table->timestamps();

            $table->index(['budget_year', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('element_progress_archive_load_logs');
    }
};
