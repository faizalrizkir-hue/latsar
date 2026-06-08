@extends('layouts.dashboard-shell')
@php
    $pageTitle = $title ?? 'Rekap Elemen';
    $summaryStyles = collect($summaryStyles ?? [])->filter()->values();
    if ($summaryStyles->isEmpty()) {
        $summaryStyles = collect(['css/element1-kegiatan-asurans.css', 'css/element1-summary.css']);
    }
@endphp

@push('head')
    @foreach ($summaryStyles as $stylePath)
        @php
            $resolvedStylePath = ltrim((string) $stylePath, '/');
        @endphp
        <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url($resolvedStylePath) }}">
    @endforeach
@endpush

@section('content')
    @php
        $qaFeatureEnabled = (bool) config('app.features.qa_enabled', false);
        $overallLevel = is_numeric($levelData['level'] ?? null) ? (int) $levelData['level'] : null;
        $overallLevelClass = $overallLevel !== null && $overallLevel >= 1 && $overallLevel <= 5 ? 'is-level-'.$overallLevel : 'pending';
        $overallLevelQa = is_numeric($levelDataQa['level'] ?? null) ? (int) $levelDataQa['level'] : null;
        $overallLevelQaClass = $overallLevelQa !== null && $overallLevelQa >= 1 && $overallLevelQa <= 5 ? 'is-level-'.$overallLevelQa : 'pending';
        $summaryHeaderCode = (string) ($summaryHeaderCode ?? 'E1');
        $summaryHeaderSubtitle = (string) ($summaryHeaderSubtitle ?? 'Rekap skor dan level dari topik');
        $summaryLevelLabel = (string) ($summaryLevelLabel ?? 'Level Elemen');
        $summaryInfoModalTitle = (string) ($summaryInfoModalTitle ?? 'Informasi Level Elemen');
        $summaryInfoLevels = collect($summaryInfoLevels ?? [])->values();
        $mandiriPrefix = $qaFeatureEnabled ? 'Mandiri: ' : '';
        $mandiriLevelTitle = $qaFeatureEnabled ? 'Informasi Level Elemen (Mandiri):' : 'Informasi Level Elemen:';
        $levelInfoMap = $summaryInfoLevels
            ->filter(fn ($item) => is_array($item))
            ->keyBy(fn ($item) => (int) ($item['level'] ?? 0));
        $scoreComponents = collect($scoreComponents ?? [])->values();
        $scoreComponentsQa = collect($scoreComponentsQa ?? [])->values();
        $scoreComponentsBySlug = $scoreComponents->keyBy(fn ($item) => (string) ($item['slug'] ?? ''));
        $formatSummaryPercent = static function (float $value): string {
            $formatted = number_format(round($value, 2), 2, '.', '');

            return rtrim(rtrim($formatted, '0'), '.');
        };
        $hasQaData = is_numeric($elementScoreQa ?? null) && is_numeric($weightedTotalQa ?? null) && $overallLevelQa !== null;
        $overallLevelInfo = is_int($overallLevel) ? (array) ($levelInfoMap->get($overallLevel, [])) : [];
        $overallLevelDescription = trim((string) ($overallLevelInfo['description'] ?? ''));
        $overallPredikat = trim((string) ($levelData['predikat'] ?? ''));
        if ($overallPredikat === '') {
            $overallPredikat = 'Belum Dinilai';
        }
        $overallLevelInfoQa = is_int($overallLevelQa) ? (array) ($levelInfoMap->get($overallLevelQa, [])) : [];
        $overallLevelDescriptionQa = trim((string) ($overallLevelInfoQa['description'] ?? ''));
        $completionPercent = max(0, min(100, (int) $completion));
        $levelNarrativeFull = $overallLevelDescription !== ''
            ? $overallLevelDescription
            : 'Belum ada narasi level elemen.';
        $completionToneClass = $completionPercent >= 80 ? 'is-good' : ($completionPercent >= 50 ? 'is-mid' : 'is-low');
        $predikatToneClass = $overallLevel !== null && $overallLevel >= 3 ? 'is-good' : ($overallLevel === 2 ? 'is-mid' : 'is-low');
        $elementInfoIconMap = [
            1 => '<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M15.5 12.6 17 21l-5-2.7L7 21l1.5-8.4"/><path d="m10.4 8 1.1 1.1 2.2-2.2"/></svg>',
            2 => '<svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="12" rx="2.5"/><path d="M9 7V5.8A1.8 1.8 0 0 1 10.8 4h2.4A1.8 1.8 0 0 1 15 5.8V7"/><path d="m9.5 13 1.6 1.6 3-3"/></svg>',
            3 => '<svg viewBox="0 0 24 24"><path d="M12 3 5 6v5.5c0 4.3 3 8.2 7 9.5 4-1.3 7-5.2 7-9.5V6l-7-3Z"/><path d="m9.3 11.7 2 2 3.6-3.6"/></svg>',
        ];
        $summaryHeaderElementNumber = null;
        if (preg_match('/^E\s*(\d+)$/i', trim($summaryHeaderCode), $summaryHeaderCodeMatch)) {
            $summaryHeaderElementNumber = (int) ($summaryHeaderCodeMatch[1] ?? 0);
        } elseif (preg_match('/elem(?:ent|en)\s+(\d+)/i', (string) $title, $summaryTitleMatch)) {
            $summaryHeaderElementNumber = (int) ($summaryTitleMatch[1] ?? 0);
        }
        $summaryHeaderIconSvg = $elementInfoIconMap[$summaryHeaderElementNumber] ?? null;
    @endphp
    <div class="keg-page element1-summary-page qa-display-off" id="element1SummaryPage" data-element1-summary-level="{{ $overallLevel ?? '' }}">
        <div class="keg-head">
            <div class="keg-title">
                <button type="button" class="keg-title-icon keg-info-trigger" data-element1-info-open aria-label="Lihat informasi level elemen">
                    @if($summaryHeaderIconSvg !== null)
                        <span class="keg-title-icon-glyph" aria-hidden="true">{!! $summaryHeaderIconSvg !!}</span>
                    @else
                        {{ $summaryHeaderCode }}
                    @endif
                </button>
                <div>
                    <h4>{{ $title }}</h4>
                    <small>{{ $summaryHeaderSubtitle }}</small>
                </div>
            </div>
            <div class="keg-score-wrap">
                <div class="keg-chip level {{ $overallLevelClass }}">
                    <span class="label">{{ $summaryLevelLabel }} <span class="qa-mandiri-suffix">Mandiri</span></span>
                    <span class="value">{{ $overallLevel !== null ? $overallLevel : '-' }}</span>
                </div>
                <div class="keg-chip level qa-only {{ $overallLevelQaClass }}">
                    <span class="label qa-level-font">{{ $summaryLevelLabel }} QA</span>
                    <span class="value">{{ $overallLevelQa !== null ? $overallLevelQa : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="summary-grid element1-summary-grid mb-3">
            <article class="keg-card element1-stat-card element1-overview-card">
                <div class="element1-overview-grid">
                    <div class="element1-overview-item">
                        <span class="element1-overview-icon is-score" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M4 18h16"/><path d="M7 18V9"/><path d="M12 18V6"/><path d="M17 18v-4"/></svg>
                        </span>
                        <span class="element1-stat-split-label">Skor Elemen</span>
                        <div class="element1-overview-values">
                            <span class="element1-overview-value">{{ $mandiriPrefix }}<strong>{{ number_format((float) ($elementScore ?? 0), 2) }}</strong></span>
                            <span class="element1-overview-value qa-only">QA: <strong>{{ $hasQaData ? number_format((float) ($elementScoreQa ?? 0), 2) : '-' }}</strong></span>
                        </div>
                    </div>
                    <div class="element1-overview-item">
                        <span class="element1-overview-icon is-weighted" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M12 4v16"/><path d="M5 9h14"/><path d="m7 9 5-5 5 5"/><path d="m7 15 5 5 5-5"/></svg>
                        </span>
                        <span class="element1-stat-split-label">Skor Tertimbang</span>
                        <div class="element1-overview-values">
                            <span class="element1-overview-value">{{ $mandiriPrefix }}<strong>{{ number_format((float) $weightedTotal, 2) }}</strong></span>
                            <span class="element1-overview-value qa-only">QA: <strong>{{ $hasQaData ? number_format((float) ($weightedTotalQa ?? 0), 2) : '-' }}</strong></span>
                        </div>
                    </div>
                    <div class="element1-overview-item">
                        <span class="element1-overview-icon is-progress" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M4 12h16"/><path d="M4 7h8"/><path d="M4 17h12"/><circle cx="18" cy="7" r="2"/><circle cx="14" cy="17" r="2"/></svg>
                        </span>
                        <span class="element1-stat-split-label">Topik & Verifikasi</span>
                        <div class="element1-overview-values">
                            <span class="element1-overview-value">{{ (int) $subtopicCount }} topik</span>
                            <span class="element1-overview-value">{{ $mandiriPrefix }}<strong>{{ (int) $totalVerifiedRows }}/{{ (int) $totalRows }}</strong> pernyataan</span>
                            <span class="element1-overview-value element1-overview-value-muted">{{ (int) $completion }}% terverifikasi</span>
                            <span class="element1-overview-value qa-only">QA: <strong>{{ (int) ($totalQaVerifiedRows ?? 0) }}/{{ (int) $totalRows }}</strong> pernyataan</span>
                            <span class="element1-overview-value element1-overview-value-muted qa-only">QA: {{ (int) ($completionQa ?? 0) }}% terverifikasi</span>
                        </div>
                    </div>
                </div>
                <div class="element1-overview-highlights">
                    <div class="element1-overview-highlight is-level">
                        <div class="element1-overview-highlight-title">{{ $mandiriLevelTitle }}</div>
                        <p class="element1-overview-highlight-text is-main">{{ $levelNarrativeFull }}</p>
                        <p class="element1-overview-highlight-text is-qa qa-only">
                            <strong>QA:</strong>
                            {{ $overallLevelDescriptionQa !== '' ? $overallLevelDescriptionQa : 'Belum ada narasi level elemen QA.' }}
                        </p>
                    </div>
                </div>
            </article>
        </div>

        <div class="keg-card">
            @if($qaFeatureEnabled)
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
            @endif
            <div class="table-responsive">
                <table class="table keg-table align-middle element1-summary-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">No</th>
                            <th>Topik</th>
                            <th style="width:190px;">Skor Topik</th>
                            <th style="width:190px;">Level</th>
                            <th style="width:210px;">Verifikasi</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subtopicSummaries as $index => $item)
                            @php
                                $rowLevel = (int) ($item['level'] ?? 0);
                                $rowLevelClass = $rowLevel >= 1 && $rowLevel <= 5 ? 'is-level-'.$rowLevel : 'pending';
                                $rowLevelQa = is_numeric($item['qa_level'] ?? null) ? (int) $item['qa_level'] : 0;
                                $rowLevelQaClass = $rowLevelQa >= 1 && $rowLevelQa <= 5 ? 'is-level-'.$rowLevelQa : 'pending';
                                $summaryDescId = 'summary-level-desc-'.$index;
                                $summaryQaDescId = 'summary-qa-level-desc-'.$index;
                                $rowScoreComponent = (array) ($scoreComponentsBySlug->get((string) ($item['slug'] ?? '')) ?? []);
                                $rowWeight = is_numeric($rowScoreComponent['weight'] ?? null) ? (float) $rowScoreComponent['weight'] : 0.0;
                                $rowWeightLabel = $formatSummaryPercent($rowWeight * 100).'%';
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="pernyataan-record">
                                        <div class="pernyataan pernyataan-line">
                                            <span class="pernyataan-title">{{ $item['title'] }}</span>
                                            <span
                                                class="hint-bubble-trigger pernyataan-weight-hint"
                                                tabindex="0"
                                                role="button"
                                                aria-label="Info bobot topik"
                                                data-hint="Bobot topik: {{ $rowWeightLabel }}">?</span>
                                        </div>
                                        <div class="pernyataan-labels">
                                            <span class="bobot subtopic-predikat {{ $rowLevelClass !== 'pending' ? 'predikat-'.$rowLevelClass : 'predikat-pending' }}">
                                                @if($qaFeatureEnabled)<span class="qa-mandiri-prefix">Mandiri: </span>@endif{{ $item['predikat'] ?? '-' }}
                                            </span>
                                            <span class="bobot subtopic-predikat qa-only qa-level-font {{ $rowLevelQaClass !== 'pending' ? 'predikat-'.$rowLevelQaClass : 'predikat-pending' }}">
                                                QA: {{ $item['qa_predikat'] ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if(!empty($item['level_description']))
                                        <div class="subtopic-level-copy is-collapsible" data-row-desc-wrap>
                                            <div
                                                id="{{ $summaryDescId }}"
                                                class="subtopic-level-desc"
                                                data-row-desc>@if($qaFeatureEnabled)<span class="qa-mandiri-prefix">Mandiri: </span>@endif{{ $item['level_description'] }}</div>
                                            <button
                                                type="button"
                                                class="row-desc-toggle"
                                                data-row-desc-toggle
                                                data-label-open="Lihat detail"
                                                data-label-close="Ringkas"
                                                aria-expanded="false"
                                                aria-controls="{{ $summaryDescId }}">
                                                Lihat detail
                                            </button>
                                        </div>
                                    @endif
                                    @if(!empty($item['qa_level_description'] ?? null))
                                        @if(!empty($item['level_description']))
                                            <div class="subtopic-level-separator qa-only" aria-hidden="true"></div>
                                        @endif
                                        <div class="subtopic-level-copy is-qa is-collapsible qa-only" data-row-desc-wrap>
                                            <div
                                                id="{{ $summaryQaDescId }}"
                                                class="subtopic-level-desc qa-level-font"
                                                data-row-desc>QA: {{ $item['qa_level_description'] }}</div>
                                            <button
                                                type="button"
                                                class="row-desc-toggle"
                                                data-row-desc-toggle
                                                data-label-open="Lihat detail QA"
                                                data-label-close="Ringkas QA"
                                                aria-expanded="false"
                                                aria-controls="{{ $summaryQaDescId }}">
                                                Lihat detail QA
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="keg-dual-metric">
                                        <div class="keg-dual-metric-row">
                                            <span class="keg-dual-metric-label">{{ $qaFeatureEnabled ? 'Mandiri' : 'Skor' }}</span>
                                            <span class="pill-skor">{{ is_numeric($item['score'] ?? null) ? number_format((float) ($item['score'] ?? 0), 2) : '-' }}</span>
                                        </div>
                                        <div class="keg-dual-metric-row qa-only">
                                            <span class="keg-dual-metric-label qa-level-font">QA</span>
                                            <span class="pill-skor">{{ is_numeric($item['qa_score'] ?? null) ? number_format((float) ($item['qa_score'] ?? 0), 2) : '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="keg-dual-metric">
                                        <div class="keg-dual-metric-row">
                                            <span class="keg-dual-metric-label">{{ $qaFeatureEnabled ? 'Mandiri' : 'Level' }}</span>
                                            <span class="pill-level {{ $rowLevelClass }}">
                                                {{ $rowLevel > 0 ? $rowLevel : '-' }}
                                            </span>
                                        </div>
                                        <div class="keg-dual-metric-row qa-only">
                                            <span class="keg-dual-metric-label qa-level-font">QA</span>
                                            <span class="pill-level {{ $rowLevelQaClass }}">
                                                {{ $rowLevelQa > 0 ? $rowLevelQa : '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>@if($qaFeatureEnabled)<span class="qa-mandiri-prefix">Mandiri: </span>@endif{{ (int) ($item['rows_verified'] ?? 0) }}/{{ (int) ($item['rows_total'] ?? 0) }} data</span>
                                        <span>{{ (int) ($item['progress'] ?? 0) }}%</span>
                                    </div>
                                    <div class="progress-bar element1-summary-progress">
                                        <div
                                            class="element1-summary-progress-fill"
                                            style="width: {{ max(0, min(100, (int) ($item['progress'] ?? 0))) }}%; --progress-delay: {{ $index * 110 }}ms;"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted mt-2 mb-1 qa-only">
                                        <span>QA: {{ (int) ($item['rows_qa_verified'] ?? 0) }}/{{ (int) ($item['rows_total'] ?? 0) }} data</span>
                                        <span>{{ (int) ($item['qa_progress'] ?? 0) }}%</span>
                                    </div>
                                    <div class="progress-bar element1-summary-progress is-qa qa-only">
                                        <div
                                            class="element1-summary-progress-fill is-qa"
                                            style="width: {{ max(0, min(100, (int) ($item['qa_progress'] ?? 0))) }}%; --progress-delay: {{ $index * 130 }}ms;"></div>
                                    </div>
                                </td>
                                <td class="aksi">
                                    <a class="btn-aksi btn-edit" href="{{ route('elements.show', $item['slug']) }}" title="Buka topik" aria-label="Buka topik">
                                        <svg class="aksi-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M5 12h14"></path>
                                            <path d="M13 6l6 6-6 6"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada topik yang terkonfigurasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('global-modals')
    <div class="keg-doc-modal keg-info-modal" id="element1InfoModal" hidden aria-hidden="true">
        <div class="keg-doc-backdrop" data-element1-info-close></div>
        <div class="keg-doc-dialog keg-info-dialog" role="dialog" aria-modal="true" aria-labelledby="element1InfoModalTitle">
            <div class="keg-doc-header keg-info-header">
                <h5 id="element1InfoModalTitle">{{ $summaryInfoModalTitle }}</h5>
                <button type="button" class="keg-doc-close" aria-label="Tutup modal informasi level elemen" data-element1-info-close>&times;</button>
            </div>
            <div class="keg-doc-body keg-info-body">
                <ol class="keg-info-list">
                    @foreach ($summaryInfoLevels as $infoLevel)
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
                <button type="button" class="btn btn-outline-secondary" data-element1-info-close>Tutup</button>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    (function () {
        const initElement1SummaryCountUp = () => {
            const page = document.getElementById('element1SummaryPage');
            if (!page) {
                return;
            }

            const isNumericDisplay = (text) => /^-?\d+(?:[.,]\d+)?$/.test((text || '').trim());
            const prefersReducedMotion = window.matchMedia
                && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            const nodes = page.querySelectorAll('.keg-chip .value, .pill-level, .pill-skor, .element1-stat-value, .element1-stat-split-value');
            nodes.forEach((node, idx) => {
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

                const decimals = normalized.includes('.') ? normalized.split('.')[1].length : 0;
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
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const currentValue = targetValue * eased;

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
        };

        initElement1SummaryCountUp();
        document.addEventListener('livewire:navigated', initElement1SummaryCountUp);
    })();

    (function () {
        const page = document.getElementById('element1SummaryPage');
        if (!page) {
            return;
        }

        const toggleButton = page.querySelector('[data-qa-toggle]');
        const applyQaDisplay = (showQa) => {
            page.classList.toggle('qa-display-off', !showQa);
            if (!toggleButton) {
                return;
            }

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

        const setRowDescriptionToggleLabel = (button, isExpanded) => {
            if (!button) {
                return;
            }
            button.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            button.textContent = isExpanded
                ? (button.getAttribute('data-label-close') || 'Ringkas')
                : (button.getAttribute('data-label-open') || 'Lihat detail');
        };

        const getRowDescriptionCollapsedHeight = (desc) => {
            if (!desc) {
                return 0;
            }
            const styles = window.getComputedStyle(desc);
            const lineHeight = Number.parseFloat(styles.lineHeight || '');
            const fontSize = Number.parseFloat(styles.fontSize || '');
            const resolvedLineHeight = Number.isFinite(lineHeight)
                ? lineHeight
                : (Number.isFinite(fontSize) ? fontSize * 1.46 : 18);
            return Math.ceil(resolvedLineHeight * 2);
        };

        const animateRowDescriptionToggle = (wrap, button) => {
            if (!wrap || !button) {
                return;
            }

            const desc = wrap.querySelector('[data-row-desc]');
            if (!desc) {
                return;
            }

            const isExpanding = !wrap.classList.contains('is-expanded');
            const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            setRowDescriptionToggleLabel(button, isExpanding);

            if (reduceMotion) {
                wrap.classList.toggle('is-expanded', isExpanding);
                return;
            }

            const collapsedHeight = getRowDescriptionCollapsedHeight(desc);
            const startHeight = isExpanding
                ? Math.max(collapsedHeight, Math.ceil(desc.getBoundingClientRect().height))
                : Math.ceil(desc.scrollHeight);

            wrap.classList.add('is-animating');
            desc.style.overflow = 'hidden';
            desc.style.maxHeight = `${startHeight}px`;

            if (isExpanding) {
                wrap.classList.add('is-expanded');
            }

            requestAnimationFrame(() => {
                if (!isExpanding) {
                    wrap.classList.remove('is-expanded');
                }
                const endHeight = isExpanding
                    ? Math.ceil(desc.scrollHeight)
                    : collapsedHeight;
                desc.style.maxHeight = `${endHeight}px`;
            });

            const finish = (event) => {
                if (event && event.target !== desc) {
                    return;
                }
                desc.removeEventListener('transitionend', finish);
                wrap.classList.remove('is-animating');
                desc.style.removeProperty('max-height');
                desc.style.removeProperty('overflow');
            };

            desc.addEventListener('transitionend', finish);
            window.setTimeout(finish, 460);
        };

        page.addEventListener('click', (event) => {
            const rowDescToggle = event.target.closest('[data-row-desc-toggle]');
            if (!rowDescToggle) {
                return;
            }

            event.preventDefault();
            const wrap = rowDescToggle.closest('[data-row-desc-wrap]');
            if (!wrap) {
                return;
            }

            animateRowDescriptionToggle(wrap, rowDescToggle);
        });

        applyQaDisplay(false);
    })();

    (function () {
        const page = document.getElementById('element1SummaryPage');
        const infoModal = document.getElementById('element1InfoModal');
        if (!page || !infoModal) {
            return;
        }

        const transitionMs = 220;
        let closeTimer = null;
        const getViewportUiScale = () => {
            const zoomRaw = getComputedStyle(document.body).zoom;
            const zoom = parseFloat(zoomRaw || '1');
            return Number.isFinite(zoom) && zoom > 0 ? zoom : 1;
        };
        const syncInfoModalToViewport = () => {
            if (!infoModal || !infoModal.classList.contains('is-open')) {
                return;
            }
            const scale = getViewportUiScale();
            infoModal.style.top = `${Math.round(window.scrollY / scale)}px`;
            infoModal.style.left = `${Math.round(window.scrollX / scale)}px`;
            infoModal.style.width = `${Math.ceil(window.innerWidth / scale)}px`;
            infoModal.style.height = `${Math.ceil(window.innerHeight / scale)}px`;
        };
        const clearInfoModalViewportStyles = () => {
            if (!infoModal) {
                return;
            }
            infoModal.style.removeProperty('top');
            infoModal.style.removeProperty('left');
            infoModal.style.removeProperty('width');
            infoModal.style.removeProperty('height');
        };

        const flashElementClass = (element, className, durationMs = 1200) => {
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
        };

        const getSummaryLevel = () => {
            const raw = page.getAttribute('data-element1-summary-level') || '';
            const parsed = Number.parseInt(raw, 10);
            if (!Number.isFinite(parsed) || parsed < 1 || parsed > 5) {
                return null;
            }
            return parsed;
        };

        const syncModalBodyLock = () => {
            const body = document.body;
            if (!body) {
                return;
            }

            if (infoModal.classList.contains('is-open')) {
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
        };

        const highlightModalLevel = () => {
            const summaryLevel = getSummaryLevel();
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
        };

        const openInfoModal = () => {
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
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
                    highlightModalLevel();
                });
            });
        };

        const closeInfoModal = () => {
            if (infoModal.hasAttribute('hidden') && !infoModal.classList.contains('is-open')) {
                return;
            }

            infoModal.classList.remove('is-open');
            infoModal.setAttribute('aria-hidden', 'true');
            if (closeTimer) {
                clearTimeout(closeTimer);
            }
            closeTimer = setTimeout(() => {
                if (!infoModal.classList.contains('is-open')) {
                    infoModal.setAttribute('hidden', 'hidden');
                    clearInfoModalViewportStyles();
                    syncModalBodyLock();
                }
                closeTimer = null;
            }, transitionMs);
        };

        document.addEventListener('click', (event) => {
            const openTrigger = event.target.closest('[data-element1-info-open]');
            if (openTrigger) {
                event.preventDefault();
                openInfoModal();
                flashElementClass(openTrigger, 'is-scroll-highlight', 1200);
                const targetHead = page.querySelector('.keg-head');
                if (targetHead) {
                    flashElementClass(targetHead, 'is-scroll-highlight', 1300);
                }
                return;
            }

            const closeTrigger = event.target.closest('[data-element1-info-close]');
            if (closeTrigger) {
                event.preventDefault();
                closeInfoModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && infoModal.classList.contains('is-open')) {
                closeInfoModal();
            }
        });

        document.addEventListener('livewire:navigated', () => {
            if (infoModal.classList.contains('is-open')) {
                closeInfoModal();
            }
        });

        window.addEventListener('resize', syncInfoModalToViewport);
        window.addEventListener('scroll', syncInfoModalToViewport, { passive: true });
        window.addEventListener('pageshow', syncInfoModalToViewport);
    })();
</script>
@endpush

