@extends('layouts.dashboard-shell')
@php
    $pageTitle = $title ?? 'Element';
@endphp

@section('content')
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ $title }}</h5>
                    @if ($errors->any())
                        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
                    @endif
                    @if (session('status'))
                        <div class="alert alert-success py-2">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('elements.store', $slug) }}">
                        @csrf
                        <div class="list-group mb-3">
                            @foreach($questions as $id => $label)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">{{ $label }}</div>
                                            <small class="text-muted">Bobot {{ number_format(($weights[$id] ?? 0)*100, 2) }}%</small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <select name="scores[{{ $id }}]" class="form-select" required>
                                            @for($i=1; $i<=5; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2" maxlength="500"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="verify" value="1" id="verifyCheck">
                            <label class="form-check-label" for="verifyCheck">Tandai terverifikasi</label>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a class="btn btn-outline-secondary" href="{{ route('elements.index') }}">Batal</a>
                            <button class="btn btn-primary">Simpan Penilaian</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-2">Riwayat Terbaru</h6>
                    <div class="list-group list-group-flush">
                        @forelse($assessments as $item)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="fw-semibold">Total {{ $item->weighted_total }} (Level {{ $item->level }} - {{ $item->predikat }})</div>
                                        <div class="text-muted small">
                                            oleh {{ $item->submitted_by ?? '—' }} • {{ $item->created_at?->diffForHumans() }}
                                        </div>
                                        @if($item->notes)
                                            <div class="small mt-1">{{ $item->notes }}</div>
                                        @endif
                                    </div>
                                    @if($item->verified_by)
                                        <span class="badge text-bg-success">Verified</span>
                                    @endif
                                </div>
                                <div class="mt-2 small text-muted">
                                    @foreach($item->scores as $qid => $score)
                                        <span class="me-2">Q{{ $qid }}: {{ $score }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">Belum ada penilaian.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
