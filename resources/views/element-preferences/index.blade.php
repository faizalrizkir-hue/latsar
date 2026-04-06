@extends('layouts.dashboard-shell')

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/element-preferences.css') }}">
@endpush

@section('content')
    @php
        $elements = collect((array) ($structure['elements'] ?? []))
            ->filter(fn ($item) => is_array($item))
            ->values();
        $progressArchives = collect((array) ($progressArchives ?? []))
            ->filter(fn ($item) => is_array($item))
            ->values();
        $progressArchiveLoadLogs = collect((array) ($progressArchiveLoadLogs ?? []))
            ->filter(fn ($item) => is_array($item))
            ->values();
        $activeBudgetYear = (int) old('budget_year', now('Asia/Jakarta')->year);
        $formatPercent = static function (float $weight): string {
            $value = rtrim(rtrim(number_format($weight * 100, 2, '.', ''), '0'), '.');

            return $value === '' ? '0' : $value;
        };
        $formatArchiveDate = static function ($value): string {
            if (!($value instanceof \Carbon\CarbonInterface)) {
                return '-';
            }

            return $value->copy()->timezone('Asia/Jakarta')->translatedFormat('d M Y H:i');
        };
        $normalizeLevelDescriptions = static function ($source): array {
            $levels = [
                1 => '',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
            ];

            if (!is_array($source)) {
                return $levels;
            }

            foreach ($source as $key => $item) {
                $level = null;
                $description = '';

                if (is_array($item)) {
                    $rawLevel = $item['level'] ?? $key;
                    if (is_numeric($rawLevel)) {
                        $level = (int) $rawLevel;
                    }

                    $description = trim((string) ($item['description'] ?? ''));
                } else {
                    if (is_numeric($key)) {
                        $level = (int) $key;
                    }
                    $description = trim((string) $item);
                }

                if ($level === null || $level < 1 || $level > 5) {
                    continue;
                }

                $levels[$level] = $description;
            }

            return $levels;
        };
        $stripElementPrefix = static function (string $value): string {
            $normalized = trim($value);
            if ($normalized === '') {
                return '';
            }

            $stripped = preg_replace('/^\s*element\s*\d+\s*[:\-]?\s*/i', '', $normalized);
            $stripped = is_string($stripped) ? trim($stripped) : $normalized;

            return $stripped !== '' ? $stripped : $normalized;
        };
        $stripSubtopicPrefix = static function (string $value): string {
            $normalized = trim($value);
            if ($normalized === '') {
                return '';
            }

            $stripped = preg_replace('/^\s*sub\s*topik\s*\d+\s*[-:]?\s*/i', '', $normalized);
            $stripped = is_string($stripped) ? trim($stripped) : $normalized;

            return $stripped !== '' ? $stripped : $normalized;
        };
        $formatRestoredByTable = static function (array $restoredByTable): string {
            $parts = [];
            foreach ($restoredByTable as $table => $count) {
                $tableName = trim((string) $table);
                if ($tableName === '') {
                    continue;
                }

                $rowCount = (int) $count;
                if ($rowCount <= 0) {
                    continue;
                }

                $parts[] = $tableName.' ('.$rowCount.')';
            }

            if (count($parts) === 0) {
                return '';
            }

            return implode(' â€¢ ', array_slice($parts, 0, 4));
        };
    @endphp

    <div class="element-preferences-page">
        <div class="card shadow-sm pref-intro-card pref-lift">
            <div class="card-body">
                <h3 class="mb-2">Preferensi Element</h3>
                <p class="mb-0 text-muted">
                    Pengaturan ini dipakai untuk penyesuaian total struktur penilaian: tambah/hapus element,
                    tambah/hapus sub topik, jumlah pernyataan, serta bobot masing-masing.
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mt-3 mb-0">
                <strong>Periksa kembali:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$hasPreferencesTable)
            <div class="alert alert-warning mt-3 mb-0">
                Tabel preferensi belum tersedia. Jalankan <code>php artisan migrate</code> terlebih dahulu.
            </div>
        @else
            <form
                method="POST"
                action="{{ route('element-preferences.update') }}"
                class="pref-form mt-3"
                id="elementPreferenceForm"
                data-reset-data-action="{{ route('element-preferences.reset-data') }}"
            >
                @csrf
                    <div class="pref-archive-panel card shadow-sm pref-lift">
                        <div class="pref-archive-head">
                            <h5 class="mb-1">Arsip Progress Penilaian Kapabilitas APIP</h5>
                            <p class="mb-0 text-muted">Simpan snapshot penilaian tahun ini, lalu muat kembali kapan pun tanpa menghapus riwayat arsip lain.</p>
                        </div>

                        @if(!$hasProgressArchiveTable)
                            <div class="pref-archive-empty">
                                Fitur arsip progress belum aktif. Jalankan <code>php artisan migrate</code> untuk membuat tabel arsip.
                            </div>
                        @endif

                        <div class="pref-archive-grid" @if(!$hasProgressArchiveTable) aria-disabled="true" @endif>
                            <div class="pref-archive-group">
                                <label class="form-label mb-1">Simpan Arsip</label>
                                <div class="pref-archive-inline">
                                    <input
                                        type="number"
                                        class="form-control"
                                        min="2000"
                                        max="2100"
                                        step="1"
                                        value="{{ $activeBudgetYear }}"
                                        data-archive-year-input
                                        @disabled(!$hasProgressArchiveTable)
                                    >
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        data-archive-progress-trigger
                                        @disabled(!$hasProgressArchiveTable)
                                    >
                                        Arsipkan
                                    </button>
                                </div>
                            </div>

                            <div class="pref-archive-group">
                                <label class="form-label mb-1">Pulihkan Isian Arsip</label>
                                <div class="pref-archive-inline">
                                    <select
                                        class="form-select"
                                        data-load-archive-select
                                        @disabled(!$hasProgressArchiveTable)
                                    >
                                        <option value="">Pilih arsip tahun anggaran</option>
                                        @foreach($progressArchives as $archive)
                                            @php
                                                $archiveId = (int) ($archive['id'] ?? 0);
                                                $archiveYear = (int) ($archive['budget_year'] ?? 0);
                                                $archiveRows = (int) ($archive['total_rows'] ?? 0);
                                                $archiveBy = trim((string) ($archive['archived_by'] ?? ''));
                                                $archiveUpdated = $formatArchiveDate($archive['updated_at'] ?? null);
                                                $archiveLabel = 'TA '.$archiveYear.' â€¢ '.$archiveRows.' baris';
                                                if ($archiveBy !== '') {
                                                    $archiveLabel .= ' â€¢ '.$archiveBy;
                                                }
                                                if ($archiveUpdated !== '-') {
                                                    $archiveLabel .= ' â€¢ '.$archiveUpdated;
                                                }
                                            @endphp
                                            <option value="{{ $archiveId }}">{{ $archiveLabel }}</option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary"
                                        data-load-archive-trigger
                                        @disabled(!$hasProgressArchiveTable || $progressArchives->isEmpty())
                                    >
                                        Pulihkan Arsip
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if($hasProgressArchiveLoadLogTable)
                            <div class="pref-archive-log">
                                <div class="pref-archive-log-head">
                                    <h6 class="mb-0">Riwayat Pemulihan Arsip</h6>
                                    <span class="pref-archive-log-badge">{{ $progressArchiveLoadLogs->count() }} terbaru</span>
                                </div>

                                @if($progressArchiveLoadLogs->isEmpty())
                                    <div class="pref-archive-log-empty">Belum ada aktivitas pemulihan arsip.</div>
                                @else
                                    <ul class="pref-archive-log-list">
                                        @foreach($progressArchiveLoadLogs as $loadLog)
                                            @php
                                                $logYear = (int) ($loadLog['budget_year'] ?? 0);
                                                $logRows = (int) ($loadLog['restored_total'] ?? 0);
                                                $logTables = (int) ($loadLog['restored_tables'] ?? 0);
                                                $logBy = trim((string) ($loadLog['loaded_by'] ?? ''));
                                                $logDate = $formatArchiveDate($loadLog['created_at'] ?? null);
                                                $logSummary = $formatRestoredByTable((array) ($loadLog['restored_by_table'] ?? []));
                                            @endphp
                                            <li class="pref-archive-log-item">
                                                <div class="pref-archive-log-main">
                                                    <strong>TA {{ $logYear > 0 ? $logYear : '-' }}</strong>
                                                    <span class="pref-archive-log-meta">
                                                        {{ $logDate !== '-' ? $logDate : 'Waktu tidak tersedia' }}
                                                        @if($logBy !== '')
                                                            â€¢ {{ $logBy }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="pref-archive-log-detail">{{ $logRows }} baris â€¢ {{ $logTables }} tabel dipulihkan</div>
                                                @if($logSummary !== '')
                                                    <div class="pref-archive-log-summary">{{ $logSummary }}</div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="pref-element-stack" data-elements-container>
                        @foreach($elements as $element)
                        @php
                            $elementSlug = (string) ($element['slug'] ?? '');
                            if ($elementSlug === '') {
                                continue;
                            }

                            $elementTitle = (string) ($element['title'] ?? $elementSlug);
                            $elementInputTitle = $stripElementPrefix((string) old('elements.'.$elementSlug.'.title', $elementTitle));
                            $elementNumber = null;
                            if (preg_match('/^element(\d+)$/i', $elementSlug, $elementSlugMatch)) {
                                $elementNumber = (int) ($elementSlugMatch[1] ?? 0);
                            }
                            $elementDisplayTitle = $elementInputTitle;
                            if (is_int($elementNumber) && $elementNumber > 0) {
                                $elementDisplayTitle = 'Element '.$elementNumber.($elementInputTitle !== '' ? ' : '.$elementInputTitle : '');
                            }
                            if ($elementDisplayTitle === '') {
                                $elementDisplayTitle = $elementTitle;
                            }
                            $elementWeight = (float) ($element['weight'] ?? 0);
                            $subtopics = collect((array) ($element['subtopics'] ?? []))
                                ->filter(fn ($item) => is_array($item))
                                ->values();
                            $statementCount = (int) $subtopics->sum(fn ($subtopic) => count((array) ($subtopic['rows'] ?? [])));
                            $elementInfoLevels = $normalizeLevelDescriptions($element['info_levels'] ?? []);
                        @endphp

                        <details
                            class="pref-element-card card shadow-sm pref-lift"
                            data-element-card
                            data-element-slug="{{ $elementSlug }}"
                            data-default-open="{{ $elementSlug === 'element1' ? '1' : '0' }}"
                            @if($elementSlug === 'element1') open @endif
                        >
                            <summary>
                                <div>
                                    <div class="pref-element-title">{{ $elementDisplayTitle }}</div>
                                    <div class="pref-element-meta" data-element-meta>
                                        {{ $subtopics->count() }} sub topik, {{ $statementCount }} pernyataan
                                    </div>
                                </div>
                                <span class="pref-element-arrow">â–¾</span>
                            </summary>

                            <div class="pref-element-body">
                                <input type="hidden" name="elements[{{ $elementSlug }}][slug]" value="{{ $elementSlug }}" data-element-slug-input>
                                <input type="hidden" name="elements[{{ $elementSlug }}][active]" value="1">

                                <div class="pref-element-controls">
                                    <div class="pref-weight-field pref-element-title-field">
                                        <label class="form-label mb-1">Nama Element</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="elements[{{ $elementSlug }}][title]"
                                            value="{{ $elementInputTitle }}"
                                            placeholder="Nama element"
                                        >
                                    </div>

                                    <div class="pref-weight-field">
                                        <label class="form-label mb-1" for="weight-{{ $elementSlug }}">Bobot Element (%)</label>
                                        <input
                                            id="weight-{{ $elementSlug }}"
                                            type="number"
                                            name="elements[{{ $elementSlug }}][weight]"
                                            class="form-control pref-weight-input"
                                            data-weight-type="element"
                                            step="0.01"
                                            min="0"
                                            value="{{ old('elements.'.$elementSlug.'.weight', $formatPercent($elementWeight)) }}"
                                        >
                                        <div class="text-danger small mt-1 pref-weight-warn" data-weight-warn></div>
                                    </div>

                                    <div class="pref-card-actions ms-auto">
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-add-subtopic>
                                            Tambah Sub Topik
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-remove-element>
                                            Hapus Element
                                        </button>
                                    </div>
                                </div>

                                <details class="pref-level-card pref-level-card-element" data-pref-slide>
                                    <summary>Informasi Level Element (1-5)</summary>
                                    <div class="pref-level-body">
                                        <div class="pref-level-grid">
                                            @for($level = 1; $level <= 5; $level++)
                                                <div class="pref-level-field">
                                                    <label class="form-label mb-1">Level {{ $level }}</label>
                                                    <textarea
                                                        class="form-control"
                                                        rows="2"
                                                        name="elements[{{ $elementSlug }}][info_levels][{{ $level }}]"
                                                        placeholder="Deskripsi informasi level {{ $level }}">{{ old('elements.'.$elementSlug.'.info_levels.'.$level, (string) ($elementInfoLevels[$level] ?? '')) }}</textarea>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </details>

                                <div class="pref-subtopic-stack" data-subtopics-container>
                                    @foreach($subtopics as $subtopic)
                                        @php
                                            $subtopicSlug = (string) ($subtopic['slug'] ?? '');
                                            if ($subtopicSlug === '') {
                                                continue;
                                            }

                                            $subtopicTitle = (string) ($subtopic['title'] ?? $subtopicSlug);
                                            $subtopicInputTitle = $stripSubtopicPrefix((string) old('elements.'.$elementSlug.'.subtopics.'.$subtopicSlug.'.title', $subtopicTitle));
                                            $subtopicDisplayTitle = 'Sub Topik '.$loop->iteration.($subtopicInputTitle !== '' ? ' - '.$subtopicInputTitle : '');
                                            $subtopicWeight = (float) ($subtopic['weight'] ?? 0);
                                            $rows = collect((array) ($subtopic['rows'] ?? []))
                                                ->filter(fn ($item) => is_array($item))
                                                ->values();
                                            $rowNamePrefix = 'elements['.$elementSlug.'][subtopics]['.$subtopicSlug.'][rows]';
                                            $subtopicInfoLevels = $normalizeLevelDescriptions($subtopic['info_levels'] ?? []);
                                        @endphp

                                        <details class="pref-subtopic-card card pref-lift" data-subtopic-card data-subtopic-slug="{{ $subtopicSlug }}" data-pref-slide>
                                            <summary>
                                                <div class="pref-subtopic-title" data-subtopic-heading>{{ $subtopicDisplayTitle }}</div>
                                                <span class="pref-subtopic-arrow">â–¾</span>
                                            </summary>

                                            <div class="pref-subtopic-body">
                                            <input type="hidden" name="elements[{{ $elementSlug }}][subtopics][{{ $subtopicSlug }}][slug]" value="{{ $subtopicSlug }}" data-subtopic-slug-input>
                                            <input type="hidden" name="elements[{{ $elementSlug }}][subtopics][{{ $subtopicSlug }}][active]" value="1">

                                            <div class="pref-subtopic-head">
                                                <div class="pref-subtopic-title-wrap">
                                                    <label class="form-label mb-1">Nama Sub Topik</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        name="elements[{{ $elementSlug }}][subtopics][{{ $subtopicSlug }}][title]"
                                                        value="{{ $subtopicInputTitle }}"
                                                        data-subtopic-title-input
                                                    >
                                                </div>

                                                <div class="pref-weight-field pref-subtopic-weight">
                                                    <label class="form-label mb-1">Bobot Sub Topik (%)</label>
                                                    <input
                                                        type="number"
                                                        name="elements[{{ $elementSlug }}][subtopics][{{ $subtopicSlug }}][weight]"
                                                        class="form-control pref-weight-input"
                                                        data-weight-type="subtopic"
                                                        step="0.01"
                                                        min="0"
                                                        value="{{ old('elements.'.$elementSlug.'.subtopics.'.$subtopicSlug.'.weight', $formatPercent($subtopicWeight)) }}"
                                                    >
                                                    <div class="text-danger small mt-1 pref-weight-warn" data-weight-warn></div>
                                                </div>

                                                <div class="pref-card-actions ms-auto">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" data-remove-subtopic>
                                                        Hapus Sub Topik
                                                    </button>
                                                </div>
                                            </div>

                                            <details class="pref-level-card pref-level-card-subtopic" data-pref-slide>
                                                <summary>Informasi Level Sub Topik (1-5)</summary>
                                                <div class="pref-level-body">
                                                    <div class="pref-level-grid">
                                                        @for($level = 1; $level <= 5; $level++)
                                                            <div class="pref-level-field">
                                                                <label class="form-label mb-1">Level {{ $level }}</label>
                                                                <textarea
                                                                    class="form-control"
                                                                    rows="2"
                                                                    name="elements[{{ $elementSlug }}][subtopics][{{ $subtopicSlug }}][info_levels][{{ $level }}]"
                                                                    placeholder="Deskripsi informasi level {{ $level }}">{{ old('elements.'.$elementSlug.'.subtopics.'.$subtopicSlug.'.info_levels.'.$level, (string) ($subtopicInfoLevels[$level] ?? '')) }}</textarea>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </details>

                                            <div class="table-responsive pref-row-table-wrap">
                                                <table class="table table-sm align-middle pref-row-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Aktif</th>
                                                            <th>Pernyataan</th>
                                                            <th style="width: 150px;">Bobot (%)</th>
                                                            <th class="text-center" style="width: 110px;">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        data-rows-container
                                                        data-name-prefix="{{ $rowNamePrefix }}"
                                                        data-next-index="{{ $rows->count() }}"
                                                    >
                                                        @foreach($rows as $rowIndex => $row)
                                                            @php
                                                                $rowLabel = (string) ($row['label'] ?? '');
                                                                $rowActive = (bool) ($row['active'] ?? true);
                                                                $rowWeight = (float) ($row['weight'] ?? 0);
                                                                $rowLevelHints = $normalizeLevelDescriptions($row['level_hints'] ?? []);
                                                            @endphp
                                                            <tr data-row-item>
                                                                <td class="text-center">
                                                                    <input type="hidden" name="{{ $rowNamePrefix }}[{{ $rowIndex }}][active]" value="0">
                                                                    <input
                                                                        type="checkbox"
                                                                        class="form-check-input"
                                                                        name="{{ $rowNamePrefix }}[{{ $rowIndex }}][active]"
                                                                        value="1"
                                                                        @checked($rowActive)
                                                                    >
                                                                </td>
                                                                <td>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        data-row-label-input
                                                                        name="{{ $rowNamePrefix }}[{{ $rowIndex }}][label]"
                                                                        value="{{ old('elements.'.$elementSlug.'.subtopics.'.$subtopicSlug.'.rows.'.$rowIndex.'.label', $rowLabel) }}"
                                                                        placeholder="Isi pernyataan"
                                                                    >
                                                                    <details class="pref-row-level-card mt-2" data-pref-slide>
                                                                        <summary>
                                                                            Informasi Level Pernyataan
                                                                        </summary>
                                                                        <div class="pref-level-body">
                                                                            <div class="pref-level-grid">
                                                                                @for($level = 1; $level <= 5; $level++)
                                                                                    <div class="pref-level-field">
                                                                                        <label class="form-label mb-1">Level {{ $level }}</label>
                                                                                        <textarea
                                                                                            class="form-control"
                                                                                            rows="2"
                                                                                            name="{{ $rowNamePrefix }}[{{ $rowIndex }}][level_hints][{{ $level }}]"
                                                                                            placeholder="Hint level {{ $level }} untuk pernyataan ini">{{ old('elements.'.$elementSlug.'.subtopics.'.$subtopicSlug.'.rows.'.$rowIndex.'.level_hints.'.$level, (string) ($rowLevelHints[$level] ?? '')) }}</textarea>
                                                                                    </div>
                                                                                @endfor
                                                                            </div>
                                                                        </div>
                                                                    </details>
                                                                </td>
                                                                <td>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control pref-weight-input"
                                                                        data-weight-type="row"
                                                                        step="0.01"
                                                                        min="0"
                                                                        name="{{ $rowNamePrefix }}[{{ $rowIndex }}][weight]"
                                                                        value="{{ old('elements.'.$elementSlug.'.subtopics.'.$subtopicSlug.'.rows.'.$rowIndex.'.weight', $formatPercent($rowWeight)) }}"
                                                                    >
                                                                    <div class="text-danger small mt-1 pref-weight-warn" data-weight-warn></div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-outline-danger btn-sm" data-remove-row>Hapus</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="pref-subtopic-actions">
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-add-row>
                                                    Tambah Pernyataan
                                                </button>
                                            </div>
                                            </div>
                                        </details>
                                    @endforeach
                                </div>
                            </div>
                        </details>
                        @endforeach
                    </div>

                    <div class="pref-toolbar card shadow-sm pref-lift">
                        <div class="pref-toolbar-text">
                            Bobot diisi dalam persen. Sistem akan normalisasi otomatis saat disimpan.
                        </div>
                        <div class="pref-toolbar-actions">
                            <button type="button" class="btn btn-outline-danger" data-pref-reset-data-trigger>Reset Data</button>
                            <button type="button" class="btn btn-outline-primary" data-add-element>Tambah Element</button>
                            <button type="submit" class="btn btn-primary">Simpan Preferensi</button>
                        </div>
                    </div>
            </form>

            <form id="archiveProgressForm" method="POST" action="{{ route('element-preferences.archive-progress') }}" class="d-none">
                @csrf
                <input type="hidden" name="budget_year" value="">
            </form>

            <form id="loadArchiveForm" method="POST" action="{{ route('element-preferences.load-archive') }}" class="d-none">
                @csrf
                <input type="hidden" name="archive_id" value="">
            </form>
        @endif
    </div>
@endsection

@push('global-modals')
<div class="dms-confirm-modal" id="prefActionConfirmModal" hidden aria-hidden="true">
    <div class="dms-confirm-modal__backdrop" data-pref-confirm-close></div>
    <div class="dms-confirm-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="prefActionConfirmModalTitle">
        <div class="dms-confirm-modal__eyebrow">Konfirmasi Tindakan</div>
        <h3 class="dms-confirm-modal__title" id="prefActionConfirmModalTitle">Konfirmasi</h3>
        <p class="dms-confirm-modal__body" id="prefActionConfirmModalMessage">Lanjutkan tindakan ini?</p>
        <div class="dms-confirm-modal__verify" id="prefActionConfirmModalVerify" hidden>
            <div class="dms-confirm-modal__verify-label" id="prefActionConfirmModalVerifyLabel">Ketik kata kunci untuk melanjutkan.</div>
            <input
                type="text"
                class="form-control dms-confirm-modal__verify-input"
                id="prefActionConfirmModalVerifyInput"
                autocomplete="off"
                spellcheck="false"
                placeholder="">
            <div class="dms-confirm-modal__verify-hint" id="prefActionConfirmModalVerifyHint"></div>
        </div>
        <div class="dms-confirm-modal__actions">
            <button type="button" class="btn dms-confirm-modal__cancel" data-pref-confirm-close>Batal</button>
            <button type="button" class="btn dms-confirm-modal__confirm" id="prefActionConfirmModalConfirm">Lanjutkan</button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
(() => {
    const page = document.querySelector('.element-preferences-page');
    if (!page) return;

    const preferenceForm = document.getElementById('elementPreferenceForm');
    if (!preferenceForm) return;

    const archiveProgressForm = document.getElementById('archiveProgressForm');
    const archiveProgressYearField = archiveProgressForm?.querySelector('input[name="budget_year"]') || null;
    const loadArchiveForm = document.getElementById('loadArchiveForm');
    const loadArchiveIdField = loadArchiveForm?.querySelector('input[name="archive_id"]') || null;
    const archiveYearInput = page.querySelector('[data-archive-year-input]');
    const loadArchiveSelect = page.querySelector('[data-load-archive-select]');

    const elementsContainer = page.querySelector('[data-elements-container]');
    if (!elementsContainer) return;
    const updateActionUrl = preferenceForm.getAttribute('action') || '';
    const resetDataActionUrl = preferenceForm.dataset.resetDataAction || updateActionUrl;

    const confirmModal = document.getElementById('prefActionConfirmModal');
    const confirmTitle = document.getElementById('prefActionConfirmModalTitle');
    const confirmMessage = document.getElementById('prefActionConfirmModalMessage');
    const confirmSubmit = document.getElementById('prefActionConfirmModalConfirm');
    const confirmVerifyWrap = document.getElementById('prefActionConfirmModalVerify');
    const confirmVerifyLabel = document.getElementById('prefActionConfirmModalVerifyLabel');
    const confirmVerifyInput = document.getElementById('prefActionConfirmModalVerifyInput');
    const confirmVerifyHint = document.getElementById('prefActionConfirmModalVerifyHint');

    const transitionMs = 180;
    const elementAccordionDurationMs = 360;
    const elementAccordionEasing = 'cubic-bezier(0.22, 1, 0.36, 1)';
    const levelAccordionDurationMs = 300;
    const elementSpotlightDurationMs = 1700;
    let closeTimer = null;
    let pendingAction = null;
    let lastTrigger = null;
    let confirmRequiredPhrase = '';
    const reducedMotionQuery = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;

    const getViewportUiScale = () => {
        const zoomRaw = getComputedStyle(document.body).zoom;
        const zoom = Number.parseFloat(zoomRaw || '1');
        return Number.isFinite(zoom) && zoom > 0 ? zoom : 1;
    };

    const syncModalToViewport = () => {
        if (!confirmModal || !confirmModal.classList.contains('is-open')) return;

        const scale = getViewportUiScale();
        confirmModal.style.top = `${Math.round(window.scrollY / scale)}px`;
        confirmModal.style.left = `${Math.round(window.scrollX / scale)}px`;
        confirmModal.style.width = `${Math.ceil(window.innerWidth / scale)}px`;
        confirmModal.style.height = `${Math.ceil(window.innerHeight / scale)}px`;
    };

    const clearModalViewportStyles = () => {
        if (!confirmModal) return;

        confirmModal.style.removeProperty('top');
        confirmModal.style.removeProperty('left');
        confirmModal.style.removeProperty('width');
        confirmModal.style.removeProperty('height');
    };

    const normalizeSlug = (value) => {
        return String(value || '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9_]+/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_+|_+$/g, '');
    };

    const stripElementPrefix = (value) => {
        const normalized = String(value || '').trim();
        if (!normalized) return '';

        const stripped = normalized.replace(/^\s*element\s*\d+\s*[:\-]?\s*/i, '').trim();
        return stripped || normalized;
    };

    const stripSubtopicPrefix = (value) => {
        const normalized = String(value || '').trim();
        if (!normalized) return '';

        const stripped = normalized.replace(/^\s*sub\s*topik\s*\d+\s*[-:]?\s*/i, '').trim();
        return stripped || normalized;
    };

    const resolveElementNumberFromSlug = (slug, fallbackIndex = 0) => {
        const normalizedSlug = String(slug || '').trim().toLowerCase();
        const match = normalizedSlug.match(/^element(\d+)$/);
        if (match) {
            return Number.parseInt(match[1], 10);
        }

        return fallbackIndex > 0 ? fallbackIndex : null;
    };

    const buildElementDisplayTitleFromSlug = (slug, nameValue, fallbackIndex = 0) => {
        const elementNumber = resolveElementNumberFromSlug(slug, fallbackIndex);
        const cleanName = stripElementPrefix(nameValue);

        if (Number.isFinite(elementNumber) && elementNumber > 0) {
            return cleanName !== '' ? `Element ${elementNumber} : ${cleanName}` : `Element ${elementNumber}`;
        }

        return cleanName !== '' ? cleanName : 'Element';
    };

    const buildElementDisplayTitle = (elementCard, nameValue = '') => {
        const elementSlug = String(elementCard?.dataset?.elementSlug || '');
        const allCards = Array.from(elementsContainer.querySelectorAll('[data-element-card]'));
        const fallbackIndex = Math.max(1, allCards.indexOf(elementCard) + 1);

        return buildElementDisplayTitleFromSlug(elementSlug, nameValue, fallbackIndex);
    };

    const buildSubtopicDisplayTitle = (nameValue, fallbackIndex = 1) => {
        const cleanName = stripSubtopicPrefix(nameValue);
        const index = Number.isFinite(fallbackIndex) && fallbackIndex > 0 ? fallbackIndex : 1;

        return cleanName !== '' ? `Sub Topik ${index} - ${cleanName}` : `Sub Topik ${index}`;
    };

    const getElementCardBody = (elementCard) => {
        return elementCard?.querySelector(':scope > .pref-element-body') || elementCard?.querySelector('.pref-element-body');
    };

    const clearElementCardAnimationState = (elementCard, body) => {
        if (!body) return;

        body.style.removeProperty('height');
        body.style.removeProperty('overflow');
        body.style.removeProperty('transition');
        body.style.removeProperty('opacity');
        body.style.removeProperty('transform');
        elementCard?.removeAttribute('data-animating');
    };

    const openElementCard = (elementCard) => {
        if (!elementCard) return;
        const body = getElementCardBody(elementCard);
        if (elementCard.getAttribute('data-animating') === '1') {
            clearElementCardAnimationState(elementCard, body);
        }
        if (elementCard.open) return;

        if (!body) {
            elementCard.open = true;
            return;
        }

        elementCard.setAttribute('data-animating', '1');
        elementCard.open = true;

        body.style.height = '0px';
        body.style.opacity = '0';
        body.style.transform = 'translateY(-4px)';
        body.style.overflow = 'hidden';
        void body.offsetHeight;

        const targetHeight = body.scrollHeight;
        body.style.transition = `height ${elementAccordionDurationMs}ms ${elementAccordionEasing}, opacity 260ms ease, transform 320ms ${elementAccordionEasing}`;
        body.style.height = `${targetHeight}px`;
        body.style.opacity = '1';
        body.style.transform = 'translateY(0)';

        const onTransitionEnd = (event) => {
            if (event.target !== body || event.propertyName !== 'height') return;
            body.removeEventListener('transitionend', onTransitionEnd);
            clearElementCardAnimationState(elementCard, body);
        };
        body.addEventListener('transitionend', onTransitionEnd);
    };

    const closeElementCard = (elementCard) => {
        if (!elementCard) return;
        const body = getElementCardBody(elementCard);
        if (elementCard.getAttribute('data-animating') === '1') {
            clearElementCardAnimationState(elementCard, body);
        }
        if (!elementCard.open) return;

        if (!body) {
            elementCard.open = false;
            return;
        }

        elementCard.setAttribute('data-animating', '1');

        const startHeight = body.scrollHeight;
        body.style.height = `${startHeight}px`;
        body.style.opacity = '1';
        body.style.transform = 'translateY(0)';
        body.style.overflow = 'hidden';
        void body.offsetHeight;

        body.style.transition = `height ${elementAccordionDurationMs}ms ${elementAccordionEasing}, opacity 220ms ease, transform 280ms ${elementAccordionEasing}`;
        body.style.height = '0px';
        body.style.opacity = '0';
        body.style.transform = 'translateY(-4px)';

        const onTransitionEnd = (event) => {
            if (event.target !== body || event.propertyName !== 'height') return;
            body.removeEventListener('transitionend', onTransitionEnd);
            elementCard.open = false;
            clearElementCardAnimationState(elementCard, body);
        };
        body.addEventListener('transitionend', onTransitionEnd);
    };

    const collapseOtherElementCards = (activeCard) => {
        const elementCards = Array.from(elementsContainer.querySelectorAll('[data-element-card]'));
        elementCards.forEach((elementCard) => {
            if (elementCard === activeCard) return;
            if (!elementCard.open && elementCard.getAttribute('data-animating') !== '1') return;
            closeElementCard(elementCard);
        });
    };

    const toggleElementCard = (elementCard) => {
        if (!elementCard) return;

        if (elementCard.open) {
            closeElementCard(elementCard);
            return;
        }

        collapseOtherElementCards(elementCard);
        openElementCard(elementCard);
    };

    const spotlightElementCard = (elementCard) => {
        if (!elementCard) return;

        const prefersReducedMotion = !!reducedMotionQuery?.matches;
        const previousTimerId = Number.parseInt(elementCard.dataset.spotlightTimerId || '0', 10);
        if (Number.isFinite(previousTimerId) && previousTimerId > 0) {
            window.clearTimeout(previousTimerId);
        }

        if (typeof elementCard.scrollIntoView === 'function') {
            elementCard.scrollIntoView({
                behavior: prefersReducedMotion ? 'auto' : 'smooth',
                block: 'start',
                inline: 'nearest',
            });
        }

        elementCard.classList.remove('pref-element-card-spotlight');
        void elementCard.offsetWidth;
        elementCard.classList.add('pref-element-card-spotlight');

        const timerId = window.setTimeout(() => {
            elementCard.classList.remove('pref-element-card-spotlight');
            elementCard.removeAttribute('data-spotlight-timer-id');
        }, elementSpotlightDurationMs);
        elementCard.dataset.spotlightTimerId = String(timerId);
    };

    const getSlideDetailsBody = (detailsCard) => {
        return detailsCard?.querySelector(':scope > .pref-level-body')
            || detailsCard?.querySelector(':scope > .pref-subtopic-body')
            || detailsCard?.querySelector('.pref-level-body')
            || detailsCard?.querySelector('.pref-subtopic-body');
    };

    const clearSlideDetailsAnimationState = (detailsCard, body) => {
        if (!body) return;

        body.style.removeProperty('height');
        body.style.removeProperty('overflow');
        body.style.removeProperty('transition');
        body.style.removeProperty('will-change');
        detailsCard?.removeAttribute('data-animating');
    };

    const resolveSlideDetailsDuration = (detailsCard, contentHeight) => {
        if (reducedMotionQuery?.matches) {
            return 0;
        }

        const isRowLevelCard = detailsCard?.classList.contains('pref-row-level-card');
        const isSubtopicCard = detailsCard?.classList.contains('pref-subtopic-card');
        if (isSubtopicCard) {
            if (contentHeight > 1280) {
                return 0;
            }

            return Math.min(320, Math.max(180, Math.round(contentHeight * 0.18)));
        }

        const instantThreshold = isRowLevelCard ? 560 : 920;
        if (contentHeight > instantThreshold) {
            return 0;
        }

        if (isRowLevelCard) {
            return Math.min(220, Math.max(140, Math.round(contentHeight * 0.24)));
        }

        return Math.min(levelAccordionDurationMs, Math.max(170, Math.round(contentHeight * 0.2)));
    };

    const openSlideDetails = (detailsCard) => {
        if (!detailsCard || detailsCard.getAttribute('data-animating') === '1') return;
        if (detailsCard.open) return;

        const body = getSlideDetailsBody(detailsCard);
        if (!body) {
            detailsCard.open = true;
            return;
        }

        detailsCard.setAttribute('data-animating', '1');
        detailsCard.open = true;

        const targetHeight = body.scrollHeight;
        const duration = resolveSlideDetailsDuration(detailsCard, targetHeight);
        if (duration <= 0) {
            clearSlideDetailsAnimationState(detailsCard, body);
            return;
        }

        body.style.height = '0px';
        body.style.overflow = 'hidden';
        body.style.willChange = 'height';
        void body.offsetHeight;

        body.style.transition = `height ${duration}ms ${elementAccordionEasing}`;
        body.style.height = `${targetHeight}px`;

        const onTransitionEnd = (event) => {
            if (event.target !== body || event.propertyName !== 'height') return;
            body.removeEventListener('transitionend', onTransitionEnd);
            clearSlideDetailsAnimationState(detailsCard, body);
        };
        body.addEventListener('transitionend', onTransitionEnd);
    };

    const closeSlideDetails = (detailsCard) => {
        if (!detailsCard || detailsCard.getAttribute('data-animating') === '1') return;
        if (!detailsCard.open) return;

        const body = getSlideDetailsBody(detailsCard);
        if (!body) {
            detailsCard.open = false;
            return;
        }

        detailsCard.setAttribute('data-animating', '1');

        const startHeight = body.scrollHeight;
        const duration = resolveSlideDetailsDuration(detailsCard, startHeight);
        if (duration <= 0) {
            detailsCard.open = false;
            clearSlideDetailsAnimationState(detailsCard, body);
            return;
        }

        body.style.height = `${startHeight}px`;
        body.style.overflow = 'hidden';
        body.style.willChange = 'height';
        void body.offsetHeight;

        body.style.transition = `height ${duration}ms ${elementAccordionEasing}`;
        body.style.height = '0px';

        const onTransitionEnd = (event) => {
            if (event.target !== body || event.propertyName !== 'height') return;
            body.removeEventListener('transitionend', onTransitionEnd);
            detailsCard.open = false;
            clearSlideDetailsAnimationState(detailsCard, body);
        };
        body.addEventListener('transitionend', onTransitionEnd);
    };

    const toggleSlideDetails = (detailsCard) => {
        if (!detailsCard || detailsCard.getAttribute('data-animating') === '1') return;

        if (detailsCard.open) {
            closeSlideDetails(detailsCard);
            return;
        }

        openSlideDetails(detailsCard);
    };

    const collapseOtherSubtopicCards = (activeSubtopicCard) => {
        if (!activeSubtopicCard) return;

        const elementCard = activeSubtopicCard.closest('[data-element-card]');
        const scope = elementCard || elementsContainer;
        const subtopicCards = Array.from(scope.querySelectorAll('[data-subtopic-card]'));

        subtopicCards.forEach((subtopicCard) => {
            if (subtopicCard === activeSubtopicCard) return;
            if (!subtopicCard.open && subtopicCard.getAttribute('data-animating') !== '1') return;
            closeSlideDetails(subtopicCard);
        });
    };

    const normalizeVerificationValue = (value) => {
        return String(value || '').trim().toLowerCase();
    };

    const resetConfirmVerificationState = () => {
        confirmRequiredPhrase = '';
        if (confirmVerifyWrap) {
            confirmVerifyWrap.setAttribute('hidden', 'hidden');
        }
        if (confirmVerifyLabel) {
            confirmVerifyLabel.textContent = 'Ketik kata kunci untuk melanjutkan.';
        }
        if (confirmVerifyInput) {
            confirmVerifyInput.value = '';
            confirmVerifyInput.placeholder = '';
            confirmVerifyInput.classList.remove('is-valid', 'is-invalid');
            confirmVerifyInput.removeAttribute('aria-invalid');
        }
        if (confirmVerifyHint) {
            confirmVerifyHint.textContent = '';
            confirmVerifyHint.classList.remove('is-danger');
        }
        if (confirmSubmit) {
            confirmSubmit.disabled = false;
        }
    };

    const syncConfirmVerificationState = () => {
        if (!confirmSubmit) return;

        if (confirmRequiredPhrase === '') {
            confirmSubmit.disabled = false;
            return;
        }

        const typed = normalizeVerificationValue(confirmVerifyInput?.value || '');
        const expected = normalizeVerificationValue(confirmRequiredPhrase);
        const isMatch = typed !== '' && typed === expected;

        confirmSubmit.disabled = !isMatch;
        if (!confirmVerifyInput) return;

        if (typed === '') {
            confirmVerifyInput.classList.remove('is-valid', 'is-invalid');
            confirmVerifyInput.removeAttribute('aria-invalid');
            if (confirmVerifyHint) {
                confirmVerifyHint.classList.remove('is-danger');
            }
            return;
        }

        confirmVerifyInput.classList.toggle('is-valid', isMatch);
        confirmVerifyInput.classList.toggle('is-invalid', !isMatch);
        confirmVerifyInput.setAttribute('aria-invalid', isMatch ? 'false' : 'true');
        if (confirmVerifyHint) {
            confirmVerifyHint.classList.toggle('is-danger', !isMatch);
        }
    };

    const closeConfirmModal = () => {
        pendingAction = null;
        document.body.classList.remove('pref-modal-open');

        if (confirmModal) {
            confirmModal.classList.remove('is-open');
            confirmModal.setAttribute('aria-hidden', 'true');
            confirmModal.removeAttribute('data-kind');

            if (closeTimer) {
                window.clearTimeout(closeTimer);
            }
            closeTimer = window.setTimeout(() => {
                if (!confirmModal.classList.contains('is-open')) {
                    confirmModal.setAttribute('hidden', 'hidden');
                    clearModalViewportStyles();
                }
                closeTimer = null;
            }, transitionMs);
        }

        confirmSubmit?.classList.remove('is-danger', 'is-warning');
        resetConfirmVerificationState();

        lastTrigger?.focus?.({ preventScroll: true });
        lastTrigger = null;
    };

    const openConfirmModal = ({
        title = 'Konfirmasi',
        message = 'Lanjutkan tindakan ini?',
        label = 'Lanjutkan',
        kind = 'default',
        requiredPhrase = '',
        requiredPhraseLabel = '',
        requiredPhraseHint = '',
        onConfirm = null,
        trigger = null,
    }) => {
        if (!confirmModal || !confirmTitle || !confirmMessage || !confirmSubmit || typeof onConfirm !== 'function') {
            onConfirm?.();
            return;
        }

        if (closeTimer) {
            window.clearTimeout(closeTimer);
            closeTimer = null;
        }

        pendingAction = onConfirm;
        lastTrigger = trigger;
        confirmRequiredPhrase = String(requiredPhrase || '').trim();

        confirmTitle.textContent = title;
        confirmMessage.textContent = message;
        confirmSubmit.textContent = label;
        confirmSubmit.disabled = false;
        confirmSubmit.classList.toggle('is-danger', kind === 'danger');
        confirmSubmit.classList.toggle('is-warning', kind === 'warning');

        if (confirmVerifyWrap && confirmVerifyInput && confirmVerifyLabel && confirmVerifyHint) {
            if (confirmRequiredPhrase !== '') {
                confirmVerifyWrap.removeAttribute('hidden');
                confirmVerifyLabel.textContent = requiredPhraseLabel || 'Ketik kata kunci untuk melanjutkan.';
                confirmVerifyInput.value = '';
                confirmVerifyInput.placeholder = requiredPhraseHint || `Ketik: ${confirmRequiredPhrase}`;
                confirmVerifyHint.textContent = `Kata kunci: ${confirmRequiredPhrase}`;
                confirmVerifyHint.classList.remove('is-danger');
                confirmVerifyInput.classList.remove('is-valid', 'is-invalid');
                confirmVerifyInput.removeAttribute('aria-invalid');
                syncConfirmVerificationState();
            } else {
                resetConfirmVerificationState();
            }
        }

        confirmModal.setAttribute('data-kind', kind);
        confirmModal.removeAttribute('hidden');
        confirmModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('pref-modal-open');

        requestAnimationFrame(() => {
            confirmModal.classList.add('is-open');
            syncModalToViewport();
        });
        requestAnimationFrame(() => {
            if (confirmRequiredPhrase !== '' && confirmVerifyInput) {
                confirmVerifyInput.focus({ preventScroll: true });
            } else {
                confirmSubmit.focus({ preventScroll: true });
            }
        });
    };

    const showToast = ({
        title = 'Info',
        message = '',
        type = 'info',
        duration = 3200,
    } = {}) => {
        const safeMessage = String(message || '').trim();
        if (safeMessage === '') return;

        if (typeof window.pushToast === 'function') {
            window.pushToast({ title, message: safeMessage, type, duration });
            return;
        }

        document.dispatchEvent(new CustomEvent('toast', {
            detail: { title, message: safeMessage, type, duration },
        }));
    };

    confirmVerifyInput?.addEventListener('input', () => {
        syncConfirmVerificationState();
    });

    confirmVerifyInput?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        if (!confirmModal?.classList.contains('is-open')) return;
        if (confirmSubmit?.disabled) {
            event.preventDefault();
            return;
        }

        event.preventDefault();
        confirmSubmit?.click();
    });

    const escapeHtml = (value) => {
        const text = String(value ?? '');
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const toHeadline = (value) => {
        const normalized = String(value || '')
            .replace(/_/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();

        if (!normalized) return '';

        return normalized
            .split(' ')
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    const normalizeLevelDescriptions = (source = null) => {
        const levels = { 1: '', 2: '', 3: '', 4: '', 5: '' };
        if (!source || typeof source !== 'object') {
            return levels;
        }

        Object.entries(source).forEach(([key, item]) => {
            let level = Number.parseInt(key, 10);
            let description = '';

            if (item && typeof item === 'object' && !Array.isArray(item)) {
                const rawLevel = Number.parseInt(item.level ?? key, 10);
                level = Number.isFinite(rawLevel) ? rawLevel : level;
                description = String(item.description ?? '').trim();
            } else {
                description = String(item ?? '').trim();
            }

            if (!Number.isFinite(level) || level < 1 || level > 5) {
                return;
            }

            levels[level] = description;
        });

        return levels;
    };

    const buildLevelFields = (namePrefix, valueSource, placeholderPrefix) => {
        const levels = normalizeLevelDescriptions(valueSource);
        return [1, 2, 3, 4, 5]
            .map((level) => `
                <div class="pref-level-field">
                    <label class="form-label mb-1">Level ${level}</label>
                    <textarea
                        class="form-control"
                        rows="2"
                        name="${namePrefix}[${level}]"
                        placeholder="${escapeHtml(placeholderPrefix.replace('{level}', String(level)))}">${escapeHtml(levels[level] ?? '')}</textarea>
                </div>
            `)
            .join('');
    };

    const updateElementMeta = (elementCard) => {
        if (!elementCard) return;

        const meta = elementCard.querySelector('[data-element-meta]');
        if (!meta) return;

        const subtopicCount = elementCard.querySelectorAll('[data-subtopic-card]').length;
        const statementCount = elementCard.querySelectorAll('[data-row-item]').length;
        meta.textContent = `${subtopicCount} sub topik, ${statementCount} pernyataan`;

        const titleInput = elementCard.querySelector('[data-element-title-input]');
        const titleNode = elementCard.querySelector('.pref-element-title');
        if (titleInput && titleNode) {
            const value = stripElementPrefix(titleInput.value);
            if (titleInput.value !== value) {
                titleInput.value = value;
            }
            titleNode.textContent = buildElementDisplayTitle(elementCard, value);
        }

        const subtopicCards = Array.from(elementCard.querySelectorAll('[data-subtopic-card]'));
        subtopicCards.forEach((subtopicCard, index) => {
            const headingNode = subtopicCard.querySelector('[data-subtopic-heading]');
            if (!headingNode) return;

            const subtopicTitleInput = subtopicCard.querySelector('[data-subtopic-title-input], input[name*="[title]"]');
            const cleanSubtopicName = stripSubtopicPrefix(subtopicTitleInput?.value || '');
            if (subtopicTitleInput && subtopicTitleInput.value !== cleanSubtopicName) {
                subtopicTitleInput.value = cleanSubtopicName;
            }

            headingNode.textContent = buildSubtopicDisplayTitle(cleanSubtopicName, index + 1);
        });
    };

    const parseWeightValue = (input) => {
        const rawValue = String(input?.value ?? '').trim().replace(',', '.');
        const parsed = Number.parseFloat(rawValue);

        if (!Number.isFinite(parsed) || parsed <= 0) {
            return 0;
        }

        return parsed;
    };

    const formatWeightValue = (value) => {
        const number = Number.isFinite(value) ? value : 0;
        const fixed = number.toFixed(2);
        const normalized = fixed.replace(/\.?0+$/, '');

        return normalized === '' ? '0' : normalized;
    };

    const ensureWeightWarningNode = (input) => {
        const container = input.closest('.pref-weight-field, td') || input.parentElement;
        if (!container) return null;

        let warningNode = container.querySelector('[data-weight-warn]');
        if (!warningNode) {
            warningNode = document.createElement('div');
            warningNode.className = 'text-danger small mt-1 pref-weight-warn';
            warningNode.setAttribute('data-weight-warn', '1');
            container.appendChild(warningNode);
        }

        return warningNode;
    };

    const setWeightWarning = (input, { invalid = false, message = '' } = {}) => {
        if (!input) return;

        const warningNode = ensureWeightWarningNode(input);
        input.classList.toggle('is-invalid', invalid);
        input.setAttribute('aria-invalid', invalid ? 'true' : 'false');

        if (warningNode) {
            warningNode.textContent = invalid ? String(message || '') : '';
        }
    };

    const applyGroupWeightWarning = (inputs, message) => {
        inputs.forEach((input, index) => {
            setWeightWarning(input, {
                invalid: true,
                message: index === 0 ? message : '',
            });
        });
    };

    const weightTarget = 100;
    const weightTolerance = 0.005;

    const getWeightTotalStatus = (total) => {
        if (total > weightTarget + weightTolerance) return 'over';
        if (total < weightTarget - weightTolerance) return 'under';
        return 'ok';
    };

    const buildWeightWarningMessage = (label, total) => {
        const status = getWeightTotalStatus(total);
        if (status === 'ok') return '';

        const operator = status === 'over' ? '>' : '<';
        return `Total bobot ${label} ${formatWeightValue(total)}% ${operator} 100%.`;
    };

    const validateWeightGroups = () => {
        const weightInputs = Array.from(elementsContainer.querySelectorAll('.pref-weight-input'));
        if (weightInputs.length === 0) {
            return false;
        }

        weightInputs.forEach((input) => setWeightWarning(input, { invalid: false }));

        let hasWarning = false;
        const elementInputs = Array.from(elementsContainer.querySelectorAll('.pref-weight-input[data-weight-type="element"]'));
        const elementTotal = elementInputs.reduce((total, input) => total + parseWeightValue(input), 0);
        const elementWarning = buildWeightWarningMessage('element', elementTotal);
        if (elementInputs.length > 0 && elementWarning !== '') {
            applyGroupWeightWarning(
                elementInputs,
                elementWarning
            );
            hasWarning = true;
        }

        const elementCards = Array.from(elementsContainer.querySelectorAll('[data-element-card]'));
        elementCards.forEach((elementCard) => {
            const subtopicInputs = Array.from(elementCard.querySelectorAll('.pref-weight-input[data-weight-type="subtopic"]'));
            const subtopicTotal = subtopicInputs.reduce((total, input) => total + parseWeightValue(input), 0);
            const subtopicWarning = buildWeightWarningMessage('sub topik', subtopicTotal);

            if (subtopicInputs.length > 0 && subtopicWarning !== '') {
                applyGroupWeightWarning(
                    subtopicInputs,
                    subtopicWarning
                );
                hasWarning = true;
            }

            const subtopicCards = Array.from(elementCard.querySelectorAll('[data-subtopic-card]'));
            subtopicCards.forEach((subtopicCard) => {
                const subtopicName = subtopicCard.querySelector('input[name*="[title]"]')?.value?.trim()
                    || subtopicCard.querySelector('[data-subtopic-heading]')?.textContent?.trim()
                    || 'sub topik ini';
                const rowInputs = Array.from(subtopicCard.querySelectorAll('.pref-weight-input[data-weight-type="row"]'));
                const rowTotal = rowInputs.reduce((total, input) => total + parseWeightValue(input), 0);
                const rowWarning = buildWeightWarningMessage(`pernyataan pada ${subtopicName}`, rowTotal);

                if (rowInputs.length > 0 && rowWarning !== '') {
                    applyGroupWeightWarning(
                        rowInputs,
                        rowWarning
                    );
                    hasWarning = true;
                }
            });
        });

        return hasWarning;
    };

    const createRow = (namePrefix, index, options = {}) => {
        const row = document.createElement('tr');
        row.setAttribute('data-row-item', '1');

        const label = escapeHtml(options.label ?? '');
        const weight = escapeHtml(options.weight ?? '0');
        const isActive = options.active === false ? '' : 'checked';
        const levelFields = buildLevelFields(
            `${namePrefix}[${index}][level_hints]`,
            options.level_hints ?? null,
            'Hint level {level} untuk pernyataan ini'
        );

        row.innerHTML = `
            <td class="text-center">
                <input type="hidden" name="${namePrefix}[${index}][active]" value="0">
                <input type="checkbox" class="form-check-input" name="${namePrefix}[${index}][active]" value="1" ${isActive}>
            </td>
            <td>
                <input type="text" class="form-control" data-row-label-input name="${namePrefix}[${index}][label]" value="${label}" placeholder="Isi pernyataan">
                <details class="pref-row-level-card mt-2" data-pref-slide>
                    <summary>
                        Informasi Level Pernyataan
                    </summary>
                    <div class="pref-level-body">
                        <div class="pref-level-grid">
                            ${levelFields}
                        </div>
                    </div>
                </details>
            </td>
            <td>
                <input type="number" class="form-control pref-weight-input" data-weight-type="row" step="0.01" min="0" name="${namePrefix}[${index}][weight]" value="${weight}">
                <div class="text-danger small mt-1 pref-weight-warn" data-weight-warn></div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm" data-remove-row>Hapus</button>
            </td>
        `;

        return row;
    };

    const addRowToContainer = (container, options = {}) => {
        const namePrefix = container.dataset.namePrefix || '';
        const currentIndex = Number.parseInt(container.dataset.nextIndex || '0', 10);
        const nextIndex = Number.isFinite(currentIndex) ? currentIndex : 0;
        const row = createRow(namePrefix, nextIndex, options);
        container.appendChild(row);
        container.dataset.nextIndex = String(nextIndex + 1);
    };

    const nextElementSlug = () => {
        const slugs = Array.from(elementsContainer.querySelectorAll('[data-element-card]'))
            .map((card) => String(card.dataset.elementSlug || ''))
            .filter((slug) => /^element\d+$/.test(slug))
            .map((slug) => Number.parseInt(slug.replace('element', ''), 10))
            .filter((value) => Number.isFinite(value));

        const max = slugs.length > 0 ? Math.max(...slugs) : 0;
        return `element${max + 1}`;
    };

    const nextSubtopicSlug = (elementCard) => {
        const elementSlug = String(elementCard.dataset.elementSlug || '');
        const subtopicSlugs = Array.from(elementCard.querySelectorAll('[data-subtopic-card]'))
            .map((card) => String(card.dataset.subtopicSlug || ''));

        const pattern = new RegExp(`^${elementSlug}_subtopik_(\\d+)$`);
        const numbers = subtopicSlugs
            .map((slug) => {
                const match = slug.match(pattern);
                return match ? Number.parseInt(match[1], 10) : NaN;
            })
            .filter((value) => Number.isFinite(value));

        const max = numbers.length > 0 ? Math.max(...numbers) : 0;
        return `${elementSlug}_subtopik_${max + 1}`;
    };

    const createSubtopicCard = (elementSlug, subtopicSlug, options = {}) => {
        const rawTitle = stripSubtopicPrefix(options.title ?? '');
        const title = escapeHtml(rawTitle);
        const weight = escapeHtml(options.weight ?? '0');
        const subtopicLevelFields = buildLevelFields(
            `elements[${elementSlug}][subtopics][${subtopicSlug}][info_levels]`,
            options.info_levels ?? null,
            'Deskripsi informasi level {level}'
        );

        const section = document.createElement('details');
        section.className = 'pref-subtopic-card card pref-lift';
        section.setAttribute('data-subtopic-card', '1');
        section.setAttribute('data-pref-slide', '1');
        section.dataset.subtopicSlug = subtopicSlug;
        section.open = false;

        const rowPrefix = `elements[${elementSlug}][subtopics][${subtopicSlug}][rows]`;

        section.innerHTML = `
            <summary>
                <div class="pref-subtopic-title" data-subtopic-heading>Sub Topik</div>
                <span class="pref-subtopic-arrow">â–¾</span>
            </summary>

            <div class="pref-subtopic-body">
            <input type="hidden" name="elements[${elementSlug}][subtopics][${subtopicSlug}][slug]" value="${subtopicSlug}" data-subtopic-slug-input>
            <input type="hidden" name="elements[${elementSlug}][subtopics][${subtopicSlug}][active]" value="1">

            <div class="pref-subtopic-head">
                <div class="pref-subtopic-title-wrap">
                    <label class="form-label mb-1">Nama Sub Topik</label>
                    <input
                        type="text"
                        class="form-control"
                        name="elements[${elementSlug}][subtopics][${subtopicSlug}][title]"
                        value="${title}"
                        data-subtopic-title-input
                    >
                </div>

                <div class="pref-weight-field pref-subtopic-weight">
                    <label class="form-label mb-1">Bobot Sub Topik (%)</label>
                    <input
                        type="number"
                        name="elements[${elementSlug}][subtopics][${subtopicSlug}][weight]"
                        class="form-control pref-weight-input"
                        data-weight-type="subtopic"
                        step="0.01"
                        min="0"
                        value="${weight}"
                    >
                    <div class="text-danger small mt-1 pref-weight-warn" data-weight-warn></div>
                </div>

                <div class="pref-card-actions ms-auto">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-remove-subtopic>
                        Hapus Sub Topik
                    </button>
                </div>
            </div>

            <details class="pref-level-card pref-level-card-subtopic" data-pref-slide>
                <summary>Informasi Level Sub Topik (1-5)</summary>
                <div class="pref-level-body">
                    <div class="pref-level-grid">
                        ${subtopicLevelFields}
                    </div>
                </div>
            </details>

            <div class="table-responsive pref-row-table-wrap">
                <table class="table table-sm align-middle pref-row-table">
                    <thead>
                        <tr>
                            <th class="text-center">Aktif</th>
                            <th>Pernyataan</th>
                            <th style="width: 150px;">Bobot (%)</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody data-rows-container data-name-prefix="${rowPrefix}" data-next-index="0"></tbody>
                </table>
            </div>

            <div class="pref-subtopic-actions">
                <button type="button" class="btn btn-outline-primary btn-sm" data-add-row>
                    Tambah Pernyataan
                </button>
            </div>
            </div>
        `;

        const rowsContainer = section.querySelector('[data-rows-container]');
        const rows = Array.isArray(options.rows) ? options.rows : [];
        if (rows.length > 0) {
            rows.forEach((row) => addRowToContainer(rowsContainer, row));
        } else {
            addRowToContainer(rowsContainer, { label: 'Pernyataan 1', weight: '100', active: true });
        }

        return section;
    };

    const createElementCard = (elementSlug, options = {}) => {
        const rawTitle = stripElementPrefix(options.title ?? '');
        const title = escapeHtml(rawTitle);
        const displayTitle = escapeHtml(buildElementDisplayTitleFromSlug(elementSlug, rawTitle));
        const weight = escapeHtml(options.weight ?? '0');
        const elementLevelFields = buildLevelFields(
            `elements[${elementSlug}][info_levels]`,
            options.info_levels ?? null,
            'Deskripsi informasi level {level}'
        );

        const details = document.createElement('details');
        details.className = 'pref-element-card card shadow-sm pref-lift';
        details.setAttribute('data-element-card', '1');
        details.dataset.elementSlug = elementSlug;
        details.dataset.defaultOpen = '0';
        details.open = false;

        details.innerHTML = `
            <summary>
                <div>
                    <div class="pref-element-title">${displayTitle}</div>
                    <div class="pref-element-meta" data-element-meta>0 sub topik, 0 pernyataan</div>
                </div>
                <span class="pref-element-arrow">â–¾</span>
            </summary>

            <div class="pref-element-body">
                <input type="hidden" name="elements[${elementSlug}][slug]" value="${elementSlug}" data-element-slug-input>
                <input type="hidden" name="elements[${elementSlug}][active]" value="1">

                <div class="pref-element-controls">
                    <div class="pref-weight-field pref-element-title-field">
                        <label class="form-label mb-1">Nama Element</label>
                        <input
                            type="text"
                            class="form-control"
                            name="elements[${elementSlug}][title]"
                            value="${title}"
                            placeholder="Nama element"
                            data-element-title-input
                        >
                    </div>

                    <div class="pref-weight-field">
                        <label class="form-label mb-1">Bobot Element (%)</label>
                        <input
                            type="number"
                            name="elements[${elementSlug}][weight]"
                            class="form-control pref-weight-input"
                            data-weight-type="element"
                            step="0.01"
                            min="0"
                            value="${weight}"
                        >
                        <div class="text-danger small mt-1 pref-weight-warn" data-weight-warn></div>
                    </div>

                    <div class="pref-card-actions ms-auto">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-add-subtopic>
                            Tambah Sub Topik
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-remove-element>
                            Hapus Element
                        </button>
                    </div>
                </div>

                <details class="pref-level-card pref-level-card-element" data-pref-slide>
                    <summary>Informasi Level Element (1-5)</summary>
                    <div class="pref-level-body">
                        <div class="pref-level-grid">
                            ${elementLevelFields}
                        </div>
                    </div>
                </details>

                <div class="pref-subtopic-stack" data-subtopics-container></div>
            </div>
        `;

        const subtopicsContainer = details.querySelector('[data-subtopics-container]');
        const subtopics = Array.isArray(options.subtopics) ? options.subtopics : [];

        if (subtopics.length > 0) {
            subtopics.forEach((subtopic) => {
                const rawSlug = normalizeSlug(subtopic.slug || '');
                const subtopicSlug = rawSlug || nextSubtopicSlug(details);
                const subtopicCard = createSubtopicCard(elementSlug, subtopicSlug, subtopic);
                subtopicsContainer.appendChild(subtopicCard);
            });
        } else {
            const subtopicSlug = nextSubtopicSlug(details);
            const subtopicCard = createSubtopicCard(elementSlug, subtopicSlug, {
                title: '',
                weight: '0',
            });
            subtopicsContainer.appendChild(subtopicCard);
        }

        updateElementMeta(details);
        return details;
    };

    const addSubtopicToElement = (elementCard, options = {}) => {
        if (!elementCard) return;

        const elementSlug = String(elementCard.dataset.elementSlug || '');
        if (!elementSlug) return;

        const subtopicsContainer = elementCard.querySelector('[data-subtopics-container]');
        if (!subtopicsContainer) return;

        const requestedSlug = normalizeSlug(options.slug || '');
        const subtopicSlug = requestedSlug || nextSubtopicSlug(elementCard);
        const defaultTitle = options.title ?? '';
        const subtopicCard = createSubtopicCard(elementSlug, subtopicSlug, {
            ...options,
            title: defaultTitle,
        });

        subtopicsContainer.appendChild(subtopicCard);
        updateElementMeta(elementCard);
    };

    page.addEventListener('click', (event) => {
        const resetDataTrigger = event.target.closest('[data-pref-reset-data-trigger]');
        if (resetDataTrigger) {
            event.preventDefault();

            openConfirmModal({
                title: 'Reset Data Element',
                message: 'Semua isian Element dan riwayat edit akan dihapus permanen. Tindakan ini tidak bisa dibatalkan. Lanjutkan?',
                label: 'Reset Data',
                kind: 'danger',
                trigger: resetDataTrigger,
                onConfirm: () => {
                    if (resetDataActionUrl !== '') {
                        preferenceForm.setAttribute('action', resetDataActionUrl);
                    }
                    preferenceForm.submit();
                },
            });
            return;
        }

        const archiveProgressTrigger = event.target.closest('[data-archive-progress-trigger]');
        if (archiveProgressTrigger) {
            event.preventDefault();
            if (!archiveProgressForm || !archiveProgressYearField || !archiveYearInput) {
                return;
            }

            const rawYear = String(archiveYearInput.value || '').trim();
            const budgetYear = Number.parseInt(rawYear, 10);
            if (!Number.isFinite(budgetYear) || budgetYear < 2000 || budgetYear > 2100) {
                window.alert('Tahun anggaran harus diisi antara 2000 sampai 2100.');
                archiveYearInput.focus();
                return;
            }

            openConfirmModal({
                title: 'Arsipkan Progress Penilaian',
                message: `Semua progress penilaian aktif akan disimpan ke arsip Tahun Anggaran ${budgetYear}. Lanjutkan?`,
                label: 'Arsipkan',
                kind: 'warning',
                trigger: archiveProgressTrigger,
                onConfirm: () => {
                    archiveProgressYearField.value = String(budgetYear);
                    archiveProgressForm.submit();
                },
            });
            return;
        }

        const loadArchiveTrigger = event.target.closest('[data-load-archive-trigger]');
        if (loadArchiveTrigger) {
            event.preventDefault();
            if (!loadArchiveForm || !loadArchiveIdField || !loadArchiveSelect) {
                return;
            }

            const archiveId = Number.parseInt(String(loadArchiveSelect.value || ''), 10);
            if (!Number.isFinite(archiveId) || archiveId <= 0) {
                window.alert('Pilih arsip tahun anggaran yang akan dimuat.');
                loadArchiveSelect.focus();
                return;
            }

            const selectedOption = loadArchiveSelect.options[loadArchiveSelect.selectedIndex];
            const archiveLabel = selectedOption ? selectedOption.textContent?.trim() || `ID ${archiveId}` : `ID ${archiveId}`;

            openConfirmModal({
                title: 'Pulihkan Arsip Progress',
                message: `Isian progress aktif akan diganti dengan arsip "${archiveLabel}". Notifikasi aktivitas tidak diubah. Tindakan ini tidak menghapus arsip. Lanjutkan?`,
                label: 'Pulihkan Arsip',
                kind: 'default',
                requiredPhrase: 'Inspektorat PPK',
                requiredPhraseLabel: 'Ketik kata kunci berikut untuk melanjutkan aksi ini:',
                requiredPhraseHint: 'Ketik: Inspektorat PPK',
                trigger: loadArchiveTrigger,
                onConfirm: () => {
                    loadArchiveIdField.value = String(archiveId);
                    loadArchiveForm.submit();
                },
            });
            return;
        }

        const elementSummary = event.target.closest('[data-element-card] > summary');
        if (elementSummary && page.contains(elementSummary)) {
            event.preventDefault();
            const elementCard = elementSummary.parentElement;
            toggleElementCard(elementCard);
            return;
        }

        const levelSummary = event.target.closest('[data-pref-slide] > summary');
        if (levelSummary && page.contains(levelSummary)) {
            event.preventDefault();
            const slideCard = levelSummary.parentElement;
            if (slideCard?.classList?.contains('pref-subtopic-card') && !slideCard.open) {
                collapseOtherSubtopicCards(slideCard);
            }
            toggleSlideDetails(slideCard);
            return;
        }

        const addElementButton = event.target.closest('[data-add-element]');
        if (addElementButton) {
            event.preventDefault();
            const elementSlug = nextElementSlug();
            const elementCard = createElementCard(elementSlug, {
                title: '',
                weight: '0',
            });
            elementsContainer.appendChild(elementCard);
            updateElementMeta(elementCard);
            collapseOtherElementCards(elementCard);
            openElementCard(elementCard);
            spotlightElementCard(elementCard);
            validateWeightGroups();
            return;
        }

        const removeElementButton = event.target.closest('[data-remove-element]');
        if (removeElementButton) {
            event.preventDefault();
            const elementCard = removeElementButton.closest('[data-element-card]');
            if (!elementCard) return;

            const totalElements = elementsContainer.querySelectorAll('[data-element-card]').length;
            if (totalElements <= 1) {
                window.alert('Minimal harus ada 1 element.');
                return;
            }

            const elementTitle = elementCard.querySelector('.pref-element-title')?.textContent?.trim() || 'Element';
            openConfirmModal({
                title: 'Hapus Element',
                message: `Element "${elementTitle}" akan dihapus dari struktur. Lanjutkan?`,
                label: 'Hapus Element',
                kind: 'danger',
                trigger: removeElementButton,
                onConfirm: () => {
                    elementCard.remove();
                    validateWeightGroups();
                    showToast({
                        type: 'success',
                        title: 'Berhasil',
                        message: `Element ${elementTitle} dihapus dari draft preferensi.`,
                    });
                },
            });
            return;
        }

        const addSubtopicButton = event.target.closest('[data-add-subtopic]');
        if (addSubtopicButton) {
            event.preventDefault();
            const elementCard = addSubtopicButton.closest('[data-element-card]');
            addSubtopicToElement(elementCard);
            validateWeightGroups();
            return;
        }

        const removeSubtopicButton = event.target.closest('[data-remove-subtopic]');
        if (removeSubtopicButton) {
            event.preventDefault();
            const subtopicCard = removeSubtopicButton.closest('[data-subtopic-card]');
            const elementCard = removeSubtopicButton.closest('[data-element-card]');
            if (!subtopicCard || !elementCard) return;

            const subtopicCount = elementCard.querySelectorAll('[data-subtopic-card]').length;
            if (subtopicCount <= 1) {
                window.alert('Minimal harus ada 1 sub topik pada setiap element.');
                return;
            }

            const subtopicTitle = stripSubtopicPrefix(subtopicCard.querySelector('[data-subtopic-title-input], input[name*="[title]"]')?.value || '')
                || subtopicCard.querySelector('[data-subtopic-heading]')?.textContent?.trim()
                || 'Sub Topik';
            openConfirmModal({
                title: 'Hapus Sub Topik',
                message: `Sub topik "${subtopicTitle}" akan dihapus. Lanjutkan?`,
                label: 'Hapus Sub Topik',
                kind: 'danger',
                trigger: removeSubtopicButton,
                onConfirm: () => {
                    subtopicCard.remove();
                    updateElementMeta(elementCard);
                    validateWeightGroups();
                    showToast({
                        type: 'success',
                        title: 'Berhasil',
                        message: `Sub topik ${subtopicTitle} dihapus dari draft preferensi.`,
                    });
                },
            });
            return;
        }

        const addRowButton = event.target.closest('[data-add-row]');
        if (addRowButton) {
            event.preventDefault();
            const subtopicCard = addRowButton.closest('[data-subtopic-card]');
            const rowsContainer = subtopicCard ? subtopicCard.querySelector('[data-rows-container]') : null;
            if (!rowsContainer) return;

            addRowToContainer(rowsContainer);
            updateElementMeta(addRowButton.closest('[data-element-card]'));
            validateWeightGroups();
            return;
        }

        const removeRowButton = event.target.closest('[data-remove-row]');
        if (!removeRowButton) return;

        event.preventDefault();
        const row = removeRowButton.closest('[data-row-item]');
        const rowsContainer = removeRowButton.closest('[data-rows-container]');
        if (!row || !rowsContainer) return;

        row.remove();

        const remainingRows = rowsContainer.querySelectorAll('[data-row-item]');
        if (remainingRows.length === 0) {
            addRowToContainer(rowsContainer, { label: 'Pernyataan 1', weight: '100', active: true });
        }

        updateElementMeta(removeRowButton.closest('[data-element-card]'));
        validateWeightGroups();
        showToast({
            type: 'success',
            title: 'Berhasil',
            message: 'Pernyataan dihapus dari draft preferensi.',
            duration: 2600,
        });
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-pref-confirm-close]')) {
            event.preventDefault();
            closeConfirmModal();
            return;
        }

        if (event.target.closest('#prefActionConfirmModalConfirm')) {
            event.preventDefault();
            const action = pendingAction;
            closeConfirmModal();
            action?.();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && confirmModal?.classList.contains('is-open')) {
            closeConfirmModal();
        }
    });

    document.addEventListener('livewire:navigated', closeConfirmModal);
    window.addEventListener('scroll', syncModalToViewport, { passive: true });
    window.addEventListener('resize', syncModalToViewport);
    window.addEventListener('pageshow', syncModalToViewport);

    page.addEventListener('input', (event) => {
        if (event.target.matches('[data-element-title-input], .pref-element-title-field input')) {
            const elementCard = event.target.closest('[data-element-card]');
            updateElementMeta(elementCard);
            validateWeightGroups();
            return;
        }

        if (event.target.matches('[data-subtopic-title-input], .pref-subtopic-title-wrap input[name*="[title]"]')) {
            const elementCard = event.target.closest('[data-element-card]');
            updateElementMeta(elementCard);
            validateWeightGroups();
            return;
        }

        if (event.target.matches('.pref-weight-input')) {
            validateWeightGroups();
        }
    });

    preferenceForm.addEventListener('submit', (event) => {
        const isResetDataSubmission = preferenceForm.getAttribute('action') === resetDataActionUrl;

        if (isResetDataSubmission) {
            return;
        }

        if (updateActionUrl !== '') {
            preferenceForm.setAttribute('action', updateActionUrl);
        }

        const elementCards = Array.from(elementsContainer.querySelectorAll('[data-element-card]'));
        elementCards.forEach((elementCard, index) => {
            const titleInput = elementCard.querySelector('[data-element-title-input], .pref-element-title-field input');
            if (!titleInput) return;

            const cleanName = stripElementPrefix(titleInput.value);
            const canonicalTitle = buildElementDisplayTitleFromSlug(
                elementCard.dataset.elementSlug || '',
                cleanName,
                index + 1
            );
            titleInput.value = canonicalTitle;

            const subtopicCards = Array.from(elementCard.querySelectorAll('[data-subtopic-card]'));
            subtopicCards.forEach((subtopicCard, subtopicIndex) => {
                const subtopicTitleInput = subtopicCard.querySelector('[data-subtopic-title-input], .pref-subtopic-title-wrap input[name*="[title]"]');
                if (!subtopicTitleInput) return;

                const cleanSubtopicName = stripSubtopicPrefix(subtopicTitleInput.value);
                const canonicalSubtopicTitle = buildSubtopicDisplayTitle(cleanSubtopicName, subtopicIndex + 1);
                subtopicTitleInput.value = canonicalSubtopicTitle;
            });
        });
    });

    page.querySelectorAll('[data-element-card]').forEach((elementCard) => {
        const shouldOpen = String(elementCard.dataset.defaultOpen || '') === '1';
        elementCard.open = shouldOpen;
        elementCard.querySelectorAll('[data-subtopic-card]').forEach((subtopicCard) => {
            subtopicCard.open = false;
            const subtopicBody = getSlideDetailsBody(subtopicCard);
            clearSlideDetailsAnimationState(subtopicCard, subtopicBody);
        });
        const titleInput = elementCard.querySelector('.pref-element-title-field input');
        if (titleInput) {
            titleInput.setAttribute('data-element-title-input', '1');
        }
        updateElementMeta(elementCard);
    });
    validateWeightGroups();
})();
</script>
@endpush

