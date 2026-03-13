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
        if (!Schema::hasTable('element1_kegiatan_asurans_row_doc_selections')) {
            return;
        }

        Schema::drop('element1_kegiatan_asurans_row_doc_selections');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('element1_kegiatan_asurans_row_doc_selections')) {
            return;
        }

        Schema::create('element1_kegiatan_asurans_row_doc_selections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_id');
            $table->unsignedBigInteger('doc_file_id');
            $table->timestamps();

            $table->unique(['row_id', 'doc_file_id'], 'uniq_element1_kegiatan_row_doc_file');
            $table->index('row_id');
            $table->index('doc_file_id');
        });
    }
};
