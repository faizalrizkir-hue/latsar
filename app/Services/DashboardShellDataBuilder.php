<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ElementTeamAssignment;
use App\Models\Notification;
use App\Support\DashboardNavNormalizer;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

class DashboardShellDataBuilder
{
    /**
     * @param array<string, mixed> $viewData
     * @return array<string, mixed>
     */
    public function build(array $viewData): array
    {
        $sessionUser = is_array($viewData['user'] ?? null) ? $viewData['user'] : [];
        $notificationScopeSlug = trim((string) ($viewData['notificationScopeSlug'] ?? (string) request()->route('slug', '')));
        $notificationScopeElementSlug = ElementTeamAssignment::topLevelElementSlug($notificationScopeSlug);
        $notificationScopeLabel = '';
        if (preg_match('/^element(\d+)$/i', $notificationScopeElementSlug, $matches)) {
            $notificationScopeLabel = 'Penugasan Element '.($matches[1] ?? '');
        }

        $notifications = $viewData['notifications'] ?? null;
        $notificationItems = isset($notifications)
            ? collect($notifications)
            : Notification::feedForUser($sessionUser, $notificationScopeSlug, 50);

        $notificationCount = $notificationItems->count();
        $notificationUnreadCount = $notificationCount;
        $latestNotification = $notificationCount > 0 ? $notificationItems->first() : null;
        $latestNotificationSignature = $latestNotification
            ? trim((string) (($latestNotification->id ?? '0').'|'.($latestNotification->created_at?->timestamp ?? 0)))
            : '';

        $notificationFeedUrl = route('notifications.feed');
        $notificationAuthUrl = route('notifications.auth');
        $notificationMarkReadUrl = route('notifications.mark-read');

        $reverbConnection = (array) config('broadcasting.connections.reverb', []);
        $reverbOptions = (array) ($reverbConnection['options'] ?? []);
        $reverbAppKey = trim((string) ($reverbConnection['key'] ?? ''));
        $reverbHost = trim((string) ($reverbOptions['host'] ?? '127.0.0.1'));
        $reverbPort = (int) ($reverbOptions['port'] ?? 8080);
        $reverbScheme = strtolower(trim((string) ($reverbOptions['scheme'] ?? 'http')));
        if ($reverbScheme !== 'https') {
            $reverbScheme = 'http';
        }

        $notificationRealtimeEnabled = $reverbAppKey !== ''
            && strtolower((string) config('broadcasting.default', '')) === 'reverb';
        $userRoleKey = strtolower((string) ($sessionUser['role'] ?? ''));
        $canManageAccounts = in_array($userRoleKey, ['administrator', 'admin', 'superadmin'], true);

        $notificationRealtimeChannels = [];
        if ($notificationScopeElementSlug !== '') {
            $notificationRealtimeChannels[] = 'private-notifications.element.'.$notificationScopeElementSlug;
        } elseif (in_array($userRoleKey, ['administrator', 'admin', 'superadmin', 'qa'], true)) {
            $notificationRealtimeChannels[] = 'private-notifications.all';
        } else {
            $assignedNotificationElementSlugs = ElementTeamAssignment::assignedElementSlugsForUser($sessionUser);
            if (is_array($assignedNotificationElementSlugs) && count($assignedNotificationElementSlugs) > 0) {
                foreach ($assignedNotificationElementSlugs as $assignedElementSlug) {
                    $assignedElementSlug = trim((string) $assignedElementSlug);
                    if ($assignedElementSlug === '') {
                        continue;
                    }
                    $notificationRealtimeChannels[] = 'private-notifications.element.'.$assignedElementSlug;
                }
            } else {
                $notificationRealtimeChannels[] = 'private-notifications.all';
            }
        }
        $notificationRealtimeChannels = array_values(array_unique(array_filter($notificationRealtimeChannels)));

        $photoPath = $sessionUser['profile_photo'] ?? '';
        $photoUrl = '';
        $userRoleLabel = $sessionUser['role_label'] ?? Account::roleLabel($sessionUser['role'] ?? null);

        $resolvePhotoUrl = static function (?string $path): string {
            $resolvedPhotoPath = trim((string) $path);

            if ($resolvedPhotoPath === '') {
                return '';
            }

            if (Str::startsWith($resolvedPhotoPath, ['http://', 'https://'])) {
                return $resolvedPhotoPath;
            }

            if (Str::startsWith($resolvedPhotoPath, ['/', '\\'])) {
                return $resolvedPhotoPath;
            }

            return asset('uploads/'.$resolvedPhotoPath);
        };

        $avatarLabel = static function (?string $name, string $fallback = 'US'): string {
            $normalized = trim((string) $name);

            if ($normalized === '') {
                return Str::upper($fallback);
            }

            $compact = preg_replace('/\s+/', '', $normalized);
            $compact = is_string($compact) && $compact !== '' ? $compact : $normalized;

            return Str::upper(Str::substr($compact, 0, 2));
        };

        $elementPreferenceService = app(ElementPreferenceService::class);
        $visibleElementNavSlugs = ElementTeamAssignment::assignedElementSlugsForUser($sessionUser);
        $subtopicModulesForNav = collect($elementPreferenceService->subtopicModules(true))
            ->filter(fn ($item) => is_array($item));

        $subtopicTitleBySlug = $subtopicModulesForNav
            ->mapWithKeys(function ($module, $slug) {
                $resolvedSlug = (string) $slug;
                if ($resolvedSlug === '') {
                    return [];
                }

                $resolvedTitle = trim((string) ($module['subtopic_title'] ?? ''));
                if ($resolvedTitle === '') {
                    return [];
                }

                return [$resolvedSlug => $resolvedTitle];
            })
            ->all();

        $resolveElementNumber = static function (string $slug, string $title, int $fallbackIndex = 0): string {
            if (preg_match('/^element(\d+)$/i', $slug, $matches)) {
                return (string) ($matches[1] ?? '');
            }

            if (preg_match('/element\s*(\d+)/i', $title, $matches)) {
                return (string) ($matches[1] ?? '');
            }

            return $fallbackIndex > 0 ? (string) $fallbackIndex : 'E';
        };

        $normalizeSubtopicNavTitle = static function (string $title, int $position): string {
            $resolvedTitle = trim($title);
            if ($resolvedTitle === '') {
                return 'Sub Topik '.$position;
            }

            $cleanedTitle = preg_replace('/^\s*sub\s*topik\s*\d+\s*[-:]?\s*/i', '', $resolvedTitle);
            $cleanedTitle = is_string($cleanedTitle) ? trim($cleanedTitle) : $resolvedTitle;

            return $cleanedTitle !== '' ? $cleanedTitle : $resolvedTitle;
        };

        $structureElements = collect((array) ($elementPreferenceService->structure()['elements'] ?? []))
            ->filter(fn ($item) => is_array($item))
            ->values();

        $navElements = $structureElements
            ->filter(function (array $element) use ($visibleElementNavSlugs): bool {
                $elementSlug = (string) ($element['slug'] ?? '');
                if ($elementSlug === '' || !(bool) ($element['active'] ?? false)) {
                    return false;
                }

                if ($visibleElementNavSlugs === null) {
                    return true;
                }

                return in_array($elementSlug, $visibleElementNavSlugs, true);
            })
            ->map(function (array $element, int $elementIndex) use ($subtopicTitleBySlug, $resolveElementNumber, $normalizeSubtopicNavTitle) {
                $elementSlug = (string) ($element['slug'] ?? '');
                $elementTitle = trim((string) ($element['title'] ?? $elementSlug));
                if ($elementTitle === '') {
                    $elementTitle = Str::headline($elementSlug);
                }

                $subtopics = collect((array) ($element['subtopics'] ?? []))
                    ->filter(fn ($item) => is_array($item) && (bool) ($item['active'] ?? false))
                    ->values()
                    ->map(function (array $subtopic, int $subtopicIndex) use ($subtopicTitleBySlug, $normalizeSubtopicNavTitle) {
                        $subtopicSlug = (string) ($subtopic['slug'] ?? '');
                        $subtopicTitle = trim((string) ($subtopicTitleBySlug[$subtopicSlug] ?? ($subtopic['title'] ?? $subtopicSlug)));
                        if ($subtopicTitle === '') {
                            $subtopicTitle = Str::headline($subtopicSlug);
                        }

                        return [
                            'slug' => $subtopicSlug,
                            'title' => $normalizeSubtopicNavTitle($subtopicTitle, $subtopicIndex + 1),
                        ];
                    })
                    ->filter(fn ($item) => $item['slug'] !== '')
                    ->values()
                    ->all();

                $elementNumber = $resolveElementNumber($elementSlug, $elementTitle, $elementIndex + 1);
                $navTitle = is_numeric($elementNumber) ? 'Element '.$elementNumber : $elementTitle;
                $iconLabel = is_numeric($elementNumber) ? $elementNumber : 'E';

                return [
                    'slug' => $elementSlug,
                    'title' => $elementTitle,
                    'nav_title' => $navTitle,
                    'icon_label' => Str::upper($iconLabel),
                    'subtopics' => $subtopics,
                ];
            })
            ->values();

        if ($navElements->isEmpty()) {
            $subtopicModules = collect($elementPreferenceService->subtopicModules(true))
                ->filter(fn ($item) => is_array($item));

            $fallbackElements = $subtopicModules
                ->groupBy(fn ($module, $slug) => ElementTeamAssignment::topLevelElementSlug((string) $slug))
                ->filter(fn ($modules, $elementSlug) => is_string($elementSlug) && $elementSlug !== '')
                ->map(function ($modules, $elementSlug) use ($resolveElementNumber, $normalizeSubtopicNavTitle) {
                    $firstModule = $modules->first();
                    $elementTitle = trim((string) ($firstModule['page_title'] ?? ''));
                    if ($elementTitle === '') {
                        $elementTitle = Str::headline((string) $elementSlug);
                    }

                    $subtopicPosition = 1;
                    $subtopics = $modules
                        ->map(function ($module, $slug) use ($normalizeSubtopicNavTitle, &$subtopicPosition) {
                            $subtopicSlug = (string) $slug;
                            $subtopicTitle = trim((string) ($module['subtopic_title'] ?? $subtopicSlug));
                            if ($subtopicTitle === '') {
                                $subtopicTitle = Str::headline($subtopicSlug);
                            }

                            return [
                                'slug' => $subtopicSlug,
                                'title' => $normalizeSubtopicNavTitle($subtopicTitle, $subtopicPosition++),
                            ];
                        })
                        ->filter(fn ($item) => ($item['slug'] ?? '') !== '')
                        ->values()
                        ->all();

                    $elementNumber = $resolveElementNumber((string) $elementSlug, $elementTitle);
                    $navTitle = is_numeric($elementNumber) ? 'Element '.$elementNumber : $elementTitle;
                    $iconLabel = is_numeric($elementNumber) ? $elementNumber : 'E';

                    return [
                        'slug' => (string) $elementSlug,
                        'title' => $elementTitle,
                        'nav_title' => $navTitle,
                        'icon_label' => Str::upper($iconLabel),
                        'subtopics' => $subtopics,
                    ];
                })
                ->values();

            if ($visibleElementNavSlugs !== null) {
                $fallbackElements = $fallbackElements
                    ->filter(fn ($element) => in_array((string) ($element['slug'] ?? ''), $visibleElementNavSlugs, true))
                    ->values();
            }

            $navElements = $fallbackElements;
        }

        $navElements = DashboardNavNormalizer::sanitize($navElements);
        $maxSubtopicCount = max(1, (int) $navElements->max(fn ($item) => collect($item['subtopics'] ?? [])->count()));
        $navElements = $navElements
            ->map(function (array $item) use ($maxSubtopicCount): array {
                $subtopicCount = collect($item['subtopics'] ?? [])->count();
                $coveragePercent = (int) round(($subtopicCount / $maxSubtopicCount) * 100);

                $item['subtopic_count'] = $subtopicCount;
                $item['coverage_percent'] = max(8, min(100, $coveragePercent));

                return $item;
            })
            ->values();

        $normalizeElementDirectoryTitle = static function (string $navTitle, string $elementTitle): string {
            $resolvedNavTitle = trim($navTitle);
            $resolvedElementTitle = trim($elementTitle);
            if ($resolvedNavTitle === '') {
                return $resolvedElementTitle;
            }

            if ($resolvedElementTitle === '') {
                return $resolvedNavTitle;
            }

            $cleanedTitle = preg_replace('/^\s*element\s*\d+\s*[:\-]?\s*/i', '', $resolvedElementTitle);
            $cleanedTitle = is_string($cleanedTitle) ? trim($cleanedTitle) : $resolvedElementTitle;
            if ($cleanedTitle === '' || strcasecmp($cleanedTitle, $resolvedNavTitle) === 0) {
                return $resolvedNavTitle;
            }

            return $resolvedNavTitle.' : '.$cleanedTitle;
        };

        $formatSubtopicDirectoryTitle = static function (string $subtopicTitle, int $position): string {
            $resolvedTitle = trim($subtopicTitle);
            if ($resolvedTitle === '') {
                return 'Sub Topik '.$position;
            }

            $cleanedTitle = preg_replace('/^\s*sub\s*topik\s*\d+\s*[-:]?\s*/i', '', $resolvedTitle);
            $cleanedTitle = is_string($cleanedTitle) ? trim($cleanedTitle) : $resolvedTitle;
            if ($cleanedTitle === '') {
                return 'Sub Topik '.$position;
            }

            return 'Sub Topik '.$position.' - '.$cleanedTitle;
        };

        $elementDirectoryBySlug = [];
        $subtopicDirectoryBySlug = [];
        $subtopicPositionBySlug = [];
        foreach ($navElements as $elementNav) {
            $elementSlug = (string) ($elementNav['slug'] ?? '');
            if ($elementSlug === '') {
                continue;
            }

            $elementDirectory = $normalizeElementDirectoryTitle(
                (string) ($elementNav['nav_title'] ?? ''),
                (string) ($elementNav['title'] ?? '')
            );
            $elementDirectoryBySlug[$elementSlug] = $elementDirectory;

            $subtopics = collect((array) ($elementNav['subtopics'] ?? []))->values();
            foreach ($subtopics as $subtopicIndex => $subtopicNav) {
                $subtopicSlug = (string) ($subtopicNav['slug'] ?? '');
                if ($subtopicSlug === '') {
                    continue;
                }

                $subtopicDirectoryBySlug[$subtopicSlug] = [
                    'element_slug' => $elementSlug,
                    'element_directory' => $elementDirectory,
                    'subtopic_title_raw' => trim((string) ($subtopicNav['title'] ?? '')),
                    'subtopic_directory' => $formatSubtopicDirectoryTitle((string) ($subtopicNav['title'] ?? ''), $subtopicIndex + 1),
                ];
                $subtopicPositionBySlug[$subtopicSlug] = $subtopicIndex + 1;
            }
        }

        $routeName = request()->route()?->getName();
        $currentSlug = trim((string) request()->route('slug', ''));
        $defaultHeadnavTitle = trim((string) (($viewData['pageTitle'] ?? null) ?? 'Halaman'));

        $headnavCrumbs = [
            [
                'label' => 'Dashboard',
                'url' => route('dashboard'),
                'is_current' => false,
            ],
        ];

        $pushHeadnavCrumb = static function (array &$crumbs, string $label, ?string $url = null): void {
            $resolvedLabel = trim($label);
            if ($resolvedLabel === '') {
                return;
            }

            $crumbs[] = [
                'label' => $resolvedLabel,
                'url' => $url,
                'is_current' => false,
            ];
        };

        if ($routeName === 'dashboard') {
            // keep single breadcrumb
        } elseif ($routeName === 'elements.show') {
            if ($currentSlug !== '' && Str::contains($currentSlug, '_')) {
                $resolvedElementSlug = trim((string) Str::before($currentSlug, '_'));
                $resolvedElementDirectory = trim((string) ($elementDirectoryBySlug[$resolvedElementSlug] ?? ''));
                $modulePageTitleText = trim((string) (($viewData['modulePageTitle'] ?? null) ?? ''));
                if ($resolvedElementDirectory === '' && $resolvedElementSlug !== '') {
                    $elementNumber = '';
                    if (preg_match('/^element(\d+)$/i', $resolvedElementSlug, $matches)) {
                        $elementNumber = (string) ($matches[1] ?? '');
                    }
                    $elementNavLabel = $elementNumber !== '' ? 'Element '.$elementNumber : Str::headline($resolvedElementSlug);
                    $resolvedElementDirectory = $normalizeElementDirectoryTitle(
                        $elementNavLabel,
                        $modulePageTitleText !== '' ? $modulePageTitleText : $elementNavLabel
                    );
                }
                $resolvedElementUrl = $resolvedElementSlug !== '' ? route('elements.show', $resolvedElementSlug) : null;
                $pushHeadnavCrumb(
                    $headnavCrumbs,
                    $resolvedElementDirectory,
                    $resolvedElementUrl
                );

                $resolvedSubtopicPosition = (int) ($subtopicPositionBySlug[$currentSlug] ?? 0);
                if ($resolvedSubtopicPosition <= 0 && preg_match('/sub\s*topik\s*(\d+)/i', (string) (($viewData['title'] ?? null) ?? ''), $matches)) {
                    $resolvedSubtopicPosition = max(1, (int) ($matches[1] ?? 1));
                }
                if ($resolvedSubtopicPosition <= 0) {
                    $resolvedSubtopicPosition = 1;
                }

                $resolvedSubtopicTitle = trim((string) ($subtopicDirectoryBySlug[$currentSlug]['subtopic_title_raw'] ?? ''));
                if ($resolvedSubtopicTitle === '') {
                    $resolvedSubtopicTitle = trim((string) (($viewData['moduleSubtopicTitle'] ?? ($viewData['title'] ?? null)) ?? ''));
                }
                if ($resolvedSubtopicTitle === '') {
                    $resolvedSubtopicTitle = Str::headline(str_replace('_', ' ', (string) Str::after($currentSlug, $resolvedElementSlug.'_')));
                }

                $resolvedSubtopicDirectory = $formatSubtopicDirectoryTitle($resolvedSubtopicTitle, $resolvedSubtopicPosition);
                $pushHeadnavCrumb(
                    $headnavCrumbs,
                    $resolvedSubtopicDirectory,
                    route('elements.show', $currentSlug)
                );
            } elseif ($currentSlug !== '' && isset($elementDirectoryBySlug[$currentSlug])) {
                $pushHeadnavCrumb(
                    $headnavCrumbs,
                    (string) $elementDirectoryBySlug[$currentSlug],
                    route('elements.show', $currentSlug)
                );
            } elseif ($defaultHeadnavTitle !== '') {
                $pushHeadnavCrumb($headnavCrumbs, $defaultHeadnavTitle);
            }
        } elseif ($routeName === 'elements.index') {
            $pushHeadnavCrumb($headnavCrumbs, 'Penilaian Element', route('elements.index'));
        } elseif (is_string($routeName) && Str::startsWith($routeName, 'dms.')) {
            $pushHeadnavCrumb($headnavCrumbs, 'Data Management System', route('dms.index'));
            if ($routeName === 'dms.create') {
                $pushHeadnavCrumb($headnavCrumbs, 'Tambah Dokumen');
            } elseif ($routeName === 'dms.edit') {
                $pushHeadnavCrumb($headnavCrumbs, 'Edit Dokumen');
            }
        } elseif ($routeName === 'aoi.index') {
            $pushHeadnavCrumb($headnavCrumbs, 'Area Of Improvement (AoI)', route('aoi.index'));
        } elseif (is_string($routeName) && Str::startsWith($routeName, 'informasi-umum.')) {
            $pushHeadnavCrumb($headnavCrumbs, 'Informasi Umum', route('informasi-umum.index'));
        } elseif ($routeName === 'profile.edit') {
            $pushHeadnavCrumb($headnavCrumbs, 'Edit Profil', route('profile.edit'));
        } elseif ($routeName === 'accounts.index') {
            $pushHeadnavCrumb($headnavCrumbs, 'Manajemen Akun', route('accounts.index'));
        } elseif (is_string($routeName) && Str::startsWith($routeName, 'element-preferences.')) {
            $pushHeadnavCrumb($headnavCrumbs, 'Preferensi Element', route('element-preferences.index'));
        } elseif ($defaultHeadnavTitle !== '') {
            $pushHeadnavCrumb($headnavCrumbs, $defaultHeadnavTitle);
        }

        $headnavCrumbs = collect($headnavCrumbs)
            ->map(function (array $crumb): array {
                $crumb['label'] = trim((string) ($crumb['label'] ?? ''));
                $crumb['url'] = ($crumb['url'] ?? null) ? trim((string) $crumb['url']) : null;
                $crumb['is_current'] = false;

                return $crumb;
            })
            ->filter(fn (array $crumb) => $crumb['label'] !== '')
            ->values()
            ->all();
        $headnavCrumbCount = count($headnavCrumbs);
        if ($headnavCrumbCount > 0) {
            $headnavCrumbs[$headnavCrumbCount - 1]['is_current'] = true;
            $headnavCrumbs[$headnavCrumbCount - 1]['url'] = null;
        }

        $headnavTitle = implode(' / ', collect($headnavCrumbs)
            ->map(fn ($crumb) => trim((string) ($crumb['label'] ?? '')))
            ->filter()
            ->values()
            ->all());
        $photoUrl = $resolvePhotoUrl($photoPath);

        $toastQueue = [];
        if (session('login_welcome_toast')) {
            $toastQueue[] = session('login_welcome_toast');
        }
        if (session('status')) {
            $toastQueue[] = ['type' => 'success', 'title' => 'Berhasil', 'message' => session('status')];
        }
        if (session('error')) {
            $toastQueue[] = ['type' => 'error', 'title' => 'Gagal', 'message' => session('error')];
        }

        $errors = $viewData['errors'] ?? app('view')->shared('errors');
        if (!$errors instanceof ViewErrorBag) {
            $errors = new ViewErrorBag();
        }
        if ($errors->any()) {
            $toastQueue[] = ['type' => 'error', 'title' => 'Periksa lagi', 'message' => $errors->first()];
        }

        $idleTimeoutMinutes = max(1, (int) config('session.idle_timeout', 60));
        $idleTimeoutMs = $idleTimeoutMinutes * 60 * 1000;

        return [
            'sessionUser' => $sessionUser,
            'notificationScopeSlug' => $notificationScopeSlug,
            'notificationScopeElementSlug' => $notificationScopeElementSlug,
            'notificationScopeLabel' => $notificationScopeLabel,
            'notificationItems' => $notificationItems,
            'notificationCount' => $notificationCount,
            'notificationUnreadCount' => $notificationUnreadCount,
            'latestNotification' => $latestNotification,
            'latestNotificationSignature' => $latestNotificationSignature,
            'notificationFeedUrl' => $notificationFeedUrl,
            'notificationAuthUrl' => $notificationAuthUrl,
            'notificationMarkReadUrl' => $notificationMarkReadUrl,
            'reverbAppKey' => $reverbAppKey,
            'reverbHost' => $reverbHost,
            'reverbPort' => $reverbPort,
            'reverbScheme' => $reverbScheme,
            'notificationRealtimeEnabled' => $notificationRealtimeEnabled,
            'userRoleKey' => $userRoleKey,
            'canManageAccounts' => $canManageAccounts,
            'notificationRealtimeChannels' => $notificationRealtimeChannels,
            'photoPath' => $photoPath,
            'photoUrl' => $photoUrl,
            'userRoleLabel' => $userRoleLabel,
            'resolvePhotoUrl' => $resolvePhotoUrl,
            'avatarLabel' => $avatarLabel,
            'navElements' => $navElements,
            'elementDirectoryBySlug' => $elementDirectoryBySlug,
            'subtopicDirectoryBySlug' => $subtopicDirectoryBySlug,
            'subtopicPositionBySlug' => $subtopicPositionBySlug,
            'headnavCrumbs' => $headnavCrumbs,
            'headnavTitle' => $headnavTitle,
            'toastQueue' => $toastQueue,
            'idleTimeoutMs' => $idleTimeoutMs,
        ];
    }
}
