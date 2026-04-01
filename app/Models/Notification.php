<?php

namespace App\Models;

use App\Events\NotificationFeedUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Throwable;

class Notification extends Model
{
    public $timestamps = false;

    private static array $columnExistsCache = [];

    protected $fillable = [
        'element_slug',
        'subtopic_slug',
        'subtopic_title',
        'statement',
        'row_id',
        'coordinator_name',
        'coordinator_username',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function coordinatorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'coordinator_username', 'username')
            ->select(['username', 'display_name', 'profile_photo', 'role']);
    }

    public static function queryForUser(array $sessionUser, ?string $scopeSlug = null): Builder
    {
        $scopeElementSlug = ElementTeamAssignment::topLevelElementSlug((string) $scopeSlug);
        $roleKey = strtolower(trim((string) ($sessionUser['role'] ?? '')));
        $isAdminOrQa = in_array($roleKey, ['administrator', 'admin', 'superadmin', 'qa'], true);

        $query = self::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (self::hasColumn('element_slug')) {
            if ($scopeElementSlug !== '') {
                if (ElementTeamAssignment::isRestrictedRole($roleKey)
                    && !ElementTeamAssignment::canUserAccessSlug($sessionUser, $scopeElementSlug)) {
                    $query->whereRaw('1 = 0');

                    return $query;
                }

                $elementNumber = self::extractElementNumber($scopeElementSlug);
                $query->where(function (Builder $builder) use ($scopeElementSlug, $elementNumber) {
                    $builder->where('element_slug', $scopeElementSlug);

                    if ($elementNumber !== '') {
                        $builder
                            ->orWhere(function (Builder $legacyBuilder) use ($elementNumber) {
                                $legacyBuilder
                                    ->whereNull('element_slug')
                                    ->where('subtopic_title', 'like', 'Element '.$elementNumber.'%');
                            });
                    }
                });
            } else {
                $assignedElementSlugs = ElementTeamAssignment::assignedElementSlugsForUser($sessionUser);
                if ($assignedElementSlugs !== null) {
                    if (count($assignedElementSlugs) === 0) {
                        $query->whereRaw('1 = 0');

                        return $query;
                    }

                    $query->whereIn('element_slug', $assignedElementSlugs);
                } elseif (!$isAdminOrQa && ElementTeamAssignment::isRestrictedRole($roleKey)) {
                    $query->whereRaw('1 = 0');

                    return $query;
                }
            }
        } else {
            $assignedElementSlugs = ElementTeamAssignment::assignedElementSlugsForUser($sessionUser);
            if ($assignedElementSlugs !== null) {
                if (count($assignedElementSlugs) === 0) {
                    $query->whereRaw('1 = 0');

                    return $query;
                }
                self::applyLegacyElementFilter($query, $assignedElementSlugs);
            }
        }

        return $query;
    }

    public static function feedForUser(array $sessionUser, ?string $scopeSlug = null, int $limit = 50): Collection
    {
        $limit = max(1, min(100, (int) $limit));
        $feedColumns = collect([
            'id',
            'element_slug',
            'subtopic_slug',
            'subtopic_title',
            'statement',
            'coordinator_name',
            'coordinator_username',
            'created_at',
        ])
            ->filter(fn (string $column): bool => self::hasColumn($column))
            ->values()
            ->all();

        if ($feedColumns === []) {
            return collect();
        }

        return self::queryForUser($sessionUser, $scopeSlug)
            ->select($feedColumns)
            ->with(['coordinatorAccount'])
            ->limit($limit)
            ->get();
    }

    public static function createScoped(array $attributes): ?self
    {
        if (!Schema::hasTable((new self())->getTable())) {
            return null;
        }

        $candidateColumns = [
            'element_slug',
            'subtopic_slug',
            'subtopic_title',
            'statement',
            'row_id',
            'coordinator_name',
            'coordinator_username',
            'created_at',
        ];

        $existingColumns = collect($candidateColumns)
            ->filter(fn (string $column): bool => self::hasColumn($column))
            ->values()
            ->all();

        if ($existingColumns === []) {
            return null;
        }

        $payload = collect($attributes)
            ->only($existingColumns)
            ->all();

        if (!array_key_exists('created_at', $payload)) {
            $payload['created_at'] = now();
        }

        $dedupeWindowSeconds = 45;
        $duplicateQuery = self::query();

        foreach (['element_slug', 'subtopic_slug', 'statement', 'coordinator_username'] as $field) {
            if (!array_key_exists($field, $payload)) {
                continue;
            }

            $value = $payload[$field];
            if ($value === null || $value === '') {
                $duplicateQuery->whereNull($field);
            } else {
                $duplicateQuery->where($field, $value);
            }
        }

        if (array_key_exists('row_id', $payload)) {
            if ($payload['row_id'] === null || $payload['row_id'] === '') {
                $duplicateQuery->whereNull('row_id');
            } else {
                $duplicateQuery->where('row_id', (int) $payload['row_id']);
            }
        }

        $duplicate = $duplicateQuery
            ->where('created_at', '>=', now()->subSeconds($dedupeWindowSeconds))
            ->orderByDesc('id')
            ->first();

        if ($duplicate instanceof self) {
            return $duplicate;
        }

        $notification = self::query()->create($payload);

        if ($notification instanceof self) {
            try {
                event(new NotificationFeedUpdated(
                    elementSlug: self::hasColumn('element_slug') ? (string) ($notification->element_slug ?? '') : null,
                    subtopicSlug: self::hasColumn('subtopic_slug') ? (string) ($notification->subtopic_slug ?? '') : null,
                    notificationId: (int) ($notification->id ?? 0),
                    occurredAt: (int) now()->timestamp
                ));
            } catch (Throwable $exception) {
                // Realtime is optional; persistence must not fail when websocket server is down.
                Log::warning('Notification realtime broadcast failed.', [
                    'notification_id' => (int) ($notification->id ?? 0),
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $notification;
    }

    public function getCoordinatorRoleLabelAttribute(): string
    {
        $role = $this->coordinatorAccount?->role;

        return $role !== null
            ? Account::roleLabel($role)
            : 'Pengguna';
    }

    private static function hasColumn(string $column): bool
    {
        if (array_key_exists($column, self::$columnExistsCache)) {
            return self::$columnExistsCache[$column];
        }

        $exists = Schema::hasTable((new self())->getTable())
            && Schema::hasColumn((new self())->getTable(), $column);

        self::$columnExistsCache[$column] = $exists;

        return $exists;
    }

    private static function applyLegacyElementFilter(Builder $query, array $elementSlugs): void
    {
        $elementNumbers = collect($elementSlugs)
            ->map(fn ($slug) => self::extractElementNumber((string) $slug))
            ->filter(fn ($value) => $value !== '')
            ->unique()
            ->values();

        if ($elementNumbers->isEmpty()) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->where(function (Builder $builder) use ($elementNumbers) {
            foreach ($elementNumbers as $elementNumber) {
                $builder->orWhere('subtopic_title', 'like', 'Element '.$elementNumber.'%');
            }
        });
    }

    private static function extractElementNumber(string $slug): string
    {
        if (preg_match('/^element(\d+)$/i', $slug, $matches)) {
            return (string) ($matches[1] ?? '');
        }

        return '';
    }
}
