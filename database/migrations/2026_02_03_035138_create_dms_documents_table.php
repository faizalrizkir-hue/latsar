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
        if (Schema::hasTable('dms_documents')) {
            return;
        }

        Schema::create('dms_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('type', 150);
            $table->string('doc_no', 100)->unique();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('element', 100)->nullable();
            $table->string('subtopic', 150)->nullable();
            $table->string('tag', 100)->nullable();
            $table->enum('status', ['Aktif', 'Arsip'])->default('Aktif');
            $table->string('uploader', 150)->nullable();
            $table->string('updated_by', 150)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('year');
            $table->index('type');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dms_documents');
    }
};
