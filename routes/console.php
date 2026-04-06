<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

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

$opsHealthLog = trim((string) config('ops.health.log_file', 'logs/ops-health.log'));
$opsHealthLogPath = str_starts_with($opsHealthLog, DIRECTORY_SEPARATOR)
    ? $opsHealthLog
    : storage_path($opsHealthLog);

Schedule::command('ops:health --json')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo($opsHealthLogPath);
