<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\ElementTeamAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function authorizeChannel(Request $request): JsonResponse
    {
        $sessionUser = (array) Session::get('user', []);
        if ($sessionUser === []) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $socketId = trim((string) $request->input('socket_id', ''));
        $channelName = trim((string) $request->input('channel_name', ''));

        if (!preg_match('/^\d+\.\d+$/', $socketId) || $channelName === '') {
            return response()->json(['message' => 'Invalid channel auth payload'], 422);
        }

        if (!$this->canAccessChannel($sessionUser, $channelName)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $reverbConnection = (array) config('broadcasting.connections.reverb', []);
        $appKey = trim((string) ($reverbConnection['key'] ?? ''));
        $appSecret = trim((string) ($reverbConnection['secret'] ?? ''));
        if ($appKey === '' || $appSecret === '') {
            return response()->json(['message' => 'Realtime credentials are not configured'], 500);
        }

        $signature = hash_hmac('sha256', $socketId.':'.$channelName, $appSecret);

        return response()->json([
            'auth' => $appKey.':'.$signature,
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        $scopeSlug = trim((string) $request->query('scope', ''));
        $sessionUser = (array) Session::get('user', []);

        return response()->json($this->buildFeedPayload($sessionUser, $scopeSlug));
    }

    public function markRead(Request $request): JsonResponse
    {
        $scopeSlug = trim((string) $request->input('scope', $request->query('scope', '')));
        $sessionUser = (array) Session::get('user', []);
        $username = trim((string) ($sessionUser['username'] ?? ''));

        if ($username !== '' && Schema::hasTable('notification_reads')) {
            $notificationIds = Notification::queryForUser($sessionUser, $scopeSlug)
                ->limit(50)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            if ($notificationIds !== []) {
                $now = now();
                $rows = collect($notificationIds)
                    ->map(fn (int $notificationId) => [
                        'notification_id' => $notificationId,
                        'username' => $username,
                        'read_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                    ->all();

                NotificationRead::query()->upsert(
                    $rows,
                    ['notification_id', 'username'],
                    ['read_at', 'updated_at']
                );
            }
        }

        return response()->json($this->buildFeedPayload($sessionUser, $scopeSlug));
    }

    private function buildFeedPayload(array $sessionUser, string $scopeSlug): array
    {
        $notifications = Notification::feedForUser($sessionUser, $scopeSlug, 50)->values();
        $latestNotification = $notifications->first();
        $signature = $latestNotification
            ? trim((string) (($latestNotification->id ?? '0').'|'.($latestNotification->created_at?->timestamp ?? 0)))
            : '';

        $readMap = $this->readMapForUser($sessionUser, $notifications);
        $unreadCount = $notifications
            ->filter(fn (Notification $notification) => !isset($readMap[(int) ($notification->id ?? 0)]))
            ->count();

        return [
            'count' => $notifications->count(),
            'unread_count' => $unreadCount,
            'signature' => $signature,
            'items' => $notifications
                ->map(fn (Notification $notification) => $this->formatNotificationItem(
                    $notification,
                    isset($readMap[(int) ($notification->id ?? 0)])
                ))
                ->values()
                ->all(),
        ];
    }

    private function canAccessChannel(array $sessionUser, string $channelName): bool
    {
        $channel = trim($channelName);
        if (!Str::startsWith($channel, 'private-notifications.')) {
            return false;
        }

        $roleKey = strtolower(trim((string) ($sessionUser['role'] ?? '')));
        $isAdminOrQa = in_array($roleKey, ['administrator', 'admin', 'superadmin', 'qa'], true);
        if ($isAdminOrQa) {
            return true;
        }

        if ($channel === 'private-notifications.all') {
            return false;
        }

        if (!preg_match('/^private-notifications\.element\.(element\d+)$/i', $channel, $matches)) {
            return false;
        }

        $elementSlug = strtolower(trim((string) ($matches[1] ?? '')));
        if ($elementSlug === '') {
            return false;
        }

        if (ElementTeamAssignment::isRestrictedRole($roleKey)) {
            return ElementTeamAssignment::canUserAccessSlug($sessionUser, $elementSlug);
        }

        return true;
    }

    private function readMapForUser(array $sessionUser, Collection $notifications): array
    {
        $username = trim((string) ($sessionUser['username'] ?? ''));
        if ($username === '' || $notifications->isEmpty() || !Schema::hasTable('notification_reads')) {
            return [];
        }

        $notificationIds = $notifications
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values()
            ->all();

        if ($notificationIds === []) {
            return [];
        }

        return NotificationRead::query()
            ->where('username', $username)
            ->whereIn('notification_id', $notificationIds)
            ->pluck('notification_id')
            ->map(fn ($id) => (int) $id)
            ->flip()
            ->all();
    }

    private function formatNotificationItem(Notification $notification, bool $isRead = false): array
    {
        $notifyActorName = trim((string) ($notification->coordinator_name ?: ($notification->coordinatorAccount?->display_name ?? $notification->coordinator_username ?? 'Pengguna')));
        $notifyActorRole = trim((string) ($notification->coordinator_role_label ?? 'Pengguna'));
        $notifyActorPhotoUrl = $this->resolvePhotoUrl($notification->coordinatorAccount?->profile_photo ?? '');
        $notifyActorInitials = $this->avatarLabel($notifyActorName, 'U');
        $notifyTitle = trim((string) ($notification->subtopic_title ?? 'Notifikasi'));
        $notifyTitle = preg_replace('/^\s*element\s*\d+\s*[-:]?\s*/i', '', $notifyTitle);
        $notifyTitle = is_string($notifyTitle) ? trim($notifyTitle) : 'Notifikasi';
        $notifyTitle = preg_replace('/^\s*sub\s*topik\s*\d+\s*[-:]?\s*/i', '', $notifyTitle);
        $notifyTitle = is_string($notifyTitle) ? trim($notifyTitle) : 'Notifikasi';
        if ($notifyTitle === '') {
            $notifyTitle = 'Notifikasi';
        }

        $notifyStatement = trim((string) ($notification->statement ?? ''));
        if ($notifyStatement !== '' && !Str::contains($notifyStatement, '·')) {
            $normalized = preg_replace('/^.*?\bmelakukan\b\s*/iu', '', $notifyStatement);
            $normalized = is_string($normalized) ? $normalized : $notifyStatement;
            $normalized = preg_replace('/\bpada\s+element\s+\d+.*?:\s*/iu', '', $normalized);
            $normalized = is_string($normalized) ? trim($normalized) : $notifyStatement;

            $legacyActionMap = [
                'reset verifikasi final qa' => 'Reset QA',
                'verifikasi final qa' => 'Verifikasi QA',
                'reset verifikasi qa' => 'Reset QA',
                'verifikasi qa' => 'Verifikasi QA',
                'reset verifikasi' => 'Reset Verifikasi',
                'verifikasi' => 'Verifikasi',
                'pembersihan data' => 'Hapus Isian',
                'pengisian/perubahan data' => 'Isi Data',
                'isi/ubah data' => 'Isi Data',
            ];

            foreach ($legacyActionMap as $legacyAction => $compactAction) {
                if (Str::startsWith(Str::lower($normalized), $legacyAction)) {
                    $rest = trim((string) Str::substr($normalized, Str::length($legacyAction)));
                    $normalized = $compactAction.($rest !== '' ? ' · '.$rest : '');
                    break;
                }
            }

            $notifyStatement = $normalized;
        }
        $notifyStatement = Str::limit($notifyStatement, 64, '…');

        $notifyActionText = '';
        $notifyDetailText = $notifyStatement;
        if (Str::contains($notifyStatement, '·')) {
            [$notifyActionText, $notifyDetailText] = array_pad(explode('·', $notifyStatement, 2), 2, '');
            $notifyActionText = trim((string) $notifyActionText);
            $notifyDetailText = trim((string) $notifyDetailText);
        }

        $notifyActionClass = match (Str::lower($notifyActionText)) {
            'isi data', 'isi/ubah data' => 'is-save',
            'hapus isian', 'bersihkan' => 'is-clear',
            'verifikasi', 'verifikasi qa', 'verifikasi final qa' => 'is-verify',
            'reset verifikasi', 'reset qa', 'reset final qa', 'reset verifikasi qa' => 'is-verify-reset',
            default => 'is-save',
        };

        return [
            'actor_name' => $notifyActorName,
            'actor_role' => $notifyActorRole,
            'actor_photo_url' => $notifyActorPhotoUrl,
            'actor_initials' => $notifyActorInitials,
            'title' => $notifyTitle,
            'action_text' => $notifyActionText,
            'action_class' => $notifyActionClass,
            'detail_text' => $notifyDetailText,
            'time_label' => $notification->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-',
            'is_read' => $isRead,
        ];
    }

    private function resolvePhotoUrl(?string $path): string
    {
        $photoPath = trim((string) $path);

        if ($photoPath === '') {
            return '';
        }

        if (Str::startsWith($photoPath, ['http://', 'https://'])) {
            return $photoPath;
        }

        if (Str::startsWith($photoPath, ['/', '\\'])) {
            return $photoPath;
        }

        return asset('uploads/'.$photoPath);
    }

    private function avatarLabel(?string $name, string $fallback = 'US'): string
    {
        $normalized = trim((string) $name);

        if ($normalized === '') {
            return Str::upper($fallback);
        }

        $compact = preg_replace('/\s+/', '', $normalized);
        $compact = is_string($compact) && $compact !== '' ? $compact : $normalized;

        return Str::upper(Str::substr($compact, 0, 2));
    }
}
