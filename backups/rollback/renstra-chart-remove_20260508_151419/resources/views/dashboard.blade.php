@extends('layouts.dashboard-shell')
@php
    $dashboardUi = is_array($dashboardUi ?? null) ? $dashboardUi : [];
    $pageTitle = $pageTitle ?? (string) ($dashboardUi['page_title'] ?? 'Dashboard Kapabilitas APIP');
    $overallLevel = is_numeric($dashboardUi['overall_level'] ?? null) ? (int) $dashboardUi['overall_level'] : null;
    $overallLevelClass = (string) ($dashboardUi['overall_level_class'] ?? 'pending');
    $overallLevelLabel = (string) ($dashboardUi['overall_level_label'] ?? 'Belum Dinilai');
    $overallPredikat = (string) ($dashboardUi['overall_predikat'] ?? 'Belum Dinilai');
    $overallDescription = (string) ($dashboardUi['overall_description'] ?? '');
    $overallLevelQa = is_numeric($dashboardUi['overall_level_qa'] ?? null) ? (int) $dashboardUi['overall_level_qa'] : null;
    $overallLevelQaClass = (string) ($dashboardUi['overall_level_qa_class'] ?? 'pending');
    $overallLevelLabelQa = (string) ($dashboardUi['overall_level_qa_label'] ?? 'Belum Dinilai');
    $overallPredikatQa = (string) ($dashboardUi['overall_predikat_qa'] ?? 'Belum Dinilai');
    $overallDescriptionQa = (string) ($dashboardUi['overall_description_qa'] ?? '');
    $weightHintText = (string) ($dashboardUi['weight_hint_text'] ?? '');
    $weightedScoreHintText = (string) ($dashboardUi['weighted_score_hint_text'] ?? 'Rumus Skor Tertimbang: Bobot (%) x Skor');
    $levelPredikatHintText = (string) ($dashboardUi['level_predikat_hint_text'] ?? '');
    $segmentArcLength = (float) ($dashboardUi['segment_arc_length'] ?? 314.16);
    $segmentLength = (float) ($dashboardUi['segment_length'] ?? ($segmentArcLength / 5));
    $segmentOffsets = is_array($dashboardUi['segment_offsets'] ?? null)
        ? $dashboardUi['segment_offsets']
        : [0, -$segmentLength, -$segmentLength * 2, -$segmentLength * 3, -$segmentLength * 4];
    $renstraTrendSeries = is_array($renstraTrendSeries ?? null) ? $renstraTrendSeries : [];
    $canManageRenstraTrend = (bool) ($canManageRenstraTrend ?? false);
    $formatPercent = static function (float $value): string {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    };
@endphp

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/dashboard-home.css') }}">
@endpush

