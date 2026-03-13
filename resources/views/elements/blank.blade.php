@extends('layouts.dashboard-shell')
@php
    $pageTitle = $title ?? 'Element';
@endphp

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-uppercase text-muted small mb-2">Halaman dikosongkan</p>
            <h5 class="card-title mb-1">{{ $title }}</h5>
            <p class="text-muted mb-0">Konten untuk sub-topik ini telah dikosongkan sesuai permintaan.</p>
        </div>
    </div>
@endsection
