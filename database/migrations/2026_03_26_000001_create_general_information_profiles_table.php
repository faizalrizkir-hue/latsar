<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('general_information_profiles')) {
            return;
        }

        Schema::create('general_information_profiles', function (Blueprint $table) {
            $table->id();
            $table->text('dasar_hukum_penilaian')->nullable();
            $table->string('pemerintah_daerah', 255)->nullable();
            $table->string('nama_skpd', 255)->nullable();
            $table->string('bidang', 255)->nullable();
            $table->string('kepala_pemerintah_daerah', 255)->nullable();
            $table->text('undang_undang_pendirian')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->string('inspektur', 255)->nullable();
            $table->text('alamat_kantor')->nullable();
            $table->text('jumlah_kantor_wilayah')->nullable();
            $table->string('kontak', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_information_profiles');
    }
};
