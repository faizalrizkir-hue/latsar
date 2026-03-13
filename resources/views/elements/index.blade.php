@extends('layouts.dashboard-shell')
@php
    $pageTitle = 'Daftar Elemen';
@endphp

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Daftar Elemen & Subtopik</h5>
            <div class="list-group">
                @foreach($pages as $slug => $title)
                    <a href="{{ route('elements.show', $slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span>{{ $title }}</span>
                        <span class="text-muted small">{{ $slug }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
