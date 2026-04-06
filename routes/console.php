<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Services\SchemaMetadataCache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ops:health {--json : Output JSON summary}', function () {
    $checks = [];
    $failures = 0;
    $warnings = 0;
    $checkedAt = now()->toIso8601String();
    $maxFailedJobs = max(0, (int) config('ops.health.max_failed_jobs', 0));
    $maxPendingJobs = max(0, (int) config('ops.health.max_pending_jobs', 500));

    $recordCheck = static function (string $name, string $status, string $detail) use (&$checks, &$failures, &$warnings): void {
        $normalizedStatus = in_array($status, ['ok', 'warn', 'fail'], true) ? $status : 'fail';
        if ($normalizedStatus === 'fail') {
            $failures++;
        } elseif ($normalizedStatus === 'warn') {
            $warnings++;
        }

        $checks[] = [
            'name' => $name,
            'status' => $normalizedStatus,
            'detail' => $detail,
        ];
    };

    $appKey = trim((string) config('app.key', ''));
    $recordCheck(
        'app_key',
        $appKey !== '' ? 'ok' : 'fail',
        $appKey !== '' ? 'APP_KEY terdeteksi.' : 'APP_KEY kosong.'
    );

    $writablePaths = [
        'storage/logs' => storage_path('logs'),
        'storage/framework/cache/data' => storage_path('framework/cache/data'),
        'bootstrap/cache' => base_path('bootstrap/cache'),
    ];
    foreach ($writablePaths as $name => $path) {
        if (!File::exists($path)) {
            $recordCheck($name, 'warn', 'Direktori belum ada: '.$path);
            continue;
        }

        $recordCheck(
            $name,
            is_writable($path) ? 'ok' : 'fail',
            is_writable($path) ? 'Writable: '.$path : 'Tidak writable: '.$path
        );
    }

    try {
        DB::scalar('SELECT 1');
        $recordCheck('database_ping', 'ok', 'Koneksi database responsif.');
    } catch (\Throwable $exception) {
        $recordCheck('database_ping', 'fail', 'Koneksi database gagal: '.$exception->getMessage());
    }

    try {
        if (Schema::hasTable('failed_jobs')) {
            $failedJobsCount = (int) DB::table('failed_jobs')->count();
            $status = $failedJobsCount > $maxFailedJobs ? 'warn' : 'ok';
            $recordCheck(
                'failed_jobs',
                $status,
                "failed_jobs={$failedJobsCount}, batas={$maxFailedJobs}"
            );
        } else {
            $recordCheck('failed_jobs', 'warn', 'Tabel failed_jobs belum tersedia.');
        }
    } catch (\Throwable $exception) {
        $recordCheck('failed_jobs', 'fail', 'Gagal membaca failed_jobs: '.$exception->getMessage());
    }

    try {
        if (Schema::hasTable('jobs')) {
            $pendingJobsCount = (int) DB::table('jobs')->count();
            $status = $pendingJobsCount > $maxPendingJobs ? 'warn' : 'ok';
            $recordCheck(
                'pending_jobs',
                $status,
                "jobs(pending)={$pendingJobsCount}, batas={$maxPendingJobs}"
            );
        } else {
            $recordCheck('pending_jobs', 'warn', 'Tabel jobs belum tersedia.');
        }
    } catch (\Throwable $exception) {
        $recordCheck('pending_jobs', 'fail', 'Gagal membaca jobs: '.$exception->getMessage());
    }

    $overallStatus = $failures > 0 ? 'fail' : ($warnings > 0 ? 'warn' : 'ok');
    $payload = [
        'checked_at' => $checkedAt,
        'status' => $overallStatus,
        'failures' => $failures,
        'warnings' => $warnings,
        'checks' => $checks,
    ];

    if ((bool) $this->option('json')) {
        $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    } else {
        $this->line('== OPS Health Check ==');
        $this->line('Checked at: '.$checkedAt);
        foreach ($checks as $check) {
            $prefix = match ($check['status']) {
                'ok' => '[OK] ',
                'warn' => '[WARN] ',
                default => '[FAIL] ',
            };
            $this->line($prefix.$check['name'].' - '.$check['detail']);
        }
        $this->line("Summary: status={$overallStatus}, failures={$failures}, warnings={$warnings}");
    }

    return $failures > 0 ? 1 : 0;
})->purpose('Operational runtime health check for production readiness.');

Artisan::command('ops:schema-cache:bump', function () {
    /** @var SchemaMetadataCache $schemaMetadataCache */
    $schemaMetadataCache = app(SchemaMetadataCache::class);
    $schemaMetadataCache->bumpVersion();
    $this->info('Schema metadata cache version bumped.');

    return 0;
})->purpose('Invalidate cached schema metadata immediately.');

