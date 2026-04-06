<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SchemaMetadataCache
{
    private const VERSION_KEY = 'schema:metadata:version';

    private const DEFAULT_TTL_SECONDS = 600;

    /**
     * @var array<string, bool>
     */
    private array $tableExists = [];

    /**
     * @var array<string, array<int, string>>
     */
    private array $columnListings = [];

    /**
     * @var array<string, bool>
     */
    private array $columnExists = [];

    public function hasTable(string $table): bool
    {
        $normalizedTable = trim($table);
        if ($normalizedTable === '') {
            return false;
        }

        if (array_key_exists($normalizedTable, $this->tableExists)) {
            return $this->tableExists[$normalizedTable];
        }

        $exists = Cache::remember(
            $this->cacheKey('table', $normalizedTable),
            now()->addSeconds($this->ttlSeconds()),
            fn (): bool => Schema::hasTable($normalizedTable)
        );

        $this->tableExists[$normalizedTable] = (bool) $exists;

        return $this->tableExists[$normalizedTable];
    }

    /**
     * @return array<int, string>
     */
    public function columnListing(string $table): array
    {
        $normalizedTable = trim($table);
        if ($normalizedTable === '' || !$this->hasTable($normalizedTable)) {
            return [];
        }

        if (array_key_exists($normalizedTable, $this->columnListings)) {
            return $this->columnListings[$normalizedTable];
        }

        $columns = Cache::remember(
            $this->cacheKey('columns', $normalizedTable),
            now()->addSeconds($this->ttlSeconds()),
            function () use ($normalizedTable): array {
                $listing = Schema::getColumnListing($normalizedTable);
                if (!is_array($listing)) {
                    return [];
                }

                return array_values(array_filter(array_map(
                    static fn ($column): string => trim((string) $column),
                    $listing
                ), static fn (string $column): bool => $column !== ''));
            }
        );

        $this->columnListings[$normalizedTable] = $columns;

        return $columns;
    }

    public function hasColumn(string $table, string $column): bool
    {
        $normalizedTable = trim($table);
        $normalizedColumn = trim($column);
        if ($normalizedTable === '' || $normalizedColumn === '') {
            return false;
        }

        $cacheKey = $normalizedTable.'::'.Str::lower($normalizedColumn);
        if (array_key_exists($cacheKey, $this->columnExists)) {
            return $this->columnExists[$cacheKey];
        }

        $columns = $this->columnListing($normalizedTable);
        $columnExists = in_array(Str::lower($normalizedColumn), array_map(
            static fn (string $item): string => Str::lower($item),
            $columns
        ), true);

        $this->columnExists[$cacheKey] = $columnExists;

        return $columnExists;
    }

    public function bumpVersion(): void
    {
        Cache::forever(self::VERSION_KEY, $this->version() + 1);

        // Reset in-request cache to prevent stale reads immediately after bump.
        $this->tableExists = [];
        $this->columnListings = [];
        $this->columnExists = [];
    }

    private function cacheKey(string $segment, string $identifier): string
    {
        $defaultConnection = (string) config('database.default', 'mysql');
        $databaseName = (string) config('database.connections.'.$defaultConnection.'.database', '');
        $version = $this->version();

        return implode(':', [
            'schema-metadata',
            (string) $version,
            trim($defaultConnection) !== '' ? Str::lower($defaultConnection) : 'default',
            trim($databaseName) !== '' ? Str::lower($databaseName) : 'unknown-db',
            Str::slug($segment, '_'),
            sha1($identifier),
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

    private function ttlSeconds(): int
    {
        return max(30, (int) config('performance.schema_metadata_ttl_seconds', self::DEFAULT_TTL_SECONDS));
    }
}
