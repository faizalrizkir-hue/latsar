<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DmsDocument;
use App\Models\DmsFile;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DmsController extends Controller
{
    private const FILTER_CATALOG_CACHE_KEY = 'dms:filters:v1';
    private const COUNTS_CACHE_KEY = 'dms:counts:v1';

    public function index(Request $request)
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $filters = $this->buildFilters($request);
        $counts = $this->cachedCounts();
        $catalog = $this->cachedFilterCatalog();
        $typeOptions = $this->dmsTypeOptions();

        return view('dms.index', [
            'filters' => $filters,
            'counts' => $counts,
            'types' => $catalog['types'],
            'tags' => $catalog['tags'],
            'years' => $catalog['years'],
            'typeOptions' => $typeOptions,
            'notifications' => Notification::feedForUser((array) Session::get('user', []), null, 50),
            'pageTitle' => 'Data Management System',
            'user' => Session::get('user'),
        ]);
    }

    public function create()
    {
        $typeOptions = $this->dmsTypeOptions();

        return view('dms.upload', [
            'notifications' => Notification::feedForUser((array) Session::get('user', []), null, 50),
            'pageTitle' => 'Tambah Dokumen',
            'user' => Session::get('user'),
            'typeOptions' => $typeOptions,
            'existingDocNos' => DmsDocument::withTrashed()->pluck('doc_no'),
        ]);
    }

    public function store(Request $request)
    {
        $maxUploadKilobytes = $this->maxUploadKilobytes();
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'type' => 'required|string|max:150',
            'tag' => 'required|string|max:150',
            'doc_no' => 'required|array|min:1',
            'doc_no.*' => 'required|string|max:100|distinct|unique:dms_documents,doc_no',
            'name' => 'required|array|min:1',
            'name.*' => 'required|string|max:200',
            'files' => 'required|array|min:1',
            'files.*' => ['required', 'file', 'max:'.$maxUploadKilobytes, $this->secureUploadRule()],
        ]);

        $uploader = Session::get('user')['display_name'] ?? (Session::get('user')['username'] ?? null);
        $updatedBy = $uploader;

        $docNos = $data['doc_no'];
        $names = $data['name']; // nama berkas per tab
        $files = $request->file('files', []);

        // Buat satu dokumen utama, simpan semua berkas sebagai detail
        $mainDocNo = $docNos[0];
        $document = DmsDocument::create([
            'doc_no' => $mainDocNo,
            'title' => $data['title'],
            'year' => $data['year'],
            'type' => $data['type'],
            'tag' => $data['tag'],
            'status' => 'Aktif',
            'uploader' => $uploader,
            'updated_by' => $updatedBy,
        ]);

        foreach ($docNos as $idx => $docNo) {
            $file = $files[$idx] ?? null;
            if (!$file) {
                continue;
            }
            $path = $file->store('dms', 'public');
            $document->files()->create([
                'doc_no' => $docNo,
                'doc_name' => $names[$idx] ?? '',
                'file_name' => $this->normalizedOriginalFileName($file),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'size_bytes' => $file->getSize(),
                'storage_driver' => 'public',
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now(),
            ]);
        }
        $this->forgetListingCaches();

        return redirect()->route('dms.index')->with('status', 'Dokumen berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $document = DmsDocument::with(['files'])->withTrashed()->findOrFail($id);
        $typeOptions = $this->dmsTypeOptions();
        $filesForEdit = $document->files->map(function ($f) {
            $path = $f->file_path;
            if (Str::startsWith($path, ['http://', 'https://', '/'])) {
                $url = $path;
            } elseif (Str::startsWith($path, 'uploads/')) {
                $url = asset($path);
            } else {
                $url = Storage::disk($f->storage_driver ?? 'public')->url($path);
            }
            return [
                'id' => $f->id,
                'doc_no' => $f->doc_no,
                'doc_name' => $f->doc_name,
                'file_name' => $f->file_name,
                'size' => $f->file_size ?? $f->size_bytes,
                'url' => $url,
            ];
        });

        return view('dms.edit', [
            'document' => $document,
            'notifications' => Notification::feedForUser((array) Session::get('user', []), null, 50),
            'pageTitle' => 'Edit Dokumen',
            'user' => Session::get('user'),
            'typeOptions' => $typeOptions,
            'existingDocNos' => DmsDocument::withTrashed()->where('id', '!=', $id)->pluck('doc_no'),
            'existingFiles' => $filesForEdit,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $document = DmsDocument::withTrashed()->findOrFail($id);
        $maxUploadKilobytes = $this->maxUploadKilobytes();

        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'type' => 'required|string|max:150',
            'title' => 'required|string|max:200',
            'tag' => 'required|string|max:150',
            'files' => 'nullable|array',
            'files.*' => ['file', 'max:'.$maxUploadKilobytes, $this->secureUploadRule()],
            'new_doc_no' => 'nullable|array',
            'new_doc_no.*' => 'nullable|string|max:100',
            'new_doc_name' => 'nullable|array',
            'new_doc_name.*' => 'nullable|string|max:200',
            'existing_doc_no' => 'nullable|array',
            'existing_doc_no.*' => 'nullable|string|max:100',
            'existing_doc_name' => 'nullable|array',
            'existing_doc_name.*' => 'nullable|string|max:200',
            'existing_file' => 'nullable|array',
            'existing_file.*' => ['nullable', 'file', 'max:'.$maxUploadKilobytes, $this->secureUploadRule()],
            'existing_delete' => 'nullable|array',
            'existing_delete.*' => 'in:1',
        ]);

        $data['updated_by'] = Session::get('user')['display_name'] ?? Session::get('user')['username'] ?? null;
        unset($data['doc_no']); // doc_no tidak diedit lewat form ini
        $document->update($data);

        $this->updateExistingFiles(
            $document,
            $request->input('existing_doc_no', []),
            $request->input('existing_doc_name', []),
            $request->input('existing_delete', []),
            $request->file('existing_file', [])
        );

        if ($request->hasFile('files')) {
            $this->storeFiles(
                $document,
                $request->file('files', []),
                $request->input('new_doc_no', []),
                $request->input('new_doc_name', [])
            );
        }
        $this->forgetListingCaches();

        return redirect()->route('dms.index')->with('status', 'Dokumen diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $document = DmsDocument::findOrFail($id);
        $document->delete();
        $this->forgetListingCaches();

        return back()->with('status', 'Dokumen dipindahkan ke trash.');
    }

    public function archive(Request $request, int $id)
    {
        $document = DmsDocument::withTrashed()->findOrFail($id);
        if ($document->trashed()) {
            return back()->with('error', 'Dokumen di trash, pulihkan terlebih dahulu.');
        }
        $document->update(['status' => 'Arsip']);
        $this->forgetListingCaches();

        // sinkronkan status pada file terkait
        // tidak mengubah nomor/nama berkas pada arsip

        return back()->with('status', 'Dokumen berhasil diarsipkan.');
    }

    public function unarchive(Request $request, int $id)
    {
        $document = DmsDocument::withTrashed()->findOrFail($id);
        if ($document->trashed()) {
            return back()->with('error', 'Dokumen di trash, pulihkan terlebih dahulu.');
        }
        $document->update(['status' => 'Aktif']);
        $this->forgetListingCaches();

        // tidak mengubah nomor/nama berkas saat unarchive

        return back()->with('status', 'Dokumen dikembalikan ke aktif.');
    }

    public function restore(Request $request, int $id)
    {
        $document = DmsDocument::withTrashed()->findOrFail($id);
        $document->restore();
        $this->forgetListingCaches();

        return back()->with('status', 'Dokumen dipulihkan.');
    }

    private function storeFiles(DmsDocument $document, array $files, array $docNos = [], array $docNames = []): void
    {
        foreach ($files as $idx => $file) {
            if (!$file) {
                continue;
            }
            $path = $file->store('dms', 'public');
            $document->files()->create([
                'doc_no' => $docNos[$idx] ?? $document->doc_no,
                'doc_name' => $docNames[$idx] ?? $this->normalizedOriginalFileName($file),
                'file_name' => $this->normalizedOriginalFileName($file),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'size_bytes' => $file->getSize(),
                'storage_driver' => 'public',
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now(),
            ]);
        }
    }

    private function updateExistingFiles(
        DmsDocument $document,
        array $docNos,
        array $docNames,
        array $deleteFlags,
        array $replaceFiles
    ): void {
        $deleteIds = array_map('strval', array_keys($deleteFlags));

        foreach ($document->files as $file) {
            $id = (string)$file->id;

            if (in_array($id, $deleteIds, true)) {
                if ($file->file_path && Storage::disk($file->storage_driver ?? 'public')->exists($file->file_path)) {
                    Storage::disk($file->storage_driver ?? 'public')->delete($file->file_path);
                }
                $file->delete();
                continue;
            }

            $newDocNo = array_key_exists($id, $docNos) ? $docNos[$id] : $file->doc_no;
            $newDocName = array_key_exists($id, $docNames) ? $docNames[$id] : $file->doc_name;
            $file->doc_no = $newDocNo ?: $file->doc_no;
            // jika user mengosongkan nama berkas, simpan null; jika isi baru, simpan isinya
            $file->doc_name = ($newDocName !== null) ? (trim($newDocName) === '' ? null : $newDocName) : $file->doc_name;

            if (isset($replaceFiles[$id]) && $replaceFiles[$id]) {
                $uploaded = $replaceFiles[$id];
                $path = $uploaded->store('dms', 'public');
                if ($file->file_path && Storage::disk($file->storage_driver ?? 'public')->exists($file->file_path)) {
                    Storage::disk($file->storage_driver ?? 'public')->delete($file->file_path);
                }
                $file->file_name = $this->normalizedOriginalFileName($uploaded);
                $file->file_path = $path;
                $file->file_size = $uploaded->getSize();
                $file->size_bytes = $uploaded->getSize();
                $file->storage_driver = 'public';
                $file->mime_type = $uploaded->getMimeType();
                $file->uploaded_at = now();
            }

            $file->save();
        }
    }

    private function dmsTypeOptions(): array
    {
        $configured = (array) config('dms.type_options', []);
        if ($configured !== []) {
            return $configured;
        }

        return [
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
    }

    private function maxUploadKilobytes(): int
    {
        return max(256, (int) config('dms.upload.max_kilobytes', 5120));
    }

    private function secureUploadRule(): \Closure
    {
        $blockedExtensions = collect((array) config('dms.upload.blocked_extensions', []))
            ->map(fn ($ext) => strtolower(trim((string) $ext)))
            ->filter(fn ($ext) => $ext !== '')
            ->values()
            ->all();
        $blockedMimePrefixes = collect((array) config('dms.upload.blocked_mime_prefixes', []))
            ->map(fn ($mime) => strtolower(trim((string) $mime)))
            ->filter(fn ($mime) => $mime !== '')
            ->values()
            ->all();

        return function (string $attribute, mixed $value, \Closure $fail) use ($blockedExtensions, $blockedMimePrefixes): void {
            if (!$value instanceof UploadedFile) {
                return;
            }

            $extension = strtolower(trim((string) $value->getClientOriginalExtension()));
            if ($extension !== '' && in_array($extension, $blockedExtensions, true)) {
                $fail('Format berkas tidak diizinkan untuk alasan keamanan.');

                return;
            }

            $mimeType = strtolower(trim((string) $value->getMimeType()));
            if ($mimeType === '') {
                return;
            }

            foreach ($blockedMimePrefixes as $blockedPrefix) {
                if (Str::startsWith($mimeType, $blockedPrefix)) {
                    $fail('Jenis berkas terdeteksi tidak aman.');

                    return;
                }
            }
        };
    }

    private function normalizedOriginalFileName(UploadedFile $file): string
    {
        $fileName = trim((string) $file->getClientOriginalName());
        $fileName = str_replace('\\', '/', $fileName);
        $fileName = basename($fileName);
        $fileName = (string) preg_replace('/[\x00-\x1F\x7F]/u', '', $fileName);
        $fileName = (string) preg_replace('/\s+/u', ' ', $fileName);
        $fileName = trim($fileName);
        if ($fileName === '') {
            $fileName = 'berkas';
        }

        return Str::limit($fileName, 255, '');
    }

    private function buildFilters(Request $request): array
    {
        $filters = [
            'q' => trim((string)$request->input('q', '')),
            'view' => trim((string)$request->input('view', '')),
            'status' => array_values(array_filter((array)$request->input('status', []))),
            'type' => array_values(array_filter((array)$request->input('type', []))),
            'tag' => array_values(array_filter((array)$request->input('tag', []))),
            'year_from' => trim((string)$request->input('year_from', '')),
            'year_to' => trim((string)$request->input('year_to', '')),
            'sort' => trim((string)$request->input('sort', 'updated')),
            'dir' => strtolower((string)$request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc',
            'doc_no' => trim((string)$request->input('doc_no', '')),
        ];
        $filters['query'] = $request->query();
        return $filters;
    }

    /**
     * @return array{types:\Illuminate\Support\Collection,tags:\Illuminate\Support\Collection,years:\Illuminate\Support\Collection}
     */
    private function cachedFilterCatalog(): array
    {
        return Cache::remember(
            self::FILTER_CATALOG_CACHE_KEY,
            now()->addMinutes(5),
            static fn (): array => [
                'types' => DmsDocument::query()->select('type')->distinct()->orderBy('type')->pluck('type'),
                'tags' => DmsDocument::query()->select('tag')->distinct()->orderBy('tag')->pluck('tag'),
                'years' => DmsDocument::query()->select('year')->distinct()->orderByDesc('year')->pluck('year'),
            ]
        );
    }

    /**
     * @return array{active:int,archive:int,trash:int}
     */
    private function cachedCounts(): array
    {
        return Cache::remember(
            self::COUNTS_CACHE_KEY,
            now()->addMinute(),
            static fn (): array => [
                'active' => DmsDocument::where('status', 'Aktif')->count(),
                'archive' => DmsDocument::where('status', 'Arsip')->count(),
                'trash' => DmsDocument::onlyTrashed()->count(),
            ]
        );
    }

    private function forgetListingCaches(): void
    {
        Cache::forget(self::FILTER_CATALOG_CACHE_KEY);
        Cache::forget(self::COUNTS_CACHE_KEY);
    }
}
