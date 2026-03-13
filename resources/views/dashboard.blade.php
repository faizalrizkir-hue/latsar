@extends('layouts.dashboard-shell')
@php
    $pageTitle = $pageTitle ?? 'Dashboard Kapabilitas APIP';
    $overallLevel = is_numeric($overallLevelData['level'] ?? null) ? (int) $overallLevelData['level'] : null;
    $overallLevelClass = $overallLevel !== null ? 'is-level-'.$overallLevel : 'pending';
    $overallLevelLabel = $overallLevel !== null ? 'Level '.$overallLevel : 'Belum Dinilai';
    $overallPredikat = (string) ($overallLevelData['predikat'] ?? 'Belum Dinilai');
    $overallDescription = (string) ($overallLevelData['description'] ?? '');
    $accessibleElementSlugs = is_array($accessibleElementSlugs ?? null) ? $accessibleElementSlugs : null;
    $canOpenElement = function (string $slug) use ($accessibleElementSlugs): bool {
        if ($accessibleElementSlugs === null) {
            return true;
        }

        return in_array($slug, $accessibleElementSlugs, true);
    };
    $elementTitleMap = collect($elements ?? [])
        ->mapWithKeys(fn ($item) => [(string) ($item['slug'] ?? '') => (string) ($item['title'] ?? '-')]);
    $weightHintItems = collect($elementWeights ?? [])
        ->map(function ($elementWeight, $elementSlug) {
            $slug = (string) $elementSlug;
            $label = $slug;
            if (preg_match('/^element(\d+)$/', $slug, $matches)) {
                $label = 'Element '.$matches[1];
            }

            return '• '.$label.': '.number_format((float) $elementWeight * 100, 0).'%';
        })
        ->values()
        ->implode("\n");
    $weightHintText = trim("Bobot skor tertimbang:\n".$weightHintItems);
    $weightedScoreHintText = 'Rumus Skor Tertimbang: Bobot (%) x Skor';
    $levelPredikatHintText = trim(
        "Informasi Level Kapabilitas APIP:\n"
        ."Level 1 - Rintisan (0,00 - 1,98)\n"
        ."Level 2 - Terstruktur (1,99 - 2,98)\n"
        ."Level 3 - Memadai (2,99 - 3,98)\n"
        ."Level 4 - Terintegrasi (3,99 - 4,99)\n"
        ."Level 5 - Optimal (5,00)"
    );
    $segmentArcLength = 314.16;
    $segmentLength = $segmentArcLength / 5;
    $segmentOffsets = [
        0,
        -$segmentLength,
        -$segmentLength * 2,
        -$segmentLength * 3,
        -$segmentLength * 4,
    ];
@endphp

@push('head')
    <link rel="stylesheet" href="/css/dashboard-home.css">
@endpush

