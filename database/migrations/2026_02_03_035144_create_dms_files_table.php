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
        if (Schema::hasTable('dms_files')) {
            return;
        }

        Schema::create('dms_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->string('doc_no', 100)->nullable();
            $table->string('doc_name', 200)->nullable();
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('storage_driver', 50)->default('local');
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('dms_documents')->onDelete('cascade');
            $table->index('doc_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dms_files');
    }
};
