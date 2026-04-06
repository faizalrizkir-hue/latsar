<?php

return [
    'enabled' => (bool) env('SECURITY_HEADERS_ENABLED', true),

    'hsts' => [
        'enabled' => (bool) env('SECURITY_HEADERS_HSTS_ENABLED', true),
        'max_age' => (int) env('SECURITY_HEADERS_HSTS_MAX_AGE', 31536000),
        'include_subdomains' => (bool) env('SECURITY_HEADERS_HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => (bool) env('SECURITY_HEADERS_HSTS_PRELOAD', false),
    ],

    // Optional (default kosong supaya tidak mengubah perilaku CSP saat ini).
    'csp' => [
        'report_only' => (string) env('SECURITY_HEADERS_CSP_REPORT_ONLY', ''),
    ],
];
