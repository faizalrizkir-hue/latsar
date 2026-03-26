@extends('layouts.dashboard-shell')

@push('head')
    <link rel="stylesheet" href="/css/accounts.css">
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
                                    $roles = [
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
                                            <div class="account-action-bar d-flex flex-wrap gap-2 justify-content-center">
                                                @if(!in_array($roleKey, ['administrator','admin','superadmin']))
                                                    <form method="POST" action="{{ route('accounts.toggle', $account) }}" class="account-action-form">
                                                        @csrf
                                                        <button
                                                            type="{{ $account->active ? 'button' : 'submit' }}"
                                                            class="btn btn-sm account-action-btn {{ $account->active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                            @if($account->active)
                                                                data-account-modal-trigger
                                                                data-account-modal-kind="deactivate"
                                                                data-account-modal-title="Nonaktifkan Akun?"
                                                                data-account-modal-message="Akun {{ $account->display_name ?: $account->username }} akan dinonaktifkan dan tidak dapat login sampai diaktifkan kembali."
                                                                data-account-modal-confirm-label="Ya, Nonaktifkan"
                                                            @endif
                                                        >
                                                            {{ $account->active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                        </button>
                                                    </form>
                                                @endif
                                                @php
                                                    $resetId = 'reset-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $account->username);
                                                @endphp
                                                <div class="reset-wrap account-action-form d-flex align-items-center justify-content-center gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary reset-toggle account-action-btn" data-target="{{ $resetId }}">
                                                        Reset
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
                                                            class="btn btn-sm btn-outline-danger account-action-btn"
                                                            data-account-modal-trigger
                                                            data-account-modal-kind="delete"
                                                            data-account-modal-title="Hapus Akun?"
                                                            data-account-modal-message="Akun {{ $account->display_name ?: $account->username }} akan dihapus permanen dari sistem."
                                                            data-account-modal-confirm-label="Ya, Hapus"
                                                        >
                                                            Hapus
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
                                <h5 class="card-title mb-0">Penunjukan Tim Element</h5>
                                <small>Tentukan koordinator element beserta Anggota Tim yang bertugas.</small>
                            </div>
                        </div>

                        @if(!$hasElementAssignmentTable)
                            <div class="alert alert-warning mb-0">
                                Fitur penunjukan tim element memerlukan migrasi baru. Jalankan <code>php artisan migrate</code> terlebih dahulu.
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
                                    data-element-short-label="{{ preg_replace('/^element(\d+)$/', 'Element $1', $elementSlug) }}"
                                >
                                    @csrf
                                    <input type="hidden" name="action" value="save_element_assignment">
                                    <input type="hidden" name="element_slug" value="{{ $elementSlug }}">
                                    <input type="hidden" name="assignment_payload" value="{{ old('assignment_payload', '') }}" data-assignment-payload>

                                    <div class="row g-4 align-items-start">
                                        <div class="col-xl-3 col-lg-4">
                                            <div class="assignment-meta">
                                                <div class="assignment-title">{{ $elementLabel }}</div>
                                                <div class="assignment-desc">Tentukan 1 koordinator utama dan pilih Anggota Tim yang bertugas pada element ini.</div>
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
                                                                <div class="assignment-help-text">Pilih akun Koordinator aktif yang bertugas untuk verifikasi pada element ini.</div>
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
                                                            <div class="assignment-help-text assignment-rules-note">1 koordinator dan 1 Anggota Tim hanya bisa berada di 1 element. Klik simpan pada salah satu card untuk menerapkan seluruh perubahan yang sedang dipilih.</div>
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

        const openModal = (trigger) => {
            pendingForm = trigger.closest('form');
            if (!pendingForm) return;
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }
            lastTrigger = trigger;

            const kind = (trigger.getAttribute('data-account-modal-kind') || '').trim();
            titleEl.textContent = trigger.getAttribute('data-account-modal-title') || 'Konfirmasi';
            messageEl.textContent = trigger.getAttribute('data-account-modal-message') || 'Lanjutkan tindakan ini?';
            confirmBtn.textContent = trigger.getAttribute('data-account-modal-confirm-label') || 'Lanjutkan';
            confirmBtn.classList.toggle('is-danger', kind === 'delete');
            confirmBtn.classList.toggle('is-warning', kind !== 'delete');

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
