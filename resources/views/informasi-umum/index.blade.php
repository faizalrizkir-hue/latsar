@extends('layouts.dashboard-shell')

@php
    $pageTitle = $pageTitle ?? 'Informasi Umum';
    $uuReferenceUrl = 'https://peraturan.bpk.go.id/Download/28013/UU%20Nomor%2023%20Tahun%202014.pdf';
    $legalRegulations = is_array($legalRegulations ?? null) ? $legalRegulations : [];
    $profile = $profile ?? null;
    $fieldValue = static function (string $name, string $fallback = '') use ($profile): string {
        $currentValue = old($name);
        if (is_string($currentValue)) {
            return $currentValue;
        }

        $resolvedValue = $profile?->{$name};
        if (is_string($resolvedValue)) {
            return $resolvedValue;
        }

        return $fallback;
    };
    $fieldIcon = static function (string $name): string {
        return match ($name) {
            'pemerintah_daerah' => '<svg viewBox="0 0 24 24"><path d="M3 10h18"/><path d="M5 10V7l7-3 7 3v3"/><path d="M6 10v7"/><path d="M10 10v7"/><path d="M14 10v7"/><path d="M18 10v7"/><path d="M3 17h18"/></svg>',
            'nama_skpd' => '<svg viewBox="0 0 24 24"><path d="M4 20h16"/><path d="M6 20V6h12v14"/><path d="M9 9h2"/><path d="M13 9h2"/><path d="M9 13h2"/><path d="M13 13h2"/></svg>',
            'bidang' => '<svg viewBox="0 0 24 24"><path d="M4 7h16v10H4z"/><path d="M9 7V5h6v2"/><path d="M4 11h16"/></svg>',
            'kepala_pemerintah_daerah' => '<svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4 20a8 8 0 0 1 16 0"/><path d="m8 3 2 2 2-2 2 2 2-2"/></svg>',
            'undang_undang_pendirian' => '<svg viewBox="0 0 24 24"><path d="M5 4h10a3 3 0 0 1 3 3v13H8a3 3 0 0 0-3 3V4Z"/><path d="M8 4h10v16"/><path d="M10 9h6"/><path d="M10 13h6"/></svg>',
            'visi' => '<svg viewBox="0 0 24 24"><path d="M2 12s4-6 10-6 10 6 10 6-4 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>',
            'misi' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="1.2"/><path d="m19 5-4 4"/></svg>',
            'inspektur' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M6 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="m6 5 2 1"/><path d="m18 5-2 1"/></svg>',
            'alamat_kantor' => '<svg viewBox="0 0 24 24"><path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>',
            'jumlah_kantor_wilayah' => '<svg viewBox="0 0 24 24"><path d="M3 6 9 3l6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15"/><path d="M15 6v15"/></svg>',
            'kontak' => '<svg viewBox="0 0 24 24"><path d="M6 4h4l2 5-3 2a14 14 0 0 0 4 4l2-3 5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 4 6a2 2 0 0 1 2-2Z"/></svg>',
            'website' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18"/><path d="M12 3a14 14 0 0 0 0 18"/></svg>',
            default => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>',
        };
    };
    $fieldIconClass = static function (string $name): string {
        return match ($name) {
            'pemerintah_daerah', 'nama_skpd', 'bidang' => 'is-blue',
            'kepala_pemerintah_daerah', 'inspektur' => 'is-purple',
            'undang_undang_pendirian' => 'is-amber',
            'visi', 'misi' => 'is-teal',
            'alamat_kantor', 'jumlah_kantor_wilayah' => 'is-orange',
            'kontak' => 'is-rose',
            'website' => 'is-indigo',
            default => 'is-slate',
        };
    };
    $websiteText = trim($fieldValue('website'));
    $websiteHref = '';
    if ($websiteText !== '') {
        $websiteHref = preg_match('/^https?:\/\//i', $websiteText) ? $websiteText : 'https://'.$websiteText;
    }
@endphp

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/informasi-umum.css') }}">
@endpush

