<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProfileDashboardQueries
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!(bool) config('performance.dashboard_query_profile_enabled', false)) {
            return $next($request);
        }

        $routeName = (string) optional($request->route())->getName();
        if ($routeName !== 'dashboard') {
            return $next($request);
        }

        $slowQueryMs = max(1.0, (float) config('performance.dashboard_slow_query_ms', 60));
        $totalBudgetMs = max(1.0, (float) config('performance.dashboard_total_query_budget_ms', 250));
        $maxLoggedQueries = max(1, (int) config('performance.dashboard_profile_max_logged_queries', 12));

        $connection = DB::connection();
        $connection->flushQueryLog();
        $connection->enableQueryLog();
        $startedAt = microtime(true);

        try {
            $response = $next($request);
        } finally {
            $queryLog = $connection->getQueryLog();
            $connection->disableQueryLog();
            $connection->flushQueryLog();
        }

        $queryCount = count($queryLog);
        $totalQueryTimeMs = round((float) collect($queryLog)->sum(
            static fn (array $item): float => (float) ($item['time'] ?? 0)
        ), 2);

        $slowQueries = collect($queryLog)
            ->map(function (array $item): array {
                $sql = trim((string) ($item['query'] ?? ''));
                $sql = preg_replace('/\s+/u', ' ', $sql) ?: $sql;

                return [
                    'time_ms' => round((float) ($item['time'] ?? 0), 2),
                    'bindings_count' => is_countable($item['bindings'] ?? null) ? count($item['bindings']) : 0,
                    'sql' => mb_substr($sql, 0, 500),
                ];
            })
            ->filter(static fn (array $item): bool => (float) $item['time_ms'] >= $slowQueryMs)
            ->sortByDesc('time_ms')
            ->take($maxLoggedQueries)
            ->values()
            ->all();

        if ($totalQueryTimeMs < $totalBudgetMs && count($slowQueries) === 0) {
            return $response;
        }

        $requestDurationMs = round((microtime(true) - $startedAt) * 1000, 2);
        Log::warning('Dashboard query profiling threshold reached.', [
            'route' => $routeName,
            'method' => $request->getMethod(),
            'path' => '/'.ltrim($request->path(), '/'),
            'query_count' => $queryCount,
            'total_query_time_ms' => $totalQueryTimeMs,
            'request_duration_ms' => $requestDurationMs,
            'slow_query_threshold_ms' => $slowQueryMs,
            'total_query_budget_ms' => $totalBudgetMs,
            'slow_queries' => $slowQueries,
        ]);

        return $response;
    }
}
