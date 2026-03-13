@extends('layouts.dashboard-shell')
@php
    $pageTitle = $title ?? 'Element';
@endphp

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-uppercase text-muted small mb-1">Halaman Elemen</p>
            <h5 class="card-title mb-2">{{ $title }}</h5>
            <p class="text-muted mb-3">Konten detail sub-topik ini belum dipindahkan. Anda bisa gunakan tombol di bawah untuk membuka dokumen referensi atau lakukan input/penilaian langsung (TODO).</p>
            <div class="d-flex gap-2">
                <a href="{{ route('dms.index') }}" class="btn btn-outline-primary btn-sm">Buka DMS</a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
@endsection
