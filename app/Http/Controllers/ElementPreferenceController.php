<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\ElementPreferenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Throwable;

class ElementPreferenceController extends Controller
{
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
            'pageTitle' => 'Preferensi Element',
            'structure' => $structure,
            'hasPreferencesTable' => $this->elementPreferenceService->hasPreferencesTable(),
            'hasProgressArchiveTable' => $this->elementPreferenceService->hasProgressArchiveTable(),
            'hasProgressArchiveLoadLogTable' => $this->elementPreferenceService->hasProgressArchiveLoadLogTable(),
            'progressArchives' => $this->elementPreferenceService->progressArchives(),
            'progressArchiveLoadLogs' => $this->elementPreferenceService->progressArchiveLoadLogs(12),
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
            return back()->withErrors('Tabel preferensi element belum tersedia. Jalankan migrasi terlebih dahulu.');
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

        return back()->with('status', 'Preferensi element berhasil diperbarui.');
    }

    public function reset(): RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        if (!$this->elementPreferenceService->hasPreferencesTable()) {
            return back()->withErrors('Tabel preferensi element belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $username = trim((string) (Session::get('user.username') ?? Session::get('user')['username'] ?? ''));

        $this->elementPreferenceService->resetToDefaults($username !== '' ? $username : null);

        return back()->with('status', 'Preferensi element telah dikembalikan ke konfigurasi default.');
    }

    public function resetData(): RedirectResponse
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $result = $this->elementPreferenceService->resetElementDataAndHistory();
        $deletedTotal = (int) ($result['deleted_total'] ?? 0);

        return back()->with(
            'status',
            'Seluruh isian Element beserta riwayatnya berhasil dihapus. Total data terhapus: '.$deletedTotal.'.'
        );
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
        ]);

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
}