@section('content')
    <div class="apip-home-page qa-display-off" id="apipHomePage">
        <section class="apip-home-hero">
            <article class="card apip-overview-card">
                <div class="apip-overview-top">
                    <p class="apip-eyebrow">Dashboard Utama</p>
                    <div class="apip-overview-actions">
                        <button
                            type="button"
                            class="apip-overview-hint hint-bubble-trigger"
                            data-hint="{{ $weightHintText }}"
                            aria-label="Informasi bobot skor tertimbang">
                            ?
                        </button>
                    </div>
                </div>
                <h1 class="apip-title">Skor dan Level Kapabilitas APIP</h1>
                <p class="apip-subtitle">
                    Ringkasan nilai utama dari seluruh Element 1-5. Nilai akhir dihitung dari total Skor Tertimbang tiap element.
                </p>

                <div class="apip-overview-kpis">
                    <div class="apip-kpi">
                        <div class="kpi-label">Skor Kapabilitas APIP (Tertimbang) <span class="qa-mandiri-suffix">(Mandiri)</span></div>
                        <div class="kpi-value">{{ number_format((float) ($overallWeightedScore ?? 0), 2) }}</div>
                        <div class="kpi-note">Total skor tertimbang seluruh element</div>
                    </div>
                    <div class="apip-kpi">
                        <div class="kpi-label">Level Kapabilitas APIP <span class="qa-mandiri-suffix">(Mandiri)</span></div>
                        <div class="kpi-value">{{ $overallLevelLabel }}</div>
                        <div class="kpi-note">{{ $overallPredikat }}</div>
                    </div>
                    <div class="apip-kpi qa-only">
                        <div class="kpi-label">Skor Kapabilitas APIP (Tertimbang QA)</div>
                        <div class="kpi-value">{{ $overallLevelQa !== null ? number_format((float) ($overallWeightedScoreQa ?? 0), 2) : '-' }}</div>
                        <div class="kpi-note">Total skor tertimbang hasil verifikasi QA</div>
                    </div>
                    <div class="apip-kpi qa-only">
                        <div class="kpi-label qa-level-font">Level Kapabilitas APIP (QA)</div>
                        <div class="kpi-value">{{ $overallLevelLabelQa }}</div>
                        <div class="kpi-note qa-level-font">{{ $overallPredikatQa }}</div>
                    </div>
                </div>
            </article>

            <article class="card apip-meter-card {{ $overallLevelClass }}">
                <div class="apip-meter-head">
                    <h3 class="meter-title">Level Kapabilitas APIP</h3>
                    <button
                        type="button"
                        class="apip-meter-hint hint-bubble-trigger"
                        data-hint="{{ $levelPredikatHintText }}"
                        aria-label="Informasi level dan predikat kapabilitas APIP">
                        ?
                    </button>
                </div>
                <div class="apip-meter-wrap">
                    <svg viewBox="0 0 240 140" class="apip-meter-svg" role="img" aria-label="Speedometer skor kapabilitas APIP">
                        <path d="M20 120 A100 100 0 0 1 220 120" class="meter-track"></path>
                        <path d="M20 120 A100 100 0 0 1 220 120" class="meter-segment seg-l1" style="stroke-dasharray: {{ number_format((float) $segmentLength, 2, '.', '') }} {{ number_format((float) $segmentArcLength, 2, '.', '') }}; stroke-dashoffset: {{ number_format((float) $segmentOffsets[0], 2, '.', '') }};"></path>
                        <path d="M20 120 A100 100 0 0 1 220 120" class="meter-segment seg-l2" style="stroke-dasharray: {{ number_format((float) $segmentLength, 2, '.', '') }} {{ number_format((float) $segmentArcLength, 2, '.', '') }}; stroke-dashoffset: {{ number_format((float) $segmentOffsets[1], 2, '.', '') }};"></path>
                        <path d="M20 120 A100 100 0 0 1 220 120" class="meter-segment seg-l3" style="stroke-dasharray: {{ number_format((float) $segmentLength, 2, '.', '') }} {{ number_format((float) $segmentArcLength, 2, '.', '') }}; stroke-dashoffset: {{ number_format((float) $segmentOffsets[2], 2, '.', '') }};"></path>
                        <path d="M20 120 A100 100 0 0 1 220 120" class="meter-segment seg-l4" style="stroke-dasharray: {{ number_format((float) $segmentLength, 2, '.', '') }} {{ number_format((float) $segmentArcLength, 2, '.', '') }}; stroke-dashoffset: {{ number_format((float) $segmentOffsets[3], 2, '.', '') }};"></path>
                        <path d="M20 120 A100 100 0 0 1 220 120" class="meter-segment seg-l5" style="stroke-dasharray: {{ number_format((float) $segmentLength, 2, '.', '') }} {{ number_format((float) $segmentArcLength, 2, '.', '') }}; stroke-dashoffset: {{ number_format((float) $segmentOffsets[4], 2, '.', '') }};"></path>

                        @if ($overallLevelQa !== null)
                            <g class="meter-needle-group meter-needle-group-qa qa-only qa-meter-overlay {{ $overallLevelQaClass }}" data-target-deg="{{ number_format((float) ($meterNeedleDegQa ?? -90), 2, '.', '') }}" transform="rotate({{ number_format((float) ($meterNeedleDegQa ?? -90), 2, '.', '') }} 120 120)">
                                <line x1="120" y1="120" x2="120" y2="40" class="meter-needle meter-needle-qa"></line>
                                <circle cx="120" cy="120" r="6.4" class="meter-needle-core meter-needle-core-qa"></circle>
                            </g>
                        @endif
                        <g class="meter-needle-group" data-target-deg="{{ number_format((float) ($meterNeedleDeg ?? -90), 2, '.', '') }}" transform="rotate(-90 120 120)">
                            <line x1="120" y1="120" x2="120" y2="34" class="meter-needle"></line>
                            <circle cx="120" cy="120" r="8" class="meter-needle-core"></circle>
                        </g>
                    </svg>
                </div>
                <div class="meter-readout">
                    <div class="meter-score" data-final-score="{{ number_format((float) ($overallWeightedScore ?? 0), 2, '.', '') }}">0.00</div>
                    <div class="meter-score-qa qa-only {{ $overallLevelQaClass }}">
                        QA: <span class="meter-score-qa-value">{{ $overallLevelQa !== null ? number_format((float) ($overallWeightedScoreQa ?? 0), 2) : '-' }}</span>
                    </div>
                    <div class="meter-level {{ $overallLevelClass }}"><span class="qa-mandiri-prefix">Mandiri: </span>{{ $overallLevelLabel }} - <span class="meter-predikat">{{ $overallPredikat }}</span></div>
                    <div class="meter-level meter-level-qa qa-only qa-level-font {{ $overallLevelQaClass }}">QA: {{ $overallLevelLabelQa }} - <span class="meter-predikat">{{ $overallPredikatQa }}</span></div>
                    @if ($overallDescription !== '')
                        <p class="meter-note">{{ $overallDescription }}</p>
                    @endif
                    @if ($overallDescriptionQa !== '')
                        <p class="meter-note qa-only qa-level-font">QA: {{ $overallDescriptionQa }}</p>
                    @endif
                </div>
            </article>
        </section>

        <section class="card apip-element-summary-card apip-renstra-trend-card">
            <div class="section-head apip-summary-head">
                <div class="apip-summary-head-top">
                    <h3>Grafik Hasil Penilaian Peningkatan Kapabilitas APIP</h3>
                    <div class="apip-summary-actions">
                        <button
                            type="button"
                            class="apip-summary-hint hint-bubble-trigger"
                            data-hint="Grafik ini merangkum tren skor per tahun. Setiap tahun menampilkan dua batang: Hasil Penilaian (H) dan Target Renstra (T)."
                            aria-label="Informasi grafik tren renstra">
                            ?
                        </button>
                    </div>
                </div>
                <p>Grafik tunggal per tahun dengan legenda: Hasil Penilaian (H) dan Target Renstra (T).</p>
            </div>

            @php
                $renstraItems = collect($renstraTrendSeries)->values();
                $renstraRangeGroups = [];
                $currentRangeLabel = null;
                $currentRangeStart = 1;
                $currentRangeSpan = 0;
                $currentRangeYearStart = null;
                $currentRangeYearEnd = null;

                foreach ($renstraItems as $rangeIndex => $rangeItem) {
                    $rangeLabel = (string) ($rangeItem['renstra_label'] ?? '-');
                    $rangeYearLabel = (string) ($rangeItem['year_label'] ?? '-');

                    if ($currentRangeLabel === null) {
                        $currentRangeLabel = $rangeLabel;
                        $currentRangeStart = $rangeIndex + 1;
                        $currentRangeSpan = 1;
                        $currentRangeYearStart = $rangeYearLabel;
                        $currentRangeYearEnd = $rangeYearLabel;
                        continue;
                    }

                    if ($rangeLabel === $currentRangeLabel) {
                        $currentRangeSpan++;
                        $currentRangeYearEnd = $rangeYearLabel;
                        continue;
                    }

                    $renstraRangeGroups[] = [
                        'label' => $currentRangeLabel,
                        'start' => $currentRangeStart,
                        'span' => $currentRangeSpan,
                        'year_start' => $currentRangeYearStart,
                        'year_end' => $currentRangeYearEnd,
                    ];

                    $currentRangeLabel = $rangeLabel;
                    $currentRangeStart = $rangeIndex + 1;
                    $currentRangeSpan = 1;
                    $currentRangeYearStart = $rangeYearLabel;
                    $currentRangeYearEnd = $rangeYearLabel;
                }

                if ($currentRangeLabel !== null) {
                    $renstraRangeGroups[] = [
                        'label' => $currentRangeLabel,
                        'start' => $currentRangeStart,
                        'span' => $currentRangeSpan,
                        'year_start' => $currentRangeYearStart,
                        'year_end' => $currentRangeYearEnd,
                    ];
                }

                $renstraChartSeries = $renstraItems
                    ->map(function ($trendItem): array {
                        $year = is_numeric($trendItem['year'] ?? null) ? (int) $trendItem['year'] : 0;
                        $hasilScore = is_numeric($trendItem['hasil_score'] ?? null)
                            ? (float) number_format(max(0, min(5, (float) $trendItem['hasil_score'])), 2, '.', '')
                            : null;
                        $targetScore = is_numeric($trendItem['target_score'] ?? null)
                            ? (float) number_format(max(0, min(5, (float) $trendItem['target_score'])), 2, '.', '')
                            : null;

                        return [
                            'year' => $year,
                            'x' => $year > 0 ? sprintf('%04d-01-01', $year) : null,
                            'hasil_score' => $hasilScore,
                            'target_score' => $targetScore,
                            'renstra_label' => (string) ($trendItem['renstra_label'] ?? '-'),
                        ];
                    })
                    ->filter(fn (array $item): bool => is_numeric($item['year'] ?? null) && (int) $item['year'] > 0)
                    ->values()
                    ->all();
            @endphp
            @if ($renstraItems->isEmpty())
                <div class="empty-state apip-recap-empty">Data tren renstra belum tersedia.</div>
            @else
                <div class="apip-renstra-chart-legend">
                    <div class="apip-renstra-chart-legend-main">
                        @if($canManageRenstraTrend)
                            <button
                                type="button"
                                class="apip-renstra-legend-btn apip-renstra-legend-item"
                                data-renstra-modal-open
                                aria-label="Kelola hasil penilaian tahunan">
                                <span class="apip-renstra-legend-swatch is-hasil" aria-hidden="true"></span>
                                Hasil Penilaian
                            </button>
                            <button
                                type="button"
                                class="apip-renstra-legend-btn apip-renstra-legend-item"
                                data-renstra-modal-open
                                aria-label="Kelola target renstra tahunan">
                                <span class="apip-renstra-legend-swatch is-target" aria-hidden="true"></span>
                                Target Renstra
                            </button>
                        @else
                            <span class="apip-renstra-legend-item apip-renstra-legend-item--note" aria-hidden="true">
                                Input Hasil dan Target dikelola Administrator.
                            </span>
                        @endif
                    </div>
                    <div class="apip-renstra-scale-switch" role="group" aria-label="Pilih skala waktu grafik Renstra">
                        <button type="button" class="apip-renstra-scale-btn" data-renstra-scale="3y">3Y</button>
                        <button type="button" class="apip-renstra-scale-btn" data-renstra-scale="5y">5Y</button>
                        <button type="button" class="apip-renstra-scale-btn" data-renstra-scale="ytd">YTD</button>
                        <button type="button" class="apip-renstra-scale-btn is-active" data-renstra-scale="renstra">Per Renstra</button>
                    </div>
                </div>

                <div
                    class="apip-renstra-bar-chart apip-renstra-bar-chart--chartjs"
                    data-renstra-chartjs
                    data-current-year="{{ now()->year }}"
                    data-scale-active="renstra"
                    data-series='@json($renstraChartSeries)'
                    role="img"
                    aria-label="Diagram combo hasil, target, dan trend renstra per tahun">
                    <div class="apip-renstra-chartjs-wrap">
                        <canvas id="apipRenstraChartCanvas" class="apip-renstra-chartjs-canvas" aria-label="Chart hasil dan target renstra"></canvas>
                    </div>

                    <div class="apip-renstra-ranges-wrap" aria-hidden="true">
                        <div class="apip-renstra-ranges" style="--year-count: {{ max(1, $renstraItems->count()) }};">
                            @foreach ($renstraRangeGroups as $rangeGroup)
                                <span
                                    class="apip-renstra-range-chip"
                                    style="grid-column: {{ (int) ($rangeGroup['start'] ?? 1) }} / span {{ max(1, (int) ($rangeGroup['span'] ?? 1)) }};">
                                    {{ (string) ($rangeGroup['label'] ?? '-') }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($canManageRenstraTrend)
                    <div
                        class="apip-level-modal apip-renstra-input-modal"
                        id="apipRenstraInputModal"
                        data-open-on-load="{{ ($errors->has('entries') || $errors->has('entries.*.hasil_score') || $errors->has('entries.*.target_score')) ? '1' : '0' }}"
                        hidden>
                        <div class="apip-level-modal__backdrop" data-renstra-modal-close></div>
                        <div
                            class="apip-level-modal__dialog apip-renstra-input-modal__dialog is-level-4 has-footer"
                            role="dialog"
                            aria-modal="true"
                            aria-labelledby="apipRenstraInputModalTitle"
                            aria-describedby="apipRenstraInputModalDesc">
                            <div class="apip-level-modal__header">
                                <button type="button" class="apip-level-modal__close" data-renstra-modal-close aria-label="Tutup form renstra">&times;</button>
                                <p class="apip-level-modal__eyebrow">Administrator</p>
                                <h4 class="apip-level-modal__title" id="apipRenstraInputModalTitle">Input Hasil & Target Renstra</h4>
                                <p class="apip-level-modal__desc" id="apipRenstraInputModalDesc">
                                    Isi nilai 0 sampai 5. Kosongkan field jika ingin kembali memakai nilai otomatis sistem.
                                </p>
                            </div>
                            <div class="apip-level-modal__body">
                                <form id="apipRenstraInputForm" method="POST" action="{{ route('dashboard.renstra-trend.update') }}" class="apip-renstra-input-form">
                                    @csrf
                                    <div class="apip-renstra-input-grid">
                                        <div class="apip-renstra-input-grid__head">Tahun</div>
                                        <div class="apip-renstra-input-grid__head">Renstra</div>
                                        <div class="apip-renstra-input-grid__head">Hasil (H)</div>
                                        <div class="apip-renstra-input-grid__head">Target (T)</div>

                                        @foreach ($renstraItems as $trendIndex => $trendItem)
                                            @php
                                                $year = (int) ($trendItem['year'] ?? 0);
                                                $hasilInputValue = (string) ($trendItem['hasil_input_value'] ?? '');
                                                $targetInputValue = (string) ($trendItem['target_input_value'] ?? '');
                                                $hasilPlaceholder = (string) ($trendItem['hasil_score_label'] ?? '-');
                                                $targetPlaceholder = (string) ($trendItem['target_score_label'] ?? '-');
                                            @endphp
                                            <input type="hidden" name="entries[{{ $trendIndex }}][year]" value="{{ $year }}">
                                            <div class="apip-renstra-input-grid__year">{{ $year }}</div>
                                            <div class="apip-renstra-input-grid__label">{{ (string) ($trendItem['renstra_label'] ?? '-') }}</div>
                                            <div class="apip-renstra-input-grid__cell">
                                                <input
                                                    type="text"
                                                    inputmode="decimal"
                                                    class="apip-renstra-input"
                                                    name="entries[{{ $trendIndex }}][hasil_score]"
                                                    value="{{ old('entries.'.$trendIndex.'.hasil_score', $hasilInputValue) }}"
                                                    placeholder="{{ $hasilPlaceholder !== '-' ? $hasilPlaceholder : '0.00' }}"
                                                    autocomplete="off">
                                            </div>
                                            <div class="apip-renstra-input-grid__cell">
                                                <input
                                                    type="text"
                                                    inputmode="decimal"
                                                    class="apip-renstra-input"
                                                    name="entries[{{ $trendIndex }}][target_score]"
                                                    value="{{ old('entries.'.$trendIndex.'.target_score', $targetInputValue) }}"
                                                    placeholder="{{ $targetPlaceholder !== '-' ? $targetPlaceholder : '0.00' }}"
                                                    autocomplete="off">
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('entries')
                                        <div class="apip-renstra-input-error">{{ $message }}</div>
                                    @enderror
                                </form>
                            </div>
                            <div class="apip-level-modal__footer apip-level-modal__footer--actions">
                                <button type="button" class="apip-renstra-input-cancel apip-level-modal__footer-btn" data-renstra-modal-close>Tutup</button>
                                <button type="submit" class="apip-renstra-input-submit apip-level-modal__footer-btn" form="apipRenstraInputForm">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </section>

        <section class="card apip-element-summary-card apip-element-recap-card">
            @php
                $levelRecap = [
                    'level1' => [
                        'label' => 'Level 1',
                        'predikat' => 'Rintisan',
                        'level_class' => 'is-level-1',
                        'description' => 'Inisiatif pengawasan masih pada tahap awal dan belum berjalan konsisten.',
                        'elements' => [],
                    ],
                    'level2' => [
                        'label' => 'Level 2',
                        'predikat' => 'Terstruktur',
                        'level_class' => 'is-level-2',
                        'description' => 'Praktik pengawasan sudah terstruktur, tetapi implementasi belum merata.',
                        'elements' => [],
                    ],
                    'level3' => [
                        'label' => 'Level 3',
                        'predikat' => 'Memadai',
                        'level_class' => 'is-level-3',
                        'description' => 'Praktik pengawasan memadai dan konsisten dengan ruang perbaikan pada integrasi.',
                        'elements' => [],
                    ],
                    'level4' => [
                        'label' => 'Level 4',
                        'predikat' => 'Terintegrasi',
                        'level_class' => 'is-level-4',
                        'description' => 'Pengawasan terintegrasi lintas proses dengan dukungan data untuk keputusan strategis.',
                        'elements' => [],
                    ],
                    'level5' => [
                        'label' => 'Level 5',
                        'predikat' => 'Optimal',
                        'level_class' => 'is-level-5',
                        'description' => 'Kapabilitas optimal, adaptif, dan proaktif dalam menghasilkan nilai tambah organisasi.',
                        'elements' => [],
                    ],
                    'pending' => [
                        'label' => 'Belum Dinilai',
                        'predikat' => 'Belum Dinilai',
                        'level_class' => 'pending',
                        'description' => 'Element belum memiliki data penilaian mandiri yang cukup untuk menentukan level.',
                        'elements' => [],
                    ],
                ];

                foreach ($elements as $element) {
                    $levelNumber = is_numeric($element['level'] ?? null) ? (int) $element['level'] : null;
                    $bucketKey = ($levelNumber !== null && $levelNumber >= 1 && $levelNumber <= 5)
                        ? 'level'.$levelNumber
                        : 'pending';

                    $levelRecap[$bucketKey]['elements'][] = [
                        'slug' => (string) ($element['slug'] ?? ''),
                        'title' => (string) ($element['title'] ?? 'Element'),
                        'level_label' => (string) ($element['level_label'] ?? 'Belum Dinilai'),
                        'predikat' => (string) ($element['predikat'] ?? 'Belum Dinilai'),
                        'score' => is_numeric($element['score'] ?? null) ? number_format((float) $element['score'], 2) : '-',
                        'weighted_score' => number_format((float) ($element['weighted_score'] ?? 0), 2),
                        'subtopic_count' => (int) ($element['subtopic_count'] ?? count((array) ($element['subtopics'] ?? []))),
                        'assessed_subtopic_count' => (int) ($element['assessed_subtopic_count'] ?? 0),
                        'subtopic_details' => collect((array) ($element['subtopics'] ?? []))
                            ->map(function (array $subtopic): array {
                                $title = trim((string) ($subtopic['title'] ?? ''));
                                if ($title === '') {
                                    $title = trim((string) ($subtopic['slug'] ?? 'Sub Topik'));
                                }
                                $levelLabel = trim((string) ($subtopic['level_label'] ?? '-'));
                                $levelNote = trim((string) ($subtopic['level_note'] ?? $subtopic['description'] ?? ''));
                                if ($levelNote === '') {
                                    $levelNote = 'Belum ada informasi level sub topik.';
                                }

                                return [
                                    'title' => $title,
                                    'level_label' => $levelLabel !== '' ? $levelLabel : '-',
                                    'level_note' => $levelNote,
                                ];
                            })
                            ->filter(fn (array $subtopic): bool => ($subtopic['title'] ?? '') !== '')
                            ->values()
                            ->all(),
                        'description' => (string) ($element['description'] ?? ''),
                        'can_open' => (bool) ($element['can_open'] ?? true),
                    ];
                }

                $levelRecapPayload = collect($levelRecap)
                    ->map(function (array $bucket): array {
                        return [
                            'label' => (string) ($bucket['label'] ?? ''),
                            'predikat' => (string) ($bucket['predikat'] ?? ''),
                            'description' => (string) ($bucket['description'] ?? ''),
                            'count' => count($bucket['elements'] ?? []),
                            'elements' => collect((array) ($bucket['elements'] ?? []))
                                ->map(function (array $item): array {
                                    return [
                                        'title' => (string) ($item['title'] ?? ''),
                                        'level_label' => (string) ($item['level_label'] ?? 'Belum Dinilai'),
                                        'predikat' => (string) ($item['predikat'] ?? 'Belum Dinilai'),
                                        'score' => (string) ($item['score'] ?? '-'),
                                        'weighted_score' => (string) ($item['weighted_score'] ?? '0.00'),
                                        'subtopic_count' => (int) ($item['subtopic_count'] ?? 0),
                                        'assessed_subtopic_count' => (int) ($item['assessed_subtopic_count'] ?? 0),
                                        'subtopic_details' => collect((array) ($item['subtopic_details'] ?? []))
                                            ->map(function (array $subtopic): array {
                                                return [
                                                    'title' => trim((string) ($subtopic['title'] ?? '')),
                                                    'level_label' => trim((string) ($subtopic['level_label'] ?? '-')),
                                                    'level_note' => trim((string) ($subtopic['level_note'] ?? 'Belum ada informasi level sub topik.')),
                                                ];
                                            })
                                            ->filter(fn (array $subtopic): bool => ($subtopic['title'] ?? '') !== '')
                                            ->all(),
                                    ];
                                })
                                ->values()
                                ->all(),
                        ];
                    })
                    ->all();

                $totalElementCount = count($elements);
                $assessedElementCount = $totalElementCount - count($levelRecap['pending']['elements']);
                $pendingElementCount = count($levelRecap['pending']['elements']);
                $pendingBucket = $levelRecap['pending'];
                $mainLevelBuckets = collect($levelRecap)->except('pending')->all();
            @endphp
            <div class="section-head apip-summary-head">
                <div class="apip-summary-head-top">
                    <h3>Rekapitulasi Level Element</h3>
                    <div class="apip-summary-actions">
                        <button
                            type="button"
                            class="apip-summary-hint hint-bubble-trigger"
                            data-hint="Ringkasan ini menampilkan persebaran level mandiri per element. Gunakan tombol Info Level untuk membaca penjelasan level tanpa menambah detail di dashboard utama."
                            aria-label="Informasi rekapitulasi level element">
                            ?
                        </button>
                    </div>
                </div>
                <p>Dashboard ringkas: fokus pada element yang sudah berada di Level 1-5 dan yang belum dinilai.</p>
            </div>

            <div class="apip-recap-kpi-strip">
                <div class="apip-recap-kpi">
                    <span>Total Element</span>
                    <strong>{{ $totalElementCount }}</strong>
                </div>
                <div class="apip-recap-kpi">
                    <span>Sudah Dinilai</span>
                    <strong>{{ $assessedElementCount }}</strong>
                </div>
                <div class="apip-recap-kpi">
                    <span>Belum Dinilai</span>
                    <strong>{{ $pendingElementCount }}</strong>
                </div>
            </div>

            <div class="apip-recap-grid">
                @foreach ($mainLevelBuckets as $levelKey => $bucket)
                    <article class="apip-recap-level-card {{ $bucket['level_class'] }}">
                        <div class="apip-recap-level-head">
                            <div class="apip-recap-level-title-wrap">
                                <span class="level-chip {{ $bucket['level_class'] }}">{{ $bucket['label'] }}</span>
                                <h4>{{ $bucket['predikat'] }}</h4>
                            </div>
                            <button
                                type="button"
                                class="apip-level-info-btn"
                                data-level-modal-open="{{ $levelKey }}"
                                aria-label="Lihat informasi {{ $bucket['label'] }}">
                                Info Level
                            </button>
                        </div>
                        <div class="apip-recap-level-count">
                            <strong>{{ count($bucket['elements']) }}</strong>
                            <span>element</span>
                        </div>
                        <div class="apip-recap-level-list">
                            @forelse ($bucket['elements'] as $item)
                                <div class="apip-recap-element-item">
                                    <span class="apip-recap-element-name">{{ $item['title'] }}</span>
                                    @if(($item['can_open'] ?? false) && ($item['slug'] ?? '') !== '')
                                        <a class="btn-open-element" href="{{ route('elements.show', $item['slug']) }}">Buka</a>
                                    @endif
                                </div>
                            @empty
                                <div class="empty-state apip-recap-empty">Belum ada element pada kategori ini.</div>
                            @endforelse
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($pendingElementCount > 0)
                <div class="apip-recap-pending-row">
                    <article class="apip-recap-level-card pending apip-recap-level-card-pending-row">
                        <div class="apip-recap-level-head">
                            <div class="apip-recap-level-title-wrap">
                                <span class="level-chip pending">{{ $pendingBucket['label'] }}</span>
                                <h4>Perlu Tindak Lanjut Penilaian</h4>
                            </div>
                            <button
                                type="button"
                                class="apip-level-info-btn"
                                data-level-modal-open="pending"
                                aria-label="Lihat informasi {{ $pendingBucket['label'] }}">
                                Info Level
                            </button>
                        </div>
                        <div class="apip-recap-level-count">
                            <strong>{{ $pendingElementCount }}</strong>
                            <span>element</span>
                        </div>
                        <div class="apip-recap-level-list">
                            @foreach ($pendingBucket['elements'] as $item)
                                <div class="apip-recap-element-item">
                                    <span class="apip-recap-element-name">{{ $item['title'] }}</span>
                                    @if(($item['can_open'] ?? false) && ($item['slug'] ?? '') !== '')
                                        <a class="btn-open-element" href="{{ route('elements.show', $item['slug']) }}">Buka</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>
            @endif

            <div class="apip-level-modal" id="apipLevelModal" hidden>
                <div class="apip-level-modal__backdrop" data-level-modal-close></div>
                <div
                    class="apip-level-modal__dialog has-footer"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="apipLevelModalTitle"
                    aria-describedby="apipLevelModalDesc">
                    <div class="apip-level-modal__header">
                        <button type="button" class="apip-level-modal__close" data-level-modal-close aria-label="Tutup info level">&times;</button>
                        <p class="apip-level-modal__eyebrow" id="apipLevelModalEyebrow">Informasi Level</p>
                        <h4 class="apip-level-modal__title" id="apipLevelModalTitle">Level</h4>
                        <p class="apip-level-modal__desc" id="apipLevelModalDesc"></p>
                    </div>
                    <div class="apip-level-modal__body">
                        <div class="apip-level-modal__count">
                            Jumlah Element: <strong id="apipLevelModalCount">0</strong>
                        </div>
                        <div class="apip-level-modal__list" id="apipLevelModalList"></div>
                    </div>
                    <div class="apip-level-modal__footer">
                        <button type="button" class="apip-renstra-input-cancel apip-level-modal__footer-btn" data-level-modal-close>Tutup</button>
                    </div>
                </div>
            </div>
        </section>
        <button
            type="button"
            id="dashboardQaToggleFab"
            class="apip-qa-toggle apip-qa-toggle-fab"
            data-qa-toggle
            data-label-on="Sembunyikan QA"
            data-label-off="Tampilkan QA"
            aria-pressed="false"
            aria-label="Toggle tampilan level dan skor QA">
            Tampilkan QA
        </button>
    </div>
@endsection

@push('scripts')
<script>
const dashboardLevelRecapPayload = @json($levelRecapPayload ?? []);

const initDashboardLevelRecapModal = () => {
    const modal = document.getElementById('apipLevelModal');
    if (!modal) return;
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    const modalDialog = modal.querySelector('.apip-level-modal__dialog');
    const modalTitle = document.getElementById('apipLevelModalTitle');
    const modalEyebrow = document.getElementById('apipLevelModalEyebrow');
    const modalDesc = document.getElementById('apipLevelModalDesc');
    const modalCount = document.getElementById('apipLevelModalCount');
    const modalList = document.getElementById('apipLevelModalList');
    const closeButtons = modal.querySelectorAll('[data-level-modal-close]');
    const openButtons = document.querySelectorAll('[data-level-modal-open]');
    const payload = (dashboardLevelRecapPayload && typeof dashboardLevelRecapPayload === 'object')
        ? dashboardLevelRecapPayload
        : {};
    const modalToneClasses = ['is-level-1', 'is-level-2', 'is-level-3', 'is-level-4', 'is-level-5', 'is-pending'];
    const modalTransitionMs = 240;

    const getViewportUiScale = () => {
        const zoomRaw = getComputedStyle(document.body).zoom;
        const zoom = Number.parseFloat(zoomRaw || '1');
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

    const syncModalBodyLock = () => {
        const body = document.body;
        if (!body) return;
        const shouldLock = modal.classList.contains('is-open');

        if (shouldLock) {
            const scrollbarComp = Math.max(0, window.innerWidth - document.documentElement.clientWidth);
            if (scrollbarComp > 0) {
                body.style.setProperty('--apip-scrollbar-comp', `${scrollbarComp}px`);
            } else {
                body.style.removeProperty('--apip-scrollbar-comp');
            }
            body.classList.add('apip-modal-open');
            return;
        }

        body.classList.remove('apip-modal-open');
        body.style.removeProperty('--apip-scrollbar-comp');
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        window.setTimeout(() => {
            modal.hidden = true;
            clearModalViewportStyles();
            syncModalBodyLock();
        }, modalTransitionMs);
    };

    const renderModalContent = (levelKey) => {
        const levelData = payload[levelKey];
        if (!levelData) return;
        if (modalDialog) {
            modalDialog.classList.remove(...modalToneClasses);
            const toneClass = {
                level1: 'is-level-1',
                level2: 'is-level-2',
                level3: 'is-level-3',
                level4: 'is-level-4',
                level5: 'is-level-5',
                pending: 'is-pending',
            }[levelKey] || 'is-pending';
            modalDialog.classList.add(toneClass);
        }

        if (modalEyebrow) {
            modalEyebrow.textContent = `Informasi ${levelData.label ?? 'Level'}`;
        }
        if (modalTitle) {
            const label = levelData.label ?? 'Level';
            const predikat = levelData.predikat ?? '';
            modalTitle.textContent = predikat !== '' && predikat !== 'Belum Dinilai'
                ? `${label} - ${predikat}`
                : label;
        }
        if (modalDesc) {
            modalDesc.textContent = levelData.description ?? '';
        }
        if (modalCount) {
            modalCount.textContent = String(levelData.count ?? 0);
        }

        if (!modalList) return;

        const entries = Array.isArray(levelData.elements) ? levelData.elements : [];
        if (entries.length === 0) {
            modalList.innerHTML = '<div class="apip-level-modal__empty">Belum ada element pada kategori ini.</div>';
            return;
        }

        modalList.innerHTML = '';
        entries.forEach((item) => {
            const article = document.createElement('article');
            article.className = 'apip-level-modal__item';

            const title = document.createElement('h5');
            title.textContent = item.title ?? '';

            article.appendChild(title);

            const meta = document.createElement('div');
            meta.className = 'apip-level-modal__meta';
            const subtopicTotal = Number.parseInt(item.subtopic_count ?? 0, 10);
            const subtopicAssessed = Number.parseInt(item.assessed_subtopic_count ?? 0, 10);
            const isSubtopicCountValid = Number.isFinite(subtopicTotal) && subtopicTotal > 0;
            const subtopicProgress = isSubtopicCountValid
                ? `${Math.max(0, subtopicAssessed)}/${subtopicTotal}`
                : '-';
            const metaFields = [
                ['Skor', item.score ?? '-'],
                ['Tertimbang', item.weighted_score ?? '0.00'],
                ['Sub Topik', subtopicProgress],
            ];

            metaFields.forEach(([label, value]) => {
                const chip = document.createElement('span');
                chip.className = 'apip-level-modal__chip';

                const chipLabel = document.createElement('span');
                chipLabel.className = 'apip-level-modal__chip-label';
                chipLabel.textContent = label;

                const chipValue = document.createElement('span');
                chipValue.className = 'apip-level-modal__chip-value';
                chipValue.textContent = String(value);

                chip.appendChild(chipLabel);
                chip.appendChild(chipValue);
                meta.appendChild(chip);
            });

            article.appendChild(meta);

            const subtopics = Array.isArray(item.subtopic_details) ? item.subtopic_details : [];
            if (subtopics.length === 0) {
                const emptySubtopic = document.createElement('p');
                emptySubtopic.className = 'apip-level-modal__subtopics';
                emptySubtopic.textContent = 'Belum ada daftar sub topik untuk element ini.';
                article.appendChild(emptySubtopic);
            } else {
                const subtopicList = document.createElement('div');
                subtopicList.className = 'apip-level-modal__subtopic-list';

                subtopics.forEach((subtopic) => {
                    const subtopicRow = document.createElement('div');
                    subtopicRow.className = 'apip-level-modal__subtopic-item';

                    const subtopicHead = document.createElement('p');
                    subtopicHead.className = 'apip-level-modal__subtopic-head';
                    const subtopicTitle = String(subtopic.title ?? '').trim();
                    const subtopicLevelLabel = String(subtopic.level_label ?? '-').trim() || '-';
                    subtopicHead.textContent = `${subtopicTitle} (${subtopicLevelLabel})`;

                    const subtopicLevelInfo = document.createElement('p');
                    subtopicLevelInfo.className = 'apip-level-modal__subtopic-note';
                    subtopicLevelInfo.textContent = String(subtopic.level_note ?? 'Belum ada informasi level sub topik.');

                    subtopicRow.appendChild(subtopicHead);
                    subtopicRow.appendChild(subtopicLevelInfo);
                    subtopicList.appendChild(subtopicRow);
                });

                article.appendChild(subtopicList);
            }

            modalList.appendChild(article);
        });
    };

    openButtons.forEach((button) => {
        if (button.dataset.levelModalBound === '1') return;
        button.dataset.levelModalBound = '1';
        button.addEventListener('click', () => {
            const levelKey = button.getAttribute('data-level-modal-open') || '';
            renderModalContent(levelKey);
            modal.hidden = false;
            requestAnimationFrame(() => {
                modal.classList.add('is-open');
                syncModalToViewport();
                syncModalBodyLock();
            });
            if (modalDialog) {
                modalDialog.scrollTop = 0;
            }
        });
    });

    closeButtons.forEach((button) => {
        if (button.dataset.levelModalBound === '1') return;
        button.dataset.levelModalBound = '1';
        button.addEventListener('click', closeModal);
    });

    if (modal.dataset.levelModalEscBound !== '1') {
        modal.dataset.levelModalEscBound = '1';
        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            if (modal.hidden) return;
            closeModal();
        });
    }

    if (modal.dataset.levelModalViewportBound !== '1') {
        modal.dataset.levelModalViewportBound = '1';
        window.addEventListener('resize', syncModalToViewport);
        window.addEventListener('scroll', syncModalToViewport, { passive: true });
        window.addEventListener('pageshow', syncModalToViewport);
    }
};

