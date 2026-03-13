<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('element4_pengembangan_sdm_profesional_apip_edit_logs')) {
            return;
        }

        Schema::create('element4_pengembangan_sdm_profesional_apip_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_id');
            $table->string('pernyataan', 255);
            $table->string('username', 100)->nullable();
            $table->string('display_name', 150)->nullable();
            $table->string('action', 30)->default('save');
            $table->timestamps();

            $table->index('row_id', 'e4_psdpa_el_row_idx');
            $table->index('username', 'e4_psdpa_el_user_idx');
            $table->index('created_at', 'e4_psdpa_el_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('element4_pengembangan_sdm_profesional_apip_edit_logs');
    }
};
