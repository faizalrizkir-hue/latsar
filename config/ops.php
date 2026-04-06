<?php

return [
    'health' => [
        'max_failed_jobs' => (int) env('OPS_MAX_FAILED_JOBS', 0),
        'max_pending_jobs' => (int) env('OPS_MAX_PENDING_JOBS', 500),
        'log_file' => (string) env('OPS_HEALTH_LOG_FILE', 'logs/ops-health.log'),
    ],
];
