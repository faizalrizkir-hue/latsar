<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ElementTeamAssignment extends Model
{
    protected $table = 'element_team_assignments';

    protected $fillable = [
        'element_slug',
        'coordinator_username',
        'member_usernames',
    ];

    protected $casts = [
        'member_usernames' => 'array',
    ];

    public static function isRestrictedRole(?string $role): bool
    {
        return in_array(strtolower(trim((string) $role)), ['koordinator', 'auditor'], true);
    }

    public static function topLevelElementSlug(?string $slug): string
    {
        $value = trim((string) $slug);
        if ($value === '') {
            return '';
        }

        if (preg_match('/^(element\d+)/', $value, $matches)) {
            return (string) ($matches[1] ?? '');
        }

        return Str::before($value, '_');
    }

    public static function assignedElementSlugsForUser(array $user): ?array
    {
        $role = strtolower(trim((string) ($user['role'] ?? '')));
        if (!self::isRestrictedRole($role)) {
            return null;
        }

        if (!Schema::hasTable('element_team_assignments')) {
            return null;
        }

        $username = trim((string) ($user['username'] ?? ''));
        if ($username === '') {
            return [];
        }

        $query = self::query();
        if ($role === 'koordinator') {
            $query->where('coordinator_username', $username);
        } else {
            $query->whereJsonContains('member_usernames', $username);
        }

        $assigned = $query
            ->pluck('element_slug')
            ->map(fn ($slug) => self::topLevelElementSlug((string) $slug))
            ->filter(fn ($slug) => $slug !== '')
            ->unique()
            ->values()
            ->all();

        if (count($assigned) > 0) {
            return $assigned;
        }

        // Fail-open when assignment table is empty to avoid blank navigation
        // on new/clean installations before team assignment is configured.
        if (!self::query()->exists()) {
            return null;
        }

        return [];
    }

    public static function canUserAccessSlug(array $user, string $slug): bool
    {
        $assignedElementSlugs = self::assignedElementSlugsForUser($user);
        if ($assignedElementSlugs === null) {
            return true;
        }

        $targetTopLevelSlug = self::topLevelElementSlug($slug);
        if ($targetTopLevelSlug === '') {
            return false;
        }

        return in_array($targetTopLevelSlug, $assignedElementSlugs, true);
    }

    public static function coordinatorUsernameForSlug(string $slug): ?string
    {
        if (!Schema::hasTable('element_team_assignments')) {
            return null;
        }

        $targetTopLevelSlug = self::topLevelElementSlug($slug);
        if ($targetTopLevelSlug === '') {
            return null;
        }

        $coordinatorUsername = self::query()
            ->where('element_slug', $targetTopLevelSlug)
            ->value('coordinator_username');

        $coordinatorUsername = trim((string) $coordinatorUsername);

        return $coordinatorUsername !== '' ? $coordinatorUsername : null;
    }
}
