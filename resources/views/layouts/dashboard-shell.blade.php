<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'LATSAR' }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ \App\Support\VersionedAsset::url('static/logo-sikap-dark.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ \App\Support\VersionedAsset::url('static/logo-sikap-light.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="shortcut icon" href="{{ \App\Support\VersionedAsset::url('static/logo-sikap-dark.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/sidenav-theme.css') }}">
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        // Kunci tema ke light agar konsisten di semua halaman.
        (function(){
            try{
                localStorage.setItem('dashboard-theme', 'light');
            }catch(e){
                // ignore storage failures
            }
            document.documentElement.setAttribute('data-theme', 'light');
        })();
    </script>
    @livewireStyles
    @stack('head')
</head>
@php
    use Illuminate\Support\Str;

    $rawNavElements = $navElements ?? [];
    $navElementsShapeReady = \App\Support\DashboardNavNormalizer::hasRenderable($rawNavElements);
    $headnavCrumbsShapeReady = \App\Support\DashboardShellPayloadNormalizer::hasRenderableHeadnavCrumbs($headnavCrumbs ?? []);

    $layoutDataReady = isset(
        $navElements,
        $notificationItems,
        $notificationCount,
        $notificationUnreadCount,
        $headnavCrumbs,
        $toastQueue,
        $idleTimeoutMs,
        $notificationRealtimeChannels
    ) && $navElementsShapeReady && $headnavCrumbsShapeReady;

    if (!$layoutDataReady) {
        $fallbackLayoutData = app(\App\Services\DashboardShellDataBuilder::class)->build(get_defined_vars());
        foreach ($fallbackLayoutData as $layoutKey => $layoutValue) {
            if (!isset($$layoutKey)) {
                $$layoutKey = $layoutValue;
            }
        }
    }

    $sessionUser = is_array($sessionUser ?? null) ? $sessionUser : (is_array($user ?? null) ? $user : []);
    $notificationItems = \App\Support\DashboardShellPayloadNormalizer::sanitizeNotificationItems($notificationItems ?? []);
    $notificationCount = (int) ($notificationCount ?? $notificationItems->count());
    $notificationCount = max(0, $notificationCount);
    $notificationUnreadCount = (int) ($notificationUnreadCount ?? $notificationCount);
    $notificationUnreadCount = max(0, min($notificationCount, $notificationUnreadCount));
    $notificationRealtimeChannels = \App\Support\DashboardShellPayloadNormalizer::sanitizeNotificationRealtimeChannels($notificationRealtimeChannels ?? []);
    $navElements = \App\Support\DashboardNavNormalizer::sanitize($navElements ?? []);
    $headnavCrumbs = \App\Support\DashboardShellPayloadNormalizer::sanitizeHeadnavCrumbs($headnavCrumbs ?? []);
    $toastQueue = \App\Support\DashboardShellPayloadNormalizer::sanitizeToastQueue($toastQueue ?? []);
    $idleTimeoutMs = max(60_000, (int) ($idleTimeoutMs ?? (int) config('session.idle_timeout', 60) * 60 * 1000));
    $unreadNavBadge = $notificationUnreadCount > 99 ? '99+' : (string) $notificationUnreadCount;
    $appVersionTag = trim((string) config('app.asset_version', ''));
    if ($appVersionTag === '') {
        $appVersionTag = 'local';
    }
    $qaFeatureEnabled = (bool) config('app.features.qa_enabled', false);
@endphp
<body class="legacy-page{{ $qaFeatureEnabled ? '' : ' qa-feature-disabled' }}">
<div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>
<div class="app">
    <aside class="sidenav" id="sidenav">
        <div class="brand">
            <img class="logo-light" src="{{ \App\Support\VersionedAsset::url('static/logo-sikap-light.png') }}" alt="Logo SIKAP">
            <img class="logo-dark" src="{{ \App\Support\VersionedAsset::url('static/logo-sikap-dark.png') }}" alt="Logo SIKAP">
        </div>
        <div class="user-card">
            <a href="{{ route('profile.edit') }}" class="user-card-quick-action" aria-label="Edit profil" title="Edit profil">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25Z"/>
                    <path d="M14.06 4.94 17.81 8.7"/>
                </svg>
            </a>
            <div class="avatar">
                @if(!empty($photoUrl))
                    <img src="{{ $photoUrl }}" alt="Foto profil {{ $user['display_name'] ?? 'User' }}">
                @else
                    {{ $avatarLabel($user['display_name'] ?? null) }}
                @endif
            </div>
            <div class="user-card-head">
                <strong class="user-card-name">{{ $user['display_name'] ?? 'Admin' }}</strong>
            </div>
            <div class="user-card-role">{{ $userRoleLabel }}</div>
        </div>
        <ul class="nav" id="navMain">
            <li class="nav-section-label" aria-hidden="true"><span>Utama</span></li>
            <li>
                <a href="{{ route('dashboard') }}" data-nav-tooltip="Dashboard" title="Dashboard">
                    <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 14a8 8 0 1 1 16 0"/><path d="M12 14l4-4"/><circle cx="12" cy="14" r="1.2"/></svg></span>
                    <span class="nav-text">Dashboard</span>
                    @if($notificationUnreadCount > 0)
                        <span class="nav-badge" aria-label="{{ $notificationUnreadCount }} notifikasi belum dibaca">{{ $unreadNavBadge }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-section-label" aria-hidden="true"><span>Penilaian Element</span></li>
            @foreach($navElements as $elementNav)
            @continue(empty($elementNav['slug']))
            @php
                $elementNavTitle = (string) ($elementNav['nav_title'] ?? ($elementNav['title'] ?? 'Element'));
            @endphp
            <li class="has-sub">
                <a class="nav-toggle" data-sub-toggle="{{ $elementNav['slug'] }}" data-nav-tooltip="{{ $elementNavTitle }}" title="{{ $elementNavTitle }}">
                    <span class="nav-icon">{{ $elementNav['icon_label'] ?? 'E' }}</span>
                    <span class="nav-text">{{ $elementNavTitle }}</span>
                    <span class="chevron">&rsaquo;</span>
                </a>
                <ul class="nav-sub" id="sub-{{ $elementNav['slug'] }}">
                    <li class="nav-sub-parent">
                        <a href="{{ route('elements.show', $elementNav['slug']) }}">
                            <span class="sub-icon">&bull;</span>
                            <span>Rekapitulasi Element</span>
                        </a>
                    </li>
                    @foreach(($elementNav['subtopics'] ?? []) as $subtopicNav)
                        @continue(empty($subtopicNav['slug']))
                        <li class="nav-sub-child">
                            <a href="{{ route('elements.show', $subtopicNav['slug']) }}">
                                <span class="sub-icon">&bull;</span>
                                <span>{{ $subtopicNav['title'] ?? Str::headline(str_replace('_', ' ', (string) $subtopicNav['slug'])) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            @endforeach
            <li class="nav-section-label" aria-hidden="true"><span>Lainnya</span></li>
            <li><a href="{{ route('aoi.index') }}" data-nav-tooltip="Area Of Improvement (AoI)" title="Area Of Improvement (AoI)"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="14" height="14" rx="2"/><path d="M11 4v14M4 11h14"/><path d="M19 16v6M16 19h6"/></svg></span><span class="nav-text">Area Of Improvement (AoI)</span></a></li>
            <li><a href="{{ route('guides.index') }}" data-nav-tooltip="Panduan" title="Panduan"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 4h10a4 4 0 0 1 4 4v12H9a4 4 0 0 0-4 4V4Z"/><path d="M9 4h10v16"/><path d="M9 9h6"/><path d="M9 13h5"/></svg></span><span class="nav-text">Panduan</span></a></li>
            @if($userRoleKey !== 'qa')
                <li><a href="{{ route('dms.index') }}" data-nav-tooltip="Data Management System" title="Data Management System"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 8a3 3 0 0 1 3-3h4l2 2h6a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3Z"/><path d="M3 9h18"/></svg></span><span class="nav-text">Data Management System</span></a></li>
                <li><a href="{{ route('informasi-umum.index') }}" data-nav-tooltip="Informasi Umum" title="Informasi Umum"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 10.5v5"/><path d="M12 7.5h.01"/></svg></span><span class="nav-text">Informasi Umum</span></a></li>
            @endif
        </ul>
        <form action="{{ route('logout') }}" method="GET" class="sidenav-logout-form">
            <button type="submit" class="sidenav-logout-btn" data-nav-tooltip="Logout" title="Logout">
                <span class="logout-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4"/>
                        <path d="M14 16l4-4-4-4"/>
                        <path d="M18 12H9"/>
                    </svg>
                </span>
                <span class="logout-text">Logout</span>
            </button>
        </form>
        <div class="sidenav-footer-meta">&copy; Inspektorat Provinsi DKI Jakarta 2025</div>
    </aside>
    <button type="button" class="sidenav-backdrop" id="sidenavBackdrop" aria-label="Tutup menu samping"></button>

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
                                    $notifyActorName = trim((string) (data_get($notif, 'coordinator_name') ?: data_get($notif, 'coordinatorAccount.display_name') ?: data_get($notif, 'coordinator_username') ?: 'Pengguna'));
                                    $notifyActorRole = trim((string) (data_get($notif, 'coordinator_role_label') ?? 'Pengguna'));
                                    $notifyActorPhotoUrl = $resolvePhotoUrl((string) data_get($notif, 'coordinatorAccount.profile_photo', ''));
                                    $notifyActorInitials = $avatarLabel($notifyActorName, 'U');
                                    $notifyTitle = trim((string) data_get($notif, 'subtopic_title', 'Notifikasi'));
                                    $notifyTitle = preg_replace('/^\s*element\s*\d+\s*[-:]?\s*/i', '', $notifyTitle);
                                    $notifyTitle = is_string($notifyTitle) ? trim($notifyTitle) : 'Notifikasi';
                                    $notifyTitle = preg_replace('/^\s*sub\s*topik\s*\d+\s*[-:]?\s*/i', '', $notifyTitle);
                                    $notifyTitle = is_string($notifyTitle) ? trim($notifyTitle) : 'Notifikasi';
                                    if ($notifyTitle === '') {
                                        $notifyTitle = 'Notifikasi';
                                    }

                                    $notifyStatement = trim((string) data_get($notif, 'statement', ''));
                                    if ($notifyStatement !== '' && !Str::contains($notifyStatement, '|')) {
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
                                            'isi data' => 'Isi Data',
                                            'hapus isian' => 'Hapus Isian',
                                            'bersihkan' => 'Bersihkan',
                                        ];

                                        foreach ($legacyActionMap as $legacyAction => $compactAction) {
                                            if (Str::startsWith(Str::lower($normalized), $legacyAction)) {
                                                $rest = trim((string) Str::substr($normalized, Str::length($legacyAction)));
                                                $normalized = $compactAction.' | '.($rest !== '' ? $rest : '');
                                                break;
                                            }
                                        }

                                        $notifyStatement = $normalized;
                                    }
                                    $notifyStatement = Str::limit($notifyStatement, 64, '...');

                                    $notifyActionText = '';
                                    $notifyDetailText = $notifyStatement;
                                    if (Str::contains($notifyStatement, '|')) {
                                        [$notifyActionText, $notifyDetailText] = array_pad(explode('|', $notifyStatement, 2), 2, '');
                                        $notifyActionText = trim((string) $notifyActionText);
                                        $notifyDetailText = trim((string) $notifyDetailText);
                                    }

                                    $notifyCreatedAtRaw = data_get($notif, 'created_at');
                                    $notifyCreatedAt = null;
                                    if ($notifyCreatedAtRaw instanceof \Illuminate\Support\Carbon) {
                                        $notifyCreatedAt = $notifyCreatedAtRaw;
                                    } elseif ($notifyCreatedAtRaw instanceof \DateTimeInterface) {
                                        $notifyCreatedAt = \Illuminate\Support\Carbon::instance($notifyCreatedAtRaw);
                                    } elseif (is_string($notifyCreatedAtRaw) && trim($notifyCreatedAtRaw) !== '') {
                                        try {
                                            $notifyCreatedAt = \Illuminate\Support\Carbon::parse($notifyCreatedAtRaw);
                                        } catch (\Throwable $e) {
                                            $notifyCreatedAt = null;
                                        }
                                    }

                                    $notifyActionClass = match (Str::lower($notifyActionText)) {
                                        'isi data', 'isi/ubah data' => 'is-fill',
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
                                    <div class="meta">{{ $notifyCreatedAt?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }}</div>
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
                        <form action="{{ route('logout') }}" method="GET" id="idleLogoutForm" data-idle-timeout-ms="{{ $idleTimeoutMs }}">
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
    const SIDENAV_MOBILE_BREAKPOINT=960;
    const getAppRoot = () => document.querySelector('.app');
    const getSidenavToggle = () => document.getElementById('sidenavToggle');
    const isMobileViewport = () => window.innerWidth <= SIDENAV_MOBILE_BREAKPOINT;
    const updateSidenavToggleUi = (collapsed) => {
        const sidenavToggle = getSidenavToggle();
        const appRoot = getAppRoot();
        if(!sidenavToggle || !appRoot){
            return;
        }

        if(isMobileViewport()){
            const mobileOpen = appRoot.classList.contains('mobile-sidenav-open');
            sidenavToggle.setAttribute('aria-pressed', mobileOpen ? 'true' : 'false');
            sidenavToggle.setAttribute('aria-expanded', mobileOpen ? 'true' : 'false');
            sidenavToggle.setAttribute('aria-label', mobileOpen ? 'Tutup menu samping' : 'Buka menu samping');
            return;
        }

        sidenavToggle.removeAttribute('aria-expanded');
        sidenavToggle.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
        sidenavToggle.setAttribute('aria-label', collapsed ? 'Tampilkan menu samping' : 'Sembunyikan menu samping');
    };
    const setMobileSidenavOpen = (open) => {
        const appRoot = getAppRoot();
        if(!appRoot) return;

        const isOpen = Boolean(open);
        appRoot.classList.toggle('mobile-sidenav-open', isOpen);
        document.body.classList.toggle('sidenav-open-lock', isOpen);
        updateSidenavToggleUi(appRoot.classList.contains('sidenav-collapsed'));
    };
    const syncSidenavViewportMode = () => {
        const appRoot = getAppRoot();
        if(!appRoot) return;

        if(isMobileViewport()){
            if(!appRoot.hasAttribute('data-desktop-collapsed')){
                appRoot.setAttribute(
                    'data-desktop-collapsed',
                    appRoot.classList.contains('sidenav-collapsed') ? '1' : '0'
                );
            }
            appRoot.classList.remove('sidenav-collapsed');
            updateSidenavToggleUi(false);
            return;
        }

        const previousDesktopState = appRoot.getAttribute('data-desktop-collapsed');
        if(previousDesktopState !== null){
            appRoot.classList.toggle('sidenav-collapsed', previousDesktopState === '1');
            appRoot.removeAttribute('data-desktop-collapsed');
        }
        setMobileSidenavOpen(false);
        updateSidenavToggleUi(appRoot.classList.contains('sidenav-collapsed'));
    };
    const setSidenavCollapsed = (collapsed, persist = true) => {
        const appRoot = getAppRoot();
        if(!appRoot) return;

        if(isMobileViewport()){
            if(persist){
                try{
                    localStorage.setItem(SIDENAV_COLLAPSE_KEY, collapsed ? '1' : '0');
                }catch(_error){
                    // ignore storage failures
                }
            }
            updateSidenavToggleUi(false);
            return;
        }

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

        if(isMobileViewport()){
            setMobileSidenavOpen(!appRoot.classList.contains('mobile-sidenav-open'));
            return;
        }

        const nextCollapsed = !appRoot.classList.contains('sidenav-collapsed');
        setSidenavCollapsed(nextCollapsed, true);
    };
    const applyStoredTheme = () => {
        root.setAttribute('data-theme', 'light');
        try{
            localStorage.setItem('dashboard-theme', 'light');
        }catch(_error){
            // ignore storage failures
        }
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
    syncSidenavViewportMode();
    updateHeadnavScrollState();
    animateTopbarTitle();
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#sidenavToggle');
        if(!btn) return;
        e.preventDefault();
        toggleSidenav();
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
        syncSidenavViewportMode();
        setMobileSidenavOpen(false);
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
        const backdrop = e.target.closest('#sidenavBackdrop');
        if(backdrop){
            e.preventDefault();
            setMobileSidenavOpen(false);
            return;
        }

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
    document.addEventListener('click', (e) => {
        if(!isMobileViewport()) return;
        const navLink = e.target.closest('#sidenav a[href]');
        if(!navLink || navLink.classList.contains('nav-toggle')) return;
        setMobileSidenavOpen(false);
    });
    document.addEventListener('keydown', (e) => {
        if(e.key !== 'Escape') return;
        const appRoot = getAppRoot();
        if(!isMobileViewport() || !appRoot?.classList.contains('mobile-sidenav-open')) return;
        setMobileSidenavOpen(false);
    });
    window.addEventListener('resize', syncSidenavViewportMode);

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
        channelNames: [],
        fallbackTimer: null,
        refreshTimer: null,
        queuedRefresh: false,
        queuedRefreshSilent: true,
        lastRefreshAt: 0,
        isFetching: false,
        isMarkingRead: false,
        initialized: false,
    };
    const NOTIFY_REFRESH_DEBOUNCE_MS = 180;
    const NOTIFY_REFRESH_MIN_INTERVAL_MS = 450;
    const normalizeChannelNames = (channelNames = []) => Array.from(new Set(
        (Array.isArray(channelNames) ? channelNames : [])
            .map((value) => String(value || '').trim())
            .filter((value) => value !== '')
    ));
    const resolveRealtimeChannelNames = (notifyButton) => {
        if(!notifyButton){
            return [];
        }

        const channelsRaw=String(notifyButton.dataset.notifyRealtimeChannels || '[]').trim();
        let channelNames=[];
        try{
            const parsedChannels=JSON.parse(channelsRaw);
            if(Array.isArray(parsedChannels)){
                channelNames=parsedChannels;
            }
        }catch(_error){
            channelNames=[];
        }

        channelNames = normalizeChannelNames(channelNames);
        if(channelNames.length > 0){
            return channelNames;
        }

        const scopedElement=String(notifyButton.dataset.notifyScopeElement || '').trim();
        return scopedElement !== ''
            ? [`private-notifications.element.${scopedElement}`]
            : ['private-notifications.all'];
    };
    const scheduleNotificationRefresh = ({silent = true, delayMs = NOTIFY_REFRESH_DEBOUNCE_MS} = {}) => {
        notifyRealtimeState.queuedRefresh = true;
        notifyRealtimeState.queuedRefreshSilent = notifyRealtimeState.queuedRefreshSilent && Boolean(silent);

        const now = Date.now();
        const minIntervalDelay = Math.max(
            0,
            (notifyRealtimeState.lastRefreshAt + NOTIFY_REFRESH_MIN_INTERVAL_MS) - now
        );
        const normalizedDelay = Math.max(0, Number(delayMs) || 0);
        const finalDelay = Math.max(normalizedDelay, minIntervalDelay);

        if(notifyRealtimeState.refreshTimer){
            return;
        }

        notifyRealtimeState.refreshTimer = window.setTimeout(() => {
            notifyRealtimeState.refreshTimer = null;
            if(!notifyRealtimeState.queuedRefresh){
                return;
            }

            const nextSilent = notifyRealtimeState.queuedRefreshSilent;
            notifyRealtimeState.queuedRefresh = false;
            notifyRealtimeState.queuedRefreshSilent = true;
            refreshNotifications({silent: nextSilent});
        }, finalDelay);
    };
    const escapeHtml = (value = '') => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    const toNonNegativeInt = (value, fallback = 0) => {
        const parsed = Number(value);
        if(!Number.isFinite(parsed)){
            return Math.max(0, Math.floor(Number(fallback) || 0));
        }
        return Math.max(0, Math.floor(parsed));
    };
    function setNotifyReadVisual(){
        const notifyButton=getNotifyButton();
        const notifyCount=getNotifyCount();
        if(!notifyButton || !notifyCount) return;
        const countValue=toNonNegativeInt(notifyButton.dataset.notifyCount, 0);
        const unreadCount=Math.min(countValue, toNonNegativeInt(notifyButton.dataset.notifyUnread, 0));
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
        const actionClassSafe = /^is-(save|fill|clear|verify|verify-reset)$/.test(actionClass) ? actionClass : 'is-save';
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
            <div class="notify-item${isRead ? '' : ' is-unread'}" style="--notify-order:${toNonNegativeInt(index, 0)};">
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

        const previousCount=toNonNegativeInt(notifyButton.dataset.notifyCount, 0);
        const previousUnread=toNonNegativeInt(notifyButton.dataset.notifyUnread, 0);
        const previousSignature=String(notifyButton.dataset.notifySignature || '').trim();
        const countValue=toNonNegativeInt(payload?.count, 0);
        const unreadCount=Math.min(countValue, toNonNegativeInt(payload?.unread_count, countValue));
        const rawSignature=String(payload?.signature || '').trim();
        const signature=rawSignature !== '' ? rawSignature : previousSignature;
        const items=Array.isArray(payload?.items)
            ? payload.items.filter((item) => item && typeof item === 'object')
            : [];
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
        if(!notifyButton){
            return;
        }
        if(notifyRealtimeState.isFetching || notifyRealtimeState.isMarkingRead){
            scheduleNotificationRefresh({silent, delayMs: NOTIFY_REFRESH_DEBOUNCE_MS});
            return;
        }
        const feedUrlRaw=String(notifyButton.dataset.notifyFeedUrl || '').trim();
        if(feedUrlRaw === ''){
            return;
        }
        notifyRealtimeState.isFetching=true;
        notifyRealtimeState.queuedRefresh=false;
        notifyRealtimeState.queuedRefreshSilent=true;
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
            notifyRealtimeState.lastRefreshAt=Date.now();
            if(notifyRealtimeState.queuedRefresh){
                scheduleNotificationRefresh({silent: notifyRealtimeState.queuedRefreshSilent, delayMs: NOTIFY_REFRESH_DEBOUNCE_MS});
            }
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
            scheduleNotificationRefresh({silent:true, delayMs: 0});
        }, 30000);
    }
    function bindNotificationRealtimeChannel(channelName){
        if(!notifyRealtimeState.pusher){
            return null;
        }

        const channel=notifyRealtimeState.pusher.subscribe(channelName);
        const handler=(payload) => {
            if(!isEventRelevantToScope(payload)){
                return;
            }
            scheduleNotificationRefresh({silent:true, delayMs: NOTIFY_REFRESH_DEBOUNCE_MS});
        };
        channel.bind('notification.feed.updated', handler);

        return {
            name: channelName,
            channel,
            handler,
        };
    }
    function syncNotificationRealtimeChannels(){
        if(!notifyRealtimeState.pusher){
            return;
        }

        const notifyButton=getNotifyButton();
        if(!notifyButton){
            return;
        }

        const desiredChannelNames = resolveRealtimeChannelNames(notifyButton);
        const desiredNameSet = new Set(desiredChannelNames);

        notifyRealtimeState.channels = notifyRealtimeState.channels.filter((entry) => {
            if(!entry || typeof entry.name !== 'string' || !desiredNameSet.has(entry.name)){
                const staleName = String(entry?.name || '').trim();
                try{
                    entry?.channel?.unbind?.('notification.feed.updated', entry?.handler);
                }catch(_error){
                    // ignore stale binding cleanup errors
                }
                if(staleName !== ''){
                    try{
                        notifyRealtimeState.pusher?.unsubscribe(staleName);
                    }catch(_error){
                        // ignore stale channel cleanup errors
                    }
                }
                return false;
            }
            return true;
        });

        const activeNameSet = new Set(
            notifyRealtimeState.channels
                .map((entry) => String(entry?.name || '').trim())
                .filter((name) => name !== '')
        );
        desiredChannelNames.forEach((channelName) => {
            if(activeNameSet.has(channelName)){
                return;
            }
            const entry = bindNotificationRealtimeChannel(channelName);
            if(entry){
                notifyRealtimeState.channels.push(entry);
            }
        });

        notifyRealtimeState.channelNames = desiredChannelNames;
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
            syncNotificationRealtimeChannels();

            notifyRealtimeState.pusher.connection.bind('connected', () => {
                syncNotificationRealtimeChannels();
                scheduleNotificationRefresh({silent:true, delayMs: 0});
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
        const logoutUrl=String(idleLogoutForm.getAttribute('action') || '/logout').trim();
        if(logoutUrl === ''){
            window.location.assign('/logout');
            return;
        }
        window.location.assign(logoutUrl);
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
    window.addEventListener('pageshow', () => {
        syncNotificationRealtimeChannels();
        scheduleNotificationRefresh({silent:true, delayMs: 0});
    });
    document.addEventListener('livewire:navigated', () => {
        resetIdleLogout(true);
        syncNotifyReadState();
        syncNotificationRealtimeChannels();
        scheduleNotificationRefresh({silent:true, delayMs: 0});
    });
    resetIdleLogout(true);
    syncNotifyReadState();
    bootNotificationRealtime();
    scheduleNotificationRefresh({silent:true, delayMs: 0});
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


