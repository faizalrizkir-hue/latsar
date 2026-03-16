<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('element5_koordinasi_pengawasan_edit_logs')) {
            return;
        }

        Schema::create('element5_koordinasi_pengawasan_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_id');
            $table->string('pernyataan', 255);
            $table->string('username', 100)->nullable();
            $table->string('display_name', 150)->nullable();
            $table->string('action', 30)->default('save');
            $table->timestamps();

            $table->index('row_id', 'e5_kp_logs_row_id_idx');
            $table->index('username', 'e5_kp_logs_username_idx');
            $table->index('created_at', 'e5_kp_logs_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('element5_koordinasi_pengawasan_edit_logs');
    }
};
