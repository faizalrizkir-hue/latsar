<?php

namespace App\Support;

final class UserAgentSummary
{
    public static function summarize(?string $userAgent): array
    {
        $raw = trim((string) $userAgent);

        if ($raw === '') {
            return [
                'available' => false,
                'browser' => '-',
                'browser_version' => '',
                'browser_label' => '-',
                'device' => '-',
                'os' => '-',
                'raw' => '',
            ];
        }

        [$browser, $version] = self::detectBrowser($raw);

        return [
            'available' => true,
            'browser' => $browser,
            'browser_version' => $version,
            'browser_label' => trim($browser.' '.$version),
            'device' => self::detectDevice($raw),
            'os' => self::detectOperatingSystem($raw),
            'raw' => $raw,
        ];
    }

    private static function detectBrowser(string $userAgent): array
    {
        $patterns = [
            'Microsoft Edge' => '/\b(?:Edg|EdgA|EdgiOS|Edge)\/([\d.]+)/i',
            'Samsung Internet' => '/\bSamsungBrowser\/([\d.]+)/i',
            'Opera' => '/\b(?:OPR|Opera)\/([\d.]+)/i',
            'Mozilla Firefox' => '/\b(?:Firefox|FxiOS)\/([\d.]+)/i',
            'Google Chrome' => '/\b(?:Chrome|CriOS)\/([\d.]+)/i',
            'Safari' => '/\bVersion\/([\d.]+).*?\bSafari\//i',
            'Internet Explorer' => '/\b(?:MSIE\s|Trident\/.*?rv:)([\d.]+)/i',
        ];

        foreach ($patterns as $name => $pattern) {
            if (preg_match($pattern, $userAgent, $matches) === 1) {
                return [$name, self::formatVersion((string) ($matches[1] ?? ''))];
            }
        }

        return ['Browser tidak dikenal', ''];
    }

    private static function detectOperatingSystem(string $userAgent): string
    {
        if (preg_match('/\bAndroid\s+([\d.]+)/i', $userAgent, $matches) === 1) {
            return 'Android '.self::formatVersion((string) $matches[1]);
        }

        if (preg_match('/\b(?:iPhone|CPU)\s+OS\s+([\d_]+)/i', $userAgent, $matches) === 1) {
            return 'iOS '.self::formatVersion(str_replace('_', '.', (string) $matches[1]));
        }

        if (preg_match('/\bMac OS X\s+([\d_]+)/i', $userAgent, $matches) === 1) {
            return 'macOS '.self::formatVersion(str_replace('_', '.', (string) $matches[1]));
        }

        if (preg_match('/\bWindows NT\s+([\d.]+)/i', $userAgent, $matches) === 1) {
            return match ((string) $matches[1]) {
                '10.0' => 'Windows 10/11',
                '6.3' => 'Windows 8.1',
                '6.2' => 'Windows 8',
                '6.1' => 'Windows 7',
                default => 'Windows',
            };
        }

        if (stripos($userAgent, 'CrOS') !== false) {
            return 'ChromeOS';
        }

        if (stripos($userAgent, 'Linux') !== false) {
            return 'Linux';
        }

        return 'OS tidak dikenal';
    }

    private static function detectDevice(string $userAgent): string
    {
        if (preg_match('/\b(iPad|Tablet)\b/i', $userAgent) === 1) {
            return 'Tablet';
        }

        if (preg_match('/\biPhone\b/i', $userAgent) === 1) {
            return 'iPhone';
        }

        if (stripos($userAgent, 'Android') !== false) {
            return stripos($userAgent, 'Mobile') !== false ? 'Ponsel Android' : 'Tablet Android';
        }

        if (preg_match('/\bMobile\b/i', $userAgent) === 1) {
            return 'Ponsel';
        }

        if (preg_match('/\b(bot|crawler|spider)\b/i', $userAgent) === 1) {
            return 'Bot';
        }

        return 'Desktop';
    }

    private static function formatVersion(string $version): string
    {
        $parts = collect(explode('.', $version))
            ->map(fn (string $part): string => trim($part))
            ->filter(fn (string $part): bool => $part !== '' && ctype_digit($part))
            ->take(2)
            ->values();

        if ($parts->isEmpty()) {
            return '';
        }

        if ($parts->count() === 2 && $parts->get(1) === '0') {
            return (string) $parts->get(0);
        }

        return $parts->implode('.');
    }
}