const initDashboardRenstraInputModal = () => {
    const modal = document.getElementById('apipRenstraInputModal');
    if (!modal) return;
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    const dialog = modal.querySelector('.apip-level-modal__dialog');
    const openButtons = document.querySelectorAll('[data-renstra-modal-open]');
    const closeButtons = modal.querySelectorAll('[data-renstra-modal-close]');
    const transitionMs = 240;

    const getViewportUiScale = () => {
        const zoomRaw = getComputedStyle(document.body).zoom;
        const zoom = Number.parseFloat(zoomRaw || '1');
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

    const syncBodyLock = () => {
        const body = document.body;
        if (!body) return;
        const shouldLock = modal.classList.contains('is-open');

        if (shouldLock) {
            const scrollbarComp = Math.max(0, window.innerWidth - document.documentElement.clientWidth);
            if (scrollbarComp > 0) {
                body.style.setProperty('--apip-scrollbar-comp', `${scrollbarComp}px`);
            } else {
                body.style.removeProperty('--apip-scrollbar-comp');
            }
            body.classList.add('apip-modal-open');
            return;
        }

        body.classList.remove('apip-modal-open');
        body.style.removeProperty('--apip-scrollbar-comp');
    };

    const openModal = () => {
        modal.hidden = false;
        requestAnimationFrame(() => {
            modal.classList.add('is-open');
            syncModalToViewport();
            syncBodyLock();
        });
        if (dialog) {
            dialog.scrollTop = 0;
        }
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        window.setTimeout(() => {
            modal.hidden = true;
            clearModalViewportStyles();
            syncBodyLock();
        }, transitionMs);
    };

    openButtons.forEach((button) => {
        if (button.dataset.renstraModalBound === '1') return;
        button.dataset.renstraModalBound = '1';
        button.addEventListener('click', openModal);
    });

    closeButtons.forEach((button) => {
        if (button.dataset.renstraModalBound === '1') return;
        button.dataset.renstraModalBound = '1';
        button.addEventListener('click', closeModal);
    });

    if (modal.dataset.renstraModalEscBound !== '1') {
        modal.dataset.renstraModalEscBound = '1';
        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            if (modal.hidden) return;
            closeModal();
        });
    }

    if (modal.dataset.renstraModalViewportBound !== '1') {
        modal.dataset.renstraModalViewportBound = '1';
        window.addEventListener('resize', syncModalToViewport);
        window.addEventListener('scroll', syncModalToViewport, { passive: true });
        window.addEventListener('pageshow', syncModalToViewport);
    }

    if (modal.dataset.openOnLoad === '1') {
        openModal();
    }
};

