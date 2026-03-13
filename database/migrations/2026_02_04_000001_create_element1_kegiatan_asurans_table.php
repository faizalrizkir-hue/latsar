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
        if (Schema::hasTable('element1_kegiatan_asurans')) {
            return;
        }

        Schema::create('element1_kegiatan_asurans', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('pernyataan', 255);
            $table->string('level', 50)->default('-');
            $table->decimal('skor', 10, 2)->nullable();
            $table->text('analisis_bukti')->nullable();
            $table->text('analisis_nilai')->nullable();
            $table->text('grad_l1_catatan')->nullable();
            $table->text('grad_l2_catatan')->nullable();
            $table->text('grad_l3_catatan')->nullable();
            $table->text('grad_l4_catatan')->nullable();
            $table->text('grad_l5_catatan')->nullable();
            $table->text('evidence')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('dokumen_path', 255)->nullable();
            $table->text('verify_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element1_kegiatan_asurans');
    }
};
