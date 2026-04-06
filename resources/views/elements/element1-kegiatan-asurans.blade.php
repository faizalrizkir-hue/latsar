@extends('layouts.dashboard-shell')
@php
    $pageTitle = $modulePageTitle ?? 'Element 1 - Kualitas Peran dan Layanan';
@endphp

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/element1-kegiatan-asurans.css') }}">
@endpush

@section('content')
    @php
        $summaryLevelInt = is_numeric($summaryLevel ?? null) ? (int) $summaryLevel : null;
        $summaryLevelClass = ($summaryLevelInt !== null && $summaryLevelInt >= 1 && $summaryLevelInt <= 5)
            ? 'is-level-'.$summaryLevelInt
            : 'pending';
        $summaryLevelQaInt = is_numeric($summaryLevelQa ?? null) ? (int) $summaryLevelQa : null;
        $summaryLevelQaClass = ($summaryLevelQaInt !== null && $summaryLevelQaInt >= 1 && $summaryLevelQaInt <= 5)
            ? 'is-level-'.$summaryLevelQaInt
            : 'pending';
        $summaryQaHasData = (bool) ($summaryQaHasData ?? false);
        $summaryScoreQaValue = is_numeric($summaryScoreQa ?? null) ? (float) $summaryScoreQa : null;
        $userRoleLower = \Illuminate\Support\Str::lower(trim((string) data_get($user ?? [], 'role', '')));
        $isQaRole = $userRoleLower === 'qa';
        $isAnggotaTim = \Illuminate\Support\Str::contains($userRoleLower, ['anggota', 'auditor']);
        $isVerifikator = (bool) ($canVerify ?? false);
        $isQaVerifier = (bool) ($canQaVerify ?? false);
        $canSeeVerifyNoteTab = $isAnggotaTim || $isVerifikator;
        $statementLevelHintMap = is_array($statementLevelHintMap ?? null) ? $statementLevelHintMap : [];
        $moduleInfoLevels = collect($moduleInfoLevels ?? []);
        $elementInfoIconMap = [
            1 => '<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M15.5 12.6 17 21l-5-2.7L7 21l1.5-8.4"/><path d="m10.4 8 1.1 1.1 2.2-2.2"/></svg>',
            2 => '<svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="12" rx="2.5"/><path d="M9 7V5.8A1.8 1.8 0 0 1 10.8 4h2.4A1.8 1.8 0 0 1 15 5.8V7"/><path d="m9.5 13 1.6 1.6 3-3"/></svg>',
            3 => '<svg viewBox="0 0 24 24"><path d="M12 3 5 6v5.5c0 4.3 3 8.2 7 9.5 4-1.3 7-5.2 7-9.5V6l-7-3Z"/><path d="m9.3 11.7 2 2 3.6-3.6"/></svg>',
        ];
        $moduleElementNumber = null;
        if (preg_match('/element\s+(\d+)/i', (string) ($modulePageTitle ?? $pageTitle), $modulePageTitleMatch)) {
            $moduleElementNumber = (int) ($modulePageTitleMatch[1] ?? 0);
        }
        $moduleHeaderIconSvg = $elementInfoIconMap[$moduleElementNumber] ?? null;
        $levelPredikatMap = [
            1 => 'Rintisan',
            2 => 'Terstruktur',
            3 => 'Memadai',
            4 => 'Terintegrasi',
            5 => 'Optimal',
        ];
    @endphp
    <div class="keg-page qa-display-off" id="kegPage" data-disable-field-pop="1" data-summary-level="{{ $summaryLevelInt ?? '' }}">
        <div class="keg-head">
            <div class="keg-title">
                <button type="button" class="keg-title-icon keg-info-trigger" data-info-modal-open aria-label="Lihat informasi level {{ $moduleSubtopicTitle ?? 'Sub Topik' }}">
                    @if($moduleHeaderIconSvg !== null)
                        <span class="keg-title-icon-glyph" aria-hidden="true">{!! $moduleHeaderIconSvg !!}</span>
                    @else
                        {{ $moduleSubtopicCode ?? 'S1' }}
                    @endif
                </button>
                <div>
                    <h4>{{ $moduleSubtopicTitle ?? 'Sub Topik 1 - Kegiatan Asurans' }}</h4>
                </div>
            </div>
            <div class="keg-score-wrap">
                <div class="keg-chip">
                    <span class="label">Skor <span class="qa-mandiri-suffix">Mandiri</span></span>
                    <span class="value">{{ number_format((float) $summaryScore, 2) }}</span>
                </div>
                <div class="keg-chip level {{ $summaryLevelClass }}">
                    <span class="label">Level <span class="qa-mandiri-suffix">Mandiri</span></span>
                    <span class="value">{{ $summaryLevel }}</span>
                </div>
                <div class="keg-chip qa-only">
                    <span class="label qa-level-font">Skor QA</span>
                    <span class="value">{{ $summaryQaHasData && $summaryScoreQaValue !== null ? number_format($summaryScoreQaValue, 2) : '-' }}</span>
                </div>
                <div class="keg-chip level qa-only {{ $summaryLevelQaClass }}">
                    <span class="label qa-level-font">Level QA</span>
                    <span class="value">{{ $summaryQaHasData && $summaryLevelQaInt !== null ? $summaryLevelQaInt : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="keg-card">
            <div class="keg-table-toolbar">
                <button
                    type="button"
                    class="qa-toggle-btn"
                    data-qa-toggle
                    data-label-on="Sembunyikan QA"
                    data-label-off="Tampilkan QA"
                    aria-pressed="false">
                    Tampilkan QA
                </button>
            </div>
            <div class="table-responsive">
                <table class="table keg-table align-middle">
                    <thead>
                        <tr>
                            <th style="width:70px;">No</th>
                            <th>Pernyataan</th>
                            <th style="width:190px;">Level</th>
                            <th style="width:190px;">Skor</th>
                            <th style="width:250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $activeDmsFiles = collect($dmsFiles ?? [])->values();
                            $editLogsByRow = collect($editLogs ?? [])
                                ->groupBy(function ($log) {
                                    return (int) ($log->row_id ?? 0);
                                });
                        @endphp
                        @foreach ($rows as $row)
                            @php
                                $isVerified = (int) ($row->verified ?? 0) === 1;
                                $isQaVerified = (int) ($row->qa_verified ?? 0) === 1;
                                $hasQaAccess = $isQaVerifier || $isVerifikator;
                                $canOpenValidatePane = $hasQaAccess;
                                $weight = ($weights[$row->id] ?? 0) * 100;
                                $levelRaw = trim((string) ($row->level ?? '-'));
                                $levelDisplay = $levelRaw !== '' && $levelRaw !== '-' ? rtrim(rtrim(number_format((float) $levelRaw, 2, '.', ''), '0'), '.') : '-';
                                $scoreDisplay = $row->skor !== null ? number_format((float) $row->skor, 2) : '-';
                                $currentLevel = is_numeric($row->level) ? (int) $row->level : null;
                                $rowLevelClass = (!$isVerified)
                                    ? 'pending'
                                    : (($currentLevel !== null && $currentLevel >= 1 && $currentLevel <= 5)
                                        ? 'is-level-'.$currentLevel
                                        : '');
                                $currentPickedDocIds = collect($row->doc_file_ids ?? [])
                                    ->map(fn ($id) => (int) $id)
                                    ->filter(fn ($id) => $id > 0)
                                    ->values();
                                if ($currentPickedDocIds->isEmpty()) {
                                    $legacyPickedDoc = $activeDmsFiles->first(function ($file) use ($row) {
                                        return (string) ($file['url'] ?? '') === (string) ($row->dokumen_path ?? '');
                                    });
                                    if (!empty($legacyPickedDoc['id'])) {
                                        $currentPickedDocIds = collect([(int) $legacyPickedDoc['id']]);
                                    }
                                }
                                $currentPickedDocIdStrings = $currentPickedDocIds
                                    ->map(fn ($id) => (string) $id)
                                    ->values()
                                    ->all();
                                $currentPickedDocCount = count($currentPickedDocIdStrings);
                                $savedLevelValidationState = is_array($row->level_validation_state ?? null)
                                    ? $row->level_validation_state
                                    : [];
                                $savedQaLevelValidationStateRaw = $row->qa_level_validation_state ?? null;
                                if (is_string($savedQaLevelValidationStateRaw)) {
                                    $decodedQaState = json_decode($savedQaLevelValidationStateRaw, true);
                                    $savedQaLevelValidationStateRaw = is_array($decodedQaState) ? $decodedQaState : [];
                                }
                                $savedQaLevelValidationState = is_array($savedQaLevelValidationStateRaw)
                                    ? $savedQaLevelValidationStateRaw
                                    : [];
                                $statementKey = \Illuminate\Support\Str::lower(trim((string) ($row->pernyataan ?? '')));
                                $levelHints = is_array(data_get($statementLevelHintMap, $statementKey))
                                    ? data_get($statementLevelHintMap, $statementKey)
                                    : [];
                                $currentLevelHint = ($isVerified && $currentLevel !== null)
                                    ? trim((string) ($levelHints[$currentLevel] ?? ''))
                                    : '';
                                $currentLevelPredikat = ($isVerified && $currentLevel !== null)
                                    ? ($levelPredikatMap[$currentLevel] ?? ('Level '.$currentLevel))
                                    : '';
                                $currentLevelClass = ($isVerified && $currentLevel !== null && $currentLevel >= 1 && $currentLevel <= 5)
                                    ? 'is-level-'.$currentLevel
                                    : 'pending';
                                $validatedLevels = collect(range(1, 5))
                                    ->filter(fn ($i) => (int) data_get($savedLevelValidationState, (string) $i, 0) === 1);
                                $validatedLevelCount = $validatedLevels->count();
                                $maxValidatedLevel = (int) ($validatedLevels->max() ?? 0);
                                $validateToneClass = $maxValidatedLevel >= 1 && $maxValidatedLevel <= 5
                                    ? 'validate-tone-l'.$maxValidatedLevel
                                    : '';
                                $qaValidatedLevels = collect(range(1, 5))
                                    ->filter(fn ($i) => (int) data_get($savedQaLevelValidationState, (string) $i, 0) === 1);
                                $qaValidatedLevelCount = $qaValidatedLevels->count();
                                $qaMaxValidatedLevel = (int) ($qaValidatedLevels->max() ?? 0);
                                $qaValidateToneClass = $qaMaxValidatedLevel >= 1 && $qaMaxValidatedLevel <= 5
                                    ? 'validate-tone-l'.$qaMaxValidatedLevel
                                    : '';
                                $qaLevelValue = $isQaVerified ? $qaMaxValidatedLevel : 0;
                                if ($qaLevelValue < 1 || $qaLevelValue > 5) {
                                    $qaLevelValue = (is_numeric($row->level) && $isQaVerified) ? (int) $row->level : 0;
                                }
                                $qaLevelClass = ($qaLevelValue >= 1 && $qaLevelValue <= 5)
                                    ? 'is-level-'.$qaLevelValue
                                    : 'pending';
                                $qaLevelDisplay = ($qaLevelValue >= 1 && $qaLevelValue <= 5)
                                    ? (string) $qaLevelValue
                                    : '-';
                                $qaScoreDisplay = ($qaLevelValue >= 1 && $qaLevelValue <= 5)
                                    ? number_format((float) ($qaLevelValue * ($weights[$row->id] ?? 0)), 2)
                                    : '-';
                                $qaLevelHint = ($isQaVerified && $qaLevelValue >= 1 && $qaLevelValue <= 5)
                                    ? trim((string) ($levelHints[$qaLevelValue] ?? ''))
                                    : '';
                                $hasEditableLevelField = collect(range(1, 5))
                                    ->contains(fn ($i) => (int) data_get($savedLevelValidationState, (string) $i, 0) !== 1);
                                $editLockedByValidation = $isVerified && !$hasEditableLevelField;
                                $verifyNoteText = trim((string) ($row->verify_note ?? ''));
                                $hasVerifyNote = $verifyNoteText !== '';
                                $rowEditLogs = collect($editLogsByRow->get((int) $row->id, []))
                                    ->sortByDesc(function ($log) {
                                        return (string) ($log->created_at ?? '');
                                    })
                                    ->take(1)
                                    ->values();
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $row->id }}</td>
                                <td>
                                    <div class="pernyataan pernyataan-line">
                                        <span>{{ $row->pernyataan }}</span>
                                        <span
                                            class="hint-bubble-trigger pernyataan-weight-hint"
                                            tabindex="0"
                                            role="button"
                                            aria-label="Info bobot pernyataan"
                                            data-hint="Bobot pernyataan: {{ number_format($weight, 2) }}%">?</span>
                                    </div>
                                    @if ($currentLevelHint !== '')
                                        <div class="bobot subtopic-predikat {{ $currentLevelClass !== 'pending' ? 'predikat-'.$currentLevelClass : 'predikat-pending' }}">
                                            {{ $currentLevelPredikat }}
                                        </div>
                                        <div class="subtopic-level-desc">{{ $currentLevelHint }}</div>
                                    @endif
                                    @if ($qaLevelHint !== '')
                                        <div class="subtopic-level-qa-wrap">
                                            @if ($currentLevelHint !== '')
                                                <div class="subtopic-level-separator" aria-hidden="true"></div>
                                            @endif
                                            <div class="subtopic-level-desc qa-level-font">
                                                QA: {{ $qaLevelHint }}
                                            </div>
                                        </div>
                                    @endif
                                    @if ($rowEditLogs->isNotEmpty())
                                        @php
                                            $log = $rowEditLogs->first();
                                            $logDisplayName = trim((string) ($log->display_name ?? ''));
                                            $logUsername = trim((string) ($log->username ?? ''));
                                            $accountLabel = $logDisplayName !== '' ? $logDisplayName : ($logUsername !== '' ? $logUsername : '-');
                                            $actionRaw = trim((string) ($log->action ?? 'save'));
                                            [$actionLabel, $actionClass] = match ($actionRaw) {
                                                'verify' => ['Verifikasi', 'is-verify'],
                                                'verify_reset' => ['Reset Verifikasi', 'is-verify-reset'],
                                                'qa_verify' => ['Verifikasi Final QA', 'is-verify'],
                                                'qa_verify_reset' => ['Reset Final QA', 'is-verify-reset'],
                                                'clear' => ['Bersihkan', 'is-clear'],
                                                default => ['Edit Data', 'is-save'],
                                            };
                                        @endphp
                                        <div class="row-history row-history-single">
                                            <span class="row-history-time">
                                                @if ($log->created_at)
                                                    {{ $log->created_at->timezone('Asia/Jakarta')->format('d/m H:i:s') }} WIB
                                                @else
                                                    -
                                                @endif
                                            </span>
                                            <span class="row-history-action {{ $actionClass }}">{{ $actionLabel }}</span>
                                            <span class="row-history-account">{{ $accountLabel }}</span>
                                        </div>
                                    @else
                                        <div class="row-history row-history-empty">Belum ada riwayat data.</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="keg-dual-metric">
                                        <div class="keg-dual-metric-row">
                                            <span class="keg-dual-metric-label">Mandiri</span>
                                            <span class="pill-level {{ $rowLevelClass }}">{{ $levelDisplay }}</span>
                                        </div>
                                        <div class="keg-dual-metric-row qa-only">
                                            <span class="keg-dual-metric-label qa-level-font">QA</span>
                                            <span class="pill-level {{ $qaLevelClass }}">{{ $qaLevelDisplay }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="keg-dual-metric">
                                        <div class="keg-dual-metric-row">
                                            <span class="keg-dual-metric-label">Mandiri</span>
                                            <span class="pill-skor">{{ $scoreDisplay }}</span>
                                        </div>
                                        <div class="keg-dual-metric-row qa-only">
                                            <span class="keg-dual-metric-label qa-level-font">QA</span>
                                            <span class="pill-skor">{{ $qaScoreDisplay }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="aksi">
                                    <div class="aksi-wrap">
                                        <span class="aksi-edit-slot">
                                            <button
                                                type="button"
                                                class="btn-aksi btn-edit"
                                                data-toggle-row="{{ $row->id }}"
                                                data-row-mode="edit"
                                                title="{{ $isQaRole ? 'Akun QA hanya dapat verifikasi final.' : ($isVerified ? 'Data terverifikasi: mode lihat (tidak dapat diubah)' : 'Edit data') }}"
                                                aria-label="{{ $isVerified ? 'Lihat data terverifikasi' : 'Edit data' }}"
                                                {{ $isQaRole ? 'disabled' : '' }}>
                                                <svg class="aksi-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M4 20h4l10-10a2.1 2.1 0 0 0-3-3L5 17v3z"></path>
                                                    <path d="M13 7l4 4"></path>
                                                </svg>
                                            </button>
                                            @if ($isAnggotaTim && $hasVerifyNote)
                                                <span
                                                    class="aksi-edit-note-dot"
                                                    aria-label="Ada catatan verifikator"
                                                    title="Ada catatan verifikator"></span>
                                            @endif
                                        </span>
                                        <button
                                            type="button"
                                            class="btn-aksi btn-verify {{ $isVerified ? 'is-verified' : 'is-pending' }} {{ $isQaVerified ? 'is-qa-verified' : '' }}"
                                            data-toggle-row="{{ $row->id }}"
                                            data-row-mode="validate"
                                            title="{{ $isQaVerified ? 'Sudah verifikasi final QA' : ($isVerified ? 'Sudah diverifikasi, menunggu final QA' : 'Belum diverifikasi verifikator') }}"
                                            aria-label="Verifikasi"
                                            {{ $canOpenValidatePane ? '' : 'disabled' }}>
                                            <svg class="aksi-icon {{ $isQaVerified ? 'qa-double-check' : '' }}" viewBox="0 0 24 24" aria-hidden="true">
                                                @if ($isQaVerified)
                                                    <polyline class="tick-back" points="2.8 13 6.7 16.9 9.6 14"></polyline>
                                                    <polyline class="tick-front" points="7.4 13 11.6 17.2 21.2 7.6"></polyline>
                                                @else
                                                    <path d="M20 6L9 17l-5-5"></path>
                                                @endif
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('elements.store', $slug) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="row_id" value="{{ $row->id }}">
                                            <input type="hidden" name="action" value="clear">
                                            <button
                                                type="button"
                                                class="btn-aksi btn-clear"
                                                title="Hapus"
                                                aria-label="Hapus"
                                                data-clear-row-btn
                                                {{ $isQaRole || $isVerified ? 'disabled' : '' }}>
                                                <svg class="aksi-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M3 6h18"></path>
                                                    <path d="M8 6V4h8v2"></path>
                                                    <path d="M19 6l-1 14H6L5 6"></path>
                                                    <path d="M10 11v6"></path>
                                                    <path d="M14 11v6"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr class="editor-row" id="row-{{ $row->id }}" hidden>
                                <td colspan="5">
                                    <div class="editor-collapse" data-editor-collapse>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="editor-box" id="edit-{{ $row->id }}">
                                                    @php
                                                        $rowModeEditId = 'row-mode-edit-'.$row->id;
                                                        $rowModeValidateId = 'row-mode-validate-'.$row->id;
                                                    @endphp
                                                    <section class="row-mode-pane is-active" id="{{ $rowModeEditId }}" data-row-mode-pane="edit">
                                                @if ($isVerified)
                                                    <div class="keg-lock-note">
                                                        Data sudah terverifikasi.
                                                        Level yang sudah terverifikasi terkunci, level yang belum terverifikasi masih dapat diubah.
                                                    </div>
                                                @endif
                                                <form method="POST" action="{{ route('elements.store', $slug) }}">
                                                    @csrf
                                                    <input type="hidden" name="row_id" value="{{ $row->id }}">
                                                    <input type="hidden" name="action" value="save">
                                                    <input type="hidden" name="pernyataan" value="{{ $row->pernyataan }}">
                                                    @php
                                                        $supportPaneId = 'edit-support-'.$row->id;
                                                        $levelPaneId = 'edit-level-note-'.$row->id;
                                                        $verifyNotePaneId = 'edit-verify-note-'.$row->id;
                                                        $groupedPickedDmsFiles = $activeDmsFiles
                                                            ->groupBy(fn ($file) => trim((string) ($file['type'] ?? 'Tanpa Jenis')));
                                                        $totalActiveDmsDocs = (int) $activeDmsFiles->count();
                                                        $levelPickedDocs = $activeDmsFiles
                                                            ->filter(fn ($file) => in_array((string) ($file['id'] ?? ''), $currentPickedDocIdStrings, true))
                                                            ->values();
                                                    @endphp
                                                    <div class="edit-pane-wrap" data-edit-pane-wrap>
                                                        <div class="fw-semibold mb-2 edit-pane-title">Form Edit Data</div>
                                                        <div class="edit-menu" role="tablist" aria-label="Menu form edit" data-edit-menu>
                                                            <button type="button" class="edit-menu-btn is-active" role="tab" aria-selected="true" aria-controls="{{ $supportPaneId }}" data-edit-pane-trigger="{{ $supportPaneId }}" data-support-tab-btn>
                                                                Bukti Dukung
                                                            </button>
                                                            <button
                                                                type="button"
                                                                class="edit-menu-btn"
                                                                role="tab"
                                                                aria-selected="false"
                                                                aria-controls="{{ $levelPaneId }}"
                                                                data-edit-pane-trigger="{{ $levelPaneId }}"
                                                                data-level-tab-btn
                                                                @if ($currentPickedDocCount <= 0)
                                                                    title="Pilih minimal 1 dokumen di tab Bukti Dukung."
                                                                    disabled
                                                                    aria-disabled="true"
                                                                @endif>
                                                                Catatan / Analisis Bukti Per Level
                                                                @if ($validatedLevelCount > 0)
                                                                    <span class="edit-validated-badge">{{ $validatedLevelCount }} terverifikasi</span>
                                                                @endif
                                                            </button>
                                                            @if ($canSeeVerifyNoteTab && $hasVerifyNote)
                                                                <button
                                                                    type="button"
                                                                    class="edit-menu-btn has-verify-note"
                                                                    role="tab"
                                                                    aria-selected="false"
                                                                    aria-controls="{{ $verifyNotePaneId }}"
                                                                    data-edit-pane-trigger="{{ $verifyNotePaneId }}"
                                                                    data-verify-note-tab-btn>
                                                                    Catatan Verifikator
                                                                    <span class="edit-verify-note-badge">Ada Catatan</span>
                                                                </button>
                                                            @endif
                                                        </div>

                                                        <section class="edit-pane is-active" id="{{ $supportPaneId }}" data-edit-pane role="tabpanel" aria-label="Bukti Dukung">
                                                            <div class="mt-2">
                                                                <label class="form-label mb-1">Pilih Dokumen</label>
                                                                <div class="small keg-field-hint mb-1">Daftar dokumen diambil langsung dari data dokumen aktif DMS.</div>
                                                                <div class="keg-doc-dd {{ $isVerified ? 'is-disabled' : '' }}" data-doc-dd>
                                                                    <button type="button" class="keg-doc-dd-toggle keg-field keg-select" data-doc-dd-toggle aria-expanded="false" {{ $isVerified ? 'disabled' : '' }}>
                                                                        <span data-doc-dd-text>{{ $currentPickedDocCount > 0 ? $currentPickedDocCount.' dokumen dipilih' : '- pilih berkas -' }}</span>
                                                                    </button>

                                                                    <div class="keg-doc-dd-panel" data-doc-dd-panel hidden>
                                                                        <div class="keg-doc-dd-topbar" data-doc-dd-topbar>
                                                                            <input
                                                                                type="search"
                                                                                class="keg-doc-dd-search keg-field"
                                                                                placeholder="Cari dokumen..."
                                                                                data-doc-dd-search
                                                                                @if ($groupedPickedDmsFiles->isEmpty())
                                                                                    disabled
                                                                                @endif>
                                                                            <div class="keg-doc-dd-total" title="Jumlah total dokumen aktif DMS">
                                                                                <span class="keg-doc-dd-total-label">Total</span>
                                                                                <strong class="keg-doc-dd-total-value">{{ $totalActiveDmsDocs }}</strong>
                                                                            </div>
                                                                        </div>
                                                                        @if ($groupedPickedDmsFiles->isEmpty())
                                                                            <div class="keg-doc-dd-empty">Belum ada dokumen aktif pada DMS.</div>
                                                                        @else
                                                                            @foreach ($groupedPickedDmsFiles as $docType => $typedFiles)
                                                                                <div class="keg-doc-dd-group">
                                                                                    <div class="keg-doc-dd-group-head">Jenis: {{ $docType }}</div>

                                                                                    @foreach ($typedFiles->groupBy(fn ($file) => trim((string) ($file['tag'] ?? 'Tanpa Sub Jenis'))) as $docTag => $taggedFiles)
                                                                                        @php
                                                                                            $docTagText = trim((string) $docTag) !== '' ? trim((string) $docTag) : 'Tanpa Sub Jenis';
                                                                                            $docTagKey = \Illuminate\Support\Str::lower($docTagText);
                                                                                            $docTagTotal = (int) $taggedFiles->count();
                                                                                        @endphp
                                                                                        <div
                                                                                            class="keg-doc-dd-subgroup"
                                                                                            data-doc-dd-subgroup
                                                                                            data-doc-dd-tag-key="{{ $docTagKey }}"
                                                                                            data-doc-dd-tag-total="{{ $docTagTotal }}">
                                                                                            <div class="keg-doc-dd-subhead">
                                                                                                <span>Sub Jenis: {{ $docTagText }}</span>
                                                                                                <span class="keg-doc-dd-subcount">{{ $docTagTotal }}</span>
                                                                                            </div>
                                                                                            <div class="keg-doc-dd-options">
                                                                                                @foreach ($taggedFiles as $file)
                                                                                                    @php
                                                                                                        $isSelectedOption = in_array((string) ($file['id'] ?? ''), $currentPickedDocIdStrings, true);
                                                                                                        $searchText = \Illuminate\Support\Str::lower(trim((string) ($file['label'] ?? '').' '.(string) $docType.' '.(string) $docTagText));
                                                                                                    @endphp
                                                                                                    <div
                                                                                                        class="keg-doc-dd-option {{ $isSelectedOption ? 'is-selected' : '' }} {{ $isVerified ? 'is-disabled' : '' }}"
                                                                                                        data-doc-dd-option
                                                                                                        data-doc-dd-option-text="{{ $searchText }}">
                                                                                                        <label class="keg-doc-dd-option-main">
                                                                                                            <input
                                                                                                                type="checkbox"
                                                                                                                class="keg-doc-dd-check"
                                                                                                                name="doc_file_ids[]"
                                                                                                                value="{{ $file['id'] }}"
                                                                                                                data-doc-dd-check
                                                                                                                {{ $isSelectedOption ? 'checked' : '' }}
                                                                                                                {{ $isVerified ? 'disabled' : '' }}>
                                                                                                            <span class="keg-doc-dd-option-label">{{ $file['label'] }}</span>
                                                                                                        </label>
                                                                                                        @if (!empty($file['url']))
                                                                                                            <a
                                                                                                                href="{{ $file['url'] }}"
                                                                                                                target="_blank"
                                                                                                                rel="noopener noreferrer"
                                                                                                                class="keg-doc-dd-view"
                                                                                                                title="Lihat dokumen">
                                                                                                                Lihat
                                                                                                            </a>
                                                                                                        @else
                                                                                                            <span class="keg-doc-dd-view is-disabled" aria-disabled="true">Lihat</span>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            </div>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endforeach
                                                                            <div class="keg-doc-dd-empty" data-doc-dd-empty hidden>Dokumen tidak ditemukan.</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <label class="form-label mb-1">Analisis Pengujian Bukti Dukung</label>
                                                                <textarea name="analisis_bukti" class="form-control keg-field" rows="4" {{ $isVerified ? 'disabled' : '' }}>{{ $row->analisis_bukti }}</textarea>
                                                            </div>
                                                        </section>

                                                        <section class="edit-pane" id="{{ $levelPaneId }}" data-edit-pane role="tabpanel" aria-label="Catatan per Level" hidden>
                                                            <div class="fw-semibold mb-2">Catatan / Analisis Bukti Per Level</div>
                                                            <div class="keg-level-docs mb-3" data-level-docs>
                                                                <div class="keg-level-docs-head">Dokumen terpilih</div>
                                                                <div class="keg-level-docs-empty" data-level-doc-empty {{ $levelPickedDocs->isEmpty() ? '' : 'hidden' }}>
                                                                    Belum ada dokumen terpilih.
                                                                </div>
                                                                <div class="keg-level-docs-list" data-level-doc-list>
                                                                    @foreach ($levelPickedDocs as $pickedFile)
                                                                        <div class="keg-level-doc-item">
                                                                            <span class="keg-level-doc-name">{{ $pickedFile['label'] }}</span>
                                                                            @if (!empty($pickedFile['url']))
                                                                                <a
                                                                                    href="{{ $pickedFile['url'] }}"
                                                                                    target="_blank"
                                                                                    rel="noopener noreferrer"
                                                                                    class="keg-level-doc-view"
                                                                                    title="Lihat dokumen">
                                                                                    Lihat
                                                                                </a>
                                                                            @else
                                                                                <span class="keg-level-doc-view is-disabled" aria-disabled="true">Lihat</span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="row g-2">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @php
                                                                        $field = 'grad_l'.$i.'_catatan';
                                                                        $isLevelLocked = $isVerified && ((int) data_get($savedLevelValidationState, (string) $i, 0) === 1);
                                                                    @endphp
                                                                    <div class="col-md-6">
                                                                        <label class="form-label small mb-1 hint-bubble-label">
                                                                            <span>Level {{ $i }}</span>
                                                                            @if ($isLevelLocked)
                                                                                <span class="edit-level-validated-badge">Terverifikasi</span>
                                                                            @endif
                                                                            @if (!empty($levelHints[$i]))
                                                                                <span
                                                                                    class="hint-bubble-trigger"
                                                                                    role="button"
                                                                                    tabindex="0"
                                                                                    aria-label="Hint Level {{ $i }}"
                                                                                    data-hint="{{ $levelHints[$i] }}">?</span>
                                                                            @endif
                                                                        </label>
                                                                        <textarea
                                                                            name="grad_l{{ $i }}_catatan"
                                                                            class="form-control keg-field"
                                                                            rows="2"
                                                                            @if ($isLevelLocked)
                                                                                disabled
                                                                                title="Level ini sudah terverifikasi dan tidak dapat diubah."
                                                                            @endif>{{ $row->$field }}</textarea>
                                                                    </div>
                                                                @endfor
                                                            </div>
                                                        </section>

                                                        @if ($canSeeVerifyNoteTab && $hasVerifyNote)
                                                            <section class="edit-pane edit-pane-verify-note has-note" id="{{ $verifyNotePaneId }}" data-edit-pane role="tabpanel" aria-label="Catatan Verifikator" hidden>
                                                                <div class="edit-verify-note-head">Catatan Verifikator</div>
                                                                <div class="small keg-field-hint mb-2">Tab ini terisi otomatis dari menu Verifikasi.</div>
                                                                <div class="edit-verify-note-box">
                                                                    <div class="edit-verify-note-text">{!! nl2br(e($verifyNoteText)) !!}</div>
                                                                </div>
                                                            </section>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                                        <button
                                                            type="submit"
                                                            class="btn keg-form-action-btn is-save"
                                                            data-edit-save-btn
                                                            data-lock-disabled="{{ $editLockedByValidation ? '1' : '0' }}"
                                                            @if ($editLockedByValidation)
                                                                title="Semua level sudah terverifikasi dan tidak dapat diubah."
                                                            @elseif (!$isVerified && $currentPickedDocCount <= 0)
                                                                title="Pilih minimal 1 dokumen pada tab Bukti Dukung."
                                                            @endif
                                                            {{ $editLockedByValidation || (!$isVerified && $currentPickedDocCount <= 0) ? 'disabled' : '' }}>
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </form>
                                                    </section>

                                                    @if ($isVerifikator || $isQaVerifier)
                                                        <section class="row-mode-pane" id="{{ $rowModeValidateId }}" data-row-mode-pane="validate" hidden>
                                                            @php
                                                                $filledLevelNotes = collect(range(1, 5))
                                                                    ->map(function ($i) use ($row) {
                                                                        $field = 'grad_l'.$i.'_catatan';
                                                                        return [
                                                                            'level' => $i,
                                                                            'value' => trim((string) ($row->$field ?? '')),
                                                                        ];
                                                                    })
                                                                    ->filter(fn ($item) => $item['value'] !== '')
                                                                    ->values();
                                                            @endphp
                                                            <div class="edit-pane-wrap validate-pane-wrap {{ $isVerified ? 'is-verified' : 'is-pending' }} {{ $validateToneClass }}">
                                                                <section class="edit-pane is-active">
                                                                    <div class="fw-semibold mb-2 edit-pane-title">Form Verifikasi</div>

                                                                    <div class="keg-level-docs mb-3">
                                                                        <div class="keg-level-docs-head">Daftar Dokumen Terpilih</div>
                                                                        <div class="keg-level-docs-empty {{ $levelPickedDocs->isEmpty() ? '' : 'd-none' }}">
                                                                            Belum ada dokumen terpilih pada Edit Data.
                                                                        </div>
                                                                        <div class="keg-level-docs-list {{ $levelPickedDocs->isEmpty() ? 'd-none' : '' }}">
                                                                            @foreach ($levelPickedDocs as $pickedFile)
                                                                                <div class="keg-level-doc-item">
                                                                                    <span class="keg-level-doc-name">{{ $pickedFile['label'] }}</span>
                                                                                    @if (!empty($pickedFile['url']))
                                                                                        <a
                                                                                            href="{{ $pickedFile['url'] }}"
                                                                                            target="_blank"
                                                                                            rel="noopener noreferrer"
                                                                                            class="keg-level-doc-view"
                                                                                            title="Lihat dokumen">
                                                                                            Lihat
                                                                                        </a>
                                                                                    @else
                                                                                        <span class="keg-level-doc-view is-disabled" aria-disabled="true">Lihat</span>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>

                                                                    <div class="mt-2">
                                                                        <label class="form-label mb-1">Hasil Pengisian Analisis Bukti Dukung</label>
                                                                        <textarea class="form-control keg-field" rows="4" readonly>{{ $row->analisis_bukti }}</textarea>
                                                                    </div>

                                                                    @if ($isVerifikator)
                                                                        <form method="POST" action="{{ route('elements.store', $slug) }}" class="mt-3">
                                                                            @csrf
                                                                            <input type="hidden" name="row_id" value="{{ $row->id }}">
                                                                            <input type="hidden" name="action" value="verify">
                                                                            <div class="edit-pane-wrap validate-pane-wrap {{ $isVerified ? 'is-verified' : 'is-pending' }} {{ $validateToneClass }}" data-validate-pane-wrap>
                                                                                <section class="edit-pane is-active">
                                                                                    <label class="form-label mb-1">Hasil Pengisian Catatan / Analisis Bukti Per Level</label>
                                                                                    @if ($filledLevelNotes->isEmpty())
                                                                                        <div class="keg-level-docs-empty">Belum ada catatan/analisis per level yang diisi.</div>
                                                                                    @else
                                                                                        <div class="row g-2">
                                                                                            @foreach ($filledLevelNotes as $levelNote)
                                                                                                @php
                                                                                                    $isLevelValidated = (int) data_get($savedLevelValidationState, (string) $levelNote['level'], 0) === 1;
                                                                                                @endphp
                                                                                                <div class="col-md-6">
                                                                                                    <div
                                                                                                        class="level-validate-card level-validate-l{{ $levelNote['level'] }} {{ $isLevelValidated ? 'is-validated' : '' }}"
                                                                                                        data-level-validate-field>
                                                                                                        <label class="form-label small mb-1 hint-bubble-label">
                                                                                                            <span>Level {{ $levelNote['level'] }}</span>
                                                                                                            @if (!empty($levelHints[$levelNote['level']] ?? null))
                                                                                                                <span
                                                                                                                    class="hint-bubble-trigger"
                                                                                                                    role="button"
                                                                                                                    tabindex="0"
                                                                                                                    aria-label="Hint Level {{ $levelNote['level'] }}"
                                                                                                                    data-hint="{{ $levelHints[$levelNote['level']] }}">?</span>
                                                                                                            @endif
                                                                                                        </label>
                                                                                                        <textarea class="form-control keg-field" rows="2" readonly>{{ $levelNote['value'] }}</textarea>
                                                                                                        <input type="hidden" name="level_validation[{{ $levelNote['level'] }}]" value="{{ $isLevelValidated ? '1' : '0' }}" data-level-validate-input>
                                                                                                        <div class="d-flex justify-content-end mt-2">
                                                                                                            <button
                                                                                                                type="button"
                                                                                                                class="level-validate-btn level-validate-l{{ $levelNote['level'] }} {{ $isLevelValidated ? 'is-validated' : '' }}"
                                                                                                                data-level-validate-btn
                                                                                                                data-level="{{ $levelNote['level'] }}"
                                                                                                                aria-label="Verifikasi isian level {{ $levelNote['level'] }}"
                                                                                                                aria-pressed="{{ $isLevelValidated ? 'true' : 'false' }}"
                                                                                                                title="{{ $isLevelValidated ? 'Klik untuk batalkan verifikasi level ini' : 'Klik untuk verifikasi level ini' }}">
                                                                                                                <span class="level-validate-icon" aria-hidden="true"></span>
                                                                                                                <span class="level-validate-text level-validate-text-default">Verifikasi</span>
                                                                                                                <span class="level-validate-text level-validate-text-processing">Memverifikasi...</span>
                                                                                                                <span class="level-validate-text level-validate-text-done">Terverifikasi</span>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif

                                                                                    <div class="mt-2">
                                                                                        <label class="form-label mb-1">Catatan Verifikasi (opsional)</label>
                                                                                        <textarea name="verify_note" class="form-control keg-field" rows="3">{{ $row->verify_note }}</textarea>
                                                                                    </div>
                                                                                </section>
                                                                            </div>
                                                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                                                <button
                                                                                    type="button"
                                                                                    class="btn keg-form-action-btn is-reset"
                                                                                    data-verify-reset-btn>
                                                                                    Reset Verifikasi
                                                                                </button>
                                                                                <button
                                                                                    type="submit"
                                                                                    class="btn keg-form-action-btn is-verify"
                                                                                    name="verified"
                                                                                    value="1"
                                                                                    @if ($currentPickedDocCount <= 0)
                                                                                        disabled
                                                                                        title="Verifikasi memerlukan dokumen terpilih pada Edit Data."
                                                                                    @endif>
                                                                                    Simpan Verifikasi
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    @endif

                                                                    @if ($isQaVerifier)
                                                                        <form method="POST" action="{{ route('elements.store', $slug) }}" class="mt-3">
                                                                            @csrf
                                                                            <input type="hidden" name="row_id" value="{{ $row->id }}">
                                                                            <input type="hidden" name="action" value="qa_verify">

                                                                            <div class="edit-pane-wrap validate-pane-wrap {{ $isQaVerified ? 'is-verified' : 'is-pending' }} {{ $qaValidateToneClass }}" data-validate-pane-wrap>
                                                                                <section class="edit-pane is-active">
                                                                                    <label class="form-label mb-1">Hasil Pengisian Catatan / Analisis Bukti Per Level (Verifikasi QA)</label>
                                                                                    @if (!$isVerified)
                                                                                        <div class="keg-level-docs-empty">Verifikasi final QA menunggu verifikasi dari Koordinator/Admin.</div>
                                                                                    @elseif ($filledLevelNotes->isEmpty())
                                                                                        <div class="keg-level-docs-empty">Belum ada catatan/analisis per level yang diisi.</div>
                                                                                    @else
                                                                                        <div class="row g-2">
                                                                                            @foreach ($filledLevelNotes as $levelNote)
                                                                                                @php
                                                                                                    $isQaLevelValidated = (int) data_get($savedQaLevelValidationState, (string) $levelNote['level'], 0) === 1;
                                                                                                @endphp
                                                                                                <div class="col-md-6">
                                                                                                    <div
                                                                                                        class="level-validate-card level-validate-l{{ $levelNote['level'] }} {{ $isQaLevelValidated ? 'is-validated' : '' }}"
                                                                                                        data-level-validate-field>
                                                                                                        <label class="form-label small mb-1 hint-bubble-label">
                                                                                                            <span>Level {{ $levelNote['level'] }}</span>
                                                                                                            @if (!empty($levelHints[$levelNote['level']] ?? null))
                                                                                                                <span
                                                                                                                    class="hint-bubble-trigger"
                                                                                                                    role="button"
                                                                                                                    tabindex="0"
                                                                                                                    aria-label="Hint Level {{ $levelNote['level'] }}"
                                                                                                                    data-hint="{{ $levelHints[$levelNote['level']] }}">?</span>
                                                                                                            @endif
                                                                                                        </label>
                                                                                                        <textarea class="form-control keg-field" rows="2" readonly>{{ $levelNote['value'] }}</textarea>
                                                                                                        <input type="hidden" name="qa_level_validation[{{ $levelNote['level'] }}]" value="{{ $isQaLevelValidated ? '1' : '0' }}" data-level-validate-input>
                                                                                                        <div class="d-flex justify-content-end mt-2">
                                                                                                            <button
                                                                                                                type="button"
                                                                                                                class="level-validate-btn level-validate-l{{ $levelNote['level'] }} {{ $isQaLevelValidated ? 'is-validated' : '' }}"
                                                                                                                data-level-validate-btn
                                                                                                                data-level="{{ $levelNote['level'] }}"
                                                                                                                aria-label="Verifikasi final QA isian level {{ $levelNote['level'] }}"
                                                                                                                aria-pressed="{{ $isQaLevelValidated ? 'true' : 'false' }}"
                                                                                                                title="{{ $isQaLevelValidated ? 'Klik untuk batalkan verifikasi QA level ini' : 'Klik untuk verifikasi QA level ini' }}">
                                                                                                                <span class="level-validate-icon" aria-hidden="true"></span>
                                                                                                                <span class="level-validate-text level-validate-text-default">Verifikasi</span>
                                                                                                                <span class="level-validate-text level-validate-text-processing">Memverifikasi...</span>
                                                                                                                <span class="level-validate-text level-validate-text-done">Terverifikasi</span>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif

                                                                                    <div class="mt-2">
                                                                                        <label class="form-label mb-1">Hasil Verifikasi QA</label>
                                                                                        <textarea name="qa_verify_note" class="form-control keg-field" rows="3">{{ $row->qa_verify_note }}</textarea>
                                                                                    </div>
                                                                                    <div class="mt-2">
                                                                                        <label class="form-label mb-1">Rekomendasi Tindak Lanjut</label>
                                                                                        <textarea name="qa_follow_up_recommendation" class="form-control keg-field" rows="3">{{ $row->qa_follow_up_recommendation }}</textarea>
                                                                                    </div>
                                                                                </section>
                                                                            </div>

                                                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                                                <button
                                                                                    type="submit"
                                                                                    class="btn keg-form-action-btn is-reset"
                                                                                    name="qa_verified"
                                                                                    value="0"
                                                                                    {{ $isVerified ? '' : 'disabled title=Verifikasi final QA menunggu verifikasi dari Koordinator/Admin.' }}>
                                                                                    Reset Final QA
                                                                                </button>
                                                                                <button
                                                                                    type="submit"
                                                                                    class="btn keg-form-action-btn is-verify"
                                                                                    name="qa_verified"
                                                                                    value="1"
                                                                                    {{ $isVerified ? '' : 'disabled title=Verifikasi final QA menunggu verifikasi dari Koordinator/Admin.' }}>
                                                                                    {{ $isQaVerified ? 'Perbarui Final QA' : 'Simpan Final QA' }}
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    @endif
                                                                </section>
                                                            </div>
                                                        </section>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if (($isVerifikator || $isQaVerifier) && count($verifyNotes))
            <div class="note-card">
                <div class="fw-semibold mb-2">Catatan verifikasi yang sudah diisi</div>
                <ul class="note-list">
                    @foreach ($verifyNotes as $note)
                        <li class="note-item">
                            <strong>#{{ $note['id'] }} - {{ $note['pernyataan'] }}</strong>
                            <span>{{ $note['note'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection

@push('global-modals')
    <div class="keg-doc-modal keg-info-modal" id="kegInfoModal" hidden aria-hidden="true">
        <div class="keg-doc-backdrop" data-info-modal-close></div>
        <div class="keg-doc-dialog keg-info-dialog" role="dialog" aria-modal="true" aria-labelledby="kegInfoModalTitle">
            <div class="keg-doc-header keg-info-header">
                <h5 id="kegInfoModalTitle">{{ $moduleInfoModalTitle ?? 'Informasi Level Sub Topik' }}</h5>
                <button type="button" class="keg-doc-close" aria-label="Tutup modal informasi" data-info-modal-close>&times;</button>
            </div>
            <div class="keg-doc-body keg-info-body">
                <ol class="keg-info-list">
                    @foreach ($moduleInfoLevels as $infoLevel)
                        @php
                            $infoLevelValue = (int) ($infoLevel['level'] ?? 0);
                            $infoLevelRange = trim((string) ($infoLevel['score_range'] ?? ''));
                            $infoLevelDescription = trim((string) ($infoLevel['description'] ?? ''));
                        @endphp
                        @if ($infoLevelValue > 0)
                            <li class="keg-info-item" data-info-level="{{ $infoLevelValue }}">
                                <span class="keg-info-level">Level {{ $infoLevelValue }}</span>
                                @if ($infoLevelRange !== '')
                                    <div class="keg-info-range">Rentang skor: {{ $infoLevelRange }}</div>
                                @endif
                                @if ($infoLevelDescription !== '')
                                    <p>"{{ $infoLevelDescription }}"</p>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
            <div class="keg-doc-footer keg-info-footer">
                <button type="button" class="btn btn-outline-secondary" data-info-modal-close>Tutup</button>
            </div>
        </div>
    </div>

    <div class="keg-doc-modal keg-reset-modal" id="kegResetVerifyModal" hidden aria-hidden="true">
        <div class="keg-doc-backdrop" data-reset-verify-modal-close></div>
        <div class="keg-doc-dialog keg-reset-dialog" role="dialog" aria-modal="true" aria-labelledby="kegResetVerifyModalTitle">
            <div class="keg-doc-header keg-reset-header">
                <h5 id="kegResetVerifyModalTitle">Konfirmasi Reset Verifikasi</h5>
                <button type="button" class="keg-doc-close" aria-label="Tutup konfirmasi reset verifikasi" data-reset-verify-modal-close>&times;</button>
            </div>
            <div class="keg-doc-body keg-reset-body">
                <p>Apakah Anda yakin ingin melakukan <strong>Reset Verifikasi</strong>?</p>
                <p class="keg-reset-note">Status verifikasi pada pernyataan ini akan dikembalikan ke kondisi belum terverifikasi.</p>
            </div>
            <div class="keg-doc-footer keg-reset-footer">
                <button type="button" class="btn btn-outline-secondary" data-reset-verify-modal-close>Batal</button>
                <button type="button" class="btn btn-danger" data-reset-verify-confirm>Ya, Reset Verifikasi</button>
            </div>
        </div>
    </div>

    <div class="keg-doc-modal keg-clear-modal" id="kegClearRowModal" hidden aria-hidden="true">
        <div class="keg-doc-backdrop" data-clear-row-modal-close></div>
        <div class="keg-doc-dialog keg-clear-dialog" role="dialog" aria-modal="true" aria-labelledby="kegClearRowModalTitle">
            <div class="keg-doc-header keg-clear-header">
                <h5 id="kegClearRowModalTitle">Konfirmasi Hapus Data</h5>
                <button type="button" class="keg-doc-close" aria-label="Tutup konfirmasi hapus data" data-clear-row-modal-close>&times;</button>
            </div>
            <div class="keg-doc-body keg-clear-body">
                <p>Apakah Anda yakin ingin <strong>menghapus data baris ini</strong>?</p>
                <p class="keg-clear-note">Data isian, catatan level, dan status verifikasi pada baris ini akan dibersihkan.</p>
            </div>
            <div class="keg-doc-footer keg-clear-footer">
                <button type="button" class="btn btn-outline-secondary" data-clear-row-modal-close>Batal</button>
                <button type="button" class="btn btn-danger" data-clear-row-confirm>Ya, Hapus Data</button>
            </div>
        </div>
    </div>

@endpush

@push('scripts')
    <script>
        (function () {
            const page = document.getElementById('kegPage');
            if (!page) {
                return;
            }

            const infoModal = document.getElementById('kegInfoModal');
            const resetVerifyModal = document.getElementById('kegResetVerifyModal');
            const clearRowModal = document.getElementById('kegClearRowModal');
            const infoModalTransitionMs = 220;
            const docDropdownTransitionMs = 320;
            const editorRowTransitionMs = 340;
            const qaToggleButton = page.querySelector('[data-qa-toggle]');
            let infoModalCloseTimer = null;
            let resetVerifyModalCloseTimer = null;
            let clearRowModalCloseTimer = null;
            let pendingResetVerifyForm = null;
            let pendingClearRowForm = null;

            function applyQaDisplay(showQa) {
                page.classList.toggle('qa-display-off', !showQa);
                if (!qaToggleButton) {
                    return;
                }

                qaToggleButton.setAttribute('aria-pressed', showQa ? 'true' : 'false');
                qaToggleButton.classList.toggle('is-active', showQa);
                const label = showQa
                    ? (qaToggleButton.getAttribute('data-label-on') || 'Sembunyikan QA')
                    : (qaToggleButton.getAttribute('data-label-off') || 'Tampilkan QA');
                qaToggleButton.textContent = label;
            }

            if (qaToggleButton && qaToggleButton.dataset.qaToggleBound !== '1') {
                qaToggleButton.dataset.qaToggleBound = '1';
                qaToggleButton.addEventListener('click', () => {
                    const showQaNow = page.classList.contains('qa-display-off');
                    applyQaDisplay(showQaNow);
                });
            }
            applyQaDisplay(false);

            function flashElementClass(element, className, durationMs = 1200) {
                if (!element || !className) {
                    return;
                }
                const timerKey = `__${className}Timer`;
                if (element[timerKey]) {
                    clearTimeout(element[timerKey]);
                    element[timerKey] = null;
                }
                element.classList.add(className);
                element[timerKey] = setTimeout(() => {
                    element.classList.remove(className);
                    element[timerKey] = null;
                }, durationMs);
            }

            function getViewportUiScale() {
                const zoomRaw = getComputedStyle(document.body).zoom;
                const zoom = parseFloat(zoomRaw || '1');
                return Number.isFinite(zoom) && zoom > 0 ? zoom : 1;
            }

            function syncModalToViewport(modal) {
                if (!modal || !modal.classList.contains('is-open')) {
                    return;
                }

                const scale = getViewportUiScale();
                modal.style.top = `${Math.round(window.scrollY / scale)}px`;
                modal.style.left = `${Math.round(window.scrollX / scale)}px`;
                modal.style.width = `${Math.ceil(window.innerWidth / scale)}px`;
                modal.style.height = `${Math.ceil(window.innerHeight / scale)}px`;
            }

            function clearModalViewportStyles(modal) {
                if (!modal) {
                    return;
                }
                modal.style.removeProperty('top');
                modal.style.removeProperty('left');
                modal.style.removeProperty('width');
                modal.style.removeProperty('height');
            }

            function syncInfoModalToViewport() {
                syncModalToViewport(infoModal);
            }

            function syncResetVerifyModalToViewport() {
                syncModalToViewport(resetVerifyModal);
            }

            function clearResetVerifyModalViewportStyles() {
                clearModalViewportStyles(resetVerifyModal);
            }

            function syncClearRowModalToViewport() {
                syncModalToViewport(clearRowModal);
            }

            function clearInfoModalViewportStyles() {
                clearModalViewportStyles(infoModal);
            }

            function clearClearRowModalViewportStyles() {
                clearModalViewportStyles(clearRowModal);
            }

            function getSummaryLevelFromPage() {
                const raw = page.getAttribute('data-summary-level') || '';
                const parsed = Number.parseInt(raw, 10);
                if (!Number.isFinite(parsed) || parsed < 1 || parsed > 5) {
                    return null;
                }
                return parsed;
            }

            function highlightInfoModalLevel() {
                if (!infoModal) {
                    return;
                }
                const summaryLevel = getSummaryLevelFromPage();
                if (!summaryLevel) {
                    return;
                }

                const levelItem = infoModal.querySelector(`.keg-info-item[data-info-level="${summaryLevel}"]`);
                if (!levelItem) {
                    return;
                }

                infoModal.querySelectorAll('.keg-info-item.is-level-focus').forEach((item) => {
                    item.classList.remove('is-level-focus');
                });

                flashElementClass(levelItem, 'is-level-focus', 1700);
            }

            function highlightInfoTriggerArea(trigger) {
                flashElementClass(trigger, 'is-scroll-highlight', 1200);
                const targetHead = page.querySelector('.keg-head');
                if (targetHead) {
                    flashElementClass(targetHead, 'is-scroll-highlight', 1300);
                }
            }

            function syncModalBodyLock() {
                const infoOpen = !!(infoModal && infoModal.classList.contains('is-open'));
                const resetVerifyOpen = !!(resetVerifyModal && resetVerifyModal.classList.contains('is-open'));
                const clearRowOpen = !!(clearRowModal && clearRowModal.classList.contains('is-open'));
                const shouldLock = infoOpen || resetVerifyOpen || clearRowOpen;
                const body = document.body;
                if (!body) {
                    return;
                }

                if (shouldLock) {
                    const scrollbarComp = Math.max(0, window.innerWidth - document.documentElement.clientWidth);
                    if (scrollbarComp > 0) {
                        body.style.setProperty('--keg-scrollbar-comp', `${scrollbarComp}px`);
                    } else {
                        body.style.removeProperty('--keg-scrollbar-comp');
                    }
                    body.classList.add('keg-modal-open');
                    return;
                }

                body.classList.remove('keg-modal-open');
                body.style.removeProperty('--keg-scrollbar-comp');
            }

            function openInfoModal() {
                if (!infoModal) {
                    return;
                }
                if (infoModalCloseTimer) {
                    clearTimeout(infoModalCloseTimer);
                    infoModalCloseTimer = null;
                }
                const infoBody = infoModal.querySelector('.keg-info-body');
                if (infoBody) {
                    infoBody.scrollTop = 0;
                }
                infoModal.removeAttribute('hidden');
                infoModal.setAttribute('aria-hidden', 'false');
                requestAnimationFrame(() => {
                    infoModal.classList.add('is-open');
                    syncInfoModalToViewport();
                    syncModalBodyLock();
                    requestAnimationFrame(() => {
                        highlightInfoModalLevel();
                    });
                });
            }

            function closeInfoModal() {
                if (!infoModal) {
                    return;
                }
                if (infoModal.hasAttribute('hidden') && !infoModal.classList.contains('is-open')) {
                    return;
                }
                infoModal.classList.remove('is-open');
                infoModal.setAttribute('aria-hidden', 'true');
                if (infoModalCloseTimer) {
                    clearTimeout(infoModalCloseTimer);
                }
                infoModalCloseTimer = setTimeout(() => {
                    if (!infoModal.classList.contains('is-open')) {
                        infoModal.setAttribute('hidden', 'hidden');
                        clearInfoModalViewportStyles();
                        syncModalBodyLock();
                    }
                    infoModalCloseTimer = null;
                }, infoModalTransitionMs);
            }

            function openResetVerifyModal(form) {
                if (!resetVerifyModal || !form) {
                    return;
                }
                pendingResetVerifyForm = form;
                if (infoModal && infoModal.classList.contains('is-open')) {
                    closeInfoModal();
                }
                if (resetVerifyModalCloseTimer) {
                    clearTimeout(resetVerifyModalCloseTimer);
                    resetVerifyModalCloseTimer = null;
                }
                resetVerifyModal.removeAttribute('hidden');
                resetVerifyModal.setAttribute('aria-hidden', 'false');
                requestAnimationFrame(() => {
                    resetVerifyModal.classList.add('is-open');
                    syncResetVerifyModalToViewport();
                    syncModalBodyLock();
                    const confirmBtn = resetVerifyModal.querySelector('[data-reset-verify-confirm]');
                    if (confirmBtn) {
                        setTimeout(() => confirmBtn.focus(), 40);
                    }
                });
            }

            function openClearRowModal(form) {
                if (!clearRowModal || !form) {
                    return;
                }
                pendingClearRowForm = form;
                if (infoModal && infoModal.classList.contains('is-open')) {
                    closeInfoModal();
                }
                if (clearRowModalCloseTimer) {
                    clearTimeout(clearRowModalCloseTimer);
                    clearRowModalCloseTimer = null;
                }
                clearRowModal.removeAttribute('hidden');
                clearRowModal.setAttribute('aria-hidden', 'false');
                requestAnimationFrame(() => {
                    clearRowModal.classList.add('is-open');
                    syncClearRowModalToViewport();
                    syncModalBodyLock();
                    const confirmBtn = clearRowModal.querySelector('[data-clear-row-confirm]');
                    if (confirmBtn) {
                        setTimeout(() => confirmBtn.focus(), 40);
                    }
                });
            }

            function closeClearRowModal(options = {}) {
                if (!clearRowModal) {
                    return;
                }
                const shouldClearPending = options.clearPending !== false;
                if (shouldClearPending) {
                    pendingClearRowForm = null;
                }
                if (clearRowModal.hasAttribute('hidden') && !clearRowModal.classList.contains('is-open')) {
                    return;
                }
                clearRowModal.classList.remove('is-open');
                clearRowModal.setAttribute('aria-hidden', 'true');
                if (clearRowModalCloseTimer) {
                    clearTimeout(clearRowModalCloseTimer);
                }
                clearRowModalCloseTimer = setTimeout(() => {
                    if (!clearRowModal.classList.contains('is-open')) {
                        clearRowModal.setAttribute('hidden', 'hidden');
                        clearClearRowModalViewportStyles();
                        syncModalBodyLock();
                    }
                    clearRowModalCloseTimer = null;
                }, infoModalTransitionMs);
            }

            function confirmClearRow() {
                if (!pendingClearRowForm) {
                    closeClearRowModal();
                    return;
                }

                const formToSubmit = pendingClearRowForm;
                closeClearRowModal({ clearPending: false });
                pendingClearRowForm = null;

                if (typeof formToSubmit.requestSubmit === 'function') {
                    formToSubmit.requestSubmit();
                } else {
                    formToSubmit.submit();
                }
            }

            function closeResetVerifyModal(options = {}) {
                if (!resetVerifyModal) {
                    return;
                }
                const shouldClearPending = options.clearPending !== false;
                if (shouldClearPending) {
                    pendingResetVerifyForm = null;
                }
                if (resetVerifyModal.hasAttribute('hidden') && !resetVerifyModal.classList.contains('is-open')) {
                    return;
                }
                resetVerifyModal.classList.remove('is-open');
                resetVerifyModal.setAttribute('aria-hidden', 'true');
                if (resetVerifyModalCloseTimer) {
                    clearTimeout(resetVerifyModalCloseTimer);
                }
                resetVerifyModalCloseTimer = setTimeout(() => {
                    if (!resetVerifyModal.classList.contains('is-open')) {
                        resetVerifyModal.setAttribute('hidden', 'hidden');
                        clearResetVerifyModalViewportStyles();
                        syncModalBodyLock();
                    }
                    resetVerifyModalCloseTimer = null;
                }, infoModalTransitionMs);
            }

            function confirmResetVerify() {
                if (!pendingResetVerifyForm) {
                    closeResetVerifyModal();
                    return;
                }

                let verifiedInput = pendingResetVerifyForm.querySelector('input[name="verified"][data-reset-verified-input]');
                if (!verifiedInput) {
                    verifiedInput = document.createElement('input');
                    verifiedInput.type = 'hidden';
                    verifiedInput.name = 'verified';
                    verifiedInput.setAttribute('data-reset-verified-input', '1');
                    pendingResetVerifyForm.appendChild(verifiedInput);
                }
                verifiedInput.value = '0';

                const formToSubmit = pendingResetVerifyForm;
                closeResetVerifyModal({ clearPending: false });
                pendingResetVerifyForm = null;

                if (typeof formToSubmit.requestSubmit === 'function') {
                    formToSubmit.requestSubmit();
                } else {
                    formToSubmit.submit();
                }
            }

            function clearDocDropdownCloseTimer(dropdown) {
                if (!dropdown || !dropdown._docDdCloseTimer) {
                    return;
                }
                clearTimeout(dropdown._docDdCloseTimer);
                dropdown._docDdCloseTimer = null;
            }

            function closeSingleDocDropdown(dropdown) {
                if (!dropdown) {
                    return;
                }
                const panel = dropdown.querySelector('[data-doc-dd-panel]');
                const toggle = dropdown.querySelector('[data-doc-dd-toggle]');
                const searchInput = dropdown.querySelector('[data-doc-dd-search]');
                if (!panel) {
                    return;
                }

                const isCurrentlyOpen = dropdown.classList.contains('is-open') || dropdown.classList.contains('is-closing');
                if (!isCurrentlyOpen) {
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                    return;
                }

                clearDocDropdownCloseTimer(dropdown);
                dropdown.classList.remove('is-open');
                dropdown.classList.add('is-closing');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
                if (searchInput) {
                    searchInput.value = '';
                    filterDocDropdown(dropdown, '');
                }

                dropdown._docDdCloseTimer = setTimeout(() => {
                    if (!dropdown.classList.contains('is-open')) {
                        panel.setAttribute('hidden', 'hidden');
                        dropdown.classList.remove('is-closing');
                    }
                    dropdown._docDdCloseTimer = null;
                }, docDropdownTransitionMs);
            }

            function closeDocDropdowns(scope = page) {
                if (!scope) {
                    return;
                }

                const dropdowns = [];
                if (scope.matches && scope.matches('[data-doc-dd]')) {
                    dropdowns.push(scope);
                }
                if (scope.querySelectorAll) {
                    scope.querySelectorAll('[data-doc-dd]').forEach((dropdown) => dropdowns.push(dropdown));
                }

                Array.from(new Set(dropdowns)).forEach((dropdown) => closeSingleDocDropdown(dropdown));
            }

            function updateDocDropdownStickyOffsets(dropdown) {
                if (!dropdown) {
                    return;
                }
                const panel = dropdown.querySelector('[data-doc-dd-panel]');
                if (!panel || panel.hasAttribute('hidden')) {
                    return;
                }

                const topbar = panel.querySelector('[data-doc-dd-topbar]');
                const topbarHeight = topbar
                    ? Math.ceil(topbar.getBoundingClientRect().height + (Number.parseFloat(getComputedStyle(topbar).marginBottom || '0') || 0))
                    : 0;
                panel.style.setProperty('--doc-dd-sticky-top', `${topbarHeight}px`);
            }

            function openDocDropdown(dropdown) {
                if (!dropdown || dropdown.classList.contains('is-disabled')) {
                    return;
                }

                page.querySelectorAll('[data-doc-dd]').forEach((item) => {
                    if (item !== dropdown) {
                        closeSingleDocDropdown(item);
                    }
                });

                const panel = dropdown.querySelector('[data-doc-dd-panel]');
                const toggle = dropdown.querySelector('[data-doc-dd-toggle]');
                const searchInput = dropdown.querySelector('[data-doc-dd-search]');
                if (!panel) {
                    return;
                }

                clearDocDropdownCloseTimer(dropdown);
                dropdown.classList.remove('is-closing');
                panel.removeAttribute('hidden');
                updateDocDropdownStickyOffsets(dropdown);
                filterDocDropdown(dropdown, searchInput ? searchInput.value : '');
                requestAnimationFrame(() => {
                    dropdown.classList.add('is-open');
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                    requestAnimationFrame(() => {
                        updateDocDropdownStickyOffsets(dropdown);
                    });
                });
                if (searchInput && !searchInput.disabled) {
                    setTimeout(() => searchInput.focus(), 80);
                }
            }

            function filterDocDropdown(dropdown, queryText) {
                if (!dropdown) {
                    return;
                }

                const options = Array.from(dropdown.querySelectorAll('[data-doc-dd-option]'));
                if (!options.length) {
                    return;
                }

                const query = (queryText || '').trim().toLowerCase();
                let visibleCount = 0;
                options.forEach((option) => {
                    const text = String(option.getAttribute('data-doc-dd-option-text') || '').toLowerCase();
                    const matched = !query || text.includes(query);
                    option.hidden = !matched;
                    if (matched) {
                        visibleCount += 1;
                    }
                });

                dropdown.querySelectorAll('[data-doc-dd-subgroup]').forEach((subgroup) => {
                    const hasVisibleOption = subgroup.querySelectorAll('[data-doc-dd-option]:not([hidden])').length > 0;
                    subgroup.hidden = !hasVisibleOption;
                });

                dropdown.querySelectorAll('[data-doc-dd-group]').forEach((group) => {
                    const hasVisibleSubgroup = group.querySelectorAll('[data-doc-dd-subgroup]:not([hidden])').length > 0;
                    group.hidden = !hasVisibleSubgroup;
                });

                const empty = dropdown.querySelector('[data-doc-dd-empty]');
                if (empty) {
                    empty.hidden = visibleCount > 0;
                }
            }

            function updateDocDropdownSelection(dropdown) {
                if (!dropdown) {
                    return;
                }

                let selectedCount = 0;
                dropdown.querySelectorAll('[data-doc-dd-check]').forEach((check) => {
                    const selected = !!check.checked;
                    if (selected) {
                        selectedCount++;
                    }
                    const option = check.closest('[data-doc-dd-option]');
                    if (option) {
                        option.classList.toggle('is-selected', selected);
                    }
                });

                const textNode = dropdown.querySelector('[data-doc-dd-text]');
                if (textNode) {
                    textNode.textContent = selectedCount > 0
                        ? `${selectedCount} dokumen dipilih`
                        : '- pilih berkas -';
                }

                const wrap = dropdown.closest('[data-edit-pane-wrap]');
                if (wrap) {
                    updateLevelTabState(wrap);
                    updateLevelDocList(wrap);
                    updateSaveButtonState(wrap);
                }
            }

            function updateLevelTabState(wrap) {
                if (!wrap) {
                    return;
                }

                const levelTrigger = wrap.querySelector('[data-level-tab-btn]');
                if (!levelTrigger) {
                    return;
                }

                const selectedCount = wrap.querySelectorAll('[data-doc-dd-check]:checked').length;
                const hasSelectedDoc = selectedCount > 0;

                if (hasSelectedDoc) {
                    levelTrigger.disabled = false;
                    levelTrigger.setAttribute('aria-disabled', 'false');
                    levelTrigger.removeAttribute('title');
                    return;
                }

                levelTrigger.disabled = true;
                levelTrigger.setAttribute('aria-disabled', 'true');
                levelTrigger.setAttribute('title', 'Pilih minimal 1 dokumen di tab Bukti Dukung.');

                if (levelTrigger.classList.contains('is-active')) {
                    const supportTrigger = wrap.querySelector('[data-support-tab-btn]');
                    if (supportTrigger) {
                        activateEditPane(wrap, supportTrigger.getAttribute('data-edit-pane-trigger'));
                    }
                }
            }

            function updateSaveButtonState(wrap) {
                if (!wrap) {
                    return;
                }

                const form = wrap.closest('form');
                const saveButton = form ? form.querySelector('[data-edit-save-btn]') : null;
                if (!saveButton) {
                    return;
                }

                const isLocked = String(saveButton.getAttribute('data-lock-disabled') || '0') === '1';
                if (isLocked) {
                    saveButton.disabled = true;
                    saveButton.setAttribute('aria-disabled', 'true');
                    return;
                }

                const selectedCount = wrap.querySelectorAll('[data-doc-dd-check]:checked').length;
                const canSave = selectedCount > 0;
                saveButton.disabled = !canSave;
                saveButton.setAttribute('aria-disabled', canSave ? 'false' : 'true');
                if (canSave) {
                    saveButton.removeAttribute('title');
                } else {
                    saveButton.setAttribute('title', 'Pilih minimal 1 dokumen pada tab Bukti Dukung.');
                }
            }

            function updateLevelDocList(wrap) {
                if (!wrap) {
                    return;
                }

                const list = wrap.querySelector('[data-level-doc-list]');
                const empty = wrap.querySelector('[data-level-doc-empty]');
                if (!list || !empty) {
                    return;
                }

                const selectedChecks = Array.from(wrap.querySelectorAll('[data-doc-dd-check]:checked'));
                list.innerHTML = '';

                if (selectedChecks.length === 0) {
                    empty.hidden = false;
                    return;
                }

                empty.hidden = true;
                selectedChecks.forEach((check) => {
                    const option = check.closest('[data-doc-dd-option]');
                    const labelNode = option ? option.querySelector('.keg-doc-dd-option-label') : null;
                    const labelText = (labelNode ? labelNode.textContent : '').trim() || 'Dokumen';
                    const hrefNode = option ? option.querySelector('.keg-doc-dd-view[href]') : null;
                    const href = hrefNode ? String(hrefNode.getAttribute('href') || '') : '';

                    const item = document.createElement('div');
                    item.className = 'keg-level-doc-item';

                    const name = document.createElement('span');
                    name.className = 'keg-level-doc-name';
                    name.textContent = labelText;
                    item.appendChild(name);

                    if (href) {
                        const link = document.createElement('a');
                        link.className = 'keg-level-doc-view';
                        link.href = href;
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer';
                        link.title = 'Lihat dokumen';
                        link.textContent = 'Lihat';
                        item.appendChild(link);
                    } else {
                        const disabled = document.createElement('span');
                        disabled.className = 'keg-level-doc-view is-disabled';
                        disabled.setAttribute('aria-disabled', 'true');
                        disabled.textContent = 'Lihat';
                        item.appendChild(disabled);
                    }

                    list.appendChild(item);
                });
            }

            function syncDocDropdowns(scope = page) {
                if (!scope) {
                    return;
                }
                scope.querySelectorAll('[data-doc-dd]').forEach((dropdown) => {
                    updateDocDropdownSelection(dropdown);
                    filterDocDropdown(dropdown, '');

                    const panel = dropdown.querySelector('[data-doc-dd-panel]');
                    const toggle = dropdown.querySelector('[data-doc-dd-toggle]');
                    const searchInput = dropdown.querySelector('[data-doc-dd-search]');
                    if (panel) {
                        panel.setAttribute('hidden', 'hidden');
                    }
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    dropdown.classList.remove('is-open');
                    dropdown.classList.remove('is-closing');
                    clearDocDropdownCloseTimer(dropdown);
                });
            }

            const rowModeTransitionMs = 180;

            function clearRowModeSwitchTimer(row) {
                if (!row || !row._rowModeSwitchTimer) {
                    return;
                }
                clearTimeout(row._rowModeSwitchTimer);
                row._rowModeSwitchTimer = null;
            }

            function clearRowModeSwitchClasses(pane) {
                if (!pane) {
                    return;
                }
                pane.classList.remove('is-entering', 'is-enter-active', 'is-leaving', 'is-leave-active');
            }

            function activateRowMode(row, requestedMode = 'edit', options = {}) {
                if (!row) {
                    return;
                }
                clearRowModeSwitchTimer(row);
                const panes = Array.from(row.querySelectorAll('[data-row-mode-pane]'));
                if (!panes.length) {
                    row.setAttribute('data-active-mode', 'edit');
                    return;
                }
                const animate = !!options.animate;
                const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                const availableModes = new Set(panes.map((pane) => pane.getAttribute('data-row-mode-pane')));
                const activeMode = availableModes.has(requestedMode)
                    ? requestedMode
                    : (availableModes.has('edit') ? 'edit' : (panes[0].getAttribute('data-row-mode-pane') || 'edit'));

                const triggers = Array.from(row.querySelectorAll('[data-row-mode-trigger]'));

                triggers.forEach((trigger) => {
                    const mode = trigger.getAttribute('data-row-mode-trigger');
                    const active = mode === activeMode;
                    trigger.classList.toggle('is-active', active);
                    trigger.setAttribute('aria-selected', active ? 'true' : 'false');
                });

                const activePane = panes.find((pane) => pane.getAttribute('data-row-mode-pane') === activeMode) || null;
                const currentMode = row.getAttribute('data-active-mode') || '';
                const currentPane = panes.find((pane) => pane.getAttribute('data-row-mode-pane') === currentMode && !pane.hasAttribute('hidden'))
                    || panes.find((pane) => pane.classList.contains('is-active') && !pane.hasAttribute('hidden'))
                    || null;
                const shouldAnimate = animate
                    && !reduceMotion
                    && !!activePane
                    && !!currentPane
                    && activePane !== currentPane;

                if (!shouldAnimate || !activePane) {
                    panes.forEach((pane) => {
                        clearRowModeSwitchClasses(pane);
                        const mode = pane.getAttribute('data-row-mode-pane');
                        const active = mode === activeMode;
                        pane.classList.toggle('is-active', active);
                        if (active) {
                            pane.removeAttribute('hidden');
                        } else {
                            pane.setAttribute('hidden', 'hidden');
                        }
                    });
                    row.setAttribute('data-active-mode', activeMode);
                    closeDocDropdowns(row);
                    return;
                }

                panes.forEach((pane) => {
                    if (pane === currentPane || pane === activePane) {
                        return;
                    }
                    clearRowModeSwitchClasses(pane);
                    pane.classList.remove('is-active');
                    pane.setAttribute('hidden', 'hidden');
                });

                clearRowModeSwitchClasses(currentPane);
                clearRowModeSwitchClasses(activePane);
                activePane.removeAttribute('hidden');
                activePane.classList.add('is-active', 'is-entering');
                currentPane.classList.add('is-leaving');

                void activePane.offsetHeight;
                requestAnimationFrame(() => {
                    currentPane.classList.add('is-leave-active');
                    activePane.classList.remove('is-entering');
                    activePane.classList.add('is-enter-active');
                });

                row._rowModeSwitchTimer = setTimeout(() => {
                    currentPane.classList.remove('is-active');
                    currentPane.setAttribute('hidden', 'hidden');
                    clearRowModeSwitchClasses(currentPane);

                    activePane.classList.add('is-active');
                    clearRowModeSwitchClasses(activePane);

                    row._rowModeSwitchTimer = null;
                }, rowModeTransitionMs);

                row.setAttribute('data-active-mode', activeMode);
                closeDocDropdowns(row);
            }

            function resetRowModes(scope = page) {
                if (!scope) {
                    return;
                }
                const rows = [];
                if (scope.matches && scope.matches('.editor-row')) {
                    rows.push(scope);
                }
                if (scope.querySelectorAll) {
                    scope.querySelectorAll('.editor-row').forEach((row) => rows.push(row));
                }

                Array.from(new Set(rows)).forEach((row) => activateRowMode(row, 'edit', { animate: false }));
            }

            function getLevelValidationItems(form) {
                if (!form || !form.querySelectorAll) {
                    return [];
                }

                return Array.from(form.querySelectorAll('[data-level-validate-btn]'))
                    .map((button) => {
                        const level = Number.parseInt(button.getAttribute('data-level') || '0', 10);
                        const fieldWrap = button.closest('[data-level-validate-field]');
                        const hiddenInput = fieldWrap ? fieldWrap.querySelector('[data-level-validate-input]') : null;
                        return {
                            button,
                            level,
                            fieldWrap,
                            hiddenInput,
                        };
                    })
                    .filter((item) => Number.isFinite(item.level) && item.level > 0)
                    .sort((a, b) => a.level - b.level);
            }

            function isLevelValidationItemChecked(item) {
                if (!item) {
                    return false;
                }
                if (item.hiddenInput) {
                    return String(item.hiddenInput.value || '0') === '1';
                }
                return item.button ? item.button.classList.contains('is-validated') : false;
            }

            function clearValidatePaneToneClasses(paneWrap) {
                if (!paneWrap) {
                    return;
                }
                paneWrap.classList.remove(
                    'validate-tone-l1',
                    'validate-tone-l2',
                    'validate-tone-l3',
                    'validate-tone-l4',
                    'validate-tone-l5'
                );
            }

            function setValidatePaneTone(form, options = {}) {
                if (!form) {
                    return;
                }
                const paneWrap = form.querySelector('[data-validate-pane-wrap]');
                if (!paneWrap) {
                    return;
                }

                const items = Array.isArray(options.items) ? options.items : getLevelValidationItems(form);
                const processingItem = items.find((item) => item.button && item.button.classList.contains('is-processing'));
                const processingLevel = processingItem
                    ? processingItem.level
                    : Number.parseInt(options.processingLevel || '0', 10);

                let toneLevel = 0;
                if (Number.isFinite(processingLevel) && processingLevel >= 1 && processingLevel <= 5) {
                    toneLevel = processingLevel;
                } else {
                    items.forEach((item) => {
                        if (isLevelValidationItemChecked(item)) {
                            toneLevel = Math.max(toneLevel, item.level);
                        }
                    });
                }

                clearValidatePaneToneClasses(paneWrap);
                if (toneLevel >= 1 && toneLevel <= 5) {
                    paneWrap.classList.add(`validate-tone-l${toneLevel}`);
                }
            }

            function applyLevelValidationVisualState(item, checked) {
                if (!item || !item.button) {
                    return;
                }
                if (item.hiddenInput) {
                    item.hiddenInput.value = checked ? '1' : '0';
                }
                item.button.classList.remove('is-processing');
                item.button.classList.toggle('is-validated', checked);
                item.button.removeAttribute('aria-busy');
                item.button.setAttribute('aria-pressed', checked ? 'true' : 'false');
                item.button.setAttribute('title', checked ? 'Klik untuk batalkan verifikasi level ini' : 'Klik untuk verifikasi level ini');
                if (item.fieldWrap) {
                    item.fieldWrap.classList.remove('is-processing');
                    item.fieldWrap.classList.toggle('is-validated', checked);
                }
            }

            function syncAggregateLevelValidationButtons(scope = page) {
                if (!scope || !scope.querySelectorAll) {
                    return;
                }

                const forms = [];
                if (scope.matches && scope.matches('form')) {
                    forms.push(scope);
                }
                if (scope.querySelectorAll) {
                    scope.querySelectorAll('form').forEach((form) => forms.push(form));
                }

                Array.from(new Set(forms)).forEach((form) => {
                    const items = getLevelValidationItems(form);
                    if (!items.length) {
                        setValidatePaneTone(form, { items: [] });
                        return;
                    }

                    items.forEach((item) => {
                        if (!item.button.classList.contains('is-processing')) {
                            applyLevelValidationVisualState(item, isLevelValidationItemChecked(item));
                        }
                    });

                    items.forEach((item) => {
                        const previousItem = item.level > 1
                            ? items.find((candidate) => candidate.level === (item.level - 1))
                            : null;
                        const previousValidated = item.level === 1
                            ? true
                            : (previousItem ? isLevelValidationItemChecked(previousItem) : false);
                        const checked = isLevelValidationItemChecked(item);
                        const shouldDisable = item.level > 1 && !previousValidated && !checked;

                        if (!item.button.classList.contains('is-processing')) {
                            item.button.disabled = shouldDisable;
                        }
                        item.button.setAttribute('aria-disabled', shouldDisable ? 'true' : 'false');

                        if (shouldDisable) {
                            item.button.setAttribute('title', `Verifikasi Level ${item.level - 1} terlebih dahulu.`);
                        }
                    });

                    setValidatePaneTone(form, { items });
                });
            }

            function triggerLevelFieldValidation(button) {
                if (!button || button.classList.contains('is-processing')) {
                    return;
                }

                const form = button.closest('form');
                const items = getLevelValidationItems(form);
                const currentItem = items.find((item) => item.button === button);
                if (!currentItem) {
                    return;
                }

                if (button.classList.contains('is-validated')) {
                    items
                        .filter((item) => item.level >= currentItem.level)
                        .forEach((item) => applyLevelValidationVisualState(item, false));
                    syncAggregateLevelValidationButtons(form);
                    return;
                }

                if (currentItem.level > 1) {
                    const previousItem = items.find((item) => item.level === (currentItem.level - 1));
                    const previousValidated = previousItem ? isLevelValidationItemChecked(previousItem) : false;
                    if (!previousValidated) {
                        if (typeof window.pushToast === 'function') {
                            window.pushToast({
                                type: 'error',
                                title: 'Urutan Verifikasi',
                                message: `Verifikasi Level ${currentItem.level - 1} terlebih dahulu sebelum Level ${currentItem.level}.`,
                                duration: 4200,
                            });
                        }
                        return;
                    }
                }

                button.classList.add('is-processing');
                button.setAttribute('aria-busy', 'true');
                button.setAttribute('aria-pressed', 'false');
                button.disabled = true;
                if (currentItem.fieldWrap) {
                    currentItem.fieldWrap.classList.remove('is-validated');
                    currentItem.fieldWrap.classList.add('is-processing');
                }
                setValidatePaneTone(form, { items, processingLevel: currentItem.level });

                if (button._validateTimer) {
                    clearTimeout(button._validateTimer);
                }

                button._validateTimer = setTimeout(() => {
                    applyLevelValidationVisualState(currentItem, true);
                    button.disabled = false;
                    syncAggregateLevelValidationButtons(form);
                    button._validateTimer = null;
                }, 700);
            }

            function activateEditPane(wrap, paneId) {
                if (!wrap || !paneId) {
                    return;
                }
                const paneTriggers = wrap.querySelectorAll('[data-edit-pane-trigger]');
                const panes = wrap.querySelectorAll('[data-edit-pane]');

                paneTriggers.forEach((trigger) => {
                    const active = trigger.getAttribute('data-edit-pane-trigger') === paneId;
                    trigger.classList.toggle('is-active', active);
                    trigger.setAttribute('aria-selected', active ? 'true' : 'false');
                });

                panes.forEach((pane) => {
                    const active = pane.id === paneId;
                    pane.classList.toggle('is-active', active);
                    if (active) {
                        pane.removeAttribute('hidden');
                    } else {
                        pane.setAttribute('hidden', 'hidden');
                    }
                });
            }

            function resetEditPanes(scope) {
                if (!scope) {
                    return;
                }
                scope.querySelectorAll('[data-edit-pane-wrap]').forEach((wrap) => {
                    const firstTrigger = wrap.querySelector('[data-edit-pane-trigger]');
                    if (!firstTrigger) {
                        return;
                    }
                    activateEditPane(wrap, firstTrigger.getAttribute('data-edit-pane-trigger'));
                    updateLevelTabState(wrap);
                    updateLevelDocList(wrap);
                    updateSaveButtonState(wrap);
                });
            }

            function clearEditorRowTimer(row) {
                if (!row || !row._editorRowTimer) {
                    return;
                }
                clearTimeout(row._editorRowTimer);
                row._editorRowTimer = null;
            }

            function resetEditorCollapseStyles(collapse) {
                if (!collapse) {
                    return;
                }
                collapse.style.removeProperty('max-height');
                collapse.style.removeProperty('opacity');
                collapse.style.removeProperty('transform');
                collapse.style.removeProperty('overflow');
            }

            function openEditorRow(row) {
                if (!row) {
                    return;
                }
                const collapse = row.querySelector('[data-editor-collapse]');
                clearEditorRowTimer(row);
                row.removeAttribute('hidden');

                if (!collapse) {
                    row.classList.add('is-open');
                    return;
                }

                row.classList.remove('is-closing');
                row.classList.remove('is-open');
                row.classList.add('is-opening');

                collapse.style.overflow = 'hidden';
                collapse.style.maxHeight = '0px';
                collapse.style.opacity = '0';
                collapse.style.transform = 'translateY(-8px)';
                void collapse.offsetHeight;

                const targetHeight = Math.max(1, collapse.scrollHeight);
                requestAnimationFrame(() => {
                    collapse.style.maxHeight = `${targetHeight}px`;
                    collapse.style.opacity = '1';
                    collapse.style.transform = 'translateY(0)';
                });

                row._editorRowTimer = setTimeout(() => {
                    row.classList.remove('is-opening');
                    row.classList.add('is-open');
                    collapse.style.maxHeight = 'none';
                    collapse.style.overflow = 'visible';
                    collapse.style.opacity = '1';
                    collapse.style.transform = 'translateY(0)';
                    row._editorRowTimer = null;
                }, editorRowTransitionMs);
            }

            function closeEditorRow(row, options = {}) {
                if (!row) {
                    return;
                }
                const immediate = !!options.immediate;
                const collapse = row.querySelector('[data-editor-collapse]');
                const mainRow = row.previousElementSibling;
                if (mainRow && mainRow.tagName === 'TR') {
                    mainRow.classList.remove('active');
                }

                clearEditorRowTimer(row);
                if (row.hasAttribute('hidden')) {
                    row.classList.remove('is-open');
                    row.classList.remove('is-opening');
                    row.classList.remove('is-closing');
                    row.removeAttribute('data-active-mode');
                    resetEditorCollapseStyles(collapse);
                    return;
                }

                if (!collapse || immediate) {
                    row.classList.remove('is-open');
                    row.classList.remove('is-opening');
                    row.classList.remove('is-closing');
                    row.setAttribute('hidden', 'hidden');
                    row.removeAttribute('data-active-mode');
                    resetEditorCollapseStyles(collapse);
                    return;
                }

                const currentHeight = Math.max(1, collapse.scrollHeight);
                collapse.style.overflow = 'hidden';
                collapse.style.maxHeight = `${currentHeight}px`;
                collapse.style.opacity = '1';
                collapse.style.transform = 'translateY(0)';
                void collapse.offsetHeight;

                row.classList.remove('is-open');
                row.classList.remove('is-opening');
                row.classList.add('is-closing');

                requestAnimationFrame(() => {
                    collapse.style.maxHeight = '0px';
                    collapse.style.opacity = '0';
                    collapse.style.transform = 'translateY(-8px)';
                });

                row._editorRowTimer = setTimeout(() => {
                    row.classList.remove('is-closing');
                    row.setAttribute('hidden', 'hidden');
                    row.removeAttribute('data-active-mode');
                    resetEditorCollapseStyles(collapse);
                    row._editorRowTimer = null;
                }, editorRowTransitionMs);
            }

            function closeRows(options = {}) {
                const exceptRow = options.exceptRow || null;
                const immediate = !!options.immediate;
                page.querySelectorAll('.editor-row').forEach((row) => {
                    if (exceptRow && row === exceptRow) {
                        return;
                    }
                    closeEditorRow(row, { immediate });
                });
                closeDocDropdowns(page);
            }

            function scrollEditPaneIntoView(target) {
                if (!target) {
                    return false;
                }

                const topbar = document.querySelector('.legacy-page .headnav');
                const topOffset = topbar ? Math.max(16, Math.round(topbar.getBoundingClientRect().height + 12)) : 16;
                const rect = target.getBoundingClientRect();
                const viewportTop = topOffset;
                const viewportBottom = window.innerHeight - 20;

                if (rect.top >= viewportTop && rect.bottom <= viewportBottom) {
                    return false;
                }

                let nextTop = window.scrollY + rect.top - topOffset;
                if (rect.bottom > viewportBottom) {
                    const minimalTop = window.scrollY + (rect.bottom - viewportBottom);
                    nextTop = Math.min(nextTop, minimalTop);
                }

                window.scrollTo({
                    top: Math.max(0, Math.round(nextTop)),
                    behavior: 'smooth',
                });
                return true;
            }

            function isNumericDisplay(text) {
                return /^-?\d+(?:[.,]\d+)?$/.test((text || '').trim());
            }

            function runCountUpAnimations(scope = page) {
                if (!scope || !scope.querySelectorAll) {
                    return;
                }

                const prefersReducedMotion = window.matchMedia
                    && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                const countNodes = scope.querySelectorAll('.keg-chip .value, .pill-level, .pill-skor');

                countNodes.forEach((node, idx) => {
                    if (!node || node.dataset.countupDone === '1') {
                        return;
                    }

                    const rawText = (node.textContent || '').trim();
                    if (!isNumericDisplay(rawText)) {
                        return;
                    }

                    const normalized = rawText.replace(',', '.');
                    const targetValue = Number.parseFloat(normalized);
                    if (!Number.isFinite(targetValue)) {
                        return;
                    }

                    const decimals = normalized.includes('.')
                        ? normalized.split('.')[1].length
                        : 0;
                    const finalText = decimals > 0
                        ? targetValue.toFixed(decimals)
                        : String(Math.round(targetValue));

                    if (prefersReducedMotion) {
                        node.textContent = finalText;
                        node.dataset.countupDone = '1';
                        return;
                    }

                    const duration = Math.min(2300, Math.max(1200, 1400 + (Math.abs(targetValue) * 170)));
                    const delay = Math.min(idx * 90, 420);
                    const startAt = performance.now() + delay;

                    node.textContent = decimals > 0 ? (0).toFixed(decimals) : '0';

                    const tick = (now) => {
                        if (now < startAt) {
                            requestAnimationFrame(tick);
                            return;
                        }

                        const progress = Math.min((now - startAt) / duration, 1);
                        const easedProgress = 1 - Math.pow(1 - progress, 3);
                        const currentValue = targetValue * easedProgress;

                        node.textContent = decimals > 0
                            ? currentValue.toFixed(decimals)
                            : String(Math.round(currentValue));

                        if (progress < 1) {
                            requestAnimationFrame(tick);
                            return;
                        }

                        node.textContent = finalText;
                        node.dataset.countupDone = '1';
                    };

                    requestAnimationFrame(tick);
                });
            }

            function resetCountUpState(scope = page) {
                if (!scope || !scope.querySelectorAll) {
                    return;
                }
                scope.querySelectorAll('.keg-chip .value, .pill-level, .pill-skor').forEach((node) => {
                    if (!node || !node.dataset) {
                        return;
                    }
                    delete node.dataset.countupDone;
                });
            }

            function reinitCountUpForCurrentPage() {
                const activePage = document.getElementById('kegPage');
                if (!activePage) {
                    return;
                }
                resetCountUpState(activePage);
                runCountUpAnimations(activePage);
            }

            syncDocDropdowns(page);
            resetRowModes(page);
            syncModalBodyLock();
            syncAggregateLevelValidationButtons(page);
            runCountUpAnimations(page);
            document.addEventListener('livewire:navigated', reinitCountUpForCurrentPage);

            page.addEventListener('click', (event) => {
                const clearRowTrigger = event.target.closest('[data-clear-row-btn]');
                if (clearRowTrigger) {
                    event.preventDefault();
                    event.stopPropagation();
                    const form = clearRowTrigger.closest('form');
                    if (!form) {
                        return;
                    }
                    openClearRowModal(form);
                    return;
                }

                const resetVerifyTrigger = event.target.closest('[data-verify-reset-btn]');
                if (resetVerifyTrigger) {
                    event.preventDefault();
                    event.stopPropagation();
                    const form = resetVerifyTrigger.closest('form');
                    if (!form) {
                        return;
                    }
                    openResetVerifyModal(form);
                    return;
                }

                const docDdToggle = event.target.closest('[data-doc-dd-toggle]');
                if (docDdToggle) {
                    event.preventDefault();
                    const dropdown = docDdToggle.closest('[data-doc-dd]');
                    if (!dropdown || docDdToggle.disabled) {
                        return;
                    }
                    if (dropdown.classList.contains('is-open')) {
                        closeDocDropdowns(dropdown);
                    } else {
                        openDocDropdown(dropdown);
                    }
                    return;
                }

                const levelValidateBtn = event.target.closest('[data-level-validate-btn]');
                if (levelValidateBtn) {
                    event.preventDefault();
                    triggerLevelFieldValidation(levelValidateBtn);
                    return;
                }

                const editPaneTrigger = event.target.closest('[data-edit-pane-trigger]');
                if (editPaneTrigger) {
                    event.preventDefault();
                    if (editPaneTrigger.disabled) {
                        return;
                    }
                    const wrap = editPaneTrigger.closest('[data-edit-pane-wrap]');
                    if (!wrap) {
                        return;
                    }
                    activateEditPane(wrap, editPaneTrigger.getAttribute('data-edit-pane-trigger'));
                    return;
                }

                const rowTrigger = event.target.closest('[data-toggle-row]');
                if (rowTrigger) {
                    event.preventDefault();
                    const rowId = rowTrigger.getAttribute('data-toggle-row');
                    const requestedMode = rowTrigger.getAttribute('data-row-mode') || 'edit';
                    const panelRow = rowId ? page.querySelector(`#row-${rowId}`) : null;
                    if (!panelRow) {
                        return;
                    }
                    const isOpen = !panelRow.hasAttribute('hidden') && !panelRow.classList.contains('is-closing');
                    if (isOpen) {
                        const currentMode = panelRow.getAttribute('data-active-mode') || 'edit';
                        if (currentMode !== requestedMode) {
                            activateRowMode(panelRow, requestedMode, { animate: true });
                            requestAnimationFrame(() => {
                                scrollEditPaneIntoView(panelRow);
                            });
                            return;
                        }
                        closeEditorRow(panelRow);
                        return;
                    }

                    closeRows({ exceptRow: panelRow });
                    openEditorRow(panelRow);
                    resetEditPanes(panelRow);
                    resetRowModes(panelRow);
                    activateRowMode(panelRow, requestedMode);
                    const mainRow = rowTrigger.closest('tr');
                    if (mainRow) {
                        mainRow.classList.add('active');
                    }

                    const focusPaneId = rowTrigger.getAttribute('data-focus-pane');
                    const focusPane = focusPaneId ? page.querySelector(`#${focusPaneId}`) : panelRow;
                    requestAnimationFrame(() => {
                        scrollEditPaneIntoView(focusPane || panelRow);
                    });
                    return;
                }
            });

            page.addEventListener('change', (event) => {
                const docDdCheck = event.target.closest('[data-doc-dd-check]');
                if (!docDdCheck) {
                    return;
                }
                const dropdown = docDdCheck.closest('[data-doc-dd]');
                updateDocDropdownSelection(dropdown);
            });

            page.addEventListener('input', (event) => {
                const searchInput = event.target.closest('[data-doc-dd-search]');
                if (!searchInput) {
                    return;
                }
                const dropdown = searchInput.closest('[data-doc-dd]');
                filterDocDropdown(dropdown, searchInput.value);
            });

            document.addEventListener('click', (event) => {
                const clearRowConfirmTrigger = event.target.closest('[data-clear-row-confirm]');
                if (clearRowConfirmTrigger) {
                    event.preventDefault();
                    event.stopPropagation();
                    confirmClearRow();
                    return;
                }

                const clearRowCloseTrigger = event.target.closest('[data-clear-row-modal-close]');
                if (clearRowCloseTrigger) {
                    event.preventDefault();
                    event.stopPropagation();
                    closeClearRowModal();
                    return;
                }

                const resetVerifyConfirmTrigger = event.target.closest('[data-reset-verify-confirm]');
                if (resetVerifyConfirmTrigger) {
                    event.preventDefault();
                    event.stopPropagation();
                    confirmResetVerify();
                    return;
                }

                const resetVerifyCloseTrigger = event.target.closest('[data-reset-verify-modal-close]');
                if (resetVerifyCloseTrigger) {
                    event.preventDefault();
                    event.stopPropagation();
                    closeResetVerifyModal();
                    return;
                }

                const infoOpenTrigger = event.target.closest('[data-info-modal-open]');
                if (infoOpenTrigger) {
                    event.preventDefault();
                    highlightInfoTriggerArea(infoOpenTrigger);
                    openInfoModal();
                    return;
                }

                const infoCloseTrigger = event.target.closest('[data-info-modal-close]');
                if (infoCloseTrigger) {
                    event.preventDefault();
                    closeInfoModal();
                    return;
                }

                if (!event.target.closest('[data-doc-dd]')) {
                    closeDocDropdowns(page);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') {
                    return;
                }

                if (infoModal && !infoModal.hasAttribute('hidden')) {
                    closeInfoModal();
                }
                if (resetVerifyModal && !resetVerifyModal.hasAttribute('hidden')) {
                    closeResetVerifyModal();
                }
                if (clearRowModal && !clearRowModal.hasAttribute('hidden')) {
                    closeClearRowModal();
                }
                closeDocDropdowns(page);
            });

            window.addEventListener('resize', () => {
                syncInfoModalToViewport();
                syncResetVerifyModalToViewport();
                syncClearRowModalToViewport();
                page.querySelectorAll('[data-doc-dd].is-open').forEach((dropdown) => {
                    updateDocDropdownStickyOffsets(dropdown);
                });
            });
            window.addEventListener('scroll', () => {
                syncInfoModalToViewport();
                syncResetVerifyModalToViewport();
                syncClearRowModalToViewport();
            }, { passive: true });
            window.addEventListener('pageshow', () => {
                syncInfoModalToViewport();
                syncResetVerifyModalToViewport();
                syncClearRowModalToViewport();
            });
        })();
    </script>
@endpush


