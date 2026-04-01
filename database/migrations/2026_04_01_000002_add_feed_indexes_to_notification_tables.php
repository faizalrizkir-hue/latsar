<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            try {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->index(['created_at', 'id'], 'notifications_created_at_id_index');
                });
            } catch (\Throwable) {
                // ignore when index already exists
            }
        }

        if (Schema::hasTable('notification_reads')) {
            try {
                Schema::table('notification_reads', function (Blueprint $table) {
                    $table->index(['username', 'notification_id'], 'notification_reads_username_notification_index');
                });
            } catch (\Throwable) {
                // ignore when index already exists
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            try {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->dropIndex('notifications_created_at_id_index');
                });
            } catch (\Throwable) {
                // ignore when index does not exist
            }
        }

        if (Schema::hasTable('notification_reads')) {
            try {
                Schema::table('notification_reads', function (Blueprint $table) {
                    $table->dropIndex('notification_reads_username_notification_index');
                });
            } catch (\Throwable) {
                // ignore when index does not exist
            }
        }
    }
};
