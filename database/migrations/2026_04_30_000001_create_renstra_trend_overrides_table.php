<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('renstra_trend_overrides')) {
            return;
        }

        Schema::create('renstra_trend_overrides', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->decimal('hasil_score', 4, 2)->nullable();
            $table->decimal('target_score', 4, 2)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renstra_trend_overrides');
    }
};