const ensureDashboardChartJs = (() => {
    let loaderPromise = null;
    const chartScriptId = 'apip-chartjs-umd';
    const adapterScriptId = 'apip-chartjs-datefns-adapter';
    const hasDateAdapter = () => Boolean(
        window.Chart
            && window.Chart._adapters
            && window.Chart._adapters._date
            && typeof window.Chart._adapters._date.format === 'function'
    );

    const loadScript = (id, src) => new Promise((resolve, reject) => {
        const existing = document.getElementById(id);
        if (existing) {
            if (existing.dataset.loaded === '1') {
                resolve();
                return;
            }

            existing.addEventListener('load', () => resolve(), { once: true });
            existing.addEventListener('error', () => reject(new Error(`Gagal memuat ${src}`)), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.id = id;
        script.src = src;
        script.defer = true;
        script.onload = () => {
            script.dataset.loaded = '1';
            resolve();
        };
        script.onerror = () => reject(new Error(`Gagal memuat ${src}`));
        document.head.appendChild(script);
    });

    return () => {
        if (window.Chart && hasDateAdapter()) {
            return Promise.resolve();
        }

        if (loaderPromise) {
            return loaderPromise;
        }

        const chartLoader = window.Chart
            ? Promise.resolve()
            : loadScript(chartScriptId, 'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js');

        loaderPromise = chartLoader
            .then(() => hasDateAdapter()
                ? Promise.resolve()
                : loadScript(adapterScriptId, 'https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js'))
            .catch((error) => {
                loaderPromise = null;
                throw error;
            });

        return loaderPromise;
    };
})();

const initDashboardRenstraScaleSwitch = () => {
    const chartRoot = document.querySelector('[data-renstra-chartjs]');
    if (!chartRoot || chartRoot.dataset.renstraScaleBound === '1') return;

    const canvas = chartRoot.querySelector('#apipRenstraChartCanvas');
    const rangeWrap = chartRoot.querySelector('.apip-renstra-ranges-wrap');
    const scaleButtons = Array.from(document.querySelectorAll('[data-renstra-scale]'));
    if (!canvas || scaleButtons.length === 0) return;

    let rawSeries = [];
    try {
        rawSeries = JSON.parse(chartRoot.getAttribute('data-series') || '[]');
    } catch (error) {
        rawSeries = [];
    }

    const series = Array.isArray(rawSeries)
        ? rawSeries
            .map((item) => {
                const year = Number.parseInt(item?.year ?? '', 10);
                if (!Number.isFinite(year) || year <= 0) return null;
                const hasilScoreRaw = Number.parseFloat(item?.hasil_score ?? '');
                const targetScoreRaw = Number.parseFloat(item?.target_score ?? '');
                return {
                    year,
                    x: Date.UTC(year, 0, 1),
                    hasilScore: Number.isFinite(hasilScoreRaw) ? Math.max(0, Math.min(5, hasilScoreRaw)) : null,
                    targetScore: Number.isFinite(targetScoreRaw) ? Math.max(0, Math.min(5, targetScoreRaw)) : null,
                };
            })
            .filter((item) => item !== null)
            .sort((a, b) => a.year - b.year)
        : [];

    if (series.length === 0) return;

    const years = series.map((item) => item.year);
    const parsedCurrentYear = Number.parseInt(chartRoot.dataset.currentYear || '', 10);
    const fallbackCurrentYear = years[years.length - 1];
    const currentYear = Number.isFinite(parsedCurrentYear) ? parsedCurrentYear : fallbackCurrentYear;
    const closestVisibleCurrentYear = years.includes(currentYear)
        ? currentYear
        : years.filter((year) => year <= currentYear).slice(-1)[0] || fallbackCurrentYear;

    const getVisibleSeries = (scaleKey) => {
        if (scaleKey === '3y') {
            const minYear = closestVisibleCurrentYear - 2;
            return series.filter((item) => item.year >= minYear && item.year <= closestVisibleCurrentYear);
        }

        if (scaleKey === '5y') {
            const minYear = closestVisibleCurrentYear - 4;
            return series.filter((item) => item.year >= minYear && item.year <= closestVisibleCurrentYear);
        }

        if (scaleKey === 'ytd') {
            return series.filter((item) => item.year === closestVisibleCurrentYear);
        }

        return series;
    };

    const updateScaleButtonState = (activeScale) => {
        scaleButtons.forEach((button) => {
            const buttonScale = (button.getAttribute('data-renstra-scale') || '').trim().toLowerCase();
            const isActive = buttonScale === activeScale;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const chartFallback = (message) => {
        const fallback = document.createElement('div');
        fallback.className = 'empty-state apip-recap-empty';
        fallback.textContent = message;
        chartRoot.innerHTML = '';
        chartRoot.appendChild(fallback);
    };

    ensureDashboardChartJs()
        .then(() => {
            if (!window.Chart) {
                chartFallback('Library Chart.js tidak tersedia.');
                return;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) {
                chartFallback('Canvas chart tidak dapat diinisialisasi.');
                return;
            }

            const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            let chartInstance = null;
            let activeScale = 'renstra';

            const buildDatasets = (visibleSeries) => {
                const hasilData = visibleSeries.map((item) => item.hasilScore);
                const targetData = visibleSeries.map((item) => item.targetScore);

                return [
                    {
                        type: 'bar',
                        label: 'Hasil Penilaian',
                        data: hasilData,
                        backgroundColor: 'rgba(52, 71, 240, 0.9)',
                        borderColor: 'rgba(38, 54, 210, 1)',
                        borderWidth: 1,
                        borderRadius: 9,
                        barThickness: 14,
                        maxBarThickness: 16,
                        categoryPercentage: 0.68,
                        barPercentage: 0.92,
                        order: 2,
                    },
                    {
                        type: 'bar',
                        label: 'Target Renstra',
                        data: targetData,
                        backgroundColor: 'rgba(43, 201, 191, 0.88)',
                        borderColor: 'rgba(28, 171, 162, 1)',
                        borderWidth: 1,
                        borderRadius: 9,
                        barThickness: 14,
                        maxBarThickness: 16,
                        categoryPercentage: 0.68,
                        barPercentage: 0.92,
                        order: 2,
                    },
                    {
                        type: 'line',
                        label: 'Trend Hasil',
                        data: hasilData,
                        borderColor: '#f28a22',
                        backgroundColor: 'rgba(242, 138, 34, 0.2)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                        pointBorderWidth: 2,
                        pointBackgroundColor: '#fff6eb',
                        pointBorderColor: '#f28a22',
                        tension: 0.28,
                        spanGaps: true,
                        fill: false,
                        order: 1,
                    },
                ];
            };

            const renderChart = (scaleKey, animated = true) => {
                const visibleSeries = getVisibleSeries(scaleKey);
                const labels = visibleSeries.map((item) => String(item.year));
                const datasets = buildDatasets(visibleSeries);

                chartRoot.classList.toggle('is-ranges-hidden', scaleKey !== 'renstra');
                if (rangeWrap) {
                    rangeWrap.hidden = scaleKey !== 'renstra';
                }

                if (!chartInstance) {
                    chartInstance = new window.Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets,
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: prefersReducedMotion || !animated
                                ? false
                                : {
                                    duration: 640,
                                    easing: 'easeOutQuart',
                                },
                            interaction: {
                                mode: 'index',
                                axis: 'x',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    position: 'nearest',
                                    backgroundColor: 'rgba(15, 23, 42, 0.92)',
                                    borderColor: 'rgba(148, 163, 184, 0.45)',
                                    borderWidth: 1,
                                    padding: 10,
                                    titleColor: '#e2e8f0',
                                    bodyColor: '#f8fafc',
                                    displayColors: true,
                                    callbacks: {
                                        title(items) {
                                            return items?.[0]?.label ?? '';
                                        },
                                        label(context) {
                                            const value = Number.parseFloat(context?.parsed?.y);
                                            if (!Number.isFinite(value)) {
                                                return `${context.dataset.label}: -`;
                                            }
                                            return `${context.dataset.label}: ${value.toFixed(2)}`;
                                        },
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    offset: true,
                                    grid: {
                                        display: false,
                                    },
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 0,
                                        minRotation: 0,
                                        color: '#294a76',
                                        font: {
                                            size: 12,
                                            weight: '700',
                                        },
                                    },
                                },
                                y: {
                                    min: 0,
                                    max: 5,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#5a7092',
                                        font: {
                                            size: 12,
                                            weight: '700',
                                        },
                                    },
                                    grid: {
                                        color: '#d8e5f7',
                                        drawBorder: false,
                                    },
                                },
                            },
                        },
                    });
                    return;
                }

                chartInstance.options.animation = prefersReducedMotion || !animated
                    ? false
                    : {
                        duration: 520,
                        easing: 'easeOutQuart',
                    };
                chartInstance.data.labels = labels;
                chartInstance.data.datasets = datasets;
                chartInstance.update();
            };

            scaleButtons.forEach((button) => {
                if (button.dataset.renstraScaleBound === '1') return;
                button.dataset.renstraScaleBound = '1';
                button.addEventListener('click', () => {
                    const nextScale = (button.getAttribute('data-renstra-scale') || '').trim().toLowerCase();
                    if (nextScale === '' || nextScale === activeScale) return;
                    activeScale = nextScale;
                    updateScaleButtonState(activeScale);
                    renderChart(activeScale, true);
                });
            });

            updateScaleButtonState(activeScale);
            renderChart(activeScale, false);

            if (chartRoot.dataset.renstraChartResizeBound !== '1') {
                chartRoot.dataset.renstraChartResizeBound = '1';
                window.addEventListener('resize', () => {
                    if (chartInstance) {
                        chartInstance.resize();
                    }
                });
            }

            chartRoot.dataset.renstraChartReady = '1';
        })
        .catch(() => {
            chartFallback('Chart.js gagal dimuat. Silakan periksa koneksi internet dan refresh halaman.');
        });

    chartRoot.dataset.renstraScaleBound = '1';
};