Artisan::command('ops:dashboard-profile:summary {--json : Output JSON summary} {--limit=10 : Max recent entries to display} {--log= : Optional custom log file path}', function () {
    $limit = max(1, min(100, (int) $this->option('limit')));
    $customLogPath = trim((string) $this->option('log'));
    $isAbsoluteWindowsPath = preg_match('/^[A-Za-z]:[\\\\\\/]/', $customLogPath) === 1;
    $isAbsoluteUnixPath = str_starts_with($customLogPath, DIRECTORY_SEPARATOR);

    $logPath = match (true) {
        $customLogPath === '' => storage_path('logs/laravel.log'),
        $isAbsoluteWindowsPath, $isAbsoluteUnixPath => $customLogPath,
        default => base_path($customLogPath),
    };

    $entries = [];
    if (File::exists($logPath)) {
        foreach (File::lines($logPath) as $line) {
            $lineText = trim((string) $line);
            if ($lineText === '' || !str_contains($lineText, 'Dashboard query profiling threshold reached.')) {
                continue;
            }

            if (!preg_match('/^\[(?<timestamp>[^\]]+)\]\s+[^:]+:\s+Dashboard query profiling threshold reached\.\s+(?<context>\{.*\})$/', $lineText, $matches)) {
                continue;
            }

            $context = json_decode((string) ($matches['context'] ?? '{}'), true);
            if (!is_array($context)) {
                continue;
            }

            $entries[] = [
                'logged_at' => (string) ($matches['timestamp'] ?? ''),
                'query_count' => (int) ($context['query_count'] ?? 0),
                'total_query_time_ms' => round((float) ($context['total_query_time_ms'] ?? 0), 2),
                'request_duration_ms' => round((float) ($context['request_duration_ms'] ?? 0), 2),
                'slow_queries_count' => is_countable($context['slow_queries'] ?? null)
                    ? count((array) $context['slow_queries'])
                    : 0,
                'route' => trim((string) ($context['route'] ?? '')),
                'path' => trim((string) ($context['path'] ?? '')),
            ];
        }
    }

    $entriesCollection = collect($entries);
    $totalEntries = $entriesCollection->count();
    $summary = [
        'avg_query_count' => $totalEntries > 0 ? round((float) $entriesCollection->avg('query_count'), 2) : 0.0,
        'avg_total_query_time_ms' => $totalEntries > 0 ? round((float) $entriesCollection->avg('total_query_time_ms'), 2) : 0.0,
        'avg_request_duration_ms' => $totalEntries > 0 ? round((float) $entriesCollection->avg('request_duration_ms'), 2) : 0.0,
        'max_total_query_time_ms' => $totalEntries > 0 ? round((float) $entriesCollection->max('total_query_time_ms'), 2) : 0.0,
        'max_request_duration_ms' => $totalEntries > 0 ? round((float) $entriesCollection->max('request_duration_ms'), 2) : 0.0,
    ];

    $recent = $entriesCollection
        ->take(-$limit)
        ->values()
        ->all();

    $payload = [
        'log_path' => $logPath,
        'total_entries' => $totalEntries,
        'summary' => $summary,
        'recent' => $recent,
    ];

    if ((bool) $this->option('json')) {
        $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return 0;
    }

    $this->line('== Dashboard Profiling Summary ==');
    $this->line('Log path: '.$logPath);
    $this->line('Total entries: '.$totalEntries);

    if ($totalEntries === 0) {
        $this->warn('Belum ada data profiling dashboard di log.');

        return 0;
    }

    $this->line(sprintf(
        'Averages: query_count=%.2f, total_query_time_ms=%.2f, request_duration_ms=%.2f',
        $summary['avg_query_count'],
        $summary['avg_total_query_time_ms'],
        $summary['avg_request_duration_ms']
    ));
    $this->line(sprintf(
        'Max: total_query_time_ms=%.2f, request_duration_ms=%.2f',
        $summary['max_total_query_time_ms'],
        $summary['max_request_duration_ms']
    ));

    $this->line('Recent entries:');
    foreach ($recent as $entry) {
        $this->line(sprintf(
            '- %s | route=%s path=%s | q=%d total=%.2fms req=%.2fms slow=%d',
            (string) ($entry['logged_at'] ?? '-'),
            (string) ($entry['route'] ?? '-'),
            (string) ($entry['path'] ?? '-'),
            (int) ($entry['query_count'] ?? 0),
            (float) ($entry['total_query_time_ms'] ?? 0),
            (float) ($entry['request_duration_ms'] ?? 0),
            (int) ($entry['slow_queries_count'] ?? 0)
        ));
    }

    return 0;
})->purpose('Summarize dashboard query profiling entries from application log.');

$opsHealthLog = trim((string) config('ops.health.log_file', 'logs/ops-health.log'));
$opsHealthLogPath = str_starts_with($opsHealthLog, DIRECTORY_SEPARATOR)
    ? $opsHealthLog
    : storage_path($opsHealthLog);

Schedule::command('ops:health --json')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo($opsHealthLogPath);
