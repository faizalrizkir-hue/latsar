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
        if (Schema::hasTable('element_assessments')) {
            return;
        }

        Schema::create('element_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('subtopic_slug', 120);
            $table->string('subtopic_title', 200);
            $table->json('scores');
            $table->decimal('weighted_total', 4, 2);
            $table->unsignedTinyInteger('level');
            $table->string('predikat', 50);
            $table->string('notes', 500)->nullable();
            $table->string('submitted_by', 150)->nullable();
            $table->string('verified_by', 150)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['subtopic_slug', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_assessments');
    }
};
