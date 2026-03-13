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
        if (!Schema::hasTable('element1_kegiatan_asurans_doc_selections')) {
            return;
        }

        Schema::drop('element1_kegiatan_asurans_doc_selections');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('element1_kegiatan_asurans_doc_selections')) {
            return;
        }

        Schema::create('element1_kegiatan_asurans_doc_selections', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100);
            $table->unsignedBigInteger('doc_file_id');
            $table->timestamps();

            $table->unique(['username', 'doc_file_id'], 'uniq_element1_kegiatan_doc_user_file');
            $table->index('username');
            $table->index('doc_file_id');
        });
    }
};
