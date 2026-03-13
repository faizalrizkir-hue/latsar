@extends('layouts.dashboard-shell')

@section('title', $pageTitle ?? 'Tambah Dokumen')

@push('head')
    <link rel="stylesheet" href="/css/dms.css">
@endpush

@section('content')
    <div class="card shadow-sm dms-card-lift">
        <div class="card-body">
            <h5 class="card-title mb-3">Unggah Berkas DMS</h5>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @php
                $map = [
                    'Manajemen Pengawasan' => [
                        'Surat Tugas',
                        'Laporan Hasil Pengawasan (LHP)',
                        'Program Kerja Pengawasan Tahunan (PKPT)',
                        'Tanda Bukti',
                        'Telaah Sejawat',
                    ],
                    'Sumber Daya Manusia' => ['Dokumen SDM'],
                    'Keuangan' => ['Dokumen Keuangan'],
                    'Pemanfaatan Sistem Informasi (SI)' => ['Dokumen Sistem Informasi (SI)'],
                    'Pedoman/Kebijakan' => ['Dokumen Pedoman/Kebijakan'],
                    'Lainnya' => ['Dokumen Lainnya'],
                ];
                $mainType = old('type') ?? 'Manajemen Pengawasan';
                $subType = old('tag') ?? ($map[$mainType][0] ?? '');
            @endphp
            <form method="POST" action="{{ route('dms.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="status" value="Aktif">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No Dokumen</label>
                        <input type="text" name="doc_no" class="form-control" required value="{{ old('doc_no') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Judul</label>
                        <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tahun</label>
                        <input type="number" name="year" class="form-control" required min="2000" max="{{ date('Y')+1 }}" value="{{ old('year', date('Y')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jenis Dokumen</label>
                        <select name="type" id="createJenisDokumen" class="form-select dms-select" required>
                            @foreach(array_keys($map) as $key)
                                <option value="{{ $key }}" @selected($mainType===$key)>{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sub Jenis</label>
                        <select name="tag" id="createSubJenisDokumen" class="form-select dms-select" required>
                            @foreach($map[$mainType] as $opt)
                                <option value="{{ $opt }}" @selected($subType===$opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Deskripsi Dokumen</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Ringkasan isi dokumen">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Uploader</label>
                        <input type="text" name="uploader" class="form-control" value="{{ old('uploader', $user['display_name'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Lampiran (boleh lebih dari satu, max 5MB/berkas)</label>
                        <input type="file" name="files[]" class="form-control" required multiple>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="{{ route('dms.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
