<?php

namespace App\Http\Controllers;

use App\Models\GeneralInformationProfile;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
            'notifications' => Notification::orderByDesc('created_at')->limit(50)->get(),
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
}