@section('content')
    <div class="apip-home-page">
        <section class="apip-home-hero">
            <article class="card apip-overview-card">
                <div class="apip-overview-top">
                    <p class="apip-eyebrow">Dashboard Utama</p>
                    <button
                        type="button"
                        class="apip-overview-hint hint-bubble-trigger"
                        data-hint="{{ $weightHintText }}"
                        aria-label="Informasi bobot skor tertimbang">
                        ?
                    </button>
                </div>
                <h1 class="apip-title">Skor dan Level Kapabilitas APIP</h1>
                <p class="apip-subtitle">
                    Ringkasan nilai utama dari seluruh Element 1-5. Nilai akhir dihitung dari total Skor Tertimbang tiap element.
                </p>

                <div class="apip-overview-kpis">
                    <div class="apip-kpi">
                        <div class="kpi-label">Skor Kapabilitas APIP</div>
                        <div class="kpi-value">{{ number_format((float) ($overallWeightedScore ?? 0), 2) }}</div>
                        <div class="kpi-note">Total skor tertimbang seluruh element</div>
                    </div>
                    <div class="apip-kpi">
                        <div class="kpi-label">Level Kapabilitas APIP</div>
                        <div class="kpi-value">{{ $overallLevelLabel }}</div>
                        <div class="kpi-note">{{ $overallPredikat }}</div>
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

                        <g class="meter-needle-group" data-target-deg="{{ number_format((float) ($meterNeedleDeg ?? -90), 2, '.', '') }}" transform="rotate(-90 120 120)">
                            <line x1="120" y1="120" x2="120" y2="34" class="meter-needle"></line>
                            <circle cx="120" cy="120" r="8" class="meter-needle-core"></circle>
                        </g>
                    </svg>
                </div>
                <div class="meter-readout">
                    <div class="meter-score" data-final-score="{{ number_format((float) ($overallWeightedScore ?? 0), 2, '.', '') }}">0.00</div>
                    <div class="meter-level">{{ $overallLevelLabel }} - {{ $overallPredikat }}</div>
                    @if ($overallDescription !== '')
                        <p class="meter-note">{{ $overallDescription }}</p>
                    @endif
                </div>
            </article>
        </section>

        <section class="card apip-element-summary-card">
            <div class="section-head apip-summary-head">
                <div class="apip-summary-head-top">
                    <h3>Rangkuman Skor per Element</h3>
                    <button
                        type="button"
                        class="apip-summary-hint hint-bubble-trigger"
                        data-hint="{{ $weightedScoreHintText }}"
                        aria-label="Informasi rumus skor tertimbang">
                        ?
                    </button>
                </div>
                <p>Ringkasan skor tertimbang, skor, dan level untuk setiap element.</p>
            </div>
            <div class="table-wrapper apip-element-table-wrap">
                <table class="apip-element-table">
                    <thead>
                        <tr>
                            <th>Element</th>
                            <th>Bobot</th>
                            <th>Skor</th>
                            <th>Skor Tertimbang</th>
                            <th>Level</th>
                            <th>Predikat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($elements as $element)
                            @php
                                $elementLevel = is_numeric($element['level'] ?? null) ? (int) $element['level'] : null;
                                $levelClass = $elementLevel !== null ? 'is-level-'.$elementLevel : 'pending';
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $element['title'] }}</strong>
                                    @php
                                        $elementDescription = trim((string) ($element['description'] ?? ''));
                                        $elementDescription = $elementDescription !== ''
                                            ? $elementDescription
                                            : 'Belum ada keterangan level untuk element ini.';
                                    @endphp
                                    <div class="row-meta element-level-note" title="{{ $elementDescription }}">
                                        {{ $elementDescription }}
                                    </div>
                                </td>
                                <td>{{ number_format((float) ($element['weight'] ?? 0) * 100, 0) }}%</td>
                                <td>
                                    {{ is_numeric($element['score'] ?? null) ? number_format((float) $element['score'], 2) : '-' }}
                                </td>
                                <td>{{ number_format((float) ($element['weighted_score'] ?? 0), 2) }}</td>
                                <td>
                                    <span class="level-chip {{ $levelClass }}">
                                        {{ $elementLevel !== null ? 'Level '.$elementLevel : '-' }}
                                    </span>
                                </td>
                                <td>{{ $element['predikat'] ?? '-' }}</td>
                                <td>
                                    @if($canOpenElement((string) ($element['slug'] ?? '')))
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
                        @php
                            $elementLevel = is_numeric($element['level'] ?? null) ? (int) $element['level'] : null;
                            $levelClass = $elementLevel !== null ? 'is-level-'.$elementLevel : 'pending';
                            $subtopicCount = (int) ($element['subtopic_count'] ?? 0);
                            $statementCount = (int) collect((array) ($element['subtopics'] ?? []))
                                ->sum(function (array $subtopic) {
                                    $rowsTotal = (int) ($subtopic['rows_total'] ?? 0);
                                    if ($rowsTotal > 0) {
                                        return $rowsTotal;
                                    }

                                    return (bool) ($subtopic['has_data'] ?? false) ? 1 : 0;
                                });
                        @endphp
                        <details class="apip-element-detail {{ $levelClass }}">
                            <summary>
                                <div class="detail-main">
                                    <div class="detail-title">{{ $element['title'] }}</div>
                                    <div class="detail-summary-meta">
                                        <span class="detail-meta-chip">Jumlah Sub-Topik: {{ $subtopicCount }}</span>
                                        <span class="detail-meta-chip is-alt">Jumlah Pernyataan: {{ $statementCount }}</span>
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
                                        @php
                                            $subtopicWeightTotal = (float) collect($element['subtopics'])
                                                ->sum(fn (array $subtopic) => (float) ($subtopic['weight'] ?? 0));
                                            $subtopicContributionTotal = (float) collect($element['subtopics'])
                                                ->sum(fn (array $subtopic) => (float) ($subtopic['weighted_score'] ?? 0));
                                        @endphp
                                        <div class="table-wrapper">
                                            <table class="apip-subtopic-table">
                                                <thead>
                                                    <tr>
                                                        <th>Sub Topik</th>
                                                        <th>Bobot Internal</th>
                                                        <th>Skor</th>
                                                        <th>Kontribusi ke Skor Element</th>
                                                        <th>Level / Predikat</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($element['subtopics'] as $subtopic)
                                                        @php
                                                            $subLevel = is_numeric($subtopic['level'] ?? null) ? (int) $subtopic['level'] : null;
                                                            $subLevelClass = $subLevel !== null ? 'is-level-'.$subLevel : 'pending';
                                                            $statusLabel = (bool) ($subtopic['has_data'] ?? false)
                                                                ? ((bool) ($subtopic['is_verified'] ?? false) ? 'Terverifikasi' : 'Belum Diverifikasi')
                                                                : 'Belum Dinilai';
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $subtopic['title'] }}</strong>
                                                                <div class="row-meta subtopic-level-note">
                                                                    {{ (string) ($subtopic['level_note'] ?? $subtopic['description'] ?? 'Belum ada deskripsi level sub topik.') }}
                                                                </div>
                                                            </td>
                                                            <td>{{ number_format((float) ($subtopic['weight'] ?? 0) * 100, 2) }}%</td>
                                                            <td>{{ is_numeric($subtopic['score'] ?? null) ? number_format((float) $subtopic['score'], 2) : '-' }}</td>
                                                            <td>{{ number_format((float) ($subtopic['weighted_score'] ?? 0), 2) }}</td>
                                                            <td>
                                                                <span class="level-chip {{ $subLevelClass }}">
                                                                    {{ $subLevel !== null ? 'L'.$subLevel : '-' }}
                                                                </span>
                                                                <span class="predikat-text">{{ $subtopic['predikat'] ?? '-' }}</span>
                                                            </td>
                                                            <td>{{ $statusLabel }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="subtopic-total-row">
                                                        <td>
                                                            <span class="subtopic-total-label">Total</span>
                                                        </td>
                                                        <td>
                                                            <span class="subtopic-total-value">{{ number_format($subtopicWeightTotal * 100, 2) }}%</span>
                                                        </td>
                                                        <td>-</td>
                                                        <td>
                                                            <span class="subtopic-total-value">{{ number_format($subtopicContributionTotal, 2) }}</span>
                                                        </td>
                                                        <td colspan="2">
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
        const needleGroup = card.querySelector('.meter-needle-group');
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

const initDashboardHomePage = () => {
    initDashboardElementAccordion();
    initDashboardSpeedometer();
};

document.addEventListener('DOMContentLoaded', initDashboardHomePage);
document.addEventListener('livewire:navigated', initDashboardHomePage);
</script>
@endpush