const initDashboardSpeedometer = () => {
    const meterCards = Array.from(document.querySelectorAll('.apip-meter-card'));
    if (meterCards.length === 0) return;

    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const durationMs = 1200;
    const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);
    const stopNeedleIdleVibe = (card) => {
        if (!card || !card._needleVibeRaf) return;
        window.cancelAnimationFrame(card._needleVibeRaf);
        card._needleVibeRaf = null;
    };
    const startNeedleIdleVibe = (card, needleGroup, baseDeg) => {
        if (!card || !needleGroup || !Number.isFinite(baseDeg) || prefersReducedMotion) return;
        if (card._needleVibeRaf) return;

        const amplitude = 1.15;
        const microAmplitude = 0.42;
        const primarySpeed = 0.0105;
        const microSpeed = 0.024;
        const startAt = performance.now();

        const vibrate = (now) => {
            if (!document.body.contains(needleGroup)) {
                stopNeedleIdleVibe(card);
                return;
            }

            const elapsed = now - startAt;
            const wobble =
                (Math.sin(elapsed * primarySpeed) * amplitude) +
                (Math.sin(elapsed * microSpeed) * microAmplitude);
            const currentDeg = baseDeg + wobble;

            needleGroup.setAttribute('transform', `rotate(${currentDeg.toFixed(2)} 120 120)`);
            card._needleVibeRaf = window.requestAnimationFrame(vibrate);
        };

        card._needleVibeRaf = window.requestAnimationFrame(vibrate);
    };

    meterCards.forEach((card) => {
        const needleGroup = card.querySelector('.meter-needle-group:not(.meter-needle-group-qa)') || card.querySelector('.meter-needle-group');
        const scoreEl = card.querySelector('.meter-score');
        if (!needleGroup || !scoreEl) return;

        const targetNeedleDeg = parseFloat(needleGroup.getAttribute('data-target-deg') || '-90');
        const finalScore = parseFloat(scoreEl.getAttribute('data-final-score') || '0');

        if (card.getAttribute('data-meter-animated') === '1') {
            startNeedleIdleVibe(card, needleGroup, targetNeedleDeg);
            return;
        }

        stopNeedleIdleVibe(card);

        const applyFinalState = () => {
            needleGroup.setAttribute('transform', `rotate(${targetNeedleDeg.toFixed(2)} 120 120)`);
            scoreEl.textContent = finalScore.toFixed(2);
            card.setAttribute('data-meter-animated', '1');
            startNeedleIdleVibe(card, needleGroup, targetNeedleDeg);
        };

        if (prefersReducedMotion) {
            applyFinalState();
            return;
        }

        needleGroup.setAttribute('transform', 'rotate(-90 120 120)');
        scoreEl.textContent = '0.00';

        const startAt = performance.now();
        const animate = (now) => {
            const elapsed = now - startAt;
            const t = Math.min(elapsed / durationMs, 1);
            const eased = easeOutCubic(t);

            const currentNeedle = -90 + ((targetNeedleDeg + 90) * eased);
            const currentScore = finalScore * eased;

            needleGroup.setAttribute('transform', `rotate(${currentNeedle.toFixed(2)} 120 120)`);
            scoreEl.textContent = currentScore.toFixed(2);

            if (t < 1) {
                window.requestAnimationFrame(animate);
                return;
            }

            applyFinalState();
        };

        window.requestAnimationFrame(animate);
    });
};

