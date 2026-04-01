<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notification_reads')) {
            return;
        }

        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_id');
            $table->string('username', 100);
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();

            $table->unique(['notification_id', 'username'], 'notification_reads_notification_user_unique');
            $table->index(['username', 'read_at'], 'notification_reads_username_read_at_index');
            $table->index(['notification_id', 'read_at'], 'notification_reads_notification_read_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
    }
};
