<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'LATSAR' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
    use App\Models\ElementTeamAssignment;
    $notificationItems = $notifications ?? collect();
    $notificationCount = count($notificationItems);
    $latestNotification = $notificationCount > 0 ? $notificationItems->first() : null;
    $latestNotificationSignature = $latestNotification
        ? trim((string) (($latestNotification->id ?? '0') . '|' . ($latestNotification->created_at?->timestamp ?? 0)))
        : '';
    $userRoleKey = strtolower((string) ($user['role'] ?? ''));
    $canManageAccounts = in_array($userRoleKey, ['administrator', 'admin', 'superadmin'], true);
    $photoPath = $user['profile_photo'] ?? '';
    $photoUrl = '';
    $userRoleLabel = $user['role_label'] ?? Account::roleLabel($user['role'] ?? null);
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
    $visibleElementNavSlugs = ElementTeamAssignment::assignedElementSlugsForUser($user);
    $canSeeElementNav = function (string $slug) use ($visibleElementNavSlugs): bool {
        if ($visibleElementNavSlugs === null) {
            return true;
        }

        return in_array($slug, $visibleElementNavSlugs, true);
    };
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
            <img class="logo-light" src="{{ asset('static/logo-sikap-light.png') }}" alt="Logo SIKAP">
            <img class="logo-dark" src="{{ asset('static/logo-sikap-dark.png') }}" alt="Logo SIKAP">
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
            <li><a href="{{ route('dashboard') }}"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 14a8 8 0 1 1 16 0"/><path d="M12 14l4-4"/><circle cx="12" cy="14" r="1.2"/></svg></span><span class="nav-text">Dashboard</span></a></li>
            @if($canSeeElementNav('element1'))
            <li class="has-sub">
                <a class="nav-toggle" data-sub-toggle="element1"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M15.5 12.6 17 21l-5-2.7L7 21l1.5-8.4"/><path d="m10.4 8 1.1 1.1 2.2-2.2"/></svg></span><span class="nav-text">Element 1</span><span class="chevron">›</span></a>
                <ul class="nav-sub" id="sub-element1">
                    <li class="nav-sub-parent"><a href="{{ route('elements.show','element1') }}"><span class="sub-icon">•</span><span>Rekapitulasi Element</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element1_kegiatan_asurans') }}"><span class="sub-icon">•</span><span>Kegiatan Asurans</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element1_jasa_konsultansi') }}"><span class="sub-icon">•</span><span>Kegiatan Konsultansi</span></a></li>
                </ul>
            </li>
            @endif
            @if($canSeeElementNav('element2'))
            <li class="has-sub">
                <a class="nav-toggle" data-sub-toggle="element2"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="12" rx="2.5"/><path d="M9 7V5.8A1.8 1.8 0 0 1 10.8 4h2.4A1.8 1.8 0 0 1 15 5.8V7"/><path d="m9.5 13 1.6 1.6 3-3"/></svg></span><span class="nav-text">Element 2</span><span class="chevron">›</span></a>
                <ul class="nav-sub" id="sub-element2">
                    <li class="nav-sub-parent"><a href="{{ route('elements.show','element2') }}"><span class="sub-icon">•</span><span>Rekapitulasi Element</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element2_pengembangan_informasi') }}"><span class="sub-icon">•</span><span>Pengembangan Informasi Awal</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element2_perencanaan_penugasan') }}"><span class="sub-icon">•</span><span>Perencanaan Penugasan</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element2_pelaksanaan_penugasan') }}"><span class="sub-icon">•</span><span>Pelaksanaan Penugasan</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element2_komunikasi_hasil') }}"><span class="sub-icon">•</span><span>Komunikasi Hasil Penugasan</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element2_pemantauan_tindak_lanjut') }}"><span class="sub-icon">•</span><span>Pemantauan Tindak Lanjut</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element2_pengendalian_kualitas') }}"><span class="sub-icon">•</span><span>Pengendalian Kualitas Penugasan</span></a></li>
                </ul>
            </li>
            @endif
            @if($canSeeElementNav('element3'))
            <li class="has-sub">
                <a class="nav-toggle" data-sub-toggle="element3"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3 5 6v5.5c0 4.3 3 8.2 7 9.5 4-1.3 7-5.2 7-9.5V6l-7-3Z"/><path d="m9.3 11.7 2 2 3.6-3.6"/></svg></span><span class="nav-text">Element 3</span><span class="chevron">›</span></a>
                <ul class="nav-sub" id="sub-element3">
                    <li class="nav-sub-parent"><a href="{{ route('elements.show','element3') }}"><span class="sub-icon">•</span><span>Rekapitulasi Element</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element3_perencanaan_pengawasan') }}"><span class="sub-icon">•</span><span>Perencanaan Pengawasan</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element3_pelaporan_manajemen_kld') }}"><span class="sub-icon">•</span><span>Pelaporan kepada Manajemen K/L/D</span></a></li>
                </ul>
            </li>
            @endif
            @if($canSeeElementNav('element4'))
            <li class="has-sub">
                <a class="nav-toggle" data-sub-toggle="element4"><span class="nav-icon">4</span><span class="nav-text">Element 4</span><span class="chevron">›</span></a>
                <ul class="nav-sub" id="sub-element4">
                    <li class="nav-sub-parent"><a href="{{ route('elements.show','element4') }}"><span class="sub-icon">•</span><span>Rekapitulasi Element</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element4_manajemen_kinerja') }}"><span class="sub-icon">•</span><span>Manajemen Kinerja</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element4_mekanisme_pendanaan') }}"><span class="sub-icon">•</span><span>Manajemen Sumber Daya Keuangan</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element4_perencanaan_sdm_apip') }}"><span class="sub-icon">•</span><span>Perencanaan Kebutuhan dan Pengadaan SDM Pengawasan</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element4_pengembangan_sdm_profesional_apip') }}"><span class="sub-icon">•</span><span>Pengembangan SDM Profesional APIP</span></a></li>
                    <li class="nav-sub-child"><a href="{{ route('elements.show','element4_dukungan_tik') }}"><span class="sub-icon">•</span><span>Dukungan terhadap Teknologi Informasi</span></a></li>
                </ul>
            </li>
            @endif
            @if($canSeeElementNav('element5'))
            <li class="has-sub">
                <a class="nav-toggle" data-sub-toggle="element5"><span class="nav-icon">5</span><span class="nav-text">Element 5</span><span class="chevron">›</span></a>
                <ul class="nav-sub" id="sub-element5">
                    <li><a href="{{ route('elements.show','element5_pembangunan_budaya_integritas') }}"><span class="sub-icon">•</span><span>Pembangunan Budaya Integritas</span></a></li>
                    <li><a href="{{ route('elements.show','element5_pengelolaan_komunikasi_internal') }}"><span class="sub-icon">•</span><span>Pengelolaan Komunikasi Internal</span></a></li>
                    <li><a href="{{ route('elements.show','element5_koordinasi_pengawasan') }}"><span class="sub-icon">•</span><span>Koordinasi Pengawasan Eksternal</span></a></li>
                    <li><a href="{{ route('elements.show','element5_akses_informasi_sumberdaya') }}"><span class="sub-icon">•</span><span>Akses Informasi & Sistem</span></a></li>
                </ul>
            </li>
            @endif
            <li><a href="#"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="14" height="14" rx="2"/><path d="M11 4v14M4 11h14"/><path d="M19 16v6M16 19h6"/></svg></span><span class="nav-text">Area Of Improvement (AoI)</span></a></li>
            <li><a href="{{ route('dms.index') }}"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 8a3 3 0 0 1 3-3h4l2 2h6a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3Z"/><path d="M3 9h18"/></svg></span><span class="nav-text">Data Management System</span></a></li>
            <li><a href="#"><span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 10.5v5"/><path d="M12 7.5h.01"/></svg></span><span class="nav-text">Informasi Umum</span></a></li>
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
                        <h2 id="pageTitle" style="margin-bottom:4px;">{{ $pageTitle ?? 'Halaman' }}</h2>
                    @endif
                    <div class="breadcrumb-meta" id="liveDateTime" style="color:var(--text-muted);font-weight:700;"></div>
                </div>
            </div>
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
                        data-notify-signature="{{ $latestNotificationSignature }}"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true" style="width:16px;height:16px;">
                            <path d="M6 17h12l-1-2v-5a5 5 0 1 0-10 0v5l-1 2Z"/>
                            <path d="M9 17a3 3 0 0 0 6 0"/>
                        </svg>
                        <span class="notify-count" id="notifyCount">{{ $notificationCount }}</span>
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
                            @forelse($notificationItems as $notif)
                                @php
                                    $notifyActorName = trim((string) ($notif->coordinator_name ?: ($notif->coordinatorAccount?->display_name ?? $notif->coordinator_username ?? 'Pengguna')));
                                    $notifyActorUsername = trim((string) ($notif->coordinator_username ?? ''));
                                    $notifyActorRole = trim((string) ($notif->coordinator_role_label ?? 'Pengguna'));
                                    $notifyActorPhotoUrl = $resolvePhotoUrl($notif->coordinatorAccount?->profile_photo ?? '');
                                    $notifyActorInitials = $avatarLabel($notifyActorName, 'U');
                                @endphp
                                <div class="notify-item">
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
                                                <span class="notify-role-badge">{{ $notifyActorRole }}</span>
                                            </div>
                                            @if($notifyActorUsername !== '')
                                                <div class="notify-actor-username">{{ '@' . $notifyActorUsername }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="title">{{ $notif->subtopic_title ?? 'Notifikasi' }}</div>
                                    @if(!empty($notif->statement))
                                        <div class="notify-body">{{ $notif->statement }}</div>
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
                                <img src="{{ $photoUrl }}" alt="Foto profil {{ $user['display_name'] ?? 'User' }}">
                            @else
                                {{ $avatarLabel($user['display_name'] ?? null) }}
                            @endif
                        </div>
                        <div>
                            <strong style="display:block;">{{ $user['display_name'] ?? 'Admin SIKAP' }}</strong>
                            <span style="color:var(--text-muted);font-size:0.85rem;">{{ $userRoleLabel }}</span>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true" style="stroke:currentColor;fill:none;"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="profile-dropdown" id="profileDropdown" role="menu" aria-label="Menu profil">
                        <a href="{{ route('profile.edit') }}" role="menuitem">Edit Profil</a>
                        @if($canManageAccounts)
                            <a href="{{ route('accounts.index') }}" role="menuitem">Manajemen Akun</a>
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
    applyStoredTheme();
    applyStoredSidenavState();
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
    const getProfileButton = () => document.getElementById('profileButton');
    const getProfileDropdown = () => document.getElementById('profileDropdown');
    const notifyReadStorageKey = 'dashboard-notifications-read-signature';
    function setNotifyReadVisual(isRead){
        const notifyButton=getNotifyButton();
        const notifyCount=getNotifyCount();
        if(!notifyButton || !notifyCount) return;
        const countValue=Number(notifyButton.dataset.notifyCount || '0');
        const showBadge=!isRead && countValue > 0;
        const showReadMark=isRead && countValue > 0;
        notifyButton.classList.toggle('has-alert', showBadge);
        notifyButton.classList.toggle('has-read-mark', showReadMark);
        notifyButton.classList.toggle('is-cleared', !showBadge);
        notifyCount.classList.toggle('is-hidden', !showBadge);
        notifyCount.setAttribute('aria-hidden', String(!showBadge));
    }
    function syncNotifyReadState(){
        const notifyButton=getNotifyButton();
        if(!notifyButton) return;
        const countValue=Number(notifyButton.dataset.notifyCount || '0');
        const signature=(notifyButton.dataset.notifySignature || '').trim();
        if(countValue <= 0){
            setNotifyReadVisual(true);
            return;
        }
        let storedSignature='';
        try{
            storedSignature=localStorage.getItem(notifyReadStorageKey) || '';
        }catch(_error){
            storedSignature='';
        }
        setNotifyReadVisual(signature !== '' && storedSignature === signature);
    }
    function markNotificationsRead(){
        const notifyButton=getNotifyButton();
        if(!notifyButton) return;
        const signature=(notifyButton.dataset.notifySignature || '').trim();
        if(signature !== ''){
            try{
                localStorage.setItem(notifyReadStorageKey, signature);
            }catch(_error){
                // Ignore storage failures and still update the current view.
            }
        }
        setNotifyReadVisual(true);
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
    document.addEventListener('livewire:navigated', () => resetIdleLogout(true));
    resetIdleLogout(true);
    syncNotifyReadState();
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

