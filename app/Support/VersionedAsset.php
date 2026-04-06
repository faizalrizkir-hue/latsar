<?php

namespace App\Support;

final class VersionedAsset
{
    /**
     * @var array<string, string>
     */
    private static array $versions = [];

    public static function url(string $path): string
    {
        $normalizedPath = ltrim(trim($path), '/');
        if ($normalizedPath === '') {
            return asset($path);
        }

        $assetUrl = asset($normalizedPath);
        $version = self::resolveVersion($normalizedPath);
        if ($version === '') {
            return $assetUrl;
        }

        $separator = str_contains($assetUrl, '?') ? '&' : '?';

        return $assetUrl.$separator.'v='.$version;
    }

    private static function resolveVersion(string $path): string
    {
        $forcedVersion = trim((string) config('app.asset_version', ''));
        if ($forcedVersion !== '') {
            return $forcedVersion;
        }

        if (array_key_exists($path, self::$versions)) {
            return self::$versions[$path];
        }

        $absolutePath = public_path($path);
        if (!is_file($absolutePath)) {
            self::$versions[$path] = '';
            return '';
        }

        $mtime = @filemtime($absolutePath);
        self::$versions[$path] = $mtime === false ? '' : (string) $mtime;

        return self::$versions[$path];
    }
}
