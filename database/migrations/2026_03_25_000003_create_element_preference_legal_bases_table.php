<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('element_preference_legal_bases')) {
            return;
        }

        Schema::create('element_preference_legal_bases', function (Blueprint $table) {
            $table->id();
            $table->string('action_type', 64);
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('mime_type', 160)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->text('note')->nullable();
            $table->string('uploaded_by', 100)->nullable();
            $table->string('storage_driver', 30)->default('public');
            $table->timestamps();

            $table->index(['action_type', 'created_at'], 'pref_legal_action_created_idx');
            $table->index('uploaded_by', 'pref_legal_uploaded_by_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('element_preference_legal_bases');
    }
};

