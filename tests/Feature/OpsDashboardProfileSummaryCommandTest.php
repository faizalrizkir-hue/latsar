<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OpsDashboardProfileSummaryCommandTest extends TestCase
{
    public function test_dashboard_profile_summary_command_parses_log_entries(): void
    {
        $logPath = storage_path('framework/testing/dashboard-profile-summary.log');
        File::ensureDirectoryExists(dirname($logPath));

        File::put($logPath, implode(PHP_EOL, [
            '[2026-04-06 14:00:00] local.WARNING: Dashboard query profiling threshold reached. {"route":"dashboard","path":"/","query_count":48,"total_query_time_ms":362.11,"request_duration_ms":618.54,"slow_queries":[{"time_ms":120.2}]}',
            '[2026-04-06 14:00:05] local.WARNING: Dashboard query profiling threshold reached. {"route":"dashboard","path":"/","query_count":40,"total_query_time_ms":301.44,"request_duration_ms":510.18,"slow_queries":[]}',
        ]).PHP_EOL);

        try {
            $exitCode = Artisan::call('ops:dashboard-profile:summary', [
                '--json' => true,
                '--log' => $logPath,
                '--limit' => 5,
            ]);

            $this->assertSame(0, $exitCode);

            $payload = json_decode(Artisan::output(), true);
            $this->assertIsArray($payload);
            $this->assertSame(2, (int) ($payload['total_entries'] ?? 0));
            $this->assertSame(44.0, (float) ($payload['summary']['avg_query_count'] ?? 0.0));
            $this->assertSame(331.78, (float) ($payload['summary']['avg_total_query_time_ms'] ?? 0.0));
            $this->assertSame(2, count((array) ($payload['recent'] ?? [])));
        } finally {
            File::delete($logPath);
        }
    }

    public function test_dashboard_profile_summary_command_handles_missing_log_file(): void
    {
        $missingLogPath = storage_path('framework/testing/dashboard-profile-missing.log');
        File::delete($missingLogPath);

        $exitCode = Artisan::call('ops:dashboard-profile:summary', [
            '--json' => true,
            '--log' => $missingLogPath,
        ]);

        $this->assertSame(0, $exitCode);

        $payload = json_decode(Artisan::output(), true);
        $this->assertIsArray($payload);
        $this->assertSame(0, (int) ($payload['total_entries'] ?? 0));
        $this->assertSame([], (array) ($payload['recent'] ?? []));
    }
}

