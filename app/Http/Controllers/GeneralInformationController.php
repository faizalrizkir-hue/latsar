<?php

namespace App\Http\Controllers;

use App\Models\GeneralInformationProfile;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class GeneralInformationController extends Controller
{
    private const EDITOR_ROLE = 'administrator';

    public function index()
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $sessionUser = (array) Session::get('user', []);
        $canEdit = $this->canManage($sessionUser);
        $profile = $this->resolveProfile();

        return view('informasi-umum.index', [
            'pageTitle' => 'Informasi Umum',
            'user' => $sessionUser,
            'canEdit' => $canEdit,
            'profile' => $profile,
            'legalRegulations' => $this->resolveLegalRegulations(),
            'notifications' => Notification::feedForUser($sessionUser, null, 50),
        ]);
    }

    public function update(Request $request)
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $sessionUser = (array) Session::get('user', []);
        if (!$this->canManage($sessionUser)) {
            abort(403, 'Akses khusus administrator.');
        }

        $validated = $request->validate([
            'kepala_pemerintah_daerah' => 'required|string|max:255',
            'inspektur' => 'required|string|max:255',
        ]);

        $profile = $this->resolveProfile();
        $profile->fill($validated);
        $profile->updated_by = trim((string) ($sessionUser['username'] ?? '')) ?: null;
        $profile->save();

        return back()->with('status', 'Informasi umum berhasil diperbarui.');
    }

    private function canManage(array $sessionUser): bool
    {
        $role = strtolower(trim((string) ($sessionUser['role'] ?? '')));

        return $role === self::EDITOR_ROLE;
    }

    private function resolveProfile(): GeneralInformationProfile
    {
        $profile = GeneralInformationProfile::query()->first();
        $defaults = $this->defaultProfileData();

        if (!$profile) {
            return GeneralInformationProfile::query()->create($defaults);
        }

        $missingColumns = [];
        foreach ($defaults as $column => $defaultValue) {
            if (trim((string) ($profile->{$column} ?? '')) === '') {
                $missingColumns[$column] = $defaultValue;
            }
        }

        if ($missingColumns !== []) {
            $profile->fill($missingColumns);
            $profile->save();
            $profile->refresh();
        }

        return $profile;
    }

    private function defaultProfileData(): array
    {
        return [
            'dasar_hukum_penilaian' => "Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintah Daerah",
            'pemerintah_daerah' => 'Pemerintah Provinsi DKI Jakarta',
            'nama_skpd' => 'Inspektorat Provinsi DKI Jakarta',
            'bidang' => 'Pengawasan Internal Pemerintah Daerah',
            'kepala_pemerintah_daerah' => 'Dr. Ir. Pramono Anung Wibowo, M.M.',
            'undang_undang_pendirian' => 'Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintah Daerah',
            'visi' => 'Menjadi Lembaga Pengawas Internal Terdepan Di Lingkungan Pemerintah Daerah',
            'misi' => "- Meningkatkan Sumber Daya Manusia Yang Unggul dan Terpercaya\n"
                . "- Mengembangkan Sistem Pengawasan Untuk Menjamin Mutu Tata Kelola Pemerintahan Yang Baik\n"
                . "- Penguatan Instrumen Pengawasan Terkait Tugas dan Fungsi Inspektorat\n"
                . "- Mewujudkan Lingkungan Kerja Yang Solid dan Kondusif\n"
                . "- Meningkatkan Pembinaan Terhadap Instansi dan Koordinasi Dengan Stakeholder",
            'inspektur' => 'Dhany Sukma, S.Sos., M.A.P.',
            'alamat_kantor' => "Grha Ali Sadikin Blok G Lt. 17-18\nJl. Medan Merdeka Selatan No. 8-9 Jakarta Pusat\n10110",
            'jumlah_kantor_wilayah' => '5 Inspektorat Pembantu Wilayah Kota Administratif dan 1 Inspektorat Pembantu Wilayah Kabupaten Administratif',
            'kontak' => '(021) 3822263 - 3813523',
            'website' => 'https://inspektorat.jakarta.go.id',
        ];
    }

    private function resolveLegalRegulations(): array
    {
        $directory = public_path('uploads/pedoman');
        if (!File::isDirectory($directory)) {
            return [];
        }

        $pdfFiles = collect(File::files($directory))
            ->filter(fn ($file) => strtolower((string) $file->getExtension()) === 'pdf')
            ->values();

        if ($pdfFiles->isEmpty()) {
            return [];
        }

        $knownRules = [
            [
                'match' => 'uu nomor 23 tahun 2014',
                'order' => 10,
                'category' => 'Undang-Undang',
                'title' => 'UU Nomor 23 Tahun 2014 tentang Pemerintah Daerah',
            ],
            [
                'match' => 'pp 60 tahun 2008',
                'order' => 20,
                'category' => 'Peraturan Pemerintah',
                'title' => 'PP Nomor 60 Tahun 2008 tentang Sistem Pengendalian Intern Pemerintah',
            ],
            [
                'match' => 'peraturan bpkp nomor 6 tahun 2025',
                'order' => 30,
                'category' => 'Peraturan Lembaga',
                'title' => 'Peraturan BPKP Nomor 6 Tahun 2025 tentang Peningkatan Kapabilitas APIP',
            ],
            [
                'match' => 'penguatan apip daerah',
                'order' => 40,
                'category' => 'Surat Edaran Bersama',
                'title' => 'Surat Edaran Bersama tentang Penguatan APIP Daerah',
            ],
            [
                'match' => 'surat edaran kemendagri tentang penilaian kinerja inspektur daerah',
                'order' => 50,
                'category' => 'Surat Edaran',
                'title' => 'Surat Edaran Kemendagri tentang Penilaian Kinerja Inspektur Daerah',
            ],
            [
                'match' => 'rencana strategis inspektorat provinsi dki jakarta 2025 - 2029',
                'order' => 60,
                'category' => 'Dokumen Perencanaan',
                'title' => 'Rencana Strategis Inspektorat Provinsi DKI Jakarta Tahun 2025 - 2029',
            ],
            [
                'match' => 'keputusan inspektur nomor 43 tahun 2025',
                'order' => 70,
                'category' => 'Keputusan Internal',
                'title' => 'Keputusan Inspektur Nomor 43 Tahun 2025 tentang Tim Asesor Penilaian Mandiri Kapabilitas APIP',
            ],
        ];

        return $pdfFiles
            ->map(function ($file) use ($knownRules) {
                $fileName = trim((string) $file->getFilename());
                $fileNameLower = Str::lower($fileName);
                $matchedRule = collect($knownRules)
                    ->first(fn (array $rule) => Str::contains($fileNameLower, $rule['match']));

                $fallbackTitle = trim((string) Str::of(pathinfo($fileName, PATHINFO_FILENAME))
                    ->replace(['_', '-'], ' ')
                    ->replaceMatches('/\s+/', ' ')
                    ->title());

                return [
                    'title' => $matchedRule['title'] ?? $fallbackTitle,
                    'category' => $matchedRule['category'] ?? 'Dokumen Lain',
                    'file_name' => $fileName,
                    'file_url' => asset('uploads/pedoman/'.rawurlencode($fileName)),
                    'order' => (int) ($matchedRule['order'] ?? 999),
                    'sort_name' => Str::lower($fallbackTitle),
                ];
            })
            ->sort(function (array $left, array $right): int {
                $orderCompare = ($left['order'] ?? 999) <=> ($right['order'] ?? 999);
                if ($orderCompare !== 0) {
                    return $orderCompare;
                }

                return strcmp((string) ($left['sort_name'] ?? ''), (string) ($right['sort_name'] ?? ''));
            })
            ->values()
            ->map(function (array $item): array {
                unset($item['order'], $item['sort_name']);

                return $item;
            })
            ->all();
    }
}
