@php
    use Illuminate\Support\Str;
    $viewKey = $isTrash ? 'trash' : ($isArchive ? 'archive' : 'active');
@endphp
<div class="dms-fade-shell"
     id="dmsTableWrap"
     data-dms-table
     wire:key="dms-table-{{ $viewKey }}"
     wire:transition.fade.duration.200ms
     wire:loading.class="dms-fade-out"
     wire:loading.remove.class="dms-fade-in"
     wire:target="setView,sortBy,page,perPage">
    <div class="dms-list-header">
        <div>
            <h6>Daftar Berkas {{ $isTrash ? 'Terhapus' : ($isArchive ? 'Arsip' : 'Aktif') }}</h6>
            <p>Menampilkan {{ $documents->firstItem() }} sampai {{ $documents->lastItem() }} dari {{ $documents->total() }} dokumen.</p>
        </div>
        <div class="dms-list-actions">
            @if ($isArchive)
                <button type="button" class="dms-btn dms-btn-return" wire:click="setView('')">
                    <span class="icon">↩</span> Daftar Berkas Aktif
                </button>
                <button type="button" class="dms-btn dms-btn-danger {{ $isTrash ? 'active' : '' }}" wire:click="setView('trash')" aria-pressed="{{ $isTrash ? 'true' : 'false' }}">
                    <span class="icon">&#128465;</span> Berkas Dihapus
                </button>
            @elseif ($isTrash)
                <button type="button" class="dms-btn dms-btn-return" wire:click="setView('')">
                    <span class="icon">↩</span> Daftar Berkas Aktif
                </button>
                <button type="button" class="dms-btn dms-btn-ghost {{ $isArchive ? 'active' : '' }}" wire:click="setView('archive')" aria-pressed="{{ $isArchive ? 'true' : 'false' }}">
                    <span class="icon">☰</span> Arsip
                </button>
            @else
                <button type="button" class="dms-btn dms-btn-ghost {{ $isArchive ? 'active' : '' }}" wire:click="setView('archive')" aria-pressed="{{ $isArchive ? 'true' : 'false' }}">
                    <span class="icon">☰</span> Arsip
                </button>
                <button type="button" class="dms-btn dms-btn-danger {{ $isTrash ? 'active' : '' }}" wire:click="setView('trash')" aria-pressed="{{ $isTrash ? 'true' : 'false' }}">
                    <span class="icon">&#128465;</span> Berkas Dihapus
                </button>
            @endif
        </div>
    </div>

    <div class="card shadow-sm dms-card-lift">
        <div class="table-responsive">
            <div class="dms-loading-line" wire:loading wire:target="sortBy"></div>
            <table class="table table-hover align-middle mb-0 dms-table">
                <thead>
                    <tr>
                        <th class="text-center sortable">
                            <button type="button" wire:click="sortBy('no')" wire:loading.attr="disabled" wire:target="sortBy">No <span class="sort-icon">{!! $sort === 'no' ? ($dir === 'asc' ? '&#9650;' : '&#9660;') : '&#8645;' !!}</span></button>
                        </th>
                        <th class="sortable">
                            <button type="button" wire:click="sortBy('title')" wire:loading.attr="disabled" wire:target="sortBy">Judul <span class="sort-icon">{!! $sort === 'title' ? ($dir === 'asc' ? '&#9650;' : '&#9660;') : '&#8645;' !!}</span></button>
                        </th>
                        <th class="sortable">
                            <button type="button" wire:click="sortBy('no')" wire:loading.attr="disabled" wire:target="sortBy">Daftar Berkas <span class="sort-icon">{!! $sort === 'no' ? ($dir === 'asc' ? '&#9650;' : '&#9660;') : '&#8645;' !!}</span></button>
                        </th>
                        <th class="jenis-col sortable">
                            <button type="button" wire:click="sortBy('type')" wire:loading.attr="disabled" wire:target="sortBy">Jenis / Sub Jenis <span class="sort-icon">{!! $sort === 'type' ? ($dir === 'asc' ? '&#9650;' : '&#9660;') : '&#8645;' !!}</span></button>
                        </th>
                        @if($isTrash)
                            <th class="tahun-col">
                                <span>Sisa Waktu</span>
                            </th>
                        @endif
                        <th class="tahun-col sortable">
                            <button type="button" wire:click="sortBy('year')" wire:loading.attr="disabled" wire:target="sortBy">Tahun <span class="sort-icon">{!! $sort === 'year' ? ($dir === 'asc' ? '&#9650;' : '&#9660;') : '&#8645;' !!}</span></button>
                        </th>
                        <th class="aksi-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr class="dms-hover-row"
                            data-search="{{ Str::lower($doc->doc_no.' '.$doc->title.' '.$doc->type.' '.$doc->tag.' '.($doc->uploader ?? '').' '.($doc->description ?? '')) }}"
                            data-doc-no="{{ Str::lower($doc->doc_no) }}"
                            data-type="{{ Str::lower($doc->type) }}"
                            data-tag="{{ Str::lower($doc->tag ?? '') }}">
                            <td class="text-center">{{ $loop->iteration + $documents->firstItem() - 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $doc->title }}</div>
                                <div class="doc-meta">
                                    <div>Diunggah oleh <span class="muted">{{ $doc->uploader ?? '—' }}</span></div>
                                    <div class="muted">Tanggal unggah: {{ $doc->created_at?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '—' }}</div>
                                    <div>Terakhir edit <span class="muted">{{ $doc->updated_at?->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '—' }}</span></div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @forelse($doc->files as $file)
                                        @php
                                            $path = $file->file_path ?? '';
                                            $path = str_replace('\\', '/', $path);
                                            $path = trim($path, '/');
                                            $path = preg_replace('/^(latsar-laravel\\/public\\/|latsar\\/)/', '', $path);
                                            if (\Illuminate\Support\Str::contains($path, 'uploads/dms')) {
                                                $rel = \Illuminate\Support\Str::after($path, 'uploads/dms/');
                                                $url = asset('uploads/dms/'.ltrim($rel, '/'));
                                            } elseif (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
                                                $url = $path;
                                            } elseif (\Illuminate\Support\Str::startsWith($path, ['uploads/dms'])) {
                                                $url = asset($path);
                                            } elseif (\Illuminate\Support\Str::startsWith($path, 'dms/')) {
                                                $url = asset('uploads/'.$path);
                                            } elseif ($path !== '') {
                                                $url = asset('uploads/dms/'.$path);
                                            } else {
                                                $url = null;
                                            }
                                        @endphp
                                        <div class="tag-box">
                                            @if($url)
                                                <a href="{{ $url }}" class="fw-semibold doc-link" target="_blank" rel="noopener">{{ $file->doc_no ?? '—' }}</a>
                                            @else
                                                <div class="fw-semibold">{{ $file->doc_no ?? '—' }}</div>
                                            @endif
                                            <div class="text-muted small">{{ $file->doc_name ?: ($file->file_name ?? 'Nama berkas belum diisi') }}</div>
                                        </div>
                                    @empty
                                        <span class="text-muted">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="jenis-col">
                                <div class="pill-type mb-1">{{ $doc->type ?? '-' }}</div>
                                @if(!empty($doc->tag))
                                    <span class="pill-subtype">{{ $doc->tag }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            @if($isTrash)
                                <td class="tahun-col">
                                    @if(isset($doc->remaining_days))
                                        <div class="fw-bold" title="Akan dihapus otomatis pada {{ $doc->deletion_deadline?->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}">
                                            {{ floor($doc->remaining_days) }} hari
                                        </div>
                                        <div class="text-muted small">Hapus: {{ $doc->deletion_deadline?->timezone('Asia/Jakarta')->format('d-m-Y') }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endif
                            <td class="tahun-col">{{ $doc->year }}</td>
                            <td class="aksi-col text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @php
                                        $currentView = $isTrash ? 'trash' : ($isArchive ? 'archive' : '');
                                        $currentPage = $documents->currentPage();
                                        $docLabel = Str::limit(trim((string) ($doc->title ?: $doc->doc_no ?: 'dokumen ini')), 96);
                                    @endphp
                                    @if($isTrash)
                                        <form method="POST" action="{{ route('dms.restore', $doc->id) }}" onsubmit="return confirm('Pulihkan dokumen?')">
                                            @csrf
                                            <button class="action-btn action-restore" type="submit" title="Pulihkan">&#x21BA;</button>
                                        </form>
                                    @elseif($doc->status === 'Arsip')
                                        <form method="POST" action="{{ route('dms.unarchive', $doc->id) }}" onsubmit="return confirm('Kembalikan ke daftar aktif?')">
                                            @csrf
                                            <button class="action-btn action-restore" type="submit" title="Kembalikan ke Aktif">↩</button>
                                        </form>
                                        <form method="POST" action="{{ route('dms.destroy', $doc->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="action-btn action-delete"
                                                type="submit"
                                                title="Hapus"
                                                data-dms-confirm-trigger
                                                data-dms-confirm-kind="delete"
                                                data-dms-confirm-title="Hapus Dokumen?"
                                                data-dms-confirm-message="Dokumen &quot;{{ $docLabel }}&quot; akan dipindahkan ke berkas dihapus."
                                                data-dms-confirm-label="Ya, Hapus"
                                            >&#128465;</button>
                                        </form>
                                    @else
       									<form method="POST" action="{{ route('dms.archive', $doc->id) }}">
                                            @csrf
                                            <button
                                                class="action-btn action-archive"
                                                type="submit"
                                                title="Arsipkan"
                                                data-dms-confirm-trigger
                                                data-dms-confirm-kind="archive"
                                                data-dms-confirm-title="Arsipkan Dokumen?"
                                                data-dms-confirm-message="Dokumen &quot;{{ $docLabel }}&quot; akan dipindahkan ke arsip."
                                                data-dms-confirm-label="Ya, Arsipkan"
                                            >&#128188;</button>
                                        </form>
                                        <a class="action-btn action-view" href="{{ route('dms.edit', $doc->id) }}" title="Edit">&#x270E;</a>
                                        <form method="POST" action="{{ route('dms.destroy', $doc->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="action-btn action-delete"
                                                type="submit"
                                                title="Hapus"
                                                data-dms-confirm-trigger
                                                data-dms-confirm-kind="delete"
                                                data-dms-confirm-title="Hapus Dokumen?"
                                                data-dms-confirm-message="Dokumen &quot;{{ $docLabel }}&quot; akan dipindahkan ke berkas dihapus."
                                                data-dms-confirm-label="Ya, Hapus"
                                            >&#128465;</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr data-empty-row>
                            <td colspan="{{ $isTrash ? 7 : 6 }}" class="text-center text-muted py-4">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body dms-table-footer d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Tampilkan</span>
                <select class="form-select form-select-sm" style="width:auto" wire:model.live="perPage" aria-label="Pilih jumlah baris per halaman">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                </select>
                <span class="text-muted small">per halaman</span>
            </div>
            <div class="ms-auto d-flex align-items-center gap-3 flex-wrap justify-content-end">
                {{ $documents->links() }}
            </div>
        </div>
    </div>
</div>
