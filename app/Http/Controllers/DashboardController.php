<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Element1KegiatanAsurans;
use App\Models\ElementAssessment;
use App\Models\ElementTeamAssignment;
use App\Models\Notification;
use App\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private array $elementWeights = [
        'element1' => 0.40,
        'element2' => 0.20,
        'element3' => 0.20,
        'element4' => 0.10,
        'element5' => 0.10,
    ];

    private array $elementTitles = [
        'element1' => 'Element 1 : Kualitas Peran dan Layanan',
        'element2' => 'Element 2 : Profesionalisme Penugasan',
        'element3' => 'Element 3 : Manajemen Pengawasan',
        'element4' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
        'element5' => 'Element 5 : Budaya dan Hubungan Organisasi',
    ];

    public function index()
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $sessionUser = Session::get('user');
        $elements = $this->buildElementSummaries();
        $accessibleElementSlugs = ElementTeamAssignment::assignedElementSlugsForUser((array) $sessionUser);

        $overallWeightedScore = (float) number_format((float) collect($elements)
            ->sum(fn (array $item) => (float) ($item['weighted_score'] ?? 0)), 2, '.', '');
        $hasAnyElementData = collect($elements)
            ->contains(fn (array $item) => (bool) ($item['has_data'] ?? false));

        $overallLevelData = $hasAnyElementData
            ? $this->getLevelData($overallWeightedScore)
            : [
                'level' => null,
                'predikat' => 'Belum Dinilai',
                'description' => 'Data penilaian belum tersedia pada sub topik.',
            ];

        $meterPercent = $hasAnyElementData
            ? $this->meterPercentFromScore($overallWeightedScore)
            : 0;
        $meterNeedleDeg = (float) number_format(-90 + (180 * ($meterPercent / 100)), 2, '.', '');

        $notifications = Notification::orderByDesc('created_at')->limit(50)->get();
        $account = Account::where('username', $sessionUser['username'] ?? '')->first();
        $accounts = [];
        if (in_array(strtolower($sessionUser['role'] ?? ''), ['administrator','admin','superadmin'])) {
            $accounts = Account::orderBy('username')->get();
        }

        $photoUrl = $this->buildPhotoUrl($sessionUser['profile_photo'] ?? '');

        return view('dashboard', [
            'pageTitle' => 'Dashboard Kapabilitas APIP',
            'elements' => $elements,
            'elementWeights' => $this->elementWeights,
            'accessibleElementSlugs' => $accessibleElementSlugs,
            'overallWeightedScore' => $overallWeightedScore,
            'overallLevelData' => $overallLevelData,
            'meterPercent' => (float) number_format($meterPercent, 2, '.', ''),
            'meterNeedleDeg' => $meterNeedleDeg,
            'notifications' => $notifications,
            'user' => Session::get('user'),
            'account' => $account,
            'accounts' => $accounts,
            'photoUrl' => $photoUrl,
        ]);
    }

    private function buildPhotoUrl(?string $path): string
    {
        if (!$path) {
            return '';
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }
        return asset('uploads/'.$path);
    }

    private function buildElementSummaries(): array
    {
        $summaryModules = (array) config('element_summary_modules.modules', []);
        $subtopicModules = (array) config('element_subtopic_modules.modules', []);

        $elements = [];
        foreach ($this->elementWeights as $elementSlug => $elementWeight) {
            $summaryConfig = $summaryModules[$elementSlug] ?? [];
            if (!is_array($summaryConfig)) {
                $summaryConfig = [];
            }

            $subtopics = $this->buildSubtopicSummaries($elementSlug, $summaryConfig, $subtopicModules);
            [$elementScore, $subtopics, $hasData] = $this->calculateElementScore($subtopics, $summaryConfig);

            $weightedScore = $hasData
                ? (float) number_format($elementScore * $elementWeight, 2, '.', '')
                : 0.0;

            $levelData = $hasData
                ? $this->getLevelData($elementScore)
                : [
                    'level' => null,
                    'predikat' => 'Belum Dinilai',
                    'description' => 'Data penilaian untuk element ini belum tersedia.',
                ];
            $resolvedElementLevel = is_numeric($levelData['level'] ?? null)
                ? (int) $levelData['level']
                : null;
            $elementLevelDescription = $hasData
                ? $this->resolveElementLevelDescription(
                    $summaryConfig,
                    $resolvedElementLevel,
                    (string) ($levelData['description'] ?? '')
                )
                : 'Data penilaian untuk element ini belum tersedia.';

            $elements[] = [
                'slug' => $elementSlug,
                'title' => (string) ($summaryConfig['title'] ?? ($this->elementTitles[$elementSlug] ?? Str::headline($elementSlug))),
                'weight' => (float) $elementWeight,
                'score' => $hasData ? $elementScore : null,
                'weighted_score' => $weightedScore,
                'level' => $levelData['level'],
                'predikat' => (string) $levelData['predikat'],
                'description' => $elementLevelDescription,
                'has_data' => $hasData,
                'subtopics' => $subtopics,
                'subtopic_count' => count($subtopics),
                'assessed_subtopic_count' => (int) collect($subtopics)->filter(fn (array $item) => (bool) ($item['has_data'] ?? false))->count(),
            ];
        }

        return $elements;
    }

    private function buildSubtopicSummaries(string $elementSlug, array $summaryConfig, array $subtopicModules): array
    {
        $preferredSlugs = collect((array) ($summaryConfig['subtopic_slugs'] ?? []))
            ->map(fn ($slug) => trim((string) $slug))
            ->filter(fn ($slug) => $slug !== '')
            ->values();

        $moduleSlugs = collect($subtopicModules)
            ->keys()
            ->filter(fn ($slug) => is_string($slug) && Str::startsWith($slug, $elementSlug.'_'))
            ->values();

        $assessmentBySlug = $this->latestAssessmentBySubtopic($elementSlug);

        $allSlugs = $preferredSlugs
            ->concat($moduleSlugs)
            ->concat($assessmentBySlug->keys())
            ->unique()
            ->values();

        return $allSlugs->map(function ($subtopicSlug) use ($subtopicModules, $assessmentBySlug) {
            $slug = (string) $subtopicSlug;
            $moduleConfig = $subtopicModules[$slug] ?? null;
            $moduleSummary = is_array($moduleConfig)
                ? $this->subtopicSummaryFromModule($slug, $moduleConfig)
                : null;
            $assessment = $assessmentBySlug->get($slug);

            if ($moduleSummary !== null && $moduleSummary['has_data']) {
                return $moduleSummary;
            }

            if ($assessment instanceof ElementAssessment) {
                return $this->subtopicSummaryFromAssessment($assessment);
            }

            if ($moduleSummary !== null) {
                return $moduleSummary;
            }

            return [
                'slug' => $slug,
                'title' => Str::headline(str_replace('_', ' ', $slug)),
                'score' => null,
                'level' => null,
                'predikat' => 'Belum Dinilai',
                'description' => 'Belum ada data penilaian.',
                'level_note' => 'Belum ada data penilaian.',
                'has_data' => false,
                'is_verified' => false,
                'rows_total' => 0,
                'rows_verified' => 0,
                'source' => 'none',
            ];
        })->values()->all();
    }

    private function latestAssessmentBySubtopic(string $elementSlug): Collection
    {
        return ElementAssessment::query()
            ->where('subtopic_slug', 'like', $elementSlug.'_%')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('subtopic_slug')
            ->map(function (Collection $items) {
                return $items->first();
            });
    }

    private function subtopicSummaryFromModule(string $slug, array $moduleConfig): ?array
    {
        $modelClass = (string) ($moduleConfig['model'] ?? Element1KegiatanAsurans::class);
        if ($modelClass === '' || !class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
            return null;
        }

        /** @var Model $model */
        $model = new $modelClass();
        $table = $model->getTable();
        if (!Schema::hasTable($table)) {
            return null;
        }

        $weights = (array) ($moduleConfig['weights'] ?? []);
        $rows = $modelClass::query()
            ->orderBy('id')
            ->get(['id', 'level', 'skor', 'verified']);

        $score = 0.0;
        $hasData = false;
        foreach ($rows as $row) {
            if (is_numeric($row->skor)) {
                $score += (float) $row->skor;
                $hasData = true;
                continue;
            }

            if (is_numeric($row->level)) {
                $score += $this->scoreForKegiatanLevel((float) $row->level, (int) $row->id, $weights);
                $hasData = true;
            }
        }

        $score = $hasData
            ? (float) number_format($score, 2, '.', '')
            : null;

        $levelData = $hasData
            ? $this->getLevelData((float) $score)
            : [
                'level' => null,
                'predikat' => 'Belum Dinilai',
                'description' => 'Belum ada data penilaian pada sub topik ini.',
            ];
        $resolvedLevel = is_numeric($levelData['level'] ?? null)
            ? (int) $levelData['level']
            : null;
        $levelDescription = $hasData
            ? $this->resolveSubtopicLevelDescription(
                $slug,
                $resolvedLevel,
                $score,
                (string) ($levelData['description'] ?? '')
            )
            : 'Belum ada data penilaian pada sub topik ini.';

        $rowsTotal = $rows->count();
        $rowsVerified = (int) $rows
            ->filter(fn ($row) => (int) ($row->verified ?? 0) === 1)
            ->count();

        return [
            'slug' => $slug,
            'title' => (string) ($moduleConfig['subtopic_title'] ?? Str::headline(str_replace('_', ' ', $slug))),
            'score' => $score,
            'level' => $levelData['level'],
            'predikat' => (string) $levelData['predikat'],
            'description' => $levelDescription,
            'level_note' => $this->compactLevelDescription($levelDescription),
            'has_data' => $hasData,
            'is_verified' => $rowsTotal > 0 && $rowsVerified === $rowsTotal,
            'rows_total' => $rowsTotal,
            'rows_verified' => $rowsVerified,
            'source' => 'module',
        ];
    }

    private function subtopicSummaryFromAssessment(ElementAssessment $assessment): array
    {
        $score = is_numeric($assessment->weighted_total)
            ? (float) number_format((float) $assessment->weighted_total, 2, '.', '')
            : null;
        $level = is_numeric($assessment->level)
            ? (int) $assessment->level
            : null;
        $predikat = trim((string) ($assessment->predikat ?? ''));

        if ($score !== null && ($level === null || $predikat === '')) {
            $levelData = $this->getLevelData($score);
            $level = $level ?? $levelData['level'];
            $predikat = $predikat !== '' ? $predikat : (string) $levelData['predikat'];
        }

        $fallbackLevelDescription = $score !== null
            ? (string) ($this->getLevelData($score)['description'] ?? '')
            : '';
        $levelDescription = $this->resolveSubtopicLevelDescription(
            (string) $assessment->subtopic_slug,
            $level,
            $score,
            $fallbackLevelDescription
        );

        return [
            'slug' => (string) $assessment->subtopic_slug,
            'title' => (string) ($assessment->subtopic_title ?: Str::headline(str_replace('_', ' ', (string) $assessment->subtopic_slug))),
            'score' => $score,
            'level' => $level,
            'predikat' => $predikat !== '' ? $predikat : 'Belum Dinilai',
            'description' => $levelDescription,
            'level_note' => $this->compactLevelDescription($levelDescription),
            'has_data' => $score !== null,
            'is_verified' => !empty($assessment->verified_by) || !empty($assessment->verified_at),
            'rows_total' => 0,
            'rows_verified' => 0,
            'source' => 'assessment',
        ];
    }

    private function calculateElementScore(array $subtopics, array $summaryConfig): array
    {
        if (count($subtopics) === 0) {
            return [0.0, [], false];
        }

        $configuredWeightsRaw = collect((array) ($summaryConfig['subtopic_weights'] ?? []))
            ->mapWithKeys(function ($weight, $slug) {
                return [(string) $slug => (float) $weight];
            });

        $configuredWeightTotal = (float) $configuredWeightsRaw->sum();
        $weightDivider = $configuredWeightTotal > 1.5 ? 100 : 1;
        $configuredWeights = $configuredWeightsRaw
            ->map(fn ($weight) => (float) $weight / $weightDivider);

        $defaultWeight = 0.0;
        if (count($subtopics) > 0) {
            if ($configuredWeights->isEmpty()) {
                $defaultWeight = 1 / count($subtopics);
            } else {
                $missingCount = (int) collect($subtopics)
                    ->filter(fn (array $item) => !$configuredWeights->has((string) ($item['slug'] ?? '')))
                    ->count();
                $configuredTotal = (float) $configuredWeights->sum();
                if ($missingCount > 0 && $configuredTotal < 1) {
                    $defaultWeight = (1 - $configuredTotal) / $missingCount;
                }
            }
        }

        $hasData = false;
        $rawScore = 0.0;
        $enrichedSubtopics = [];

        foreach ($subtopics as $item) {
            $slug = (string) ($item['slug'] ?? '');
            $weight = (float) ($configuredWeights->get($slug, $defaultWeight));
            $hasItemData = is_numeric($item['score']);
            $score = $hasItemData ? (float) $item['score'] : 0.0;
            $weightedScore = (float) number_format($score * $weight, 2, '.', '');

            if ($hasItemData) {
                $hasData = true;
            }

            $rawScore += ($score * $weight);

            $item['weight'] = $weight;
            $item['weighted_score'] = $weightedScore;
            $enrichedSubtopics[] = $item;
        }

        $elementScore = $hasData
            ? (float) number_format($rawScore, 2, '.', '')
            : 0.0;

        return [$elementScore, $enrichedSubtopics, $hasData];
    }

    private function scoreForKegiatanLevel(float $level, int $rowId, ?array $weights = null): float
    {
        $activeWeights = is_array($weights) ? $weights : [];
        $bobot = (float) ($activeWeights[$rowId] ?? 0.25);
        return round($level * $bobot, 2);
    }

    private function meterPercentFromScore(float $score): float
    {
        $normalizedScore = max(0, min(5, $score));

        if ($normalizedScore >= 5) {
            return 100.0;
        }

        if ($normalizedScore < 2) {
            return (float) (($normalizedScore / 1.99) * 20);
        }

        if ($normalizedScore < 3) {
            return (float) (20 + ((($normalizedScore - 2) / 0.99) * 20));
        }

        if ($normalizedScore < 4) {
            return (float) (40 + ((($normalizedScore - 3) / 0.99) * 20));
        }

        return (float) (60 + ((($normalizedScore - 4) / 0.99) * 20));
    }

    private function resolveSubtopicLevelDescription(
        string $subtopicSlug,
        ?int $level,
        ?float $score = null,
        string $fallback = ''
    ): string {
        $resolvedLevel = $level;
        if ($resolvedLevel === null && is_numeric($score)) {
            $resolvedLevel = (int) ($this->getLevelData((float) $score)['level'] ?? 0);
        }

        $infoLevels = (array) config('element_subtopic_modules.modules.'.$subtopicSlug.'.info_levels', []);
        foreach ($infoLevels as $infoLevel) {
            $infoLevelNumber = is_numeric($infoLevel['level'] ?? null)
                ? (int) $infoLevel['level']
                : null;

            if ($resolvedLevel !== null && $infoLevelNumber === $resolvedLevel) {
                $description = trim((string) ($infoLevel['description'] ?? ''));
                if ($description !== '') {
                    return $description;
                }
            }
        }

        $fallbackDescription = trim($fallback);
        if ($fallbackDescription !== '') {
            return $fallbackDescription;
        }

        return 'Belum ada deskripsi level sub topik.';
    }

    private function resolveElementLevelDescription(array $summaryConfig, ?int $level, string $fallback = ''): string
    {
        $infoLevels = (array) ($summaryConfig['info_levels'] ?? []);
        foreach ($infoLevels as $infoLevel) {
            $infoLevelNumber = is_numeric($infoLevel['level'] ?? null)
                ? (int) $infoLevel['level']
                : null;

            if ($level !== null && $infoLevelNumber === $level) {
                $description = trim((string) ($infoLevel['description'] ?? ''));
                if ($description !== '') {
                    return $description;
                }
            }
        }

        $fallbackDescription = trim($fallback);
        if ($fallbackDescription !== '') {
            return $fallbackDescription;
        }

        return 'Belum ada deskripsi level element.';
    }

    private function compactLevelDescription(string $description): string
    {
        $normalized = trim((string) preg_replace('/\s+/u', ' ', $description));
        if ($normalized === '') {
            return 'Belum ada deskripsi level sub topik.';
        }
        return $normalized;
    }

    private function getLevelData(float $score): array
    {
        $baseLevel = match (true) {
            $score >= 5 => ['level' => 5, 'predikat' => 'Optimal'],
            $score >= 3.99 => ['level' => 4, 'predikat' => 'Terintegrasi'],
            $score >= 2.99 => ['level' => 3, 'predikat' => 'Memadai'],
            $score >= 1.99 => ['level' => 2, 'predikat' => 'Terstruktur'],
            default => ['level' => 1, 'predikat' => 'Rintisan'],
        };

        $descriptions = [
            1 => 'Inisiatif pengawasan masih pada tahap awal dan belum berjalan konsisten.',
            2 => 'Praktik pengawasan sudah terstruktur, tetapi implementasi belum merata.',
            3 => 'Praktik pengawasan memadai dan konsisten dengan ruang perbaikan pada integrasi.',
            4 => 'Pengawasan terintegrasi lintas proses dengan dukungan data untuk keputusan strategis.',
            5 => 'Kapabilitas optimal, adaptif, dan proaktif dalam menghasilkan nilai tambah organisasi.',
        ];

        $baseLevel['description'] = (string) ($descriptions[$baseLevel['level']] ?? '');
        return $baseLevel;
    }
}
