<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ElementProgressArchive;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AoiController extends Controller
{
    public function index()
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $items = $this->collectAoiItems();

        return view('aoi.index', [
            'pageTitle' => 'Area Of Improvement (AoI)',
            'items' => $items,
            'totalItems' => $items->count(),
            'activeBudgetYear' => $this->resolveActiveBudgetYear(),
            'user' => Session::get('user'),
            'notifications' => Notification::feedForUser((array) Session::get('user', []), null, 50),
        ]);
    }

    private function resolveActiveBudgetYear(): int
    {
        $fallbackYear = (int) now('Asia/Jakarta')->year;
        if (!Schema::hasTable('element_progress_archives')) {
            return $fallbackYear;
        }

        $loadedYear = ElementProgressArchive::query()
            ->whereNotNull('last_loaded_at')
            ->orderByDesc('last_loaded_at')
            ->orderByDesc('id')
            ->value('budget_year');

        return is_numeric($loadedYear) ? (int) $loadedYear : $fallbackYear;
    }

    private function collectAoiItems(): Collection
    {
        $modules = (array) config('element_subtopic_modules.modules', []);
        $items = collect();

        foreach ($modules as $slug => $module) {
            if (!is_array($module)) {
                continue;
            }

            $modelClass = trim((string) ($module['model'] ?? ''));
            if ($modelClass === '' || !class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
                continue;
            }

            $table = (new $modelClass())->getTable();
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'qa_verified')) {
                continue;
            }

            $hasQaNoteColumn = Schema::hasColumn($table, 'qa_verify_note');
            $hasRecommendationColumn = Schema::hasColumn($table, 'qa_follow_up_recommendation');
            if (!$hasQaNoteColumn && !$hasRecommendationColumn) {
                continue;
            }

            $columns = ['id', 'pernyataan', 'qa_verified'];
            if ($hasQaNoteColumn) {
                $columns[] = 'qa_verify_note';
            }
            if ($hasRecommendationColumn) {
                $columns[] = 'qa_follow_up_recommendation';
            }
            if (Schema::hasColumn($table, 'qa_verified_by')) {
                $columns[] = 'qa_verified_by';
            }
            if (Schema::hasColumn($table, 'qa_verified_at')) {
                $columns[] = 'qa_verified_at';
            }

            $rows = $modelClass::query()
                ->select(array_values(array_unique($columns)))
                ->where('qa_verified', 1)
                ->orderBy('id')
                ->get();

            $elementTitle = trim((string) ($module['page_title'] ?? Str::headline((string) $slug)));
            $subtopicTitle = trim((string) ($module['subtopic_title'] ?? Str::headline((string) $slug)));

            foreach ($rows as $row) {
                $hasilVerifikasiQa = trim((string) ($row->qa_verify_note ?? ''));
                $rekomendasiTindakLanjut = trim((string) ($row->qa_follow_up_recommendation ?? ''));
                if ($hasilVerifikasiQa === '' && $rekomendasiTindakLanjut === '') {
                    continue;
                }

                $verifiedAt = $this->parseDateTime($row->qa_verified_at ?? null);
                $items->push([
                    'slug' => (string) $slug,
                    'element_title' => $elementTitle,
                    'subtopic_title' => $subtopicTitle,
                    'row_id' => (int) ($row->id ?? 0),
                    'pernyataan' => trim((string) ($row->pernyataan ?? '')),
                    'hasil_verifikasi_qa' => $hasilVerifikasiQa,
                    'rekomendasi_tindak_lanjut' => $rekomendasiTindakLanjut,
                    'qa_verified_by' => trim((string) ($row->qa_verified_by ?? '')),
                    'qa_verified_at' => $verifiedAt?->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s').' WIB',
                    'qa_verified_at_print' => $this->formatPrintDate($verifiedAt),
                    'qa_verified_at_ts' => $verifiedAt?->getTimestamp() ?? 0,
                ]);
            }
        }

        return $items
            ->sortByDesc('qa_verified_at_ts')
            ->values();
    }

    private function parseDateTime(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function formatPrintDate(?Carbon $value): string
    {
        if ($value === null) {
            return '';
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $date = $value->copy()->timezone('Asia/Jakarta');
        $month = $months[(int) $date->format('n')] ?? $date->format('F');

        return $date->format('d').' '.$month.' '.$date->format('Y');
    }
}
