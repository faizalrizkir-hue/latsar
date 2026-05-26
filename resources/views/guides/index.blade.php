@extends('layouts.dashboard-shell')

@php
    $pageTitle = $pageTitle ?? 'Panduan';
    $guides = is_array($guides ?? null) ? $guides : [];
    $processImages = is_array($processImages ?? null) ? $processImages : [];
    $guideDirectory = trim((string) ($guideDirectory ?? 'uploads/panduan'));
    $processImageCount = count($processImages);
@endphp

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/guides.css') }}">
@endpush

@section('content')
    <div class="guides-page" data-guides-page>
        <section class="guide-hero" aria-labelledby="guide-page-title">
            <div class="guide-hero__content">
                <span class="guide-kicker">Pusat Bantuan Aplikasi</span>
                <h1 id="guide-page-title">Panduan</h1>
                <p>Pilih panduan sesuai peran, baca dokumen dalam tampilan flipbook interaktif, dan lihat gambar proses bisnis PK APIP.</p>
            </div>
        </section>

        <section class="guide-picker" aria-label="Daftar panduan">
            @foreach($guides as $index => $guide)
                @php
                    $isAvailable = (bool) ($guide['available'] ?? false);
                    $guideKey = trim((string) ($guide['key'] ?? 'guide-'.$index));
                    $guideTitle = trim((string) ($guide['title'] ?? 'Panduan'));
                    $guideAudience = trim((string) ($guide['audience'] ?? ''));
                    $guideDescription = trim((string) ($guide['description'] ?? ''));
                    $guideUrl = trim((string) ($guide['file_url'] ?? ''));
                    $guideFileName = trim((string) ($guide['file_name'] ?? ''));
                    $guidePreferredFile = trim((string) ($guide['preferred_file'] ?? ''));
                    $guideSize = trim((string) ($guide['file_size'] ?? ''));
                    $guideModified = trim((string) ($guide['last_modified'] ?? ''));
                    $guideAccent = trim((string) ($guide['accent'] ?? 'blue'));
                    $guideCardTitle = match ($guideKey) {
                        'administrator' => 'Administrator',
                        'user' => 'User',
                        default => $guideTitle,
                    };
                    $guideDisplayTitle = match ($guideKey) {
                        'administrator' => 'Panduan Administrator',
                        'user' => 'Panduan User',
                        default => $guideTitle,
                    };
                    $guideAudienceLabel = match ($guideKey) {
                        'administrator' => 'Akses pengelola sistem',
                        'user' => 'Koordinator / Anggota Tim',
                        default => $guideAudience,
                    };
                    $guideEmbedUrl = trim((string) ($guide['embed_url'] ?? ''));
                    $guideEmbedId = trim((string) ($guide['embed_id'] ?? ''));
                    $guideEmbedRatio = trim((string) ($guide['embed_ratio'] ?? '3:2'));
                    $guideEmbedLightbox = trim((string) ($guide['embed_lightbox'] ?? 'yes'));
                    $guideEmbedWidth = trim((string) ($guide['embed_width'] ?? '100%'));
                    $guideEmbedHeight = trim((string) ($guide['embed_height'] ?? 'auto'));
                    $guideEmbedVersion = trim((string) ($guide['embed_version'] ?? '1'));
                    $guideEmbedScriptUrl = trim((string) ($guide['embed_script_url'] ?? ''));
                    $guideEmbedTitle = trim((string) ($guide['embed_title'] ?? ''));
                    $guideEmbedProvider = trim((string) ($guide['embed_provider'] ?? 'Online Flipbook'));
                @endphp
                <button
                    type="button"
                    class="guide-card guide-card--{{ $guideAccent }}{{ $index === 0 ? ' is-active' : '' }}{{ $isAvailable ? '' : ' is-missing' }}"
                    data-guide-selector
                    data-guide-key="{{ $guideKey }}"
                    data-guide-mode="guide"
                    data-guide-title="{{ $guideDisplayTitle }}"
                    data-guide-audience="{{ $guideAudienceLabel }}"
                    data-guide-description="{{ $guideDescription }}"
                    data-guide-url="{{ $guideUrl }}"
                    data-guide-file="{{ $guideFileName }}"
                    data-guide-preferred-file="{{ $guidePreferredFile }}"
                    data-guide-size="{{ $guideSize }}"
                    data-guide-modified="{{ $guideModified }}"
                    data-guide-embed-url="{{ $guideEmbedUrl }}"
                    data-guide-embed-id="{{ $guideEmbedId }}"
                    data-guide-embed-ratio="{{ $guideEmbedRatio }}"
                    data-guide-embed-lightbox="{{ $guideEmbedLightbox }}"
                    data-guide-embed-width="{{ $guideEmbedWidth }}"
                    data-guide-embed-height="{{ $guideEmbedHeight }}"
                    data-guide-embed-version="{{ $guideEmbedVersion }}"
                    data-guide-embed-script-url="{{ $guideEmbedScriptUrl }}"
                    data-guide-embed-title="{{ $guideEmbedTitle }}"
                    data-guide-embed-provider="{{ $guideEmbedProvider }}"
                    aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                >
                    <span class="guide-card__icon" aria-hidden="true">
                        @if($guideKey === 'administrator')
                            <svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.6 3 8.6 7 10 4-1.4 7-5.4 7-10V6l-7-3Z"/><path d="M9 12l2 2 4-5"/></svg>
                        @else
                            <svg viewBox="0 0 24 24"><path d="M7 8a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z"/><path d="M3 21a8 8 0 0 1 16 0"/><path d="M18 8h3"/><path d="M19.5 6.5v3"/></svg>
                        @endif
                    </span>
                    <span class="guide-card__body">
                        <span class="guide-card__topline">
                            <span class="guide-card__title">{{ $guideCardTitle }}</span>
                            <span class="guide-card__audience">{{ $guideAudienceLabel }}</span>
                        </span>
                        <span class="guide-card__desc">{{ $guideDescription }}</span>
                        <span class="guide-card__meta">
                            @if($guideEmbedUrl !== '')
                                <span>{{ $guideEmbedProvider }}</span>
                                @if($guideFileName !== '')
                                    <span>PDF lokal tersedia</span>
                                @endif
                            @elseif($isAvailable)
                                <span>{{ $guideFileName }}</span>
                                @if($guideSize !== '')
                                    <span>{{ $guideSize }}</span>
                                @endif
                                @if($guideModified !== '')
                                    <span>{{ $guideModified }}</span>
                                @endif
                            @else
                                <span>Letakkan PDF di public/{{ $guideDirectory }}/{{ $guidePreferredFile }}</span>
                            @endif
                        </span>
                    </span>
                    <span class="guide-card__status">{{ $guideEmbedUrl !== '' ? 'Embed' : ($isAvailable ? 'Buka' : 'Belum Ada File') }}</span>
                </button>
            @endforeach

            @php
                $firstProcessImage = $processImages[0] ?? [];
                $firstProcessImageUrl = trim((string) ($firstProcessImage['file_url'] ?? ''));
            @endphp
            <button
                type="button"
                class="guide-card guide-card--amber{{ $processImageCount > 0 ? '' : ' is-missing' }}"
                data-guide-selector
                data-guide-key="process-business"
                data-guide-mode="process"
                data-guide-title="Gambar Proses Bisnis"
                data-guide-audience="Alur Kerja PK APIP"
                data-guide-description="Visualisasi tahapan kerja, relasi aktivitas, dan alur koordinasi proses bisnis PK APIP."
                data-guide-url="{{ $firstProcessImageUrl }}"
                aria-pressed="false"
            >
                <span class="guide-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 5h16v5H4z"/><path d="M7 10v4h10v-4"/><path d="M12 14v5"/><path d="M7 19h10"/><path d="M7 19v-3"/><path d="M17 19v-3"/></svg>
                </span>
                <span class="guide-card__body">
                    <span class="guide-card__topline">
                        <span class="guide-card__title">Proses Bisnis</span>
                        <span class="guide-card__audience">Alur Kerja PK APIP</span>
                    </span>
                    <span class="guide-card__desc">Gambar proses bisnis untuk melihat tahapan dan alur koordinasi secara ringkas.</span>
                    <span class="guide-card__meta">
                        @if($processImageCount > 0)
                            <span>{{ $processImageCount }} gambar tersedia</span>
                            @if(trim((string) ($firstProcessImage['file_name'] ?? '')) !== '')
                                <span>{{ $firstProcessImage['file_name'] }}</span>
                            @endif
                        @else
                            <span>Letakkan gambar di public/{{ $guideDirectory }}</span>
                        @endif
                    </span>
                </span>
                <span class="guide-card__status">{{ $processImageCount > 0 ? 'Lihat' : 'Belum Ada Gambar' }}</span>
            </button>
        </section>

        <section class="guide-reader" aria-label="Area konten panduan" data-guide-reader>
            <div class="guide-reader__head">
                <div>
                    <span class="guide-reader__eyebrow" data-guide-current-audience>Panduan</span>
                    <h2 data-guide-current-title>Panduan</h2>
                    <p data-guide-current-description>Pilih salah satu panduan untuk mulai membaca.</p>
                </div>
                <a
                    href="#"
                    class="guide-open-link"
                    target="_blank"
                    rel="noopener noreferrer"
                    data-guide-open-link
                    hidden
                >
                    Buka PDF
                </a>
            </div>

            <div class="guide-toolbar" data-guide-toolbar>
                <button type="button" class="guide-icon-btn" data-guide-prev aria-label="Halaman sebelumnya" title="Halaman sebelumnya">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                </button>
                <div class="guide-page-indicator" aria-live="polite">
                    <span data-guide-page-current>0</span>
                    <span>/</span>
                    <span data-guide-page-total>0</span>
                </div>
                <button type="button" class="guide-icon-btn" data-guide-next aria-label="Halaman berikutnya" title="Halaman berikutnya">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                </button>
                <span class="guide-toolbar__divider" aria-hidden="true"></span>
                <button type="button" class="guide-icon-btn" data-guide-zoom-out aria-label="Perkecil" title="Perkecil">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M8 11h6"/><path d="m16 16 4 4"/></svg>
                </button>
                <span class="guide-zoom-label" data-guide-zoom-label>100%</span>
                <button type="button" class="guide-icon-btn" data-guide-zoom-in aria-label="Perbesar" title="Perbesar">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M8 11h6"/><path d="M11 8v6"/><path d="m16 16 4 4"/></svg>
                </button>
            </div>

            <div class="guide-loading" data-guide-loading hidden>
                <span></span>
                <strong>Memuat flipbook...</strong>
            </div>

            <div class="guide-book-stage" data-guide-book-stage>
                <div class="guide-book" data-guide-book>
                    <div class="guide-book__page guide-book__page--left" data-guide-page-shell="left">
                        <div class="guide-book__paper">
                            <canvas data-guide-canvas-left></canvas>
                            <span class="guide-book__page-number" data-guide-left-label></span>
                        </div>
                    </div>
                    <div class="guide-book__spine" aria-hidden="true"></div>
                    <div class="guide-book__page guide-book__page--right" data-guide-page-shell="right">
                        <div class="guide-book__paper">
                            <canvas data-guide-canvas-right></canvas>
                            <span class="guide-book__page-number" data-guide-right-label></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="guide-embed-preview" data-guide-embed-preview hidden></div>

            <div class="guide-process-panel" data-guide-process-panel data-has-images="{{ $processImageCount > 0 ? '1' : '0' }}" hidden>
                <div class="guide-process-list">
                    @forelse($processImages as $image)
                        @php
                            $imageTitle = trim((string) ($image['title'] ?? 'Gambar Proses Bisnis'));
                            $imageUrl = trim((string) ($image['file_url'] ?? ''));
                            $imageFileName = trim((string) ($image['file_name'] ?? ''));
                            $imageSize = trim((string) ($image['file_size'] ?? ''));
                            $imageModified = trim((string) ($image['last_modified'] ?? ''));
                        @endphp

                        <article class="guide-process-card">
                            <a
                                class="guide-process-card__media"
                                href="{{ $imageUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Buka {{ $imageTitle }}"
                            >
                                <img src="{{ $imageUrl }}" alt="Gambar proses bisnis {{ $imageTitle }}" loading="lazy">
                            </a>
                            <div class="guide-process-card__content">
                                <span class="guide-process-card__tag">Proses Bisnis</span>
                                <h3>{{ $imageTitle }}</h3>
                                <p>Gambaran proses bisnis PK APIP yang melengkapi panduan penggunaan aplikasi.</p>
                                <div class="guide-process-card__meta">
                                    @if($imageFileName !== '')
                                        <span>{{ $imageFileName }}</span>
                                    @endif
                                    @if($imageSize !== '')
                                        <span>{{ $imageSize }}</span>
                                    @endif
                                    @if($imageModified !== '')
                                        <span>{{ $imageModified }}</span>
                                    @endif
                                </div>
                                <a class="guide-process-card__link" href="{{ $imageUrl }}" target="_blank" rel="noopener noreferrer">
                                    Lihat Gambar
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="guide-process-empty">
                            <span>Belum Ada Gambar</span>
                            <h3>Gambar proses bisnis belum tersedia</h3>
                            <p>Letakkan file gambar di public/{{ $guideDirectory }} agar bagian proses bisnis bisa ditampilkan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <iframe
                class="guide-native-preview"
                data-guide-native-preview
                title="Pratinjau PDF panduan"
                hidden
            ></iframe>

            <div class="guide-empty" data-guide-empty hidden>
                <span class="guide-empty__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M5 4h10a4 4 0 0 1 4 4v12H9a4 4 0 0 0-4 4V4Z"/><path d="M9 4h10v16"/><path d="M9 10h6"/><path d="M9 14h4"/></svg>
                </span>
                <h3 data-guide-empty-title>File panduan belum tersedia</h3>
                <p data-guide-empty-message>Tambahkan PDF panduan ke folder public/{{ $guideDirectory }} agar flipbook bisa ditampilkan.</p>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="{{ \App\Support\VersionedAsset::url('js/guides.js') }}"></script>
@endpush
