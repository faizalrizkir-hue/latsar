<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('element2_perencanaan_penugasan_edit_logs')) {
            return;
        }

        Schema::create('element2_perencanaan_penugasan_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_id');
            $table->string('pernyataan', 255);
            $table->string('username', 100)->nullable();
            $table->string('display_name', 150)->nullable();
            $table->string('action', 30)->default('save');
            $table->timestamps();

            $table->index('row_id');
            $table->index('username');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('element2_perencanaan_penugasan_edit_logs');
    }
};
