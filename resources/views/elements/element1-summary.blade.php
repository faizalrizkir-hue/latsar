@extends('layouts.dashboard-shell')
@php
    $pageTitle = $title ?? 'Rekap Element';
    $summaryStyles = collect($summaryStyles ?? [])->filter()->values();
    if ($summaryStyles->isEmpty()) {
        $summaryStyles = collect(['css/element1-kegiatan-asurans.css', 'css/element1-summary.css']);
    }
@endphp

@push('head')
    @foreach ($summaryStyles as $stylePath)
        <link rel="stylesheet" href="/{{ ltrim($stylePath, '/') }}">
    @endforeach
@endpush

@section('content')
    @php
        $overallLevel = (int) ($levelData['level'] ?? 1);
        $overallLevelClass = $overallLevel >= 1 && $overallLevel <= 5 ? 'is-level-'.$overallLevel : 'pending';
        $summaryHeaderCode = (string) ($summaryHeaderCode ?? 'E1');
        $summaryHeaderSubtitle = (string) ($summaryHeaderSubtitle ?? 'Rekap skor dan level dari sub topik');
        $summaryLevelLabel = (string) ($summaryLevelLabel ?? 'Level Element');
        $summaryInfoModalTitle = (string) ($summaryInfoModalTitle ?? 'Informasi Level Element');
        $summaryInfoLevels = collect($summaryInfoLevels ?? [])->values();
        $scoreComponents = collect($scoreComponents ?? [])->values();
        $elementInfoIconMap = [
            1 => '<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M15.5 12.6 17 21l-5-2.7L7 21l1.5-8.4"/><path d="m10.4 8 1.1 1.1 2.2-2.2"/></svg>',
            2 => '<svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="12" rx="2.5"/><path d="M9 7V5.8A1.8 1.8 0 0 1 10.8 4h2.4A1.8 1.8 0 0 1 15 5.8V7"/><path d="m9.5 13 1.6 1.6 3-3"/></svg>',
            3 => '<svg viewBox="0 0 24 24"><path d="M12 3 5 6v5.5c0 4.3 3 8.2 7 9.5 4-1.3 7-5.2 7-9.5V6l-7-3Z"/><path d="m9.3 11.7 2 2 3.6-3.6"/></svg>',
        ];
        $summaryHeaderElementNumber = null;
        if (preg_match('/^E\s*(\d+)$/i', trim($summaryHeaderCode), $summaryHeaderCodeMatch)) {
            $summaryHeaderElementNumber = (int) ($summaryHeaderCodeMatch[1] ?? 0);
        } elseif (preg_match('/element\s+(\d+)/i', (string) $title, $summaryTitleMatch)) {
            $summaryHeaderElementNumber = (int) ($summaryTitleMatch[1] ?? 0);
        }
        $summaryHeaderIconSvg = $elementInfoIconMap[$summaryHeaderElementNumber] ?? null;
    @endphp
    <div class="keg-page element1-summary-page" id="element1SummaryPage" data-element1-summary-level="{{ $overallLevel }}">
        <div class="keg-head">
            <div class="keg-title">
                <button type="button" class="keg-title-icon keg-info-trigger" data-element1-info-open aria-label="Lihat informasi level element">
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
                    <span class="label">{{ $summaryLevelLabel }}</span>
                    <span class="value">{{ $overallLevel }}</span>
                </div>
            </div>
        </div>

        <div class="summary-grid element1-summary-grid mb-3">
            <article class="keg-card element1-stat-card">
                <div class="element1-stat-label">Skor Tertimbang</div>
                <div class="element1-stat-value">{{ number_format((float) $weightedTotal, 2) }}</div>
                <div class="element1-stat-note keg-formula">
                    {{ number_format((float) ($elementScore ?? 0), 2) }} x {{ number_format(((float) ($elementWeight ?? 0)) * 100, 0) }}%
                </div>
            </article>
            <article class="keg-card element1-stat-card">
                <div class="element1-stat-label">Skor</div>
                <div class="element1-stat-value">{{ number_format((float) ($elementScore ?? 0), 2) }}</div>
                @if ($scoreComponents->isNotEmpty())
                    <div class="element1-stat-note keg-formula">
                        @foreach ($scoreComponents as $component)
                            ({{ number_format((float) ($component['score'] ?? 0), 2) }} x {{ number_format(((float) ($component['weight'] ?? 0)) * 100, 0) }}%){{ !$loop->last ? ' + ' : '' }}
                        @endforeach
                    </div>
                @else
                    <div class="element1-stat-note keg-formula">Belum ada formula sub topik.</div>
                @endif
            </article>
            <article class="keg-card element1-stat-card">
                <div class="element1-stat-label">Sub Topik & Progress</div>
                <div class="element1-stat-value">{{ (int) $subtopicCount }}</div>
                <div class="element1-stat-note">{{ (int) $totalVerifiedRows }}/{{ (int) $totalRows }} data tervalidasi ({{ (int) $completion }}%)</div>
            </article>
        </div>

        <div class="keg-card">
            <div class="table-responsive">
                <table class="table keg-table align-middle element1-summary-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">No</th>
                            <th>Sub Topik</th>
                            <th style="width:160px;">Skor Sub Topik</th>
                            <th style="width:140px;">Level</th>
                            <th style="width:210px;">Verifikasi</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subtopicSummaries as $index => $item)
                            @php
                                $rowLevel = (int) ($item['level'] ?? 0);
                                $rowLevelClass = $rowLevel >= 1 && $rowLevel <= 5 ? 'is-level-'.$rowLevel : 'pending';
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="pernyataan">{{ $item['title'] }}</div>
                                    <div class="bobot subtopic-predikat {{ $rowLevelClass !== 'pending' ? 'predikat-'.$rowLevelClass : 'predikat-pending' }}">
                                        {{ $item['predikat'] ?? '-' }}
                                    </div>
                                    @if(!empty($item['level_description']))
                                        <div class="subtopic-level-desc">{{ $item['level_description'] }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="pill-skor">{{ number_format((float) ($item['score'] ?? 0), 2) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="pill-level {{ $rowLevelClass }}">
                                        {{ $rowLevel > 0 ? $rowLevel : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>{{ (int) ($item['rows_verified'] ?? 0) }}/{{ (int) ($item['rows_total'] ?? 0) }} data</span>
                                        <span>{{ (int) ($item['progress'] ?? 0) }}%</span>
                                    </div>
                                    <div class="progress-bar element1-summary-progress">
                                        <div
                                            class="element1-summary-progress-fill"
                                            style="width: {{ max(0, min(100, (int) ($item['progress'] ?? 0))) }}%; --progress-delay: {{ $index * 110 }}ms;"></div>
                                    </div>
                                </td>
                                <td class="aksi">
                                    <a class="btn-aksi btn-edit" href="{{ route('elements.show', $item['slug']) }}" title="Buka sub topik" aria-label="Buka sub topik">
                                        <svg class="aksi-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M5 12h14"></path>
                                            <path d="M13 6l6 6-6 6"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada sub topik yang terkonfigurasi.</td>
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
                <button type="button" class="keg-doc-close" aria-label="Tutup modal informasi level element" data-element1-info-close>&times;</button>
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

            const nodes = page.querySelectorAll('.keg-chip .value, .pill-level, .pill-skor, .element1-stat-value');
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