const initDashboardQaToggle = () => {
    const page = document.getElementById('apipHomePage');
    if (!page) return;

    let toggleButton = document.getElementById('dashboardQaToggleFab');
    if (!toggleButton) {
        toggleButton = page.querySelector('[data-qa-toggle]');
    }
    if (toggleButton && toggleButton.dataset.viewportMounted !== '1') {
        document.body.appendChild(toggleButton);
        toggleButton.dataset.viewportMounted = '1';
    }

    const applyQaDisplay = (showQa) => {
        page.classList.toggle('qa-display-off', !showQa);
        if (!toggleButton) return;

        toggleButton.setAttribute('aria-pressed', showQa ? 'true' : 'false');
        toggleButton.classList.toggle('is-active', showQa);
        const label = showQa
            ? (toggleButton.getAttribute('data-label-on') || 'Sembunyikan QA')
            : (toggleButton.getAttribute('data-label-off') || 'Tampilkan QA');
        toggleButton.textContent = label;
    };

    if (toggleButton && toggleButton.dataset.qaToggleBound !== '1') {
        toggleButton.dataset.qaToggleBound = '1';
        toggleButton.addEventListener('click', () => {
            const showQaNow = page.classList.contains('qa-display-off');
            applyQaDisplay(showQaNow);
        });
    }

    applyQaDisplay(false);
};

const initDashboardHomePage = () => {
    initDashboardLevelRecapModal();
    initDashboardRenstraInputModal();
    initDashboardRenstraScaleSwitch();
    initDashboardQaToggle();
    initDashboardSpeedometer();
};

document.addEventListener('DOMContentLoaded', initDashboardHomePage);
document.addEventListener('livewire:navigated', initDashboardHomePage);
</script>
@endpush
