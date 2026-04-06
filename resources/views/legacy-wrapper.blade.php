@extends('layouts.dashboard-shell')
@php
    $pageTitle = $pageTitle ?? 'Halaman';
@endphp

@push('head')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/legacy.css') }}">
    @if(!empty($legacyStyles))
        <style>{!! $legacyStyles !!}</style>
    @endif
@endpush

@section('content')
    {!! $legacyBody !!}
@endsection

