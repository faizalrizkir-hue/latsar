<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'LATSAR' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/dashboard.css?v={{ @filemtime(public_path('css/dashboard.css')) }}">
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        // Terapkan tema tersimpan sedini mungkin supaya tidak kedip dan tidak kembali ke gelap
        (function(){
            try{
                const saved = localStorage.getItem('dashboard-theme');
                const initial = saved || 'light';
                document.documentElement.setAttribute('data-theme', initial);
            }catch(e){
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    @livewireStyles
    @stack('head')
</head>
@php
    use Illuminate\Support\Str;
    use App\Models\Account;
    use App\Models\Notification;
    use App\Models\ElementTeamAssignment;
    use App\Services\ElementPreferenceService;
    $sessionUser = is_array($user ?? null) ? $user : [];
    $notificationScopeSlug = trim((string) ($notificationScopeSlug ?? (string) request()->route('slug', '')));
    $notificationScopeElementSlug = ElementTeamAssignment::topLevelElementSlug($notificationScopeSlug);
    $notificationScopeLabel = '';
    if (preg_match('/^element(\d+)$/i', $notificationScopeElementSlug, $matches)) {
        $notificationScopeLabel = 'Penugasan Element '.($matches[1] ?? '');
    }
    $notificationItems = isset($notifications)
        ? collect($notifications)
        : Notification::feedForUser($sessionUser, $notificationScopeSlug, 50);
    $notificationCount = $notificationItems->count();
    $notificationUnreadCount = $notificationCount;
    $latestNotification = $notificationCount > 0 ? $notificationItems->first() : null;
    $latestNotificationSignature = $latestNotification
        ? trim((string) (($latestNotification->id ?? '0') . '|' . ($latestNotification->created_at?->timestamp ?? 0)))
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
    $notificationRealtimeEnabled = $reverbAppKey !== '' && strtolower((string) config('broadcasting.default', '')) === 'reverb';
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
                ->values();

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
                    ->values();
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
    $maxSubtopicCount = max(1, (int) $navElements->max(fn ($item) => collect((array) ($item['subtopics'] ?? []))->count()));
    $navElements = $navElements->map(function (array $item) use ($maxSubtopicCount): array {
        $subtopicCount = collect((array) ($item['subtopics'] ?? []))->count();
        $coveragePercent = (int) round(($subtopicCount / $maxSubtopicCount) * 100);

        $item['subtopic_count'] = $subtopicCount;
        $item['coverage_percent'] = max(8, min(100, $coveragePercent));

        return $item;
    })->values();
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
    $defaultHeadnavTitle = trim((string) ($pageTitle ?? 'Halaman'));
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
            $modulePageTitleText = trim((string) ($modulePageTitle ?? ''));
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
            if ($resolvedSubtopicPosition <= 0 && preg_match('/sub\s*topik\s*(\d+)/i', (string) ($title ?? ''), $matches)) {
                $resolvedSubtopicPosition = max(1, (int) ($matches[1] ?? 1));
            }
            if ($resolvedSubtopicPosition <= 0) {
                $resolvedSubtopicPosition = 1;
            }

            $resolvedSubtopicTitle = trim((string) ($subtopicDirectoryBySlug[$currentSlug]['subtopic_title_raw'] ?? ''));
            if ($resolvedSubtopicTitle === '') {
                $resolvedSubtopicTitle = trim((string) ($moduleSubtopicTitle ?? $title ?? ''));
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
    if ($errors->any()) {
        $toastQueue[] = ['type' => 'error', 'title' => 'Periksa lagi', 'message' => $errors->first()];
    }
    $idleTimeoutMinutes = max(1, (int) config('session.idle_timeout', 60));
    $idleTimeoutMs = $idleTimeoutMinutes * 60 * 1000;
@endphp
<body class="legacy-page">
<div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>
<div class="app">
    <aside class="sidenav" id="sidenav">
        <div class="brand">
            <img class="logo-light" src="/static/logo-sikap-light.png" alt="Logo SIKAP">
            <img class="logo-dark" src="/static/logo-sikap-dark.png" alt="Logo SIKAP">
        </div>
        <div class="user-card">
            <div class="avatar">
                @if(!empty($photoUrl))
                    <img src="{{ $photoUrl }}" alt="Foto profil {{ $user['display_name'] ?? 'User' }}">
                @else
                    {{ $avatarLabel($user['display_name'] ?? null) }}
                @endif
            </div>
            <strong>{{ $user['display_name'] ?? 'Admin' }}</strong>
            <div style="color:var(--text-muted);font-size:0.85rem;">{{ $userRoleLabel }}</div>
        </div>
        <ul class="nav" id="navMain">
            <li class="nav-section-label" aria-hidden="true"><span>Utama</span></li>
            <li><a href="{{ route('dashboard') }}"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 14a8 8 0 1 1 16 0"/><path d="M12 14l4-4"/><circle cx="12" cy="14" r="1.2"/></svg></span><span class="nav-text">Dashboard</span></a></li>
            <li class="nav-section-label" aria-hidden="true"><span>Penilaian Element</span></li>
            @foreach($navElements as $elementNav)
            <li class="has-sub" style="--nav-progress: {{ (int) ($elementNav['coverage_percent'] ?? 0) }}%;">
                <a class="nav-toggle" data-sub-toggle="{{ $elementNav['slug'] }}">
                    <span class="nav-icon">{{ $elementNav['icon_label'] }}</span>
                    <span class="nav-text">{{ $elementNav['nav_title'] }}</span>
                    <span class="chevron">›</span>
                </a>
                <ul class="nav-sub" id="sub-{{ $elementNav['slug'] }}">
                    <li class="nav-sub-parent">
                        <a href="{{ route('elements.show', $elementNav['slug']) }}">
                            <span class="sub-icon">•</span>
                            <span>Rekapitulasi Element</span>
                        </a>
                    </li>
                    @foreach($elementNav['subtopics'] as $subtopicNav)
                        <li class="nav-sub-child">
                            <a href="{{ route('elements.show', $subtopicNav['slug']) }}">
                                <span class="sub-icon">•</span>
                                <span>{{ $subtopicNav['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            @endforeach
            <li class="nav-section-label" aria-hidden="true"><span>Lainnya</span></li>
            <li><a href="{{ route('aoi.index') }}"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="14" height="14" rx="2"/><path d="M11 4v14M4 11h14"/><path d="M19 16v6M16 19h6"/></svg></span><span class="nav-text">Area Of Improvement (AoI)</span></a></li>
            @if($userRoleKey !== 'qa')
                <li><a href="{{ route('dms.index') }}"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 8a3 3 0 0 1 3-3h4l2 2h6a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3Z"/><path d="M3 9h18"/></svg></span><span class="nav-text">Data Management System</span></a></li>
                <li><a href="{{ route('informasi-umum.index') }}"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 10.5v5"/><path d="M12 7.5h.01"/></svg></span><span class="nav-text">Informasi Umum</span></a></li>
            @endif
        </ul>
    </aside>

    <main class="content legacy-content">
    <div class="headnav topbar">
            <div class="topbar-primary">
                <button type="button" class="topbar-sidenav-toggle" id="sidenavToggle" aria-pressed="false" aria-controls="sidenav" aria-label="Sembunyikan menu samping">
                    <span class="icon" aria-hidden="true">
                        <svg class="icon-collapse" viewBox="0 0 24 24">
                            <path d="M15 6l-6 6 6 6"/>
                        </svg>
                        <svg class="icon-expand" viewBox="0 0 24 24">
                            <path d="M9 6l6 6-6 6"/>
                        </svg>
                    </span>
                </button>
                <div class="breadcrumb">
                    @if(!isset($showPageTitle) || $showPageTitle)
                        <nav id="pageTitle" class="topbar-page-title breadcrumb-trail" aria-label="Lokasi halaman">
                            @foreach($headnavCrumbs as $crumbIndex => $crumb)
                                @if($crumbIndex > 0)
                                    <span class="breadcrumb-separator" aria-hidden="true">/</span>
                                @endif
                                @if(!empty($crumb['url']) && empty($crumb['is_current']))
                                    <a class="breadcrumb-link" href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                @else
                                    <span class="breadcrumb-current">{{ $crumb['label'] }}</span>
                                @endif
                            @endforeach
                        </nav>
                    @endif
                    <div class="topbar-context-row">
                        <div class="breadcrumb-meta" id="liveDateTime"></div>
                    </div>
                </div>
            </div>
            <div class="top-actions-divider" aria-hidden="true"></div>
            <div class="top-actions">
                <button class="theme-toggle" id="themeToggle" aria-pressed="true">
                    <span class="icon" aria-hidden="true">
                        <svg class="icon-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4.5" /><path d="M12 2.5v2.5M12 19v2.5M4.5 12H2M22 12h-2.5M5.6 5.6 4 4M20 20l-1.6-1.6M18.4 5.6 20 4M4 20l1.6-1.6" /></svg>
                        <svg class="icon-moon" viewBox="0 0 24 24"><path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5 8.5 8.5 0 1 0 20.5 14.5Z" /></svg>
                    </span>
                    <span class="text">Gelap</span>
                </button>
                <div class="notify-menu">
                    <button
                        type="button"
                        class="notify-button{{ $notificationCount > 0 ? ' has-alert' : '' }}"
                        id="notifyButton"
                        aria-expanded="false"
                        aria-haspopup="true"
                        aria-controls="notifyDropdown"
                        aria-label="Notifikasi"
                        data-notify-count="{{ $notificationCount }}"
                        data-notify-unread="{{ $notificationUnreadCount }}"
                        data-notify-signature="{{ $latestNotificationSignature }}"
                        data-notify-feed-url="{{ $notificationFeedUrl }}"
                        data-notify-auth-url="{{ $notificationAuthUrl }}"
                        data-notify-mark-read-url="{{ $notificationMarkReadUrl }}"
                        data-notify-csrf-token="{{ csrf_token() }}"
                        data-notify-scope="{{ $notificationScopeSlug }}"
                        data-notify-scope-element="{{ $notificationScopeElementSlug }}"
                        data-notify-realtime-channels='@json($notificationRealtimeChannels)'
                        data-notify-realtime-enabled="{{ $notificationRealtimeEnabled ? '1' : '0' }}"
                        data-notify-realtime-key="{{ $reverbAppKey }}"
                        data-notify-realtime-host="{{ $reverbHost }}"
                        data-notify-realtime-port="{{ $reverbPort }}"
                        data-notify-realtime-scheme="{{ $reverbScheme }}"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true" style="width:16px;height:16px;">
                            <path d="M6 17h12l-1-2v-5a5 5 0 1 0-10 0v5l-1 2Z"/>
                            <path d="M9 17a3 3 0 0 0 6 0"/>
                        </svg>
                        <span class="notify-count" id="notifyCount">{{ $notificationUnreadCount }}</span>
                    </button>
                    <div class="notify-dropdown" id="notifyDropdown" role="menu" aria-label="Notifikasi">
                        <div class="notify-header">
                            <span class="label">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M6 17h12l-1-2v-5a5 5 0 1 0-10 0v5l-1 2Z"/>
                                    <path d="M9 17a3 3 0 0 0 6 0"/>
                                </svg>
                                <span>Notifikasi</span>
                            </span>
                            <span class="notify-summary is-static" aria-label="Rekap jumlah notifikasi">
                                <span class="notify-summary-count">{{ $notificationCount }}</span>
                                <span class="notify-summary-text">item</span>
                            </span>
                        </div>
                        <div class="notify-list" id="notifyList">
                            @forelse($notificationItems as $notifIndex => $notif)
                                @php
                                    $notifyActorName = trim((string) ($notif->coordinator_name ?: ($notif->coordinatorAccount?->display_name ?? $notif->coordinator_username ?? 'Pengguna')));
                                    $notifyActorRole = trim((string) ($notif->coordinator_role_label ?? 'Pengguna'));
                                    $notifyActorPhotoUrl = $resolvePhotoUrl($notif->coordinatorAccount?->profile_photo ?? '');
                                    $notifyActorInitials = $avatarLabel($notifyActorName, 'U');
                                    $notifyTitle = trim((string) ($notif->subtopic_title ?? 'Notifikasi'));
                                    $notifyTitle = preg_replace('/^\s*element\s*\d+\s*[-:]?\s*/i', '', $notifyTitle);
                                    $notifyTitle = is_string($notifyTitle) ? trim($notifyTitle) : 'Notifikasi';
                                    $notifyTitle = preg_replace('/^\s*sub\s*topik\s*\d+\s*[-:]?\s*/i', '', $notifyTitle);
                                    $notifyTitle = is_string($notifyTitle) ? trim($notifyTitle) : 'Notifikasi';
                                    if ($notifyTitle === '') {
                                        $notifyTitle = 'Notifikasi';
                                    }

                                    $notifyStatement = trim((string) ($notif->statement ?? ''));
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
                                @endphp
                                <div class="notify-item" style="--notify-order: {{ (int) $notifIndex }};">
                                    <div class="notify-item-top">
                                        <div class="notify-avatar" aria-hidden="true">
                                            @if($notifyActorPhotoUrl !== '')
                                                <img src="{{ $notifyActorPhotoUrl }}" alt="Foto profil {{ $notifyActorName }}">
                                            @else
                                                {{ $notifyActorInitials !== '' ? $notifyActorInitials : 'U' }}
                                            @endif
                                        </div>
                                        <div class="notify-actor">
                                            <div class="notify-actor-line">
                                                <span class="notify-actor-name">{{ $notifyActorName }}</span>
                                            </div>
                                            <div class="notify-actor-role">{{ $notifyActorRole }}</div>
                                        </div>
                                    </div>
                                    <div class="title">{{ $notifyTitle }}</div>
                                    @if($notifyActionText !== '' || $notifyDetailText !== '')
                                        <div class="notify-action-row">
                                            @if($notifyActionText !== '')
                                                <span class="notify-action-badge {{ $notifyActionClass }}">{{ $notifyActionText }}</span>
                                            @endif
                                            @if($notifyDetailText !== '')
                                                <span class="notify-body">{{ $notifyDetailText }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="meta">{{ $notif->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i') }}</div>
                                </div>
                            @empty
                                <div class="notify-item notify-item-empty">Belum ada notifikasi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="profile-menu-top">
                    <button type="button" class="profile-button" id="profileButton" aria-expanded="false" aria-haspopup="true" aria-controls="profileDropdown">
                        <div class="avatar">
                            @if(!empty($photoUrl))
                                <img src="{{ $photoUrl }}" alt="Foto profil {{ $sessionUser['display_name'] ?? 'User' }}">
                            @else
                                {{ $avatarLabel($sessionUser['display_name'] ?? null) }}
                            @endif
                        </div>
                        <div class="profile-meta">
                            <strong class="profile-name">{{ $sessionUser['display_name'] ?? 'Admin SIKAP' }}</strong>
                            <span class="profile-role">{{ $userRoleLabel }}</span>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true" style="stroke:currentColor;fill:none;"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="profile-dropdown" id="profileDropdown" role="menu" aria-label="Menu profil">
                        <a href="{{ route('profile.edit') }}" role="menuitem">Edit Profil</a>
                        @if($canManageAccounts)
                            <a href="{{ route('accounts.index') }}" role="menuitem">Manajemen Akun</a>
                            <a href="{{ route('element-preferences.index') }}" role="menuitem">Preferensi Element</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" id="idleLogoutForm" data-idle-timeout-ms="{{ $idleTimeoutMs }}">
                            @csrf
                            <button type="submit" role="menuitem">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-panel legacy-container">
            <div class="legacy-body">
                @yield('content')
            </div>
        </div>
    </main>

</div>
@stack('global-modals')
<script data-navigate-once>
    const initialToasts = @json($toastQueue);
    const root=document.documentElement;
    const SIDENAV_COLLAPSE_KEY='dashboard-sidenav-collapsed';
    const getAppRoot = () => document.querySelector('.app');
    const getSidenavToggle = () => document.getElementById('sidenavToggle');
    const getThemeToggle = () => document.getElementById('themeToggle');
    const updateSidenavToggleUi = (collapsed) => {
        const sidenavToggle = getSidenavToggle();
        sidenavToggle?.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
        sidenavToggle?.setAttribute('aria-label', collapsed ? 'Tampilkan menu samping' : 'Sembunyikan menu samping');
    };
    const setSidenavCollapsed = (collapsed, persist = true) => {
        const appRoot = getAppRoot();
        if(!appRoot) return;
        appRoot.classList.toggle('sidenav-collapsed', collapsed);
        updateSidenavToggleUi(collapsed);
        if(persist){
            try{
                localStorage.setItem(SIDENAV_COLLAPSE_KEY, collapsed ? '1' : '0');
            }catch(_error){
                // ignore storage failures
            }
        }
        if(!collapsed){
            keepActiveNavLinkVisibleAfterLayout();
        }
    };
    const applyStoredSidenavState = () => {
        let collapsed = false;
        try{
            collapsed = localStorage.getItem(SIDENAV_COLLAPSE_KEY) === '1';
        }catch(_error){
            collapsed = false;
        }
        setSidenavCollapsed(collapsed, false);
    };
    const toggleSidenav = () => {
        const appRoot = getAppRoot();
        if(!appRoot) return;
        const nextCollapsed = !appRoot.classList.contains('sidenav-collapsed');
        setSidenavCollapsed(nextCollapsed, true);
    };
    const applyStoredTheme = () => {
        const savedTheme = localStorage.getItem('dashboard-theme') || 'light';
        root.setAttribute('data-theme', savedTheme);
        const themeToggle = getThemeToggle();
        const themeToggleText = themeToggle?.querySelector('.text');
        themeToggle?.setAttribute('aria-pressed', savedTheme === 'dark' ? 'true' : 'false');
        if(themeToggleText){
            themeToggleText.textContent = savedTheme === 'dark' ? 'Terang' : 'Gelap';
        }
    };
    const toggleTheme = () => {
        const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('dashboard-theme', next);
        applyStoredTheme();
    };
    const keepActiveNavLinkVisible = () => {
        const appRoot = getAppRoot();
        if (appRoot?.classList.contains('sidenav-collapsed')) return;
        const sidenav = document.getElementById('sidenav');
        const activeLink = document.querySelector('#navMain a.active');
        if (!sidenav || !activeLink) return;

        const padding = 24;
        const sidenavRect = sidenav.getBoundingClientRect();
        const activeRect = activeLink.getBoundingClientRect();

        if (activeRect.top < sidenavRect.top + padding) {
            sidenav.scrollTop -= (sidenavRect.top + padding) - activeRect.top;
            return;
        }

        if (activeRect.bottom > sidenavRect.bottom - padding) {
            sidenav.scrollTop += activeRect.bottom - (sidenavRect.bottom - padding);
        }
    };
    const SUBMENU_OPEN_BUFFER = 24;
    const expandSubMenu = (submenu) => {
        if (!submenu) return;
        submenu.style.maxHeight = (submenu.scrollHeight + SUBMENU_OPEN_BUFFER) + 'px';
    };
    const keepActiveNavLinkVisibleAfterLayout = () => {
        requestAnimationFrame(keepActiveNavLinkVisible);
        window.setTimeout(keepActiveNavLinkVisible, 180);
    };
    const getHeadnav = () => document.querySelector('.headnav.topbar');
    const updateHeadnavScrollState = () => {
        const headnav = getHeadnav();
        if (!headnav) return;

        headnav.classList.toggle('is-scrolled', window.scrollY > 8);
    };
    const animateTopbarTitle = () => {
        const titleNode = document.getElementById('pageTitle');
        if (!titleNode) return;

        titleNode.classList.remove('topbar-title-anim');
        // Reflow for deterministic animation restart after wire:navigate.
        void titleNode.offsetWidth;
        titleNode.classList.add('topbar-title-anim');

        window.setTimeout(() => {
            titleNode.classList.remove('topbar-title-anim');
        }, 220);
    };
    applyStoredTheme();
    applyStoredSidenavState();
    updateHeadnavScrollState();
    animateTopbarTitle();
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#sidenavToggle');
        if(!btn) return;
        e.preventDefault();
        toggleSidenav();
    });
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#themeToggle');
        if(!btn) return;
        e.preventDefault();
        toggleTheme();
    });
    function renderClock(){
        const liveDateEl=document.getElementById('liveDateTime');
        if(!liveDateEl) return;
        const now=new Date();
        const formatter=new Intl.DateTimeFormat('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:false,timeZone:'Asia/Jakarta'});
        liveDateEl.textContent=formatter.format(now)+' WIB';
    }
    renderClock(); setInterval(renderClock,1000);
    document.addEventListener('livewire:navigated', () => {
        applyStoredTheme();
        applyStoredSidenavState();
        renderClock();
        updateHeadnavScrollState();
        animateTopbarTitle();
        syncActiveNavMenu();
    });
    const closeAllSubMenus = () => {
        document.querySelectorAll('.nav li.has-sub').forEach(li=>{
            li.classList.remove('open');
            const sub=li.querySelector('.nav-sub');
            if(sub){ sub.style.maxHeight='0px'; }
        });
    };
    const normalizePath = (value = '') => {
        const raw = (value || '').trim();
        if (!raw) return '/';
        const clean = raw.split('?')[0].split('#')[0];
        if (clean === '/') return '/';
        return clean.replace(/\/+$/, '') || '/';
    };
    const syncActiveNavMenu = () => {
        const navMain = document.getElementById('navMain');
        if (!navMain) return;

        const links = Array.from(navMain.querySelectorAll('a[href]'));
        links.forEach(link => link.classList.remove('active'));
        closeAllSubMenus();

        const currentPath = normalizePath(window.location.pathname);
        let matchedLink = null;

        for (const link of links) {
            const hrefPath = normalizePath(new URL(link.getAttribute('href'), window.location.origin).pathname);
            if (hrefPath === currentPath) {
                matchedLink = link;
                break;
            }
        }

        if (!matchedLink) {
            const matchedByPrefix = links
                .map(link => ({
                    link,
                    path: normalizePath(new URL(link.getAttribute('href'), window.location.origin).pathname),
                }))
                .filter(item => item.path !== '/' && currentPath.startsWith(`${item.path}/`))
                .sort((a, b) => b.path.length - a.path.length)[0];

            matchedLink = matchedByPrefix ? matchedByPrefix.link : null;
        }

        if (!matchedLink) return;
        matchedLink.classList.add('active');

        const parentWithSub = matchedLink.closest('li.has-sub');
        if (!parentWithSub) {
            keepActiveNavLinkVisibleAfterLayout();
            return;
        }

        parentWithSub.classList.add('open');
        const submenu = parentWithSub.querySelector('.nav-sub');
        expandSubMenu(submenu);
        keepActiveNavLinkVisibleAfterLayout();
    };
    syncActiveNavMenu();
    window.addEventListener('scroll', updateHeadnavScrollState, { passive: true });
    window.addEventListener('pageshow', updateHeadnavScrollState);
    window.addEventListener('pageshow', syncActiveNavMenu);
    const openSubMenu = (parent, sub) => {
        if(!parent || !sub) return;
        closeAllSubMenus();
        parent.classList.add('open');
        expandSubMenu(sub);
        keepActiveNavLinkVisibleAfterLayout();
    };
    // Delegated handler keeps submenu toggle working after wire:navigate swaps DOM.
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.nav-toggle[data-sub-toggle]');
        if(!btn) return;
        e.preventDefault();
        const targetId=btn.getAttribute('data-sub-toggle');
        const parent=btn.closest('li.has-sub');
        const sub=document.getElementById(`sub-${targetId}`);
        if(!parent||!sub) return;
        const appRoot = getAppRoot();
        if(appRoot?.classList.contains('sidenav-collapsed')){
            setSidenavCollapsed(false, true);
            // Tunggu layout sidebar melebar agar kalkulasi tinggi submenu stabil.
            window.setTimeout(() => {
                openSubMenu(parent, sub);
            }, 180);
            return;
        }
        const isOpen=parent.classList.contains('open');
        if(!isOpen){
            openSubMenu(parent, sub);
            return;
        }
        closeAllSubMenus();
    });

    const getNotifyButton = () => document.getElementById('notifyButton');
    const getNotifyCount = () => document.getElementById('notifyCount');
    const getNotifyDropdown = () => document.getElementById('notifyDropdown');
    const getNotifyList = () => document.getElementById('notifyList');
    const getNotifySummaryCount = () => document.querySelector('#notifyDropdown .notify-summary-count');
    const getProfileButton = () => document.getElementById('profileButton');
    const getProfileDropdown = () => document.getElementById('profileDropdown');
    const notifyRealtimeState = {
        pusher: null,
        channels: [],
        fallbackTimer: null,
        isFetching: false,
        isMarkingRead: false,
        initialized: false,
    };
    const escapeHtml = (value = '') => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    function setNotifyReadVisual(){
        const notifyButton=getNotifyButton();
        const notifyCount=getNotifyCount();
        if(!notifyButton || !notifyCount) return;
        const countValue=Math.max(0, Number(notifyButton.dataset.notifyCount || '0'));
        const unreadCount=Math.max(0, Number(notifyButton.dataset.notifyUnread || '0'));
        const showBadge=unreadCount > 0;
        const showReadMark=unreadCount <= 0 && countValue > 0;
        notifyButton.classList.toggle('has-alert', showBadge);
        notifyButton.classList.toggle('has-read-mark', showReadMark);
        notifyButton.classList.toggle('is-cleared', !showBadge);
        notifyCount.textContent=String(unreadCount);
        notifyCount.classList.toggle('is-hidden', !showBadge);
        notifyCount.setAttribute('aria-hidden', String(!showBadge));
    }
    function syncNotifyReadState(){
        setNotifyReadVisual();
    }
    async function markNotificationsRead(){
        const notifyButton=getNotifyButton();
        if(!notifyButton || notifyRealtimeState.isMarkingRead) return;
        const markReadUrlRaw=String(notifyButton.dataset.notifyMarkReadUrl || '').trim();
        if(markReadUrlRaw === ''){
            notifyButton.dataset.notifyUnread='0';
            setNotifyReadVisual();
            return;
        }
        const scopeSlug=String(notifyButton.dataset.notifyScope || '').trim();
        const csrfToken=String(notifyButton.dataset.notifyCsrfToken || '').trim();
        notifyRealtimeState.isMarkingRead=true;
        try{
            const response=await fetch(markReadUrlRaw, {
                method:'POST',
                credentials:'same-origin',
                headers:{
                    'Accept':'application/json',
                    'X-Requested-With':'XMLHttpRequest',
                    'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8',
                    ...(csrfToken !== '' ? {'X-CSRF-TOKEN': csrfToken} : {}),
                },
                body:new URLSearchParams({
                    scope:scopeSlug,
                }).toString(),
            });
            if(!response.ok){
                throw new Error(`HTTP ${response.status}`);
            }
            const payload=await response.json();
            applyNotificationPayload(payload);
        }catch(error){
            // keep silent to avoid noisy UX; fallback still refreshes by polling/realtime
        }finally{
            notifyRealtimeState.isMarkingRead=false;
        }
    }
    function renderNotificationItem(item, index){
        const actorName = escapeHtml(item?.actor_name || 'Pengguna');
        const actorRole = escapeHtml(item?.actor_role || 'Pengguna');
        const actorInitials = escapeHtml(item?.actor_initials || 'U');
        const actorPhotoUrl = String(item?.actor_photo_url || '').trim();
        const title = escapeHtml(item?.title || 'Notifikasi');
        const actionText = String(item?.action_text || '').trim();
        const detailText = String(item?.detail_text || '').trim();
        const actionClass = String(item?.action_class || 'is-save').trim();
        const actionClassSafe = /^is-(save|clear|verify|verify-reset)$/.test(actionClass) ? actionClass : 'is-save';
        const timeLabel = escapeHtml(item?.time_label || '-');
        const isRead = Boolean(item?.is_read);

        const avatarHtml = actorPhotoUrl !== ''
            ? `<img src="${escapeHtml(actorPhotoUrl)}" alt="Foto profil ${actorName}">`
            : actorInitials;
        const actionHtml = actionText !== ''
            ? `<span class="notify-action-badge ${actionClassSafe}">${escapeHtml(actionText)}</span>`
            : '';
        const detailHtml = detailText !== ''
            ? `<span class="notify-body">${escapeHtml(detailText)}</span>`
            : '';
        const actionRowHtml = (actionHtml !== '' || detailHtml !== '')
            ? `<div class="notify-action-row">${actionHtml}${detailHtml}</div>`
            : '';

        return `
            <div class="notify-item${isRead ? '' : ' is-unread'}" style="--notify-order:${Number(index || 0)};">
                <div class="notify-item-top">
                    <div class="notify-avatar" aria-hidden="true">${avatarHtml}</div>
                    <div class="notify-actor">
                        <div class="notify-actor-line">
                            <span class="notify-actor-name">${actorName}</span>
                        </div>
                        <div class="notify-actor-role">${actorRole}</div>
                    </div>
                </div>
                <div class="title">${title}</div>
                ${actionRowHtml}
                <div class="meta">${timeLabel}</div>
            </div>
        `;
    }
    function renderNotificationEmpty(){
        return '<div class="notify-item notify-item-empty">Belum ada notifikasi.</div>';
    }
    function markAllNotificationItemsReadDom(){
        const notifyList=getNotifyList();
        if(!notifyList) return;
        notifyList.querySelectorAll('.notify-item.is-unread').forEach((item)=>{
            item.classList.remove('is-unread');
        });
    }
    function applyNotificationPayload(payload, {forceListRender=false} = {}){
        const notifyButton=getNotifyButton();
        const notifyCount=getNotifyCount();
        const notifySummaryCount=getNotifySummaryCount();
        const notifyList=getNotifyList();
        if(!notifyButton || !notifyCount || !notifyList) return;

        const previousCount=Math.max(0, Number(notifyButton.dataset.notifyCount || '0'));
        const previousUnread=Math.max(0, Number(notifyButton.dataset.notifyUnread || '0'));
        const previousSignature=String(notifyButton.dataset.notifySignature || '').trim();
        const countValue=Math.max(0, Number(payload?.count || 0));
        const unreadCount=Math.max(0, Number(payload?.unread_count ?? countValue));
        const signature=String(payload?.signature || '').trim();
        const items=Array.isArray(payload?.items) ? payload.items : [];
        const shouldRenderList =
            forceListRender
            || signature !== previousSignature
            || countValue !== previousCount;

        notifyButton.dataset.notifyCount=String(countValue);
        notifyButton.dataset.notifyUnread=String(unreadCount);
        notifyButton.dataset.notifySignature=signature;
        notifyCount.textContent=String(unreadCount);
        if(notifySummaryCount){
            notifySummaryCount.textContent=String(countValue);
        }

        if(shouldRenderList){
            if(items.length === 0){
                notifyList.innerHTML=renderNotificationEmpty();
            }else{
                notifyList.innerHTML=items.map((item, index) => renderNotificationItem(item, index)).join('');
            }
        }else if(previousUnread > 0 && unreadCount === 0){
            // Keep DOM stable to prevent flicker; only clear unread marker state.
            markAllNotificationItemsReadDom();
        }

        syncNotifyReadState();
    }
    async function refreshNotifications({silent=true} = {}){
        const notifyButton=getNotifyButton();
        if(!notifyButton || notifyRealtimeState.isFetching || notifyRealtimeState.isMarkingRead){
            return;
        }
        const feedUrlRaw=String(notifyButton.dataset.notifyFeedUrl || '').trim();
        if(feedUrlRaw === ''){
            return;
        }
        notifyRealtimeState.isFetching=true;
        try{
            const feedUrl=new URL(feedUrlRaw, window.location.origin);
            const scopeSlug=String(notifyButton.dataset.notifyScope || '').trim();
            if(scopeSlug !== ''){
                feedUrl.searchParams.set('scope', scopeSlug);
            }
            feedUrl.searchParams.set('_ts', String(Date.now()));
            const response=await fetch(feedUrl.toString(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if(!response.ok){
                throw new Error(`HTTP ${response.status}`);
            }
            const payload=await response.json();
            applyNotificationPayload(payload);
        }catch(error){
            if(!silent){
                console.warn('Gagal memuat notifikasi realtime.', error);
            }
        }finally{
            notifyRealtimeState.isFetching=false;
        }
    }
    function isEventRelevantToScope(payload){
        const notifyButton=getNotifyButton();
        if(!notifyButton){
            return false;
        }
        const scopedElement=String(notifyButton.dataset.notifyScopeElement || '').trim();
        if(scopedElement === ''){
            return true;
        }
        const incomingElement=String(payload?.element_slug || '').trim();
        if(incomingElement === ''){
            return true;
        }
        return incomingElement === scopedElement;
    }
    function ensureNotificationFallbackPolling(){
        if(notifyRealtimeState.fallbackTimer){
            return;
        }
        notifyRealtimeState.fallbackTimer=window.setInterval(() => {
            refreshNotifications({silent:true});
        }, 30000);
    }
    function bootNotificationRealtime(){
        if(notifyRealtimeState.initialized){
            return;
        }
        notifyRealtimeState.initialized=true;
        ensureNotificationFallbackPolling();

        const notifyButton=getNotifyButton();
        if(!notifyButton){
            return;
        }
        const realtimeEnabled=String(notifyButton.dataset.notifyRealtimeEnabled || '0') === '1';
        const reverbKey=String(notifyButton.dataset.notifyRealtimeKey || '').trim();
        if(!realtimeEnabled || reverbKey === '' || typeof window.Pusher !== 'function'){
            return;
        }

        const reverbHost=String(notifyButton.dataset.notifyRealtimeHost || window.location.hostname).trim() || window.location.hostname;
        const reverbPort=Number(notifyButton.dataset.notifyRealtimePort || (window.location.protocol === 'https:' ? 443 : 8080));
        const reverbScheme=String(notifyButton.dataset.notifyRealtimeScheme || window.location.protocol.replace(':', '') || 'http').toLowerCase();
        const useTls=reverbScheme === 'https';
        const wsPort=Number.isFinite(reverbPort) && reverbPort > 0 ? reverbPort : (useTls ? 443 : 80);
        const authEndpoint=String(notifyButton.dataset.notifyAuthUrl || '').trim();
        const csrfToken=String(notifyButton.dataset.notifyCsrfToken || '').trim();
        const channelsRaw=String(notifyButton.dataset.notifyRealtimeChannels || '[]').trim();

        let channelNames=[];
        try{
            const parsedChannels=JSON.parse(channelsRaw);
            if(Array.isArray(parsedChannels)){
                channelNames=parsedChannels
                    .map((value)=>String(value || '').trim())
                    .filter((value)=>value !== '');
            }
        }catch(_error){
            channelNames=[];
        }
        if(channelNames.length === 0){
            const scopedElement=String(notifyButton.dataset.notifyScopeElement || '').trim();
            channelNames=scopedElement !== ''
                ? [`private-notifications.element.${scopedElement}`]
                : ['private-notifications.all'];
        }

        try{
            window.Pusher.logToConsole=false;
            notifyRealtimeState.pusher=new window.Pusher(reverbKey, {
                wsHost: reverbHost,
                wsPort,
                wssPort: wsPort,
                forceTLS: useTls,
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
                cluster: 'mt1',
                ...(authEndpoint !== '' ? {
                    authEndpoint,
                    auth: {
                        headers: {
                            ...(csrfToken !== '' ? {'X-CSRF-TOKEN': csrfToken} : {}),
                        },
                    },
                } : {}),
            });

            notifyRealtimeState.channels = channelNames.map((channelName) => {
                const channel=notifyRealtimeState.pusher.subscribe(channelName);
                channel.bind('notification.feed.updated', (payload) => {
                    if(!isEventRelevantToScope(payload)){
                        return;
                    }
                    refreshNotifications({silent:true});
                });

                return channel;
            });

            notifyRealtimeState.pusher.connection.bind('connected', () => {
                refreshNotifications({silent:true});
            });
        }catch(error){
            console.warn('Koneksi realtime notifikasi gagal diinisialisasi.', error);
        }
    }
    function closeNotify(){
        const notifyDropdown=getNotifyDropdown();
        const notifyButton=getNotifyButton();
        notifyDropdown?.classList.remove('show','open');
        notifyButton?.setAttribute('aria-expanded','false');
    }
    function closeProfile(){
        const profileDropdown=getProfileDropdown();
        const profileButton=getProfileButton();
        profileDropdown?.classList.remove('show','open');
        profileButton?.setAttribute('aria-expanded','false');
    }
    function toggleNotify(){
        const notifyDropdown=getNotifyDropdown();
        const notifyButton=getNotifyButton();
        const open=notifyDropdown?.classList.contains('open');
        closeProfile();
        notifyDropdown?.classList.toggle('open',!open);
        notifyDropdown?.classList.toggle('show',!open);
        notifyButton?.setAttribute('aria-expanded',String(!open));
        if(!open){
            markNotificationsRead();
        }
    }
    function toggleProfile(){
        const profileDropdown=getProfileDropdown();
        const profileButton=getProfileButton();
        const open=profileDropdown?.classList.contains('open');
        closeNotify();
        profileDropdown?.classList.toggle('open',!open);
        profileDropdown?.classList.toggle('show',!open);
        profileButton?.setAttribute('aria-expanded',String(!open));
    }
    const getIdleLogoutForm = () => document.getElementById('idleLogoutForm');
    let idleLogoutTimer = null;
    let idleLogoutLocked = false;
    let lastIdleActivityAt = 0;
    function getIdleTimeoutMs(){
        const idleLogoutForm=getIdleLogoutForm();
        if(!idleLogoutForm || idleLogoutLocked){
            return 0;
        }
        const timeoutMs=Number(idleLogoutForm.dataset.idleTimeoutMs || '0');
        if(!Number.isFinite(timeoutMs) || timeoutMs <= 0){
            return 0;
        }
        return timeoutMs;
    }
    function clearIdleLogoutTimer(){
        if(!idleLogoutTimer){
            return;
        }
        window.clearTimeout(idleLogoutTimer);
        idleLogoutTimer=null;
    }
    function scheduleIdleLogout(delayMs){
        if(idleLogoutLocked){
            return;
        }
        clearIdleLogoutTimer();
        const safeDelay=Math.max(0, Math.round(delayMs));
        idleLogoutTimer=window.setTimeout(runIdleLogout, safeDelay);
    }
    function runIdleLogout(){
        const idleLogoutForm=getIdleLogoutForm();
        if(!idleLogoutForm || idleLogoutLocked){
            return;
        }
        idleLogoutLocked=true;
        clearIdleLogoutTimer();
        closeNotify();
        closeProfile();
        if(typeof idleLogoutForm.requestSubmit === 'function'){
            idleLogoutForm.requestSubmit();
            return;
        }
        idleLogoutForm.submit();
    }
    function syncIdleLogoutDeadline(){
        const timeoutMs=getIdleTimeoutMs();
        if(timeoutMs <= 0){
            return;
        }
        const now=Date.now();
        if(lastIdleActivityAt <= 0){
            lastIdleActivityAt=now;
        }
        const expiresAt=lastIdleActivityAt + timeoutMs;
        if(now >= expiresAt){
            runIdleLogout();
            return;
        }
        scheduleIdleLogout(expiresAt - now);
    }
    function resetIdleLogout(force=false){
        const timeoutMs=getIdleTimeoutMs();
        if(timeoutMs <= 0){
            return;
        }
        const now=Date.now();
        if(!force && (now - lastIdleActivityAt) < 1000){
            return;
        }
        lastIdleActivityAt=now;
        scheduleIdleLogout(timeoutMs);
    }
    ['pointerdown','pointermove','keydown','scroll','touchstart'].forEach((eventName)=>{
        document.addEventListener(eventName, () => resetIdleLogout(), { passive: true });
    });
    document.addEventListener('visibilitychange', () => {
        if(document.visibilityState === 'visible'){
            syncIdleLogoutDeadline();
        }
    });
    window.addEventListener('focus', syncIdleLogoutDeadline);
    window.addEventListener('pageshow', syncIdleLogoutDeadline);
    window.addEventListener('pageshow', () => refreshNotifications({silent:true}));
    document.addEventListener('livewire:navigated', () => {
        resetIdleLogout(true);
        syncNotifyReadState();
        refreshNotifications({silent:true});
    });
    resetIdleLogout(true);
    syncNotifyReadState();
    bootNotificationRealtime();
    refreshNotifications({silent:true});
    // Delegated handlers keep profile/notification dropdown functional after wire:navigate.
    document.addEventListener('click',(e)=>{
        const notifyButton=e.target.closest('#notifyButton');
        if(notifyButton){
            e.preventDefault();
            e.stopPropagation();
            toggleNotify();
            return;
        }
        const profileButton=e.target.closest('#profileButton');
        if(profileButton){
            e.preventDefault();
            e.stopPropagation();
            toggleProfile();
            return;
        }
        if(e.target.closest('#notifyDropdown') || e.target.closest('#profileDropdown')){
            return;
        }
        closeNotify();
        closeProfile();
    });

    // Global toast helper
    const toastStack=document.getElementById('toastStack');
    const getViewportUiScale = () => {
        const zoomRaw = getComputedStyle(document.body).zoom;
        const zoom = parseFloat(zoomRaw || '1');
        return Number.isFinite(zoom) && zoom > 0 ? zoom : 1;
    };
    const syncToastStackToViewport = () => {
        if(!toastStack) return;

        const scale = getViewportUiScale();
        const isCompactViewport = window.innerWidth <= 640;
        const topOffset = isCompactViewport ? 78 : 92;
        const top = (window.scrollY + topOffset) / scale;

        toastStack.style.position = 'absolute';
        toastStack.style.top = `${Math.round(top)}px`;
        toastStack.style.right = 'auto';
        toastStack.style.bottom = 'auto';

        if (isCompactViewport) {
            const left = (window.scrollX + 10) / scale;
            toastStack.style.left = `${Math.round(left)}px`;
            toastStack.style.transform = 'none';
            toastStack.style.width = `${Math.round((window.innerWidth - 20) / scale)}px`;
            return;
        }

        const left = (window.scrollX + (window.innerWidth / 2)) / scale;
        toastStack.style.left = `${Math.round(left)}px`;
        toastStack.style.transform = 'translateX(-50%)';
        toastStack.style.width = '';
    };
    function pushToast({title='Info',message='',type='info',duration=4800}={}){
        if(!toastStack||!message)return;
        syncToastStackToViewport();
        const toast=document.createElement('div');
        toast.className=`toast-card toast-${type}`;

        const tone=document.createElement('div');
        tone.className='toast-tone';
        tone.setAttribute('aria-hidden','true');

        const body=document.createElement('div');
        body.className='toast-body';
        const titleEl=document.createElement('div');
        titleEl.className='toast-title';
        titleEl.textContent=title;
        const messageEl=document.createElement('div');
        messageEl.className='toast-message';
        messageEl.textContent=message;
        body.appendChild(titleEl);
        body.appendChild(messageEl);

        toast.appendChild(tone);
        toast.appendChild(body);

        const close=()=>{toast.classList.remove('show');setTimeout(()=>toast.remove(),260);};
        toastStack.appendChild(toast);
        requestAnimationFrame(()=>toast.classList.add('show'));
        setTimeout(close,duration);
    }
    window.pushToast = pushToast;
    syncToastStackToViewport();
    window.addEventListener('scroll', syncToastStackToViewport, { passive:true });
    window.addEventListener('resize', syncToastStackToViewport);
    window.addEventListener('pageshow', syncToastStackToViewport);
    document.addEventListener('livewire:navigated', syncToastStackToViewport);
    (initialToasts||[]).forEach(t=>pushToast(t));
    document.addEventListener('toast', (e)=>{ pushToast(e.detail||{}); });

    // Global hint bubble portal (prevents clipping by parent overflow)
    (function(){
        const body=document.body;
        if(!body) return;

        const pop=document.createElement('div');
        pop.className='hint-bubble-pop';
        pop.hidden=true;
        body.appendChild(pop);
        body.classList.add('hint-bubble-ready');

        let activeTrigger=null;
        function clamp(value,min,max){
            return Math.min(Math.max(value,min),max);
        }

        function getUiScale(){
            const zoomRaw=getComputedStyle(body).zoom;
            const zoom=parseFloat(zoomRaw || '1');
            return Number.isFinite(zoom) && zoom > 0 ? zoom : 1;
        }

        function setPopContent(trigger){
            const text=(trigger?.getAttribute('data-hint')||'').trim();
            if(!text) return false;
            pop.textContent=text;
            return true;
        }

        function placePopByTrigger(trigger){
            if(!trigger) return;
            const scale=getUiScale();
            const triggerRect=trigger.getBoundingClientRect();
            const triggerLeft=(triggerRect.left + window.scrollX) / scale;
            const triggerTop=(triggerRect.top + window.scrollY) / scale;
            const triggerWidth=triggerRect.width / scale;
            const triggerBottom=(triggerRect.bottom + window.scrollY) / scale;
            const viewportPadding=8;
            const gap=4;
            const viewportLeft=window.scrollX / scale;
            const viewportTop=window.scrollY / scale;
            const viewportRight=viewportLeft + (window.innerWidth / scale);
            const viewportBottom=viewportTop + (window.innerHeight / scale);

            pop.hidden=false;
            pop.classList.add('show');
            pop.classList.remove('is-below');

            const popRect=pop.getBoundingClientRect();
            const popWidth=Math.max(1, popRect.width / scale);
            const popHeight=Math.max(1, popRect.height / scale);
            const preferredLeft=triggerLeft + (triggerWidth/2) - (popWidth/2);
            const left=clamp(preferredLeft,viewportLeft + viewportPadding,viewportRight-popWidth-viewportPadding);

            let top=triggerTop - popHeight - gap;
            let placeBelow=false;
            if(top < viewportTop + viewportPadding){
                placeBelow=true;
                top=triggerBottom + gap;
            }
            if(top + popHeight > viewportBottom - viewportPadding){
                top=viewportBottom - popHeight - viewportPadding;
            }
            top=Math.max(viewportTop + viewportPadding,top);

            const arrowX=clamp((triggerLeft + (triggerWidth/2)) - left,16,popWidth-16);
            pop.style.left=`${Math.round(left)}px`;
            pop.style.top=`${Math.round(top)}px`;
            pop.style.setProperty('--hint-arrow-x', `${Math.round(arrowX)}px`);
            pop.classList.toggle('is-below', placeBelow);
        }

        function placeActivePop(){
            if(!activeTrigger) return;
            placePopByTrigger(activeTrigger);
        }

        function showPop(trigger){
            if(!trigger) return;
            if(!setPopContent(trigger)){
                hidePop();
                return;
            }
            activeTrigger=trigger;
            placeActivePop();
        }

        function hidePop(){
            pop.classList.remove('show','is-below');
            pop.hidden=true;
            activeTrigger=null;
        }

        document.addEventListener('mouseover', (e)=>{
            const trigger=e.target.closest('.hint-bubble-trigger');
            if(!trigger){
                return;
            }
            if(trigger===activeTrigger){
                return;
            }
            showPop(trigger);
        });

        document.addEventListener('mouseout', (e)=>{
            const trigger=e.target.closest('.hint-bubble-trigger');
            if(!trigger || activeTrigger!==trigger){
                return;
            }
            const related=e.relatedTarget;
            if(related && trigger.contains(related)){
                return;
            }
            hidePop();
        });

        document.addEventListener('focusin', (e)=>{
            const trigger=e.target.closest('.hint-bubble-trigger');
            if(!trigger){
                hidePop();
                return;
            }
            showPop(trigger);
        });

        document.addEventListener('keydown', (e)=>{
            if(e.key==='Escape'){
                hidePop();
            }
        });

        window.addEventListener('resize', ()=>{
            placeActivePop();
        });

        window.addEventListener('scroll', ()=>{
            placeActivePop();
        }, true);
    })();

    // Global validation style for all forms/fields (match login page)
    (function(){
        const body=document.body;
        if(!body) return;
        if(body.hasAttribute('data-disable-field-pop') || document.querySelector('[data-disable-field-pop="1"]')) return;

        const fieldErrorMap=new WeakMap();
        const getTargetId=(target)=>{
            const id=(target.getAttribute('id')||'').trim();
            if(id!=='') return id;
            const name=(target.getAttribute('name')||'').trim();
            if(name==='') return '';
            return name.replace(/[^a-zA-Z0-9_-]/g,'_');
        };
        const ensureFieldErrorNode=(target)=>{
            let node=fieldErrorMap.get(target);
            if(node && node.isConnected){
                return node;
            }

            const baseId=getTargetId(target);
            if(baseId!==''){
                const candidate=document.getElementById(`${baseId}Error`);
                if(candidate && candidate.classList.contains('field-inline-error')){
                    node=candidate;
                }
            }

            if(!node){
                node=document.createElement('div');
                node.className='field-inline-error';
                node.setAttribute('aria-live','polite');
                if(baseId!==''){
                    node.id=`${baseId}Error`;
                }
                const wrapper=target.closest('.password-wrapper, .input-group, .form-floating');
                if(wrapper && wrapper.contains(target)){
                    wrapper.insertAdjacentElement('afterend',node);
                }else{
                    target.insertAdjacentElement('afterend',node);
                }
            }

            const describedByRaw=(target.getAttribute('aria-describedby')||'').trim();
            const describedBy=describedByRaw==='' ? [] : describedByRaw.split(/\s+/);
            if(node.id && !describedBy.includes(node.id)){
                describedBy.push(node.id);
                target.setAttribute('aria-describedby',describedBy.join(' '));
            }

            fieldErrorMap.set(target,node);
            return node;
        };
        const showFieldError=(target,message)=>{
            if(!(target instanceof HTMLElement)) return;
            const node=ensureFieldErrorNode(target);
            const text=(message||target.validationMessage||'Harap isi bidang ini.').trim();
            node.textContent=text;
            node.classList.add('show');
            target.classList.add('is-invalid');
            target.setAttribute('aria-invalid','true');
        };
        const clearFieldError=(target)=>{
            if(!(target instanceof HTMLElement)) return;
            target.classList.remove('is-invalid');
            target.removeAttribute('aria-invalid');
            const node=fieldErrorMap.get(target);
            if(node){
                node.textContent='';
                node.classList.remove('show');
            }
        };

        document.addEventListener('invalid',function(e){
            e.preventDefault();
            const target=e.target;
            if(!(target instanceof HTMLElement)) return;
            showFieldError(target,target.validationMessage);
            target.focus({preventScroll:false});
        },true);

        document.addEventListener('input',function(e){
            const target=e.target;
            if(!(target instanceof HTMLElement)) return;
            if(target.checkValidity()){
                clearFieldError(target);
                return;
            }
            if(target.classList.contains('is-invalid')){
                showFieldError(target,target.validationMessage);
            }
        },true);

        document.addEventListener('change',function(e){
            const target=e.target;
            if(!(target instanceof HTMLElement)) return;
            if(target.checkValidity()){
                clearFieldError(target);
                return;
            }
            if(target.classList.contains('is-invalid')){
                showFieldError(target,target.validationMessage);
            }
        },true);

        document.addEventListener('submit',function(e){
            const form=e.target;
            if(!(form instanceof HTMLFormElement)) return;
            const invalidTargets=Array.from(form.querySelectorAll(':invalid'));
            invalidTargets.forEach((target)=>{
                if(target instanceof HTMLElement){
                    showFieldError(target,target.validationMessage);
                }
            });
        },true);
    })();
</script>
@livewireScripts
@stack('scripts')
</body>
</html>

