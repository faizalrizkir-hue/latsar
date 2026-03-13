<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('element_team_assignments')) {
            return;
        }

        Schema::create('element_team_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('element_slug', 50)->unique();
            $table->string('coordinator_username', 100)->nullable();
            $table->json('member_usernames')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('element_team_assignments');
    }
};
