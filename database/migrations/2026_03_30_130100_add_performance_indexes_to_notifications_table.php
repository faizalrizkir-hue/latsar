<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'element_slug')
                && Schema::hasColumn('notifications', 'created_at')) {
                $table->index(['element_slug', 'created_at'], 'notifications_element_created_at_index');
            }

            if (Schema::hasColumn('notifications', 'subtopic_slug')
                && Schema::hasColumn('notifications', 'created_at')) {
                $table->index(['subtopic_slug', 'created_at'], 'notifications_subtopic_created_at_index');
            }

            if (Schema::hasColumn('notifications', 'coordinator_username')
                && Schema::hasColumn('notifications', 'created_at')) {
                $table->index(['coordinator_username', 'created_at'], 'notifications_actor_created_at_index');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            $indexes = [
                'notifications_element_created_at_index',
                'notifications_subtopic_created_at_index',
                'notifications_actor_created_at_index',
            ];

            foreach ($indexes as $indexName) {
                try {
                    $table->dropIndex($indexName);
                } catch (\Throwable) {
                    // ignore when index was never created
                }
            }
        });
    }
};
