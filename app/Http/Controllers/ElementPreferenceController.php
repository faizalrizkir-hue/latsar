<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\ElementPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Throwable;

class ElementPreferenceController extends Controller
{
    private const ARCHIVE_CAPTCHA_SESSION_KEY = 'archive_restore_captcha';

    private const RESET_DATA_CAPTCHA_SESSION_KEY = 'element_reset_data_captcha';

    public function __construct(private readonly ElementPreferenceService $elementPreferenceService)
    {
    }

    public function index(): View|RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $structure = $this->elementPreferenceService->structure();

        return view('element-preferences.index', [
            'pageTitle' => 'Preferensi Elemen',
            'structure' => $structure,
            'hasPreferencesTable' => $this->elementPreferenceService->hasPreferencesTable(),
            'hasProgressArchiveTable' => $this->elementPreferenceService->hasProgressArchiveTable(),
            'progressArchives' => $this->elementPreferenceService->progressArchives(),
            'user' => Session::get('user', []),
            'notifications' => Notification::feedForUser((array) Session::get('user', []), null, 50),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        if (!$this->elementPreferenceService->hasPreferencesTable()) {
            return back()->withErrors('Tabel preferensi elemen belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $inputElements = $request->input('elements', []);
        if (!is_array($inputElements)) {
            return back()->withErrors('Format data preferensi tidak valid.');
        }

        $normalizedStructure = $this->elementPreferenceService->buildStructureFromInput($inputElements);
        $username = trim((string) (Session::get('user.username') ?? Session::get('user')['username'] ?? ''));

        $this->elementPreferenceService->saveStructure(
            $normalizedStructure,
            $username !== '' ? $username : null
        );

        return back()->with('status', 'Preferensi elemen berhasil diperbarui.');
    }

    public function reset(): RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        if (!$this->elementPreferenceService->hasPreferencesTable()) {
            return back()->withErrors('Tabel preferensi elemen belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $username = trim((string) (Session::get('user.username') ?? Session::get('user')['username'] ?? ''));

        $this->elementPreferenceService->resetToDefaults($username !== '' ? $username : null);

        return back()->with('status', 'Preferensi elemen telah dikembalikan ke konfigurasi default.');
    }

    public function resetData(Request $request): RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $validated = $request->validate([
            'reset_captcha_answer' => ['required', 'string', 'regex:/^[A-Za-z0-9]{4}$/'],
        ], [
            'reset_captcha_answer.required' => 'Captcha reset data wajib diisi.',
            'reset_captcha_answer.regex' => 'Captcha reset data harus berisi 4 karakter huruf dan angka.',
        ]);

        $expectedCaptcha = strtoupper((string) Session::pull(self::RESET_DATA_CAPTCHA_SESSION_KEY, ''));
        $submittedCaptcha = strtoupper(trim((string) $validated['reset_captcha_answer']));
        if ($expectedCaptcha === '' || !hash_equals($expectedCaptcha, $submittedCaptcha)) {
            return back()->withErrors([
                'reset_captcha_answer' => 'Captcha reset data tidak sesuai. Silakan coba lagi.',
            ]);
        }

        $result = $this->elementPreferenceService->resetElementDataAndHistory();
        $deletedTotal = (int) ($result['deleted_total'] ?? 0);

        return back()->with(
            'status',
            'Seluruh isian elemen beserta riwayatnya berhasil dihapus. Total data terhapus: '.$deletedTotal.'.'
        );
    }

    public function resetDataRedirect(): RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        return redirect()
            ->route('element-preferences.index')
            ->withErrors('Reset data harus dijalankan melalui tombol konfirmasi agar token keamanan valid.');
    }

    public function archiveProgress(Request $request): RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        if (!$this->elementPreferenceService->hasProgressArchiveTable()) {
            return back()->withErrors('Tabel arsip progress belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $validated = $request->validate([
            'budget_year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $username = trim((string) (Session::get('user.username') ?? Session::get('user')['username'] ?? ''));

        try {
            $result = $this->elementPreferenceService->archiveProgressByBudgetYear(
                (int) $validated['budget_year'],
                $username !== '' ? $username : null
            );
        } catch (Throwable $exception) {
            return back()->withErrors('Gagal mengarsipkan progress: '.$exception->getMessage());
        }

        $budgetYear = (int) ($result['budget_year'] ?? (int) $validated['budget_year']);
        $totalRows = (int) ($result['total_rows'] ?? 0);
        $wasReplaced = (bool) ($result['replaced'] ?? false);

        return back()->with(
            'status',
            ($wasReplaced
                ? 'Arsip progress Tahun Anggaran '.$budgetYear.' berhasil diperbarui.'
                : 'Arsip progress Tahun Anggaran '.$budgetYear.' berhasil dibuat.')
            .' Total data tersimpan: '.$totalRows.' baris.'
        );
    }

    public function loadArchive(Request $request): RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        if (!$this->elementPreferenceService->hasProgressArchiveTable()) {
            return back()->withErrors('Tabel arsip progress belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $validated = $request->validate([
            'archive_id' => ['required', 'integer', 'min:1'],
            'archive_captcha_answer' => ['required', 'string', 'regex:/^[A-Za-z0-9]{4}$/'],
        ], [
            'archive_captcha_answer.required' => 'Captcha pemulihan arsip wajib diisi.',
            'archive_captcha_answer.regex' => 'Captcha pemulihan arsip harus berisi 4 karakter huruf dan angka.',
        ]);

        $expectedCaptcha = strtoupper((string) Session::pull(self::ARCHIVE_CAPTCHA_SESSION_KEY, ''));
        $submittedCaptcha = strtoupper(trim((string) $validated['archive_captcha_answer']));
        if ($expectedCaptcha === '' || !hash_equals($expectedCaptcha, $submittedCaptcha)) {
            return back()->withErrors([
                'archive_captcha_answer' => 'Captcha pemulihan arsip tidak sesuai. Silakan coba lagi.',
            ]);
        }

        $username = trim((string) (Session::get('user.username') ?? Session::get('user')['username'] ?? ''));

        try {
            $result = $this->elementPreferenceService->loadProgressArchive(
                (int) $validated['archive_id'],
                $username !== '' ? $username : null
            );
        } catch (Throwable $exception) {
            return back()->withErrors('Gagal memuat arsip progress: '.$exception->getMessage());
        }

        $budgetYear = (int) ($result['budget_year'] ?? 0);
        $restoredTotal = (int) ($result['restored_total'] ?? 0);

        return back()->with(
            'status',
            'Arsip Tahun Anggaran '.$budgetYear.' berhasil dipulihkan. Total data dipulihkan: '
            .$restoredTotal
            .' baris. Catatan: notifikasi aktivitas tidak diubah saat pemulihan arsip.'
        );
    }

    public function archiveCaptcha(): JsonResponse|RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $captcha = $this->generateActionCaptcha();
        Session::put(self::ARCHIVE_CAPTCHA_SESSION_KEY, $captcha);

        return response()->json([
            'captcha' => $captcha,
        ]);
    }

    public function resetDataCaptcha(): JsonResponse|RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $captcha = $this->generateActionCaptcha();
        Session::put(self::RESET_DATA_CAPTCHA_SESSION_KEY, $captcha);

        return response()->json([
            'captcha' => $captcha,
        ]);
    }

    private function generateActionCaptcha(): string
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $numbers = '23456789';
        $pool = $letters.$numbers;
        $chars = [
            $letters[random_int(0, strlen($letters) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
        ];

        while (count($chars) < 4) {
            $chars[] = $pool[random_int(0, strlen($pool) - 1)];
        }

        shuffle($chars);

        return implode('', $chars);
    }
}
