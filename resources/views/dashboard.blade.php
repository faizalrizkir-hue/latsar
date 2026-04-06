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

        <section class="card apip-element-summary-card">
            <div class="section-head apip-summary-head">
                <div class="apip-summary-head-top">
                    <h3>Rangkuman Skor per Element</h3>
                    <div class="apip-summary-actions">
                        <button
                            type="button"
                            class="apip-summary-hint hint-bubble-trigger"
                            data-hint="{{ $weightedScoreHintText }}"
                            aria-label="Informasi rumus skor tertimbang">
                            ?
                        </button>
                    </div>
                </div>
                <p>Ringkasan skor tertimbang, skor, dan level untuk setiap element.</p>
            </div>
            <div class="table-wrapper apip-element-table-wrap">
                <table class="apip-element-table">
                    <thead>
                        <tr>
                            <th>Element</th>
                            <th>Bobot</th>
                            <th>Skor Element</th>
                            <th>Skor Tertimbang Element</th>
                            <th>Level</th>
                            <th>Predikat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($elements as $element)
                            <tr>
                                <td>
                                    <strong>{{ $element['title'] }}</strong>
                                    <div class="row-meta element-level-note" title="{{ $element['description'] ?? '' }}">
                                        <span class="qa-mandiri-prefix">Mandiri: </span>{{ $element['description'] ?? '' }}
                                    </div>
                                    <div class="row-meta qa-note-separator qa-only" aria-hidden="true"></div>
                                    <div class="row-meta element-level-note qa-only qa-level-font" title="{{ $element['qa_description'] ?? '' }}">
                                        QA: {{ $element['qa_description'] ?? '' }}
                                    </div>
                                </td>
                                <td>{{ number_format((float) ($element['weight'] ?? 0) * 100, 0) }}%</td>
                                <td>
                                    <div class="apip-split-metric">
                                        <div class="apip-split-metric-row">
                                            <span class="apip-split-metric-label qa-mandiri-label">Mandiri</span>
                                            <span>{{ is_numeric($element['score'] ?? null) ? number_format((float) $element['score'], 2) : '-' }}</span>
                                        </div>
                                        <div class="apip-split-metric-row qa-only">
                                            <span class="apip-split-metric-label">QA</span>
                                            <span>{{ is_numeric($element['qa_score'] ?? null) ? number_format((float) $element['qa_score'], 2) : '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="apip-split-metric">
                                        <div class="apip-split-metric-row">
                                            <span class="apip-split-metric-label qa-mandiri-label">Mandiri</span>
                                            <span>{{ number_format((float) ($element['weighted_score'] ?? 0), 2) }}</span>
                                        </div>
                                        <div class="apip-split-metric-row qa-only">
                                            <span class="apip-split-metric-label">QA</span>
                                            <span>{{ number_format((float) ($element['qa_weighted_score'] ?? 0), 2) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="apip-split-metric">
                                        <div class="apip-split-metric-row">
                                            <span class="apip-split-metric-label qa-mandiri-label">Mandiri</span>
                                            <span class="level-chip {{ $element['level_class'] ?? 'pending' }}">
                                                {{ $element['level_label'] ?? '-' }}
                                            </span>
                                        </div>
                                        <div class="apip-split-metric-row qa-only">
                                            <span class="apip-split-metric-label">QA</span>
                                            <span class="level-chip {{ $element['qa_level_class'] ?? 'pending' }}">
                                                {{ $element['qa_level_label'] ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="apip-split-metric">
                                        <div class="apip-split-metric-row">
                                            <span class="apip-split-metric-label qa-mandiri-label">Mandiri</span>
                                            <span>{{ $element['predikat'] ?? '-' }}</span>
                                        </div>
                                        <div class="apip-split-metric-row qa-only">
                                            <span class="apip-split-metric-label">QA</span>
                                            <span>{{ $element['qa_predikat'] ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if((bool) ($element['can_open'] ?? true))
                                        <a class="btn-open-element" href="{{ route('elements.show', $element['slug']) }}">Buka Element</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">Belum ada data ringkasan element.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="apip-detail-stack">
                <div class="section-head apip-detail-head">
                    <h4>Rincian per Element</h4>
                    <p>Klik tombol panah untuk melihat rincian sub topik setiap element.</p>
                </div>
                <section class="apip-element-detail-grid">
                    @foreach ($elements as $element)
                        <details class="apip-element-detail {{ $element['level_class'] ?? 'pending' }}">
                            <summary>
                                <div class="detail-main">
                                    <div class="detail-title">{{ $element['title'] }}</div>
                                    <div class="detail-summary-meta">
                                        <span class="detail-meta-chip">Jumlah Sub-Topik: {{ (int) ($element['subtopic_count'] ?? 0) }}</span>
                                        <span class="detail-meta-chip is-alt">Jumlah Pernyataan: {{ (int) ($element['statement_count'] ?? 0) }}</span>
                                    </div>
                                </div>
                                <span class="detail-arrow-btn" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M7 10l5 5 5-5"></path>
                                    </svg>
                                </span>
                            </summary>

                            <div class="detail-slide">
                                <div class="detail-body">
                                    @if (!empty($element['subtopics']))
                                        <div class="table-wrapper">
                                            <table class="apip-subtopic-table">
                                                <thead>
                                                    <tr>
                                                        <th>Sub Topik</th>
                                                        <th>Bobot Internal</th>
                                                        <th>Skor</th>
                                                        <th>Kontribusi ke Skor Element</th>
                                                        <th>Level / Predikat</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($element['subtopics'] as $subtopic)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $subtopic['title'] }}</strong>
                                                                <div class="row-meta subtopic-level-note">
                                                                    <span class="qa-mandiri-prefix">Mandiri: </span>{{ (string) ($subtopic['level_note'] ?? 'Belum ada deskripsi level sub topik.') }}
                                                                </div>
                                                                <div class="row-meta qa-note-separator qa-only" aria-hidden="true"></div>
                                                                <div class="row-meta subtopic-level-note qa-only qa-level-font">
                                                                    QA: {{ (string) ($subtopic['qa_level_note'] ?? 'Belum ada deskripsi level QA sub topik.') }}
                                                                </div>
                                                            </td>
                                                            <td>{{ $formatPercent((float) ($subtopic['weight'] ?? 0) * 100) }}%</td>
                                                            <td>
                                                                <div class="apip-split-metric">
                                                                    <div class="apip-split-metric-row">
                                                                        <span class="apip-split-metric-label qa-mandiri-label">Mandiri</span>
                                                                        <span>{{ is_numeric($subtopic['score'] ?? null) ? number_format((float) $subtopic['score'], 2) : '-' }}</span>
                                                                    </div>
                                                                    <div class="apip-split-metric-row qa-only">
                                                                        <span class="apip-split-metric-label">QA</span>
                                                                        <span>{{ is_numeric($subtopic['qa_score'] ?? null) ? number_format((float) $subtopic['qa_score'], 2) : '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="apip-split-metric">
                                                                    <div class="apip-split-metric-row">
                                                                        <span class="apip-split-metric-label qa-mandiri-label">Mandiri</span>
                                                                        <span>{{ number_format((float) ($subtopic['weighted_score'] ?? 0), 2) }}</span>
                                                                    </div>
                                                                    <div class="apip-split-metric-row qa-only">
                                                                        <span class="apip-split-metric-label">QA</span>
                                                                        <span>{{ number_format((float) ($subtopic['qa_weighted_score'] ?? 0), 2) }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="apip-split-metric">
                                                                    <div class="apip-split-metric-row">
                                                                        <span class="apip-split-metric-label qa-mandiri-label">Mandiri</span>
                                                                        <span class="level-chip {{ $subtopic['level_class'] ?? 'pending' }}">
                                                                            {{ $subtopic['level_label'] ?? '-' }}
                                                                        </span>
                                                                        <span class="predikat-text">{{ $subtopic['predikat'] ?? '-' }}</span>
                                                                    </div>
                                                                    <div class="apip-split-metric-row qa-only">
                                                                        <span class="apip-split-metric-label">QA</span>
                                                                        <span class="level-chip {{ $subtopic['qa_level_class'] ?? 'pending' }}">
                                                                            {{ $subtopic['qa_level_label'] ?? '-' }}
                                                                        </span>
                                                                        <span class="predikat-text">{{ $subtopic['qa_predikat'] ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="subtopic-total-row">
                                                        <td>
                                                            <span class="subtopic-total-label">Total</span>
                                                        </td>
                                                        <td>
                                                            <span class="subtopic-total-value">{{ (string) ($element['subtopic_weight_total_percent'] ?? '0') }}%</span>
                                                        </td>
                                                        <td>-</td>
                                                        <td>
                                                            <div class="apip-split-metric">
                                                                <div class="apip-split-metric-row">
                                                                    <span class="apip-split-metric-label qa-mandiri-label">Mandiri</span>
                                                                    <span class="subtopic-total-value">{{ number_format((float) ($element['subtopic_contribution_total'] ?? 0), 2) }}</span>
                                                                </div>
                                                                <div class="apip-split-metric-row qa-only">
                                                                    <span class="apip-split-metric-label">QA</span>
                                                                    <span class="subtopic-total-value">{{ number_format((float) ($element['subtopic_qa_contribution_total'] ?? 0), 2) }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="subtopic-total-note">Jumlah Bobot Internal & Kontribusi per element</span>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <div class="empty-state">Belum ada sub topik terdaftar untuk element ini.</div>
                                    @endif
                                </div>
                            </div>
                        </details>
                    @endforeach
                </section>
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
const initDashboardElementAccordion = () => {
    const accordions = Array.from(document.querySelectorAll('.apip-element-detail'));
    if (accordions.length === 0) return;

    const easing = 'cubic-bezier(0.22, 1, 0.36, 1)';
    const durationMs = 400;

    const clearAnimationState = (details, slide) => {
        details.classList.remove('is-opening', 'is-closing');
        details.removeAttribute('data-animating');
        slide.style.transition = '';
        slide.style.height = '';
        slide.style.overflow = '';
    };

    const openWithAnimation = (details, slide) => {
        if (details.getAttribute('data-animating') === '1') return;
        details.setAttribute('data-animating', '1');
        details.classList.add('is-opening');
        details.open = true;

        slide.style.height = '0px';
        slide.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            const targetHeight = slide.scrollHeight;
            slide.style.transition = `height ${durationMs}ms ${easing}, opacity 240ms ease`;
            slide.style.height = targetHeight + 'px';
        });

        window.setTimeout(() => {
            clearAnimationState(details, slide);
        }, durationMs + 40);
    };

    const closeWithAnimation = (details, slide) => {
        if (details.getAttribute('data-animating') === '1') return;
        details.setAttribute('data-animating', '1');
        details.classList.add('is-closing');

        const startHeight = slide.scrollHeight;
        slide.style.height = startHeight + 'px';
        slide.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            slide.style.transition = `height ${durationMs}ms ${easing}, opacity 220ms ease`;
            slide.style.height = '0px';
        });

        window.setTimeout(() => {
            details.open = false;
            clearAnimationState(details, slide);
        }, durationMs + 40);
    };

    accordions.forEach((details) => {
        if (details.getAttribute('data-accordion-bound') === '1') return;
        details.setAttribute('data-accordion-bound', '1');

        const summary = details.querySelector(':scope > summary');
        const slide = details.querySelector(':scope > .detail-slide');
        if (!summary || !slide) return;

        summary.addEventListener('click', function (event) {
            event.preventDefault();
            if (details.open) {
                closeWithAnimation(details, slide);
            } else {
                openWithAnimation(details, slide);
            }
        });
    });
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
    initDashboardQaToggle();
    initDashboardElementAccordion();
    initDashboardSpeedometer();
};

document.addEventListener('DOMContentLoaded', initDashboardHomePage);
document.addEventListener('livewire:navigated', initDashboardHomePage);
</script>
@endpush

