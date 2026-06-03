@extends('layouts.dashboard-shell')

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/accounts.css') }}">
@endpush

@section('content')
    <div class="accounts-page">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100 accounts-card gradient-card">
                    <div class="card-body">
                        <div class="card-hero accounts-hero d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Tambah Akun</h5>
                                <small>Buat kredensial baru untuk pengguna.</small>
                            </div>
                        </div>
                        @if(session('status'))
                            <div class="alert alert-success py-2">{{ session('status') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>Periksa lagi:</strong>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('accounts.store') }}" class="vstack gap-3">
                            @csrf
                            <input type="hidden" name="action" value="create_user">
                            <div>
                                <label class="form-label" for="newUsername">Username</label>
                                <input type="text" class="form-control" id="newUsername" name="new_username" required value="{{ old('new_username') }}">
                            </div>
                            <div>
                                <label class="form-label" for="newDisplayName">Nama tampilan</label>
                                <input type="text" class="form-control" id="newDisplayName" name="new_display_name" value="{{ old('new_display_name') }}">
                            </div>
                            <div>
                                <label class="form-label" for="newRole">Role</label>
                                @php
                                    $roles = $roleOptions ?? [
                                        'administrator' => 'Administrator',
                                        'koordinator' => 'Koordinator',
                                        'qa' => 'QA BPKP',
                                        'auditor' => 'Anggota Tim',
                                    ];
                                @endphp
                                <select class="form-select" id="newRole" name="new_role" required>
                                    @foreach($roles as $roleValue => $roleLabel)
                                        <option value="{{ $roleValue }}" @selected(old('new_role')===$roleValue)>{{ $roleLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label" for="newPassword">Password</label>
                                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Buat akun</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 accounts-card card-soft">
                    <div class="card-body">
                        <div class="card-hero card-soft-hero d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Daftar Akun</h5>
                                <small>Kelola status, reset password, atau hapus akun.</small>
                            </div>
                            <span class="badge bg-secondary">{{ $accounts->total() }} akun</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $currentRoleGroup = null; @endphp
                                @forelse($accounts as $account)
                                    @php
                                        $roleKey = strtolower($account->role);
                                        if (in_array($roleKey, ['administrator','admin','superadmin'])) {
                                            $roleClass = 'badge-role-admin';
                                            $roleLabel = 'Administrator';
                                        } elseif ($roleKey === 'qa') {
                                            $roleClass = 'badge-role-qa';
                                            $roleLabel = 'QA BPKP';
                                        } elseif ($roleKey === 'koordinator') {
                                            $roleClass = 'badge-role-koordinator';
                                            $roleLabel = 'Koordinator';
                                        } else {
                                            $roleClass = 'badge-role-anggota';
                                            $roleLabel = 'Anggota Tim';
                                        }
                                    @endphp
                                    @if($currentRoleGroup !== $roleLabel)
                                        <tr class="account-group-row">
                                            <td colspan="4">
                                                <span class="badge {{ $roleClass }}">{{ $roleLabel }}</span>
                                            </td>
                                        </tr>
                                        @php $currentRoleGroup = $roleLabel; @endphp
                                    @endif
                                    <tr>
                                        <td>{{ $account->username }}</td>
                                        <td>{{ $account->display_name }}</td>
                                        <td>
                                            @if($account->active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center account-action-cell">
                                            @php
                                                $sessionUsername = trim((string) ($user['username'] ?? ''));
                                                $isCurrentUserAccount = $sessionUsername !== '' && $sessionUsername === $account->username;
                                                $roleEditId = 'role-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $account->username);
                                            @endphp
                                            <div class="account-action-bar d-flex flex-wrap gap-2 justify-content-center">
                                                <div class="reset-wrap account-action-form d-flex align-items-center justify-content-center gap-2">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm reset-toggle account-action-btn account-action-btn-icon account-action-role"
                                                        data-target="{{ $roleEditId }}"
                                                        @disabled($isCurrentUserAccount)
                                                        aria-label="Ganti role akun {{ $account->display_name ?: $account->username }}"
                                                        data-action-label="{{ $isCurrentUserAccount ? 'Role terkunci' : 'Ganti role' }}"
                                                        title="{{ $isCurrentUserAccount ? 'Role akun yang sedang digunakan tidak bisa diubah.' : 'Ganti role' }}"
                                                    >
                                                        <svg class="action-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                            <rect x="4" y="5" width="16" height="14" rx="2.6"></rect>
                                                            <path d="M8 9h8"></path>
                                                            <path d="M8 13h4.8"></path>
                                                            <path class="action-badge" d="M15.2 14.2h4.4v4.4h-4.4Z"></path>
                                                            <path class="action-mark" d="M17.4 12.9v1.3"></path>
                                                            <path class="action-mark" d="M17.4 18.6v1.3"></path>
                                                            <path class="action-mark" d="M14.1 16.4h1.3"></path>
                                                            <path class="action-mark" d="M19.6 16.4h1.3"></path>
                                                        </svg>
                                                        <span class="visually-hidden">Ganti Role</span>
                                                    </button>
                                                    <div class="reset-slide" id="{{ $roleEditId }}" aria-hidden="true">
                                                        @php
                                                            $currentRoleValue = strtolower((string) $account->role);
                                                            $currentRoleDisplay = ($roleOptions ?? $roles)[$currentRoleValue] ?? $roleLabel;
                                                        @endphp
                                                        <form
                                                            method="POST"
                                                            action="{{ route('accounts.store') }}"
                                                            class="account-reset-form d-flex align-items-center gap-2"
                                                            data-role-change-form
                                                            data-account-display-name="{{ $account->display_name ?: $account->username }}"
                                                            data-account-username="{{ $account->username }}"
                                                        >
                                                            @csrf
                                                            <input type="hidden" name="action" value="change_role">
                                                            <input type="hidden" name="change_username" value="{{ $account->username }}">
                                                            <select
                                                                name="change_role"
                                                                class="form-select form-select-sm account-reset-input"
                                                                data-current-role-label="{{ $currentRoleDisplay }}"
                                                                required
                                                            >
                                                                @foreach(($roleOptions ?? $roles) as $roleValue => $roleName)
                                                                    <option value="{{ $roleValue }}" @selected(strtolower((string) $account->role) === strtolower((string) $roleValue))>{{ $roleName }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="submit" class="btn btn-primary btn-sm account-action-btn account-action-btn-solid">Simpan</button>
                                                            <button type="button" class="btn btn-light btn-sm reset-cancel account-action-btn">Batal</button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @if(!in_array($roleKey, ['administrator','admin','superadmin']))
                                                    <form method="POST" action="{{ route('accounts.toggle', $account) }}" class="account-action-form">
                                                        @csrf
                                                        <button
                                                            type="{{ $account->active ? 'button' : 'submit' }}"
                                                            class="btn btn-sm account-action-btn account-action-btn-icon {{ $account->active ? 'account-action-deactivate' : 'account-action-activate' }}"
                                                            aria-label="{{ $account->active ? 'Nonaktifkan akun '.$account->username : 'Aktifkan akun '.$account->username }}"
                                                            data-action-label="{{ $account->active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                            title="{{ $account->active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                            @if($account->active)
                                                                data-account-modal-trigger
                                                                data-account-modal-kind="deactivate"
                                                                data-account-modal-title="Nonaktifkan Akun?"
                                                                data-account-modal-message="Akun {{ $account->display_name ?: $account->username }} akan dinonaktifkan dan tidak dapat login sampai diaktifkan kembali."
                                                                data-account-modal-confirm-label="Ya, Nonaktifkan"
                                                            @endif
                                                        >
                                                            @if($account->active)
                                                                <svg class="action-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                                    <rect x="3.5" y="7" width="17" height="10" rx="5"></rect>
                                                                    <circle cx="8.5" cy="12" r="2.8"></circle>
                                                                    <path class="action-mark" d="M14.3 10.3 17.7 13.7"></path>
                                                                    <path class="action-mark" d="M17.7 10.3 14.3 13.7"></path>
                                                                </svg>
                                                                <span class="visually-hidden">Nonaktifkan</span>
                                                            @else
                                                                <svg class="action-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                                    <rect x="3.5" y="7" width="17" height="10" rx="5"></rect>
                                                                    <circle cx="15.5" cy="12" r="2.8"></circle>
                                                                    <path class="action-mark" d="m6.4 12 1.5 1.5 3-3.2"></path>
                                                                </svg>
                                                                <span class="visually-hidden">Aktifkan</span>
                                                            @endif
                                                        </button>
                                                    </form>
                                                @endif
                                                @php
                                                    $resetId = 'reset-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $account->username);
                                                @endphp
                                                <div class="reset-wrap account-action-form d-flex align-items-center justify-content-center gap-2">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm reset-toggle account-action-btn account-action-btn-icon account-action-reset"
                                                        data-target="{{ $resetId }}"
                                                        aria-label="Reset password akun {{ $account->username }}"
                                                        data-action-label="Reset password"
                                                        title="Reset password"
                                                    >
                                                        <svg class="action-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                            <circle cx="7.7" cy="14" r="3.2"></circle>
                                                            <path d="M10.9 14h9.3"></path>
                                                            <path d="M15.5 14v2.4"></path>
                                                            <path d="M18.6 14v1.75"></path>
                                                            <path class="action-badge" d="M5.2 7.75A8 8 0 0 1 18.4 5.3"></path>
                                                            <path class="action-mark" d="M18.35 2.9v2.65h-2.7"></path>
                                                        </svg>
                                                        <span class="visually-hidden">Reset Password</span>
                                                    </button>
                                                    <div class="reset-slide" id="{{ $resetId }}" aria-hidden="true">
                                                        <form method="POST" action="{{ route('accounts.reset', $account) }}" class="account-reset-form d-flex align-items-center gap-2">
                                                            @csrf
                                                            <input type="password" name="password" class="form-control form-control-sm account-reset-input" placeholder="Password baru" required>
                                                            <button type="submit" class="btn btn-primary btn-sm account-action-btn account-action-btn-solid">Simpan</button>
                                                            <button type="button" class="btn btn-light btn-sm reset-cancel account-action-btn">Batal</button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @if(!in_array($roleKey, ['administrator','admin','superadmin']))
                                                    <form method="POST" action="{{ route('accounts.store') }}" class="account-action-form">
                                                        @csrf
                                                        <input type="hidden" name="action" value="delete_account">
                                                        <input type="hidden" name="delete_username" value="{{ $account->username }}">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm account-action-btn account-action-btn-icon account-action-delete"
                                                            aria-label="Hapus akun {{ $account->username }}"
                                                            data-action-label="Hapus"
                                                            title="Hapus akun"
                                                            data-account-modal-trigger
                                                            data-account-modal-kind="delete"
                                                            data-account-modal-title="Hapus Akun?"
                                                            data-account-modal-message="Akun {{ $account->display_name ?: $account->username }} akan dihapus permanen dari sistem."
                                                            data-account-modal-confirm-label="Ya, Hapus"
                                                        >
                                                            <svg class="action-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path d="M4 6h16"></path>
                                                                <path d="M9 6V4h6v2"></path>
                                                                <path d="M18.5 6 17.6 20H6.4L5.5 6"></path>
                                                                <path d="M10 10v6.5"></path>
                                                                <path d="M14 10v6.5"></path>
                                                            </svg>
                                                            <span class="visually-hidden">Hapus</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada akun.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $accounts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="card shadow-sm border-0 accounts-card card-soft">
                    <div class="card-body">
                        <div class="card-hero card-soft-hero d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Penunjukan Tim Elemen</h5>
                                <small>Tentukan koordinator elemen beserta Anggota Tim yang bertugas.</small>
                            </div>
                        </div>

                        @if(!$hasElementAssignmentTable)
                            <div class="alert alert-warning mb-0">
                                Fitur penunjukan tim elemen memerlukan migrasi baru. Jalankan <code>php artisan migrate</code> terlebih dahulu.
                            </div>
                        @else
                            @php
                                $oldAssignmentPayload = collect();
                                if (old('action') === 'save_element_assignment') {
                                    $decodedAssignmentPayload = json_decode((string) old('assignment_payload', ''), true);
                                    if (is_array($decodedAssignmentPayload)) {
                                        $oldAssignmentPayload = collect($decodedAssignmentPayload);
                                    }
                                }
                            @endphp
                            <div class="assignment-stack">
                            @foreach($elementOptions as $elementSlug => $elementLabel)
                                @php
                                    $currentAssignment = $elementAssignments->get($elementSlug);
                                    $oldAssignmentState = $oldAssignmentPayload->get($elementSlug);
                                    $usingOldPayload = is_array($oldAssignmentState);
                                    $usingOldAssignment = !$usingOldPayload && old('action') === 'save_element_assignment' && old('element_slug') === $elementSlug;
                                    $selectedCoordinator = $usingOldPayload
                                        ? trim((string) ($oldAssignmentState['coordinator_username'] ?? ''))
                                        : ($usingOldAssignment
                                            ? (string) old('coordinator_username', '')
                                            : (string) ($currentAssignment?->coordinator_username ?? ''));
                                    $selectedMembers = $usingOldPayload
                                        ? collect((array) ($oldAssignmentState['member_usernames'] ?? []))->map(fn ($username) => (string) $username)->all()
                                        : ($usingOldAssignment
                                            ? collect((array) old('member_usernames', []))->map(fn ($username) => (string) $username)->all()
                                            : collect((array) ($currentAssignment?->member_usernames ?? []))->map(fn ($username) => (string) $username)->all());
                                    $selectedMemberCount = count($selectedMembers);
                                @endphp

                                <form
                                    method="POST"
                                    action="{{ route('accounts.store') }}"
                                    class="element-assignment-form"
                                    data-element-assignment-form
                                    data-element-slug="{{ $elementSlug }}"
                                    data-element-short-label="{{ preg_replace('/^element(\d+)$/', 'Elemen $1', $elementSlug) }}"
                                >
                                    @csrf
                                    <input type="hidden" name="action" value="save_element_assignment">
                                    <input type="hidden" name="element_slug" value="{{ $elementSlug }}">
                                    <input type="hidden" name="assignment_payload" value="{{ old('assignment_payload', '') }}" data-assignment-payload>

                                    <div class="row g-4 align-items-start">
                                        <div class="col-xl-3 col-lg-4">
                                            <div class="assignment-meta">
                                                <div class="assignment-title">{{ $elementLabel }}</div>
                                                <div class="assignment-desc">Tentukan 1 koordinator utama dan pilih Anggota Tim yang bertugas pada elemen ini.</div>
                                            </div>
                                        </div>

                                        <div class="col-xl-9 col-lg-8">
                                            <div class="assignment-panel">
                                                <div class="row g-3">
                                                    <div class="col-lg-5">
                                                        <div class="assignment-field-card">
                                                            <label class="form-label d-block mb-0">Koordinator</label>
                                                            @if($coordinators->isEmpty())
                                                                <div class="assignment-empty-state mt-3">Belum ada akun Koordinator aktif.</div>
                                                            @else
                                                                <div class="assignment-coordinator-grid mt-3">
                                                                    @php
                                                                        $clearCoordinatorId = 'coordinator-'.$elementSlug.'-none';
                                                                    @endphp
                                                                    <div class="form-check assignment-coordinator-pill is-clear-option">
                                                                        <input
                                                                            class="form-check-input"
                                                                            type="radio"
                                                                            id="{{ $clearCoordinatorId }}"
                                                                            name="coordinator_username"
                                                                            value=""
                                                                            data-assignment-coordinator-radio
                                                                            @checked($selectedCoordinator === '')
                                                                        >
                                                                        <label class="form-check-label" for="{{ $clearCoordinatorId }}">
                                                                            <span class="member-name">Belum ditetapkan</span>
                                                                            <span class="member-username">Tanpa koordinator</span>
                                                                            <span class="member-assignment-note" data-assignment-coordinator-note></span>
                                                                        </label>
                                                                    </div>

                                                                    @foreach($coordinators as $coordinator)
                                                                        @php
                                                                            $coordinatorName = $coordinator->display_name ?: $coordinator->username;
                                                                            $coordinatorInputId = 'coordinator-'.$elementSlug.'-'.$coordinator->id;
                                                                        @endphp
                                                                        <div class="form-check assignment-coordinator-pill">
                                                                            <input
                                                                                class="form-check-input"
                                                                                type="radio"
                                                                                id="{{ $coordinatorInputId }}"
                                                                                name="coordinator_username"
                                                                                value="{{ $coordinator->username }}"
                                                                                data-assignment-coordinator-radio
                                                                                @checked($selectedCoordinator === $coordinator->username)
                                                                            >
                                                                            <label class="form-check-label" for="{{ $coordinatorInputId }}">
                                                                                <span class="member-name">{{ $coordinatorName }}</span>
                                                                                <span class="member-username">{{ '@'.$coordinator->username }}</span>
                                                                                <span class="member-assignment-note" data-assignment-coordinator-note></span>
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="assignment-help-text">Pilih akun Koordinator aktif yang bertugas untuk verifikasi pada elemen ini.</div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-7">
                                                        <div class="assignment-field-card">
                                                            <div class="assignment-member-head">
                                                                <label class="form-label d-block mb-0">Anggota Tim</label>
                                                                <span class="assignment-member-count" data-assignment-member-count>{{ $selectedMemberCount }} dipilih</span>
                                                            </div>
                                                            @if($teamMembers->isEmpty())
                                                                <div class="assignment-empty-state">Belum ada akun Anggota Tim aktif.</div>
                                                            @else
                                                                <div class="assignment-member-grid">
                                                                    @foreach($teamMembers as $member)
                                                                        @php
                                                                            $memberName = $member->display_name ?: $member->username;
                                                                            $memberInputId = 'member-'.$elementSlug.'-'.$member->id;
                                                                        @endphp
                                                                        <div class="form-check element-member-pill">
                                                                            <input
                                                                                class="form-check-input"
                                                                                type="checkbox"
                                                                                id="{{ $memberInputId }}"
                                                                                name="member_usernames[]"
                                                                                value="{{ $member->username }}"
                                                                                data-assignment-member-checkbox
                                                                                @checked(in_array($member->username, $selectedMembers, true))
                                                                            >
                                                                            <label class="form-check-label" for="{{ $memberInputId }}">
                                                                                <span class="member-name">{{ $memberName }}</span>
                                                                                <span class="member-username">{{ '@'.$member->username }}</span>
                                                                                <span class="member-assignment-note" data-assignment-member-note></span>
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="assignment-actions">
                                                            <div class="assignment-help-text assignment-rules-note">1 koordinator dan 1 Anggota Tim hanya bisa berada di 1 elemen. Klik simpan pada salah satu card untuk menerapkan seluruh perubahan yang sedang dipilih.</div>
                                                            <button type="submit" class="btn btn-primary assignment-submit-btn">Simpan Penunjukan</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endforeach
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('global-modals')
<div class="account-confirm-modal" id="accountConfirmModal" hidden aria-hidden="true">
    <div class="account-confirm-modal__backdrop" data-account-modal-close></div>
    <div class="account-confirm-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="accountConfirmModalTitle">
        <div class="account-confirm-modal__eyebrow">Konfirmasi Tindakan</div>
        <h3 class="account-confirm-modal__title" id="accountConfirmModalTitle">Konfirmasi</h3>
        <p class="account-confirm-modal__body" id="accountConfirmModalMessage">Lanjutkan tindakan ini?</p>
        <div class="account-confirm-modal__actions">
            <button type="button" class="btn btn-light account-confirm-modal__cancel" data-account-modal-close>Batal</button>
            <button type="button" class="btn account-confirm-modal__confirm" id="accountConfirmModalConfirm">Lanjutkan</button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    // Global confirm modal for account actions
    (function(){
        const modal = document.getElementById('accountConfirmModal');
        if (!modal) return;

        const transitionMs = 180;
        const titleEl = document.getElementById('accountConfirmModalTitle');
        const messageEl = document.getElementById('accountConfirmModalMessage');
        const confirmBtn = document.getElementById('accountConfirmModalConfirm');
        let pendingForm = null;
        let lastTrigger = null;
        let closeTimer = null;

        const getViewportUiScale = () => {
            const zoomRaw = getComputedStyle(document.body).zoom;
            const zoom = parseFloat(zoomRaw || '1');
            return Number.isFinite(zoom) && zoom > 0 ? zoom : 1;
        };

        const syncModalToViewport = () => {
            if (!modal.classList.contains('is-open')) return;

            const scale = getViewportUiScale();
            modal.style.top = `${Math.round(window.scrollY / scale)}px`;
            modal.style.left = `${Math.round(window.scrollX / scale)}px`;
            modal.style.width = `${Math.ceil(window.innerWidth / scale)}px`;
            modal.style.height = `${Math.ceil(window.innerHeight / scale)}px`;
        };

        const clearModalViewportStyles = () => {
            modal.style.removeProperty('top');
            modal.style.removeProperty('left');
            modal.style.removeProperty('width');
            modal.style.removeProperty('height');
        };

        const closeModal = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            modal.removeAttribute('data-kind');
            confirmBtn.classList.remove('is-danger', 'is-warning');
            pendingForm = null;
            if (closeTimer) {
                clearTimeout(closeTimer);
            }
            closeTimer = setTimeout(() => {
                if (!modal.classList.contains('is-open')) {
                    modal.setAttribute('hidden', 'hidden');
                    clearModalViewportStyles();
                }
                closeTimer = null;
            }, transitionMs);
            lastTrigger?.focus?.({ preventScroll: true });
            lastTrigger = null;
        };

        const openModal = (trigger, options = {}) => {
            pendingForm = options.form || trigger?.closest('form');
            if (!pendingForm) return;
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }
            lastTrigger = options.returnFocusEl || trigger;

            const kind = (options.kind ?? trigger?.getAttribute('data-account-modal-kind') ?? '').trim();
            titleEl.textContent = options.title || trigger?.getAttribute('data-account-modal-title') || 'Konfirmasi';
            messageEl.textContent = options.message || trigger?.getAttribute('data-account-modal-message') || 'Lanjutkan tindakan ini?';
            confirmBtn.textContent = options.confirmLabel || trigger?.getAttribute('data-account-modal-confirm-label') || 'Lanjutkan';
            confirmBtn.classList.toggle('is-danger', kind === 'delete');
            confirmBtn.classList.toggle('is-warning', kind === 'deactivate');

            modal.setAttribute('data-kind', kind || 'default');
            modal.removeAttribute('hidden');
            modal.setAttribute('aria-hidden', 'false');
            requestAnimationFrame(() => {
                modal.classList.add('is-open');
                syncModalToViewport();
                confirmBtn.focus({ preventScroll: true });
            });
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-account-modal-trigger]');
            if (trigger) {
                event.preventDefault();
                openModal(trigger);
                return;
            }

            if (event.target.closest('[data-account-modal-close]')) {
                event.preventDefault();
                closeModal();
            }
        });

        confirmBtn.addEventListener('click', () => {
            if (!pendingForm) {
                closeModal();
                return;
            }

            const formToSubmit = pendingForm;
            if (formToSubmit.matches('[data-role-change-form]')) {
                formToSubmit.dataset.roleChangeConfirmed = '1';
            }
            closeModal();
            formToSubmit.submit();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModal();
            }
        });

        window.addEventListener('scroll', syncModalToViewport, { passive: true });
        window.addEventListener('resize', syncModalToViewport);
        window.addEventListener('pageshow', syncModalToViewport);

        // Confirm role change before submit
        document.querySelectorAll('[data-role-change-form]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.roleChangeConfirmed === '1') {
                    delete form.dataset.roleChangeConfirmed;
                    return;
                }

                event.preventDefault();
                const roleSelect = form.querySelector('select[name="change_role"]');
                const selectedOption = roleSelect?.options?.[roleSelect.selectedIndex];
                const selectedRoleLabel = (selectedOption?.textContent || roleSelect?.value || 'role baru').trim();
                const currentRoleLabel = (roleSelect?.getAttribute('data-current-role-label') || '').trim();
                const accountLabel = (form.getAttribute('data-account-display-name') || form.getAttribute('data-account-username') || 'akun ini').trim();
                const roleMessage = currentRoleLabel && currentRoleLabel !== selectedRoleLabel
                    ? `Role ${accountLabel} akan diubah dari ${currentRoleLabel} menjadi ${selectedRoleLabel}.`
                    : `Role ${accountLabel} akan diubah menjadi ${selectedRoleLabel}.`;
                const submitBtn = form.querySelector('button[type="submit"]');

                openModal(submitBtn || form, {
                    form,
                    kind: 'role',
                    title: 'Ubah Role Akun?',
                    message: `${roleMessage} Lanjutkan?`,
                    confirmLabel: 'Ya, Ubah Role',
                    returnFocusEl: submitBtn || form,
                });
            });
        });
    })();

    // Toggle reset password slide
    (function(){
        const toggles=document.querySelectorAll('.reset-toggle');
        const slides=document.querySelectorAll('.reset-slide');

        function closeAll(){
            slides.forEach(d=>{d.classList.remove('show'); d.setAttribute('aria-hidden','true');});
            toggles.forEach(btn=>btn.classList.remove('is-open'));
        }
        toggles.forEach(btn=>{
            btn.addEventListener('click',()=>{
                const targetId=btn.getAttribute('data-target');
                const dd=document.getElementById(targetId);
                if(!dd)return;
                const isShow=dd.classList.contains('show');
                closeAll();
                if(!isShow){
                    dd.classList.add('show');
                    dd.setAttribute('aria-hidden','false');
                    btn.classList.add('is-open');
                    const input=dd.querySelector('input[name=\"password\"]');
                    input?.focus();
                }
            });
        });
        document.addEventListener('click',(e)=>{
            if(e.target.closest('.reset-wrap') || e.target.classList.contains('reset-toggle')) return;
            closeAll();
        });
        document.addEventListener('keydown',(e)=>{
            if(e.key==='Escape') closeAll();
        });
        document.querySelectorAll('.reset-cancel').forEach(btn=>{
            btn.addEventListener('click',()=>{
                closeAll();
            });
        });
    })();

    (function(){
        const assignmentForms = Array.from(document.querySelectorAll('[data-element-assignment-form]'));
        if (!assignmentForms.length) return;

        const buildAssignmentPayload = () => {
            const payload = {};

            assignmentForms.forEach((form) => {
                const elementSlug = form.getAttribute('data-element-slug') || '';
                if (!elementSlug) return;

                const memberCheckboxes = Array.from(form.querySelectorAll('[data-assignment-member-checkbox]'));

                payload[elementSlug] = {
                    coordinator_username: ((form.querySelector('[data-assignment-coordinator-radio]:checked')?.value) || '').trim(),
                    member_usernames: memberCheckboxes
                        .filter((checkbox) => checkbox.checked)
                        .map((checkbox) => (checkbox.value || '').trim())
                        .filter((value, index, items) => value !== '' && items.indexOf(value) === index),
                };
            });

            return payload;
        };

        const buildUsageMaps = () => {
            const coordinatorUsage = new Map();
            const memberUsage = new Map();

            assignmentForms.forEach((form) => {
                const elementLabel = form.getAttribute('data-element-short-label') || (form.getAttribute('data-element-slug') || '');
                const coordinatorUsername = ((form.querySelector('[data-assignment-coordinator-radio]:checked')?.value) || '').trim();

                if (coordinatorUsername !== '' && !coordinatorUsage.has(coordinatorUsername)) {
                    coordinatorUsage.set(coordinatorUsername, { form, elementLabel });
                }

                form.querySelectorAll('[data-assignment-member-checkbox]:checked').forEach((checkbox) => {
                    const memberUsername = (checkbox.value || '').trim();
                    if (memberUsername !== '' && !memberUsage.has(memberUsername)) {
                        memberUsage.set(memberUsername, { form, elementLabel });
                    }
                });
            });

            return { coordinatorUsage, memberUsage };
        };

        const syncAssignmentSummary = (form) => {
            const memberCheckboxes = Array.from(form.querySelectorAll('[data-assignment-member-checkbox]'));
            const selectedMembers = memberCheckboxes.filter((checkbox) => checkbox.checked);
            const memberCountBadge = form.querySelector('[data-assignment-member-count]');

            if (memberCountBadge) {
                memberCountBadge.textContent = `${selectedMembers.length} dipilih`;
            }
        };

        const syncAssignmentAvailability = () => {
            const { coordinatorUsage, memberUsage } = buildUsageMaps();

            assignmentForms.forEach((form) => {
                form.querySelectorAll('[data-assignment-coordinator-radio]').forEach((radio) => {
                    const coordinatorUsername = (radio.value || '').trim();
                    const usedBy = coordinatorUsername !== '' ? coordinatorUsage.get(coordinatorUsername) : null;
                    const isOwnSelection = radio.checked;
                    const isLockedByOtherElement = coordinatorUsername !== '' && !!usedBy && usedBy.form !== form && !isOwnSelection;
                    const pill = radio.closest('.assignment-coordinator-pill');
                    const usageNote = pill?.querySelector('[data-assignment-coordinator-note]');

                    radio.disabled = isLockedByOtherElement;

                    if (pill) {
                        pill.classList.toggle('is-unavailable', isLockedByOtherElement);
                        pill.classList.toggle('is-selected', radio.checked);
                    }

                    if (usageNote) {
                        usageNote.textContent = isLockedByOtherElement
                            ? `Dipakai di ${usedBy.elementLabel}`
                            : '';
                    }
                });

                form.querySelectorAll('[data-assignment-member-checkbox]').forEach((checkbox) => {
                    const memberUsername = (checkbox.value || '').trim();
                    const usedBy = memberUsage.get(memberUsername);
                    const isOwnSelection = checkbox.checked;
                    const isLockedByOtherElement = !!usedBy && usedBy.form !== form && !isOwnSelection;
                    const pill = checkbox.closest('.element-member-pill');
                    const usageNote = pill?.querySelector('[data-assignment-member-note]');

                    checkbox.disabled = isLockedByOtherElement;

                    if (pill) {
                        pill.classList.toggle('is-unavailable', isLockedByOtherElement);
                    }

                    if (usageNote) {
                        usageNote.textContent = isLockedByOtherElement
                            ? `Dipakai di ${usedBy.elementLabel}`
                            : '';
                    }
                });

                syncAssignmentSummary(form);
            });
        };

        document.addEventListener('click', (event) => {
            const blockedCoordinatorPill = event.target.closest('.assignment-coordinator-pill.is-unavailable');
            if (blockedCoordinatorPill) {
                const radio = blockedCoordinatorPill.querySelector('[data-assignment-coordinator-radio]');
                if (radio && radio.disabled) {
                    event.preventDefault();
                }

                return;
            }

            const blockedPill = event.target.closest('.element-member-pill.is-unavailable');
            if (!blockedPill) return;

            const checkbox = blockedPill.querySelector('[data-assignment-member-checkbox]');
            if (!checkbox || !checkbox.disabled) return;

            event.preventDefault();
        });

        document.addEventListener('click', (event) => {
            if (event.target.closest('label') || event.target.closest('input')) {
                return;
            }

            const coordinatorPill = event.target.closest('.assignment-coordinator-pill');
            if (coordinatorPill) {
                const radio = coordinatorPill.querySelector('[data-assignment-coordinator-radio]');
                if (radio && !radio.disabled) {
                    event.preventDefault();
                    radio.click();
                    radio.focus({ preventScroll: true });
                }
                return;
            }

            const memberPill = event.target.closest('.element-member-pill');
            if (!memberPill) return;

            const checkbox = memberPill.querySelector('[data-assignment-member-checkbox]');
            if (checkbox && !checkbox.disabled) {
                event.preventDefault();
                checkbox.click();
                checkbox.focus({ preventScroll: true });
            }
        });

        assignmentForms.forEach((form) => {
            form.querySelectorAll('[data-assignment-coordinator-radio]').forEach((radio) => {
                radio.addEventListener('change', () => {
                    syncAssignmentAvailability();
                });
            });

            form.querySelectorAll('[data-assignment-member-checkbox]').forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    syncAssignmentAvailability();
                });
            });

            form.addEventListener('submit', () => {
                const payloadInput = form.querySelector('[data-assignment-payload]');
                if (!payloadInput) return;
                payloadInput.value = JSON.stringify(buildAssignmentPayload());
            });
        });

        syncAssignmentAvailability();
    })();
</script>
@endpush

