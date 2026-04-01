<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('element_progress_archives')) {
            return;
        }

        Schema::create('element_progress_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('budget_year')->unique();
            $table->json('snapshot');
            $table->unsignedInteger('total_rows')->default(0);
            $table->string('archived_by', 100)->nullable();
            $table->string('loaded_by', 100)->nullable();
            $table->timestamp('last_loaded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('element_progress_archives');
    }
};

