@extends('layouts.dashboard-shell')
@php
    $pageTitle = $pageTitle ?? 'Area Of Improvement (AoI)';
    $items = collect($items ?? []);
    $activeBudgetYear = is_numeric($activeBudgetYear ?? null) ? (int) $activeBudgetYear : (int) now('Asia/Jakarta')->year;
@endphp

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/aoi.css') }}">
@endpush

@section('content')
    <div class="aoi-page">
        <section class="aoi-hero-card">
            <div class="aoi-hero-main">
                <h3 class="aoi-title">Area Of Improvement (AoI)</h3>
                <p class="aoi-subtitle">Daftar hasil verifikasi final QA dan rekomendasi tindak lanjut dari seluruh topik.</p>
                <span class="aoi-year-chip">Tahun Anggaran {{ $activeBudgetYear }}</span>
            </div>
            <div class="aoi-hero-actions">
                <button type="button" class="aoi-print-btn" data-aoi-print-btn aria-label="Buka print preview rekap AoI" title="Print Preview Rekap AoI">
                    <svg class="aoi-print-btn-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 9V4h10v5"></path>
                        <rect x="5" y="14" width="14" height="6" rx="1.5"></rect>
                        <rect x="4" y="9" width="16" height="7" rx="2"></rect>
                        <path d="M17 12h.01"></path>
                    </svg>
                    <span class="aoi-print-btn-text">Print Preview</span>
                </button>
            </div>
            <div class="aoi-total-chip">
                <span class="aoi-total-label">Jumlah AoI</span>
                <span class="aoi-total-value">{{ (int) ($totalItems ?? $items->count()) }}</span>
            </div>
        </section>

        <section class="aoi-table-card aoi-print-area">
            <header class="aoi-print-header" aria-hidden="true">
                <h2>Area Of Improvement Penilaian Kapabilitas APIP</h2>
                <div class="aoi-print-meta">
                    <span>Tahun Anggaran <strong>{{ $activeBudgetYear }}</strong></span>
                    <span>Total AoI <strong>{{ (int) ($totalItems ?? $items->count()) }}</strong></span>
                    <span>Tanggal Cetak <strong data-aoi-print-date>-</strong></span>
                </div>
            </header>
            @if ($items->isEmpty())
                <div class="aoi-empty">
                    Belum ada data AoI dari verifikasi final QA.
                </div>
            @else
                <div class="aoi-print-list" aria-hidden="true">
                    @foreach ($items as $index => $item)
                        <article class="aoi-print-item">
                            <div class="aoi-print-item-head">
                                <div class="aoi-print-number">{{ $index + 1 }}</div>
                                <div class="aoi-print-context">
                                    <div class="aoi-print-context-row">
                                        <span>Elemen</span>
                                        <strong>{{ $item['element_title'] ?: '-' }}</strong>
                                    </div>
                                    <div class="aoi-print-context-row">
                                        <span>Topik</span>
                                        <strong>{{ $item['subtopic_title'] ?: '-' }}</strong>
                                    </div>
                                    <div class="aoi-print-context-row">
                                        <span>Pernyataan</span>
                                        <strong>{{ $item['pernyataan'] ?: '-' }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="aoi-print-body">
                                <section class="aoi-print-section">
                                    <h3>Hasil Verifikasi QA</h3>
                                    <p>{{ $item['hasil_verifikasi_qa'] !== '' ? $item['hasil_verifikasi_qa'] : '-' }}</p>
                                </section>
                                <section class="aoi-print-section">
                                    <h3>Rekomendasi Tindak Lanjut</h3>
                                    <p>{{ $item['rekomendasi_tindak_lanjut'] !== '' ? $item['rekomendasi_tindak_lanjut'] : '-' }}</p>
                                </section>
                            </div>
                            <footer class="aoi-print-item-foot">
                                <span>Verifikator QA: <strong>{{ $item['qa_verified_by'] !== '' ? $item['qa_verified_by'] : '-' }}</strong></span>
                                <span>Tanggal Verifikasi: <strong>{{ ($item['qa_verified_at_print'] ?? '') ?: ($item['qa_verified_at'] ?: '-') }}</strong></span>
                            </footer>
                        </article>
                    @endforeach
                </div>

                <div class="table-wrapper aoi-table-wrap">
                    <table class="aoi-table">
                        <thead>
                            <tr>
                                <th style="width:64px;">No</th>
                                <th style="min-width:380px;">Elemen / Topik / Pernyataan</th>
                                <th style="min-width:280px;">Hasil Verifikasi QA</th>
                                <th style="min-width:280px;">Rekomendasi Tindak Lanjut</th>
                                <th style="width:150px;">Verifikator QA</th>
                                <th style="width:170px;">Waktu Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="aoi-context">
                                            <div class="aoi-context-item">
                                                <span class="aoi-context-label">Elemen</span>
                                                <div class="aoi-context-value aoi-col-element">{{ $item['element_title'] ?: '-' }}</div>
                                            </div>
                                            <div class="aoi-context-item">
                                                <span class="aoi-context-label">Topik</span>
                                                <div class="aoi-context-value aoi-col-subtopic">{{ $item['subtopic_title'] ?: '-' }}</div>
                                            </div>
                                            <div class="aoi-context-item">
                                                <span class="aoi-context-label">Pernyataan</span>
                                                <div class="aoi-context-value aoi-statement">{{ $item['pernyataan'] ?: '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="aoi-note">{{ $item['hasil_verifikasi_qa'] !== '' ? $item['hasil_verifikasi_qa'] : '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="aoi-note">{{ $item['rekomendasi_tindak_lanjut'] !== '' ? $item['rekomendasi_tindak_lanjut'] : '-' }}</div>
                                    </td>
                                    <td class="text-center">{{ $item['qa_verified_by'] !== '' ? $item['qa_verified_by'] : '-' }}</td>
                                    <td class="text-center">{{ $item['qa_verified_at'] ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const printButton = document.querySelector('[data-aoi-print-btn]');
            const printDateEl = document.querySelector('[data-aoi-print-date]');

            const toLocalePrintDate = (date) => {
                try {
                    return new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }).format(date) + ' WIB';
                } catch (error) {
                    return date.toLocaleString('id-ID') + ' WIB';
                }
            };

            const updatePrintMeta = () => {
                if (printDateEl) {
                    printDateEl.textContent = toLocalePrintDate(new Date());
                }
            };

            updatePrintMeta();

            if (!printButton) {
                return;
            }

            printButton.addEventListener('click', () => {
                updatePrintMeta();
                window.print();
            });
        })();
    </script>
@endpush


