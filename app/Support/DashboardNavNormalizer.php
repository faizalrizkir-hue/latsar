<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Traversable;

final class DashboardNavNormalizer
{
    public static function hasRenderable(mixed $navElements): bool
    {
        $source = self::asCollection($navElements);

        $hasValidSlug = $source->contains(
            fn ($item) => is_array($item) && trim((string) ($item['slug'] ?? '')) !== ''
        );

        $hasValidSubtopic = $source->contains(function ($item): bool {
            if (!is_array($item) || !is_iterable($item['subtopics'] ?? null)) {
                return false;
            }

            return collect($item['subtopics'])->contains(
                fn ($subtopic) => is_array($subtopic) && trim((string) ($subtopic['slug'] ?? '')) !== ''
            );
        });

        return $hasValidSlug && $hasValidSubtopic;
    }

    public static function sanitize(mixed $navElements): Collection
    {
        return self::asCollection($navElements)
            ->map(function ($item) {
                $item = self::arrayableToArray($item);
                if (!is_array($item)) {
                    return null;
                }

                $slug = trim((string) ($item['slug'] ?? ''));
                if ($slug === '') {
                    return null;
                }

                $normalized = $item;
                $normalized['slug'] = $slug;

                $title = trim((string) ($normalized['title'] ?? ''));
                if ($title === '') {
                    $title = Str::headline($slug);
                }
                $normalized['title'] = $title;

                $navTitle = trim((string) ($normalized['nav_title'] ?? ''));
                if ($navTitle === '') {
                    $navTitle = $title;
                }
                $normalized['nav_title'] = $navTitle;

                $iconLabel = trim((string) ($normalized['icon_label'] ?? ''));
                if ($iconLabel === '' && preg_match('/^element(\d+)$/i', $slug, $matches)) {
                    $iconLabel = (string) ($matches[1] ?? 'E');
                }
                if ($iconLabel === '') {
                    $iconLabel = 'E';
                }
                $normalized['icon_label'] = Str::upper($iconLabel);

                $coveragePercent = (int) ($normalized['coverage_percent'] ?? 0);
                if ($coveragePercent > 0) {
                    $coveragePercent = max(8, $coveragePercent);
                }
                $normalized['coverage_percent'] = min(100, $coveragePercent);

                $subtopics = self::sanitizeSubtopics($normalized['subtopics'] ?? []);
                $normalized['subtopics'] = $subtopics;
                $normalized['subtopic_count'] = count($subtopics);

                return $normalized;
            })
            ->filter(fn ($item) => is_array($item))
            ->values();
    }

    private static function sanitizeSubtopics(mixed $subtopics): array
    {
        return self::asCollection($subtopics)
            ->map(function ($subtopic) {
                $subtopic = self::arrayableToArray($subtopic);
                if (!is_array($subtopic)) {
                    return null;
                }

                $slug = trim((string) ($subtopic['slug'] ?? ''));
                if ($slug === '') {
                    return null;
                }

                $normalized = $subtopic;
                $normalized['slug'] = $slug;

                $title = trim((string) ($normalized['title'] ?? ''));
                if ($title === '') {
                    $title = Str::headline(str_replace('_', ' ', $slug));
                }
                $normalized['title'] = $title;

                return $normalized;
            })
            ->filter(fn ($subtopic) => is_array($subtopic))
            ->values()
            ->all();
    }

    private static function asCollection(mixed $value): Collection
    {
        if ($value instanceof Collection) {
            return $value->values();
        }

        if (is_array($value)) {
            return collect($value);
        }

        if ($value instanceof Traversable) {
            return collect(iterator_to_array($value));
        }

        return collect();
    }

    private static function arrayableToArray(mixed $value): mixed
    {
        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        return $value;
    }
}
