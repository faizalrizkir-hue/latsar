<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('element_preferences')) {
            return;
        }

        Schema::create('element_preferences', function (Blueprint $table) {
            $table->id();
            $table->json('payload');
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('element_preferences');
    }
};
