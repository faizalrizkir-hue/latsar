@extends('layouts.dashboard-shell')
@php
    $pageTitle = $pageTitle ?? 'Area Of Improvement (AoI)';
    $items = collect($items ?? []);
@endphp

@push('head')
    <link rel="stylesheet" href="/css/aoi.css?v={{ @filemtime(public_path('css/aoi.css')) }}">
@endpush

@section('content')
    <div class="aoi-page">
        <section class="aoi-hero-card">
            <div class="aoi-hero-main">
                <h3 class="aoi-title">Area Of Improvement (AoI)</h3>
                <p class="aoi-subtitle">Daftar hasil verifikasi final QA dan rekomendasi tindak lanjut dari seluruh sub topik.</p>
            </div>
            <div class="aoi-hero-actions">
                <button type="button" class="aoi-print-btn" data-aoi-print-btn>
                    Print Rekap AoI
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
                    <span>Tanggal Cetak: <strong data-aoi-print-date>-</strong></span>
                    <span>Jumlah Cetak: <strong data-aoi-print-count>0x</strong></span>
                </div>
            </header>
            @if ($items->isEmpty())
                <div class="aoi-empty">
                    Belum ada data AoI dari verifikasi final QA.
                </div>
            @else
                <div class="table-wrapper aoi-table-wrap">
                    <table class="aoi-table">
                        <thead>
                            <tr>
                                <th style="width:64px;">No</th>
                                <th style="min-width:380px;">Element / Sub Topik / Pernyataan</th>
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
                                                <span class="aoi-context-label">Element</span>
                                                <div class="aoi-context-value aoi-col-element">{{ $item['element_title'] ?: '-' }}</div>
                                            </div>
                                            <div class="aoi-context-item">
                                                <span class="aoi-context-label">Sub Topik</span>
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
            const printCountEl = document.querySelector('[data-aoi-print-count]');
            const storageKey = 'aoi_print_count_v1';

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

            const readCount = () => {
                try {
                    const raw = window.localStorage.getItem(storageKey);
                    const parsed = Number.parseInt(raw || '0', 10);
                    return Number.isFinite(parsed) && parsed > 0 ? parsed : 0;
                } catch (error) {
                    return 0;
                }
            };

            const writeCount = (value) => {
                try {
                    window.localStorage.setItem(storageKey, String(value));
                } catch (error) {
                    // ignore localStorage write issues
                }
            };

            const updatePrintMeta = (count) => {
                if (printDateEl) {
                    printDateEl.textContent = toLocalePrintDate(new Date());
                }
                if (printCountEl) {
                    printCountEl.textContent = String(count) + 'x';
                }
            };

            updatePrintMeta(readCount());

            if (!printButton) {
                return;
            }

            printButton.addEventListener('click', () => {
                const nextCount = readCount() + 1;
                writeCount(nextCount);
                updatePrintMeta(nextCount);
                window.print();
            });
        })();
    </script>
@endpush
