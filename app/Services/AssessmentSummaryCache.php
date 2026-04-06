<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AssessmentSummaryCache
{
    private const VERSION_KEY = 'assessment:summary:version';

    private const DEFAULT_TTL_SECONDS = 45;

    public function remember(string $segment, array $sessionUser, array $context, Closure $resolver): mixed
    {
        $ttlSeconds = max(5, (int) config('cache.assessment_summary_ttl_seconds', self::DEFAULT_TTL_SECONDS));
        $cacheKey = $this->cacheKey($segment, $sessionUser, $context);

        return Cache::remember($cacheKey, now()->addSeconds($ttlSeconds), $resolver);
    }

    public function bumpVersion(): void
    {
        $nextVersion = $this->version() + 1;
        Cache::forever(self::VERSION_KEY, $nextVersion);
    }

    private function cacheKey(string $segment, array $sessionUser, array $context): string
    {
        $username = Str::lower(trim((string) ($sessionUser['username'] ?? '')));
        $role = Str::lower(trim((string) ($sessionUser['role'] ?? '')));
        $normalizedContext = $this->normalizeContext($context);
        $contextHash = sha1(json_encode($normalizedContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}');

        return implode(':', [
            'assessment-summary',
            (string) $this->version(),
            trim($segment) !== '' ? Str::slug($segment, '_') : 'default',
            $role !== '' ? $role : 'guest',
            $username !== '' ? $username : 'anonymous',
            $contextHash,
        ]);
    }

    private function version(): int
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        if ($version < 1) {
            $version = 1;
            Cache::forever(self::VERSION_KEY, $version);
        }

        return $version;
    }

    private function normalizeContext(array $context): array
    {
        $normalized = [];
        foreach ($context as $key => $value) {
            $normalized[(string) $key] = is_array($value)
                ? $this->normalizeContext($value)
                : (is_scalar($value) || $value === null ? $value : (string) $value);
        }

        ksort($normalized);

        return $normalized;
    }
}
