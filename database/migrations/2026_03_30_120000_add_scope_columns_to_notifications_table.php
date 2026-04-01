<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'element_slug')) {
                $table->string('element_slug', 50)->nullable()->after('row_id');
                $table->index('element_slug');
            }

            if (!Schema::hasColumn('notifications', 'subtopic_slug')) {
                $table->string('subtopic_slug', 100)->nullable()->after('element_slug');
                $table->index('subtopic_slug');
            }
        });

        $modules = (array) config('element_subtopic_modules.modules', []);
        $titleMap = [];

        foreach ($modules as $subtopicSlug => $module) {
            if (!is_array($module)) {
                continue;
            }

            $notificationTitle = trim((string) ($module['notification_title'] ?? ''));
            if ($notificationTitle === '') {
                continue;
            }

            $resolvedSubtopicSlug = trim((string) $subtopicSlug);
            if ($resolvedSubtopicSlug === '') {
                continue;
            }

            $elementSlug = '';
            if (preg_match('/^(element\d+)/i', $resolvedSubtopicSlug, $matches)) {
                $elementSlug = strtolower((string) ($matches[1] ?? ''));
            } else {
                $elementSlug = strtolower(Str::before($resolvedSubtopicSlug, '_'));
            }

            if ($elementSlug === '' || isset($titleMap[$notificationTitle])) {
                continue;
            }

            $titleMap[$notificationTitle] = [
                'element_slug' => $elementSlug,
                'subtopic_slug' => $resolvedSubtopicSlug,
            ];
        }

        foreach ($titleMap as $notificationTitle => $scope) {
            DB::table('notifications')
                ->where('subtopic_title', $notificationTitle)
                ->where(function ($query) {
                    $query
                        ->whereNull('element_slug')
                        ->orWhere('element_slug', '');
                })
                ->update([
                    'element_slug' => $scope['element_slug'],
                    'subtopic_slug' => $scope['subtopic_slug'],
                ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'subtopic_slug')) {
                $table->dropIndex(['subtopic_slug']);
                $table->dropColumn('subtopic_slug');
            }

            if (Schema::hasColumn('notifications', 'element_slug')) {
                $table->dropIndex(['element_slug']);
                $table->dropColumn('element_slug');
            }
        });
    }
};

