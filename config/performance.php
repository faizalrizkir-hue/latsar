<?php

return [
    'schema_metadata_ttl_seconds' => (int) env('SCHEMA_METADATA_TTL_SECONDS', 600),

    'dashboard_query_profile_enabled' => (bool) env('DASHBOARD_QUERY_PROFILE_ENABLED', false),
    'dashboard_slow_query_ms' => (float) env('DASHBOARD_SLOW_QUERY_MS', 60),
    'dashboard_total_query_budget_ms' => (float) env('DASHBOARD_TOTAL_QUERY_BUDGET_MS', 250),
    'dashboard_profile_max_logged_queries' => (int) env('DASHBOARD_PROFILE_MAX_LOGGED_QUERIES', 12),
];
