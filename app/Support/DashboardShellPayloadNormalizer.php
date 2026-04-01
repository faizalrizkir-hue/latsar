<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Traversable;

final class DashboardShellPayloadNormalizer
{
    public static function hasRenderableHeadnavCrumbs(mixed $crumbs): bool
    {
        return count(self::sanitizeHeadnavCrumbs($crumbs)) > 0;
    }

    /**
     * @return array<int, array{label: string, url: ?string, is_current: bool}>
     */
    public static function sanitizeHeadnavCrumbs(mixed $crumbs): array
    {
        $normalized = self::asCollection($crumbs)
            ->map(function ($crumb): ?array {
                $crumb = self::arrayableToArray($crumb);
                if (!is_array($crumb)) {
                    return null;
                }

                $label = trim((string) ($crumb['label'] ?? ''));
                if ($label === '') {
                    return null;
                }

                $url = trim((string) ($crumb['url'] ?? ''));

                return [
                    'label' => $label,
                    'url' => $url !== '' ? $url : null,
                    'is_current' => false,
                ];
            })
            ->filter(fn ($crumb) => is_array($crumb))
            ->values()
            ->all();

        $lastIndex = count($normalized) - 1;
        if ($lastIndex >= 0) {
            $normalized[$lastIndex]['is_current'] = true;
            $normalized[$lastIndex]['url'] = null;
        }

        return $normalized;
    }

    public static function sanitizeNotificationItems(mixed $items): Collection
    {
        return self::asCollection($items)
            ->filter(fn ($item) => is_array($item) || is_object($item))
            ->values();
    }

    /**
     * @return array<int, string>
     */
    public static function sanitizeNotificationRealtimeChannels(mixed $channels): array
    {
        return self::asCollection($channels)
            ->map(fn ($channel) => trim((string) $channel))
            ->filter(fn ($channel) => $channel !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{type: string, title: string, message: string}>
     */
    public static function sanitizeToastQueue(mixed $toasts): array
    {
        $allowedTypes = ['success', 'error', 'warning', 'info'];

        return self::asCollection($toasts)
            ->map(function ($toast) use ($allowedTypes): ?array {
                $toast = self::arrayableToArray($toast);
                if (!is_array($toast)) {
                    return null;
                }

                $type = strtolower(trim((string) ($toast['type'] ?? 'info')));
                if (!in_array($type, $allowedTypes, true)) {
                    $type = 'info';
                }

                $title = trim((string) ($toast['title'] ?? ''));
                $message = trim((string) ($toast['message'] ?? ''));
                if ($title === '' && $message === '') {
                    return null;
                }

                if ($title === '') {
                    $title = match ($type) {
                        'success' => 'Berhasil',
                        'error' => 'Gagal',
                        'warning' => 'Perhatian',
                        default => 'Informasi',
                    };
                }

                return [
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                ];
            })
            ->filter(fn ($toast) => is_array($toast))
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
