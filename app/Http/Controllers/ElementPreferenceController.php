<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\ElementPreferenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

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
            'user' => Session::get('user', []),
            'notifications' => Notification::orderByDesc('created_at')->limit(50)->get(),
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
}