@section('content')
    <div class="general-info-page">
        <form method="POST" action="{{ route('informasi-umum.update') }}" class="general-form">
            @csrf

            <section class="general-card general-legal-card">
                <div class="general-card-head">
                    <div>
                        <h3>Dasar Hukum Penilaian Kapabilitas APIP</h3>
                        <p>Daftar pedoman/peraturan resmi yang menjadi acuan penilaian kapabilitas APIP.</p>
                    </div>
                </div>
                <div class="general-card-body">
                    <div class="general-legal-list">
                        @forelse($legalRegulations as $index => $regulation)
                            @php
                                $fileName = trim((string) ($regulation['file_name'] ?? ''));
                                $title = trim((string) ($regulation['title'] ?? ''));
                                $category = trim((string) ($regulation['category'] ?? ''));
                                $fileUrl = trim((string) ($regulation['file_url'] ?? ''));
                                $isAvailable = $fileUrl !== '';
                            @endphp
                            <article class="general-legal-item {{ $isAvailable ? '' : 'is-missing' }}">
                                <div class="general-legal-item__badge">{{ $index + 1 }}</div>
                                <div class="general-legal-item__content">
                                    <h4>{{ $title !== '' ? $title : 'Dokumen tanpa judul' }}</h4>
                                    <p>
                                        @if($category !== '')
                                            {{ $category }} â€¢
                                        @endif
                                        {{ $fileName !== '' ? $fileName : 'File belum ditentukan' }}
                                    </p>
                                </div>
                                <div class="general-legal-item__action">
                                    @if($isAvailable)
                                        <a
                                            href="{{ $fileUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="general-legal-open-btn"
                                        >
                                            Lihat
                                        </a>
                                    @else
                                        <span class="general-legal-open-btn is-disabled">Tidak Ada File</span>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <article class="general-legal-item is-missing">
                                <div class="general-legal-item__badge">-</div>
                                <div class="general-legal-item__content">
                                    <h4>Belum ada dokumen dasar hukum</h4>
                                    <p>Tambahkan file PDF ke folder <strong>/public/uploads/pedoman</strong>.</p>
                                </div>
                                <div class="general-legal-item__action">
                                    <span class="general-legal-open-btn is-disabled">Tidak Ada File</span>
                                </div>
                            </article>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="general-card">
                <div class="general-card-head">
                    <div>
                        <h3>Profil Satuan Kerja Perangkat Daerah</h3>
                        <p>Profil dasar organisasi untuk kebutuhan penilaian kapabilitas APIP.</p>
                    </div>
                </div>
                <div class="general-card-body">
                    <div class="general-grid">
                        <div class="general-field">
                            <label class="general-label">
                                <span class="general-label-icon {{ $fieldIconClass('pemerintah_daerah') }}" aria-hidden="true">{!! $fieldIcon('pemerintah_daerah') !!}</span>
                                <span>Pemerintah Daerah</span>
                            </label>
                            <input type="text" class="general-input is-locked" value="{{ $fieldValue('pemerintah_daerah') }}" disabled>
                        </div>

                        <div class="general-field">
                            <label class="general-label">
                                <span class="general-label-icon {{ $fieldIconClass('nama_skpd') }}" aria-hidden="true">{!! $fieldIcon('nama_skpd') !!}</span>
                                <span>Nama Satuan Kerja Perangkat Daerah</span>
                            </label>
                            <input type="text" class="general-input is-locked" value="{{ $fieldValue('nama_skpd') }}" disabled>
                        </div>

                        <div class="general-field">
                            <label class="general-label">
                                <span class="general-label-icon {{ $fieldIconClass('bidang') }}" aria-hidden="true">{!! $fieldIcon('bidang') !!}</span>
                                <span>Bidang</span>
                            </label>
                            <input type="text" class="general-input is-locked" value="{{ $fieldValue('bidang') }}" disabled>
                        </div>

                        <div class="general-field">
                            <label class="general-label" for="kepala_pemerintah_daerah">
                                <span class="general-label-icon {{ $fieldIconClass('kepala_pemerintah_daerah') }}" aria-hidden="true">{!! $fieldIcon('kepala_pemerintah_daerah') !!}</span>
                                <span>Kepala Pemerintah Daerah (Gubernur)</span>
                            </label>
                            <input
                                id="kepala_pemerintah_daerah"
                                type="text"
                                name="kepala_pemerintah_daerah"
                                class="general-input @error('kepala_pemerintah_daerah') is-invalid @enderror"
                                value="{{ $fieldValue('kepala_pemerintah_daerah') }}"
                                @disabled(!$canEdit)
                            >
                            @error('kepala_pemerintah_daerah')
                                <div class="general-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="general-field">
                            <label class="general-label" for="undang_undang_pendirian">
                                <span class="general-label-icon {{ $fieldIconClass('undang_undang_pendirian') }}" aria-hidden="true">{!! $fieldIcon('undang_undang_pendirian') !!}</span>
                                <span>Undang-Undang Pendirian</span>
                            </label>
                            <a
                                id="undang_undang_pendirian"
                                href="{{ $uuReferenceUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="general-link-field"
                            >
                                {{ $fieldValue('undang_undang_pendirian') }}
                            </a>
                        </div>

                        <div class="general-field">
                            <label class="general-label" for="visi">
                                <span class="general-label-icon {{ $fieldIconClass('visi') }}" aria-hidden="true">{!! $fieldIcon('visi') !!}</span>
                                <span>Visi</span>
                            </label>
                            <textarea
                                id="visi"
                                class="general-textarea is-locked"
                                rows="3"
                                disabled
                            >{{ $fieldValue('visi') }}</textarea>
                        </div>

                        <div class="general-field general-field-full">
                            <label class="general-label">
                                <span class="general-label-icon {{ $fieldIconClass('misi') }}" aria-hidden="true">{!! $fieldIcon('misi') !!}</span>
                                <span>Misi</span>
                            </label>
                            <textarea class="general-textarea is-locked" rows="6" disabled>{{ $fieldValue('misi') }}</textarea>
                        </div>

                        <div class="general-field">
                            <label class="general-label" for="inspektur">
                                <span class="general-label-icon {{ $fieldIconClass('inspektur') }}" aria-hidden="true">{!! $fieldIcon('inspektur') !!}</span>
                                <span>Inspektur</span>
                            </label>
                            <input
                                id="inspektur"
                                type="text"
                                name="inspektur"
                                class="general-input @error('inspektur') is-invalid @enderror"
                                value="{{ $fieldValue('inspektur') }}"
                                @disabled(!$canEdit)
                            >
                            @error('inspektur')
                                <div class="general-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="general-field">
                            <label class="general-label">
                                <span class="general-label-icon {{ $fieldIconClass('alamat_kantor') }}" aria-hidden="true">{!! $fieldIcon('alamat_kantor') !!}</span>
                                <span>Alamat Kantor</span>
                            </label>
                            <textarea class="general-textarea is-locked" rows="4" disabled>{{ $fieldValue('alamat_kantor') }}</textarea>
                        </div>

                        <div class="general-field">
                            <label class="general-label">
                                <span class="general-label-icon {{ $fieldIconClass('jumlah_kantor_wilayah') }}" aria-hidden="true">{!! $fieldIcon('jumlah_kantor_wilayah') !!}</span>
                                <span>Jumlah Kantor Wilayah</span>
                            </label>
                            <textarea class="general-textarea is-locked" rows="3" disabled>{{ $fieldValue('jumlah_kantor_wilayah') }}</textarea>
                        </div>

                        <div class="general-field">
                            <label class="general-label">
                                <span class="general-label-icon {{ $fieldIconClass('kontak') }}" aria-hidden="true">{!! $fieldIcon('kontak') !!}</span>
                                <span>Kontak</span>
                            </label>
                            <input type="text" class="general-input is-locked" value="{{ $fieldValue('kontak') }}" disabled>
                        </div>

                        <div class="general-field">
                            <label class="general-label">
                                <span class="general-label-icon {{ $fieldIconClass('website') }}" aria-hidden="true">{!! $fieldIcon('website') !!}</span>
                                <span>Website</span>
                            </label>
                            @if($websiteHref !== '')
                                <a
                                    href="{{ $websiteHref }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="general-link-field"
                                >
                                    {{ $websiteText }}
                                </a>
                            @else
                                <div class="general-link-field is-empty">-</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="general-card-footer">
                    @if($canEdit)
                        <button type="submit" class="general-save-btn">Simpan Informasi Umum</button>
                    @else
                        <div class="general-readonly-note">
                            Anda tidak memiliki izin untuk mengubah data pada halaman ini.
                        </div>
                    @endif
                </div>
            </section>
        </form>
    </div>
@endsection

