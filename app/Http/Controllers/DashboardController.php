<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Element1KegiatanAsurans;
use App\Models\ElementAssessment;
use App\Models\ElementProgressArchive;
use App\Models\ElementTeamAssignment;
use App\Models\Notification;
use App\Models\RenstraTrendOverride;
use App\Services\AssessmentSummaryCache;
use App\Services\DashboardShellDataBuilder;
use App\Services\ElementPreferenceService;
use App\Services\SchemaMetadataCache;
use App\Support\DashboardHomeViewNormalizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private array $schemaTableExists = [];

    private array $schemaColumnExists = [];

    /**
     * @var array<string, ElementAssessment>
     */
    private array $latestAssessmentBySubtopic = [];

    /**
     * @var array<string, bool>
     */
    private array $latestAssessmentLoadState = [];

    public function __construct(
        private readonly ElementPreferenceService $elementPreferenceService,
        private readonly AssessmentSummaryCache $assessmentSummaryCache,
        private readonly SchemaMetadataCache $schemaMetadataCache
    ) {
    }

    public function index()
    {
        if (!Session::has('user')) {
            return redirect()->route('login.form');
        }

        $sessionUser = (array) Session::get('user', []);
        $canManageRenstraTrend = $this->isAdminRole((string) ($sessionUser['role'] ?? ''));
        $activeBudgetYear = $this->resolveActiveBudgetYear();
        $summaryPayload = $this->assessmentSummaryCache->remember(
            'dashboard-index',
            $sessionUser,
            [],
            function () use ($sessionUser, $activeBudgetYear): array {
                $summaryModules = $this->elementPreferenceService->summaryModules(true);
                $subtopicModules = $this->elementPreferenceService->subtopicModules();
                $elementWeights = collect($summaryModules)
                    ->mapWithKeys(function (array $config, string $slug) {
                        return [(string) $slug => (float) ($config['element_weight'] ?? 0)];
                    })
                    ->all();

                $elements = $this->buildElementSummaries($summaryModules, $subtopicModules, $elementWeights);

                $accessibleElementSlugs = ElementTeamAssignment::assignedElementSlugsForUser($sessionUser);
                if ($accessibleElementSlugs !== null) {
                    $accessibleElementSlugs = array_values(array_intersect($accessibleElementSlugs, array_keys($elementWeights)));
                }

                $overallWeightedScore = (float) number_format((float) collect($elements)
                    ->sum(fn (array $item) => (float) ($item['weighted_score'] ?? 0)), 2, '.', '');
                $overallWeightedScoreQa = (float) number_format((float) collect($elements)
                    ->sum(fn (array $item) => (float) ($item['qa_weighted_score'] ?? 0)), 2, '.', '');
                $hasAnyElementData = collect($elements)
                    ->contains(fn (array $item) => (bool) ($item['has_data'] ?? false));
                $hasAnyQaElementData = collect($elements)
                    ->contains(fn (array $item) => (bool) ($item['has_qa_data'] ?? false));

                $overallLevelData = $hasAnyElementData
                    ? $this->getLevelData($overallWeightedScore)
                    : [
                        'level' => null,
                        'predikat' => 'Belum Dinilai',
                        'description' => 'Data penilaian belum tersedia pada topik.',
                    ];
                $overallLevelDataQa = $hasAnyQaElementData
                    ? $this->getLevelData($overallWeightedScoreQa)
                    : [
                        'level' => null,
                        'predikat' => 'Belum Dinilai',
                        'description' => 'Data verifikasi final QA belum tersedia pada topik.',
                    ];

                $meterPercent = $hasAnyElementData
                    ? $this->meterPercentFromScore($overallWeightedScore)
                    : 0;
                $meterNeedleDeg = (float) number_format(-90 + (180 * ($meterPercent / 100)), 2, '.', '');
                $meterPercentQa = $hasAnyQaElementData
                    ? $this->meterPercentFromScore($overallWeightedScoreQa)
                    : 0;
                $meterNeedleDegQa = (float) number_format(-90 + (180 * ($meterPercentQa / 100)), 2, '.', '');
                $renstraTrendSeries = $this->buildRenstraTrendSeries($summaryModules, $overallWeightedScore, $activeBudgetYear);

                return [
                    'elementWeights' => $elementWeights,
                    'elements' => $elements,
                    'accessibleElementSlugs' => $accessibleElementSlugs,
                    'overallWeightedScore' => $overallWeightedScore,
                    'overallWeightedScoreQa' => $overallWeightedScoreQa,
                    'overallLevelData' => $overallLevelData,
                    'overallLevelDataQa' => $overallLevelDataQa,
                    'meterPercent' => $meterPercent,
                    'meterNeedleDeg' => $meterNeedleDeg,
                    'meterPercentQa' => $meterPercentQa,
                    'meterNeedleDegQa' => $meterNeedleDegQa,
                    'renstraTrendSeries' => $renstraTrendSeries,
                ];
            }
        );

        $elementWeights = (array) ($summaryPayload['elementWeights'] ?? []);
        $elements = (array) ($summaryPayload['elements'] ?? []);
        $accessibleElementSlugs = is_array($summaryPayload['accessibleElementSlugs'] ?? null)
            ? $summaryPayload['accessibleElementSlugs']
            : null;
        $overallWeightedScore = (float) ($summaryPayload['overallWeightedScore'] ?? 0);
        $overallWeightedScoreQa = (float) ($summaryPayload['overallWeightedScoreQa'] ?? 0);
        $overallLevelData = (array) ($summaryPayload['overallLevelData'] ?? []);
        $overallLevelDataQa = (array) ($summaryPayload['overallLevelDataQa'] ?? []);
        $meterPercent = (float) ($summaryPayload['meterPercent'] ?? 0);
        $meterNeedleDeg = (float) ($summaryPayload['meterNeedleDeg'] ?? 0);
        $meterPercentQa = (float) ($summaryPayload['meterPercentQa'] ?? 0);
        $meterNeedleDegQa = (float) ($summaryPayload['meterNeedleDegQa'] ?? 0);
        $renstraTrendSeries = is_array($summaryPayload['renstraTrendSeries'] ?? null)
            ? $summaryPayload['renstraTrendSeries']
            : [];

        $elements = DashboardHomeViewNormalizer::enrichElements($elements, $accessibleElementSlugs);
        $dashboardUi = DashboardHomeViewNormalizer::buildUiMeta(
            $elementWeights,
            $overallLevelData,
            $overallLevelDataQa
        );

        $notifications = Notification::feedForUser((array) $sessionUser, null, 50);
        $photoUrl = $this->buildPhotoUrl($sessionUser['profile_photo'] ?? '');

        $viewData = [
            'pageTitle' => (string) ($dashboardUi['page_title'] ?? 'Dashboard Kapabilitas APIP'),
            'elements' => $elements,
            'elementWeights' => $elementWeights,
            'accessibleElementSlugs' => $accessibleElementSlugs,
            'overallWeightedScore' => $overallWeightedScore,
            'overallWeightedScoreQa' => $overallWeightedScoreQa,
            'overallLevelData' => $overallLevelData,
            'overallLevelDataQa' => $overallLevelDataQa,
            'meterPercent' => (float) number_format($meterPercent, 2, '.', ''),
            'meterNeedleDeg' => $meterNeedleDeg,
            'meterPercentQa' => (float) number_format($meterPercentQa, 2, '.', ''),
            'meterNeedleDegQa' => $meterNeedleDegQa,
            'renstraTrendSeries' => $renstraTrendSeries,
            'canManageRenstraTrend' => $canManageRenstraTrend,
            'activeBudgetYear' => $activeBudgetYear,
            'dashboardUi' => $dashboardUi,
            'notifications' => $notifications,
            'user' => Session::get('user'),
            'photoUrl' => $photoUrl,
        ];

        $shellData = app(DashboardShellDataBuilder::class)->build($viewData);

        return view('dashboard', array_merge($viewData, $shellData));
    }

    public function updateRenstraTrend(Request $request)
    {
        $forcedNoAssessmentYears = [2019, 2020];
        $overrideTable = (new RenstraTrendOverride())->getTable();
        if (!Schema::hasTable($overrideTable)) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Tabel pengaturan Renstra belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $entriesInput = collect((array) $request->input('entries', []))
            ->map(function ($item): array {
                $entry = is_array($item) ? $item : [];

                return [
                    'year' => $entry['year'] ?? null,
                    'hasil_score' => $this->normalizeNullableDecimalInput($entry['hasil_score'] ?? null),
                    'target_score' => $this->normalizeNullableDecimalInput($entry['target_score'] ?? null),
                ];
            })
            ->values()
            ->all();
        $request->merge(['entries' => $entriesInput]);

        $validated = $request->validate([
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.year' => ['required', 'integer', 'between:2018,2029'],
            'entries.*.hasil_score' => ['nullable', 'numeric', 'between:0,5'],
            'entries.*.target_score' => ['nullable', 'numeric', 'between:0,5'],
        ]);

        $entries = collect((array) ($validated['entries'] ?? []))
            ->map(function (array $entry) use ($forcedNoAssessmentYears): array {
                $year = (int) ($entry['year'] ?? 0);
                $hasilScore = $this->normalizeScoreRange($entry['hasil_score'] ?? null);
                if (in_array($year, $forcedNoAssessmentYears, true)) {
                    $hasilScore = 0.0;
                }

                return [
                    'year' => $year,
                    'hasil_score' => $hasilScore,
                    'target_score' => $this->normalizeScoreRange($entry['target_score'] ?? null),
                ];
            })
            ->filter(fn (array $entry): bool => $entry['year'] >= 2018 && $entry['year'] <= 2029)
            ->unique('year')
            ->values();

        $sessionUser = (array) Session::get('user', []);
        $updatedBy = trim((string) ($sessionUser['username'] ?? ''));
        foreach ($entries as $entry) {
            $year = (int) ($entry['year'] ?? 0);
            $hasilScore = $entry['hasil_score'] ?? null;
            $targetScore = $entry['target_score'] ?? null;

            if ($hasilScore === null && $targetScore === null) {
                RenstraTrendOverride::query()->where('year', $year)->delete();
                continue;
            }

            RenstraTrendOverride::query()->updateOrCreate(
                ['year' => $year],
                [
                    'hasil_score' => $hasilScore,
                    'target_score' => $targetScore,
                    'updated_by' => $updatedBy !== '' ? $updatedBy : null,
                ]
            );
        }

        $this->assessmentSummaryCache->bumpVersion();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Pengaturan grafik hasil dan target Renstra berhasil diperbarui.');
    }

    private function isAdminRole(string $role): bool
    {
        $normalizedRole = Str::lower(trim($role));
        return in_array($normalizedRole, ['administrator', 'admin', 'superadmin'], true);
    }

    private function normalizeNullableDecimalInput(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $normalized = trim(str_replace(',', '.', $value));
            return $normalized === '' ? null : $normalized;
        }

        return $value;
    }

    private function normalizeScoreRange(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        $normalized = max(0, min(5, (float) $value));

        return (float) number_format($normalized, 2, '.', '');
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

    private function resolveActiveBudgetYear(): int
    {
        $fallbackYear = (int) now('Asia/Jakarta')->year;
        if (!$this->elementPreferenceService->hasProgressArchiveTable()) {
            return $fallbackYear;
        }

        $loadedYear = ElementProgressArchive::query()
            ->whereNotNull('last_loaded_at')
            ->orderByDesc('last_loaded_at')
            ->orderByDesc('id')
            ->value('budget_year');

        return is_numeric($loadedYear) ? (int) $loadedYear : $fallbackYear;
    }

    private function buildElementSummaries(array $summaryModules, array $subtopicModules, array $elementWeights): array
    {
        $elements = [];
        foreach ($elementWeights as $elementSlug => $elementWeight) {
            $summaryConfig = $summaryModules[$elementSlug] ?? [];
            if (!is_array($summaryConfig)) {
                $summaryConfig = [];
            }

            $subtopics = $this->buildSubtopicSummaries($elementSlug, $summaryConfig, $subtopicModules);
            [$elementScore, $subtopics, $hasData] = $this->calculateElementScore(
                $subtopics,
                $summaryConfig,
                'score',
                'weighted_score'
            );
            [$elementScoreQa, $subtopics, $hasQaData] = $this->calculateElementScore(
                $subtopics,
                $summaryConfig,
                'qa_score',
                'qa_weighted_score'
            );

            $weightedScore = $hasData
                ? (float) number_format($elementScore * $elementWeight, 2, '.', '')
                : 0.0;
            $weightedScoreQa = $hasQaData
                ? (float) number_format($elementScoreQa * $elementWeight, 2, '.', '')
                : 0.0;

            $levelData = $hasData
                ? $this->getLevelData($elementScore)
                : [
                    'level' => null,
                    'predikat' => 'Belum Dinilai',
                    'description' => 'Data penilaian untuk elemen ini belum tersedia.',
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
                : 'Data penilaian untuk elemen ini belum tersedia.';
            $levelDataQa = $hasQaData
                ? $this->getLevelData($elementScoreQa)
                : [
                    'level' => null,
                    'predikat' => 'Belum Dinilai',
                    'description' => 'Data verifikasi final QA untuk elemen ini belum tersedia.',
                ];
            $resolvedQaElementLevel = is_numeric($levelDataQa['level'] ?? null)
                ? (int) $levelDataQa['level']
                : null;
            $elementQaLevelDescription = $hasQaData
                ? $this->resolveElementLevelDescription(
                    $summaryConfig,
                    $resolvedQaElementLevel,
                    (string) ($levelDataQa['description'] ?? '')
                )
                : 'Data verifikasi final QA untuk elemen ini belum tersedia.';

            $elements[] = [
                'slug' => $elementSlug,
                'title' => (string) ($summaryConfig['title'] ?? Str::headline($elementSlug)),
                'weight' => (float) $elementWeight,
                'score' => $hasData ? $elementScore : null,
                'qa_score' => $hasQaData ? $elementScoreQa : null,
                'weighted_score' => $weightedScore,
                'qa_weighted_score' => $weightedScoreQa,
                'level' => $levelData['level'],
                'qa_level' => $levelDataQa['level'],
                'predikat' => (string) $levelData['predikat'],
                'qa_predikat' => (string) $levelDataQa['predikat'],
                'description' => $elementLevelDescription,
                'qa_description' => $elementQaLevelDescription,
                'has_data' => $hasData,
                'has_qa_data' => $hasQaData,
                'subtopics' => $subtopics,
                'subtopic_count' => count($subtopics),
                'assessed_subtopic_count' => (int) collect($subtopics)->filter(fn (array $item) => (bool) ($item['has_data'] ?? false))->count(),
            ];
        }

        return $elements;
    }

    /**
     * @param array<string, array<string, mixed>> $summaryModules
     * @return array<int, array<string, mixed>>
     */
    private function buildRenstraTrendSeries(array $summaryModules, float $currentOverallWeightedScore, int $activeBudgetYear): array
    {
        $forcedNoAssessmentYears = [2019, 2020];
        $periodTargets = [
            [
                'key' => 'renstra_2018_2022',
                'label' => 'Renstra 2018 - 2022',
                'start' => 2018,
                'end' => 2022,
            ],
            [
                'key' => 'renstra_2023_2026',
                'label' => 'Renstra 2023 - 2026',
                'start' => 2023,
                'end' => 2024,
            ],
            [
                'key' => 'renstra_2025_2029',
                'label' => 'Renstra 2025 - 2029',
                'start' => 2025,
                'end' => 2029,
            ],
        ];
        $targetScoresByYear = [
            2018 => 3.00,
            2019 => 4.00,
            2020 => 4.00,
            2021 => 4.00,
            2022 => 5.00,
            2023 => 3.25,
            2024 => 3.50,
            2025 => 3.00,
            2026 => 3.00,
            2027 => 3.00,
            2028 => 3.00,
            2029 => 3.00,
        ];

        $archiveScoresByYear = $this->archiveOverallScoresByBudgetYear($summaryModules);
        $currentYear = $activeBudgetYear > 0 ? $activeBudgetYear : (int) now('Asia/Jakarta')->format('Y');
        $periodTargets = collect($periodTargets)
            ->sortByDesc(fn (array $item): int => (int) ($item['start'] ?? 0))
            ->values();

        $startYear = (int) collect($periodTargets)->min(fn (array $item): int => (int) ($item['start'] ?? 0));
        $endYear = (int) collect($periodTargets)->max(fn (array $item): int => (int) ($item['end'] ?? 0));

        if ($startYear <= 0 || $endYear < $startYear) {
            return [];
        }

        $years = range($startYear, $endYear);
        $overrideTable = (new RenstraTrendOverride())->getTable();
        $overridesByYear = collect();
        if (Schema::hasTable($overrideTable)) {
            $overridesByYear = RenstraTrendOverride::query()
                ->whereBetween('year', [$startYear, $endYear])
                ->get()
                ->keyBy(fn (RenstraTrendOverride $item): int => (int) $item->year);
        }

        return collect($years)->map(function (int $year) use ($archiveScoresByYear, $currentYear, $currentOverallWeightedScore, $periodTargets, $targetScoresByYear, $overridesByYear, $forcedNoAssessmentYears): array {
            /** @var RenstraTrendOverride|null $override */
            $override = $overridesByYear->get($year);
            $hasManualResult = is_numeric($override?->hasil_score);
            $manualResultScore = $hasManualResult
                ? (float) number_format(max(0, min(5, (float) $override->hasil_score)), 2, '.', '')
                : null;
            $hasManualTarget = is_numeric($override?->target_score);
            $manualTargetScore = $hasManualTarget
                ? (float) number_format(max(0, min(5, (float) $override->target_score)), 2, '.', '')
                : null;

            $resultScore = null;
            $sourceLabel = 'Belum ada data arsip';
            if ($hasManualResult) {
                $resultScore = $manualResultScore;
                $updatedByLabel = trim((string) ($override?->updated_by ?? ''));
                $sourceLabel = $updatedByLabel !== ''
                    ? 'Input manual admin ('.$updatedByLabel.')'
                    : 'Input manual admin';
            } elseif (array_key_exists($year, $archiveScoresByYear)) {
                $resultScore = (float) $archiveScoresByYear[$year];
                $sourceLabel = 'Arsip TA '.$year;
            } elseif ($year === $currentYear) {
                $resultScore = (float) $currentOverallWeightedScore;
                $sourceLabel = 'Data aktif TA '.$currentYear;
            }

            // Business rule: tahun 2019 dan 2020 ditetapkan sebagai tidak dilakukan penilaian (nilai hasil = 0).
            if (in_array($year, $forcedNoAssessmentYears, true)) {
                $resultScore = 0.0;
                $sourceLabel = 'Tidak dilakukan penilaian (nilai khusus 0)';
                $hasManualResult = false;
                $manualResultScore = null;
            }

            $targetPeriod = $periodTargets->first(function (array $item) use ($year): bool {
                $periodStart = (int) ($item['start'] ?? 0);
                $periodEnd = (int) ($item['end'] ?? 0);
                return $year >= $periodStart && $year <= $periodEnd;
            });
            $targetScore = $hasManualTarget
                ? $manualTargetScore
                : (array_key_exists($year, $targetScoresByYear) && is_numeric($targetScoresByYear[$year] ?? null)
                    ? (float) $targetScoresByYear[$year]
                    : null);

            $hasResultData = is_numeric($resultScore);
            $normalizedResultScore = $hasResultData
                ? (float) number_format(max(0, min(5, (float) $resultScore)), 2, '.', '')
                : null;
            $resultLevelData = $hasResultData
                ? $this->getLevelData((float) $normalizedResultScore)
                : ['level' => null, 'predikat' => 'Belum Dinilai', 'description' => 'Belum ada data penilaian pada tahun ini.'];
            $resultLevel = is_numeric($resultLevelData['level'] ?? null) ? (int) $resultLevelData['level'] : null;

            $hasTargetData = is_numeric($targetScore);
            $normalizedTargetScore = $hasTargetData
                ? (float) number_format(max(0, min(5, (float) $targetScore)), 2, '.', '')
                : null;

            return [
                'year' => $year,
                'year_label' => (string) $year,
                'renstra_label' => is_array($targetPeriod) ? (string) ($targetPeriod['label'] ?? '-') : '-',
                'hasil_has_data' => $hasResultData,
                'hasil_score' => $normalizedResultScore,
                'hasil_score_label' => $hasResultData ? number_format((float) $normalizedResultScore, 2) : '-',
                'hasil_percent' => $hasResultData ? (float) number_format(((float) $normalizedResultScore / 5) * 100, 2, '.', '') : 0.0,
                'hasil_level' => $resultLevel,
                'hasil_level_class' => $resultLevel !== null ? 'is-level-'.$resultLevel : 'pending',
                'hasil_predikat' => (string) ($resultLevelData['predikat'] ?? 'Belum Dinilai'),
                'hasil_source_label' => $sourceLabel,
                'hasil_manual' => $hasManualResult,
                'hasil_input_value' => $hasManualResult ? number_format((float) $manualResultScore, 2, '.', '') : null,
                'target_has_data' => $hasTargetData,
                'target_score' => $normalizedTargetScore,
                'target_score_label' => $hasTargetData ? number_format((float) $normalizedTargetScore, 2) : '-',
                'target_percent' => $hasTargetData ? (float) number_format(((float) $normalizedTargetScore / 5) * 100, 2, '.', '') : 0.0,
                'target_level_label' => $hasTargetData ? ('L'.((int) ($this->getLevelData((float) $normalizedTargetScore)['level'] ?? 0))) : '-',
                'target_manual' => $hasManualTarget,
                'target_input_value' => $hasManualTarget ? number_format((float) $manualTargetScore, 2, '.', '') : null,
            ];
        })->all();
    }

    /**
     * @param array<string, array<string, mixed>> $summaryModules
     * @return array<int, float>
     */
    private function archiveOverallScoresByBudgetYear(array $summaryModules): array
    {
        if (!$this->elementPreferenceService->hasProgressArchiveTable()) {
            return [];
        }

        $archives = ElementProgressArchive::query()
            ->select(['id', 'budget_year', 'snapshot', 'updated_at'])
            ->orderBy('budget_year')
            ->orderBy('updated_at')
            ->orderBy('id')
            ->get();

        if ($archives->isEmpty()) {
            return [];
        }

        $scoresByYear = [];
        foreach ($archives as $archive) {
            $year = is_numeric($archive->budget_year ?? null) ? (int) $archive->budget_year : 0;
            if ($year <= 0) {
                continue;
            }

            $snapshot = is_array($archive->snapshot) ? $archive->snapshot : [];
            $score = $this->calculateOverallScoreFromArchiveSnapshot($snapshot, $summaryModules);
            if (!is_numeric($score)) {
                continue;
            }

            $scoresByYear[$year] = (float) $score;
        }

        return $scoresByYear;
    }

    /**
     * @param array<string, mixed> $snapshot
     * @param array<string, array<string, mixed>> $summaryModules
     */
    private function calculateOverallScoreFromArchiveSnapshot(array $snapshot, array $summaryModules): ?float
    {
        $snapshotTables = is_array($snapshot['tables'] ?? null)
            ? (array) $snapshot['tables']
            : [];
        $assessmentTable = $snapshotTables['element_assessments'] ?? null;
        if (!is_array($assessmentTable)) {
            return null;
        }

        $assessmentRows = is_array($assessmentTable['rows'] ?? null)
            ? (array) ($assessmentTable['rows'] ?? [])
            : [];
        if (count($assessmentRows) === 0) {
            return null;
        }

        $latestScoreBySubtopic = [];
        foreach ($assessmentRows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $subtopicSlug = trim((string) ($row['subtopic_slug'] ?? ''));
            $weightedTotal = is_numeric($row['weighted_total'] ?? null)
                ? (float) $row['weighted_total']
                : null;
            if ($subtopicSlug === '' || $weightedTotal === null) {
                continue;
            }

            $createdAtRaw = trim((string) ($row['created_at'] ?? ''));
            $createdAtTs = $createdAtRaw !== '' ? (int) strtotime($createdAtRaw) : 0;
            $rowId = is_numeric($row['id'] ?? null) ? (int) $row['id'] : 0;

            $existing = $latestScoreBySubtopic[$subtopicSlug] ?? null;
            if (!is_array($existing)) {
                $latestScoreBySubtopic[$subtopicSlug] = [
                    'score' => (float) number_format($weightedTotal, 2, '.', ''),
                    'created_at_ts' => $createdAtTs,
                    'id' => $rowId,
                ];
                continue;
            }

            $isNewer = $createdAtTs > (int) ($existing['created_at_ts'] ?? 0)
                || ($createdAtTs === (int) ($existing['created_at_ts'] ?? 0) && $rowId > (int) ($existing['id'] ?? 0));
            if ($isNewer) {
                $latestScoreBySubtopic[$subtopicSlug] = [
                    'score' => (float) number_format($weightedTotal, 2, '.', ''),
                    'created_at_ts' => $createdAtTs,
                    'id' => $rowId,
                ];
            }
        }

        if (count($latestScoreBySubtopic) === 0) {
            return null;
        }

        $overallScore = 0.0;
        $hasAnyElementData = false;

        foreach ($summaryModules as $elementSlug => $summaryConfig) {
            if (!is_array($summaryConfig)) {
                continue;
            }

            $elementWeight = (float) ($summaryConfig['element_weight'] ?? 0);
            $subtopicSlugs = collect((array) ($summaryConfig['subtopic_slugs'] ?? []))
                ->map(fn ($slug) => trim((string) $slug))
                ->filter(fn (string $slug): bool => $slug !== '')
                ->values();
            if ($subtopicSlugs->isEmpty()) {
                continue;
            }

            $configuredWeightsRaw = collect((array) ($summaryConfig['subtopic_weights'] ?? []))
                ->mapWithKeys(function ($weight, $slug): array {
                    return [(string) $slug => (float) $weight];
                });
            $configuredWeightTotal = (float) $configuredWeightsRaw->sum();
            $weightDivider = $configuredWeightTotal > 1.5 ? 100 : 1;
            $configuredWeights = $configuredWeightsRaw
                ->map(fn ($weight) => (float) $weight / $weightDivider);

            $defaultWeight = 0.0;
            if ($configuredWeights->isEmpty()) {
                $defaultWeight = 1 / max(1, $subtopicSlugs->count());
            } else {
                $missingCount = (int) $subtopicSlugs
                    ->filter(fn (string $slug): bool => !$configuredWeights->has($slug))
                    ->count();
                $configuredTotal = (float) $configuredWeights->sum();
                if ($missingCount > 0 && $configuredTotal < 1) {
                    $defaultWeight = (1 - $configuredTotal) / $missingCount;
                }
            }

            $elementRawScore = 0.0;
            $elementHasData = false;
            foreach ($subtopicSlugs as $subtopicSlug) {
                $subtopicSnapshot = $latestScoreBySubtopic[(string) $subtopicSlug] ?? null;
                if (!is_array($subtopicSnapshot) || !is_numeric($subtopicSnapshot['score'] ?? null)) {
                    continue;
                }

                $subtopicScore = (float) ($subtopicSnapshot['score'] ?? 0);
                $subtopicWeight = (float) ($configuredWeights->get((string) $subtopicSlug, $defaultWeight));
                $elementRawScore += ($subtopicScore * $subtopicWeight);
                $elementHasData = true;
            }

            if (!$elementHasData) {
                continue;
            }

            $elementScore = (float) number_format($elementRawScore, 2, '.', '');
            $overallScore += ($elementScore * $elementWeight);
            $hasAnyElementData = true;
        }

        if (!$hasAnyElementData) {
            return null;
        }

        return (float) number_format($overallScore, 2, '.', '');
    }

    private function buildSubtopicSummaries(string $elementSlug, array $summaryConfig, array $subtopicModules): array
    {
        $preferredSlugs = collect((array) ($summaryConfig['subtopic_slugs'] ?? []))
            ->map(fn ($slug) => trim((string) $slug))
            ->filter(fn ($slug) => $slug !== '')
            ->values();

        $moduleSlugs = collect($subtopicModules)
            ->filter(function ($config, $slug) use ($elementSlug) {
                return is_array($config)
                    && is_string($slug)
                    && Str::startsWith($slug, $elementSlug.'_')
                    && (bool) ($config['is_active'] ?? true);
            })
            ->keys()
            ->values();

        $allSlugs = ($preferredSlugs->isNotEmpty() ? $preferredSlugs : $moduleSlugs)
            ->unique()
            ->values();
        $assessmentBySlug = null;

        return $allSlugs->map(function ($subtopicSlug) use ($subtopicModules, $allSlugs, &$assessmentBySlug) {
            $slug = (string) $subtopicSlug;
            $moduleConfig = $subtopicModules[$slug] ?? null;
            $moduleSummary = is_array($moduleConfig)
                ? $this->subtopicSummaryFromModule($slug, $moduleConfig)
                : null;

            if ($moduleSummary !== null && ((bool) ($moduleSummary['has_data'] ?? false) || (bool) ($moduleSummary['has_qa_data'] ?? false))) {
                return $moduleSummary;
            }

            if (!$assessmentBySlug instanceof Collection) {
                $assessmentBySlug = $this->latestAssessmentBySubtopicSlugs($allSlugs->all());
            }

            $assessment = $assessmentBySlug->get($slug);

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
                'qa_score' => null,
                'qa_level' => null,
                'qa_predikat' => 'Belum Dinilai',
                'qa_description' => 'Belum ada data verifikasi final QA.',
                'qa_level_note' => 'Belum ada data verifikasi final QA.',
                'has_qa_data' => false,
                'is_qa_verified' => false,
                'rows_total' => 0,
                'rows_verified' => 0,
                'rows_qa_verified' => 0,
                'source' => 'none',
            ];
        })->values()->all();
    }

    /**
     * @param array<int, string> $subtopicSlugs
     */
    private function latestAssessmentBySubtopicSlugs(array $subtopicSlugs): Collection
    {
        $normalizedSlugs = collect($subtopicSlugs)
            ->map(fn ($slug) => trim((string) $slug))
            ->filter(fn (string $slug) => $slug !== '')
            ->unique()
            ->values()
            ->all();

        if ($normalizedSlugs === []) {
            return collect();
        }

        $missingSlugs = array_values(array_filter(
            $normalizedSlugs,
            fn (string $slug): bool => !array_key_exists($slug, $this->latestAssessmentLoadState)
        ));

        if ($missingSlugs !== []) {
            $latestBySlug = ElementAssessment::query()
                ->whereIn('subtopic_slug', $missingSlugs)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get()
                ->groupBy('subtopic_slug')
                ->map(fn (Collection $items) => $items->first());

            foreach ($latestBySlug as $slug => $assessment) {
                if ($assessment instanceof ElementAssessment) {
                    $this->latestAssessmentBySubtopic[(string) $slug] = $assessment;
                }
            }

            foreach ($missingSlugs as $slug) {
                $this->latestAssessmentLoadState[$slug] = true;
            }
        }

        return collect($normalizedSlugs)
            ->mapWithKeys(fn (string $slug): array => [$slug => $this->latestAssessmentBySubtopic[$slug] ?? null]);
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
        if (!$this->hasTableCached($table)) {
            return null;
        }

        $weights = (array) ($moduleConfig['weights'] ?? []);
        $supportsQaVerification = $this->hasColumnCached($table, 'qa_verified')
            && $this->hasColumnCached($table, 'qa_level_validation_state');
        $selectColumns = ['id', 'level', 'skor', 'verified'];
        if ($this->hasColumnCached($table, 'level_validation_state')) {
            $selectColumns[] = 'level_validation_state';
        }
        if ($supportsQaVerification) {
            $selectColumns[] = 'qa_verified';
            $selectColumns[] = 'qa_level_validation_state';
            if ($this->hasColumnCached($table, 'qa_verify_note')) {
                $selectColumns[] = 'qa_verify_note';
            }
            if ($this->hasColumnCached($table, 'qa_follow_up_recommendation')) {
                $selectColumns[] = 'qa_follow_up_recommendation';
            }
        }

        $rows = $this->filterAoiOnlyRows(
            $modelClass::query()
                ->orderBy('id')
                ->get(array_values(array_unique($selectColumns)))
        );

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
                'description' => 'Belum ada data penilaian pada topik ini.',
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
            : 'Belum ada data penilaian pada topik ini.';

        $rowsTotal = $rows->count();
        $rowsVerified = (int) $rows
            ->filter(fn ($row) => (int) ($row->verified ?? 0) === 1)
            ->count();
        $qaRowsVerified = 0;
        $qaHasData = false;
        $qaScore = null;
        $qaDescription = 'Belum ada data verifikasi final QA pada topik ini.';
        $qaLevelData = [
            'level' => null,
            'predikat' => 'Belum Dinilai',
            'description' => 'Belum ada data verifikasi final QA pada topik ini.',
        ];

        if ($supportsQaVerification) {
            $qaRawScore = 0.0;
            foreach ($rows as $row) {
                if ((int) ($row->qa_verified ?? 0) !== 1) {
                    continue;
                }

                $qaRowsVerified++;
                $qaLevel = $this->maxValidatedLevelFromState($row->qa_level_validation_state ?? null);
                if ($qaLevel === null && is_numeric($row->level)) {
                    $qaLevel = (int) $row->level;
                }

                if ($qaLevel !== null) {
                    $qaRawScore += $this->scoreForKegiatanLevel((float) $qaLevel, (int) $row->id, $weights);
                    $qaHasData = true;
                }
            }

            if ($qaHasData) {
                $qaScore = (float) number_format($qaRawScore, 2, '.', '');
                $qaLevelData = $this->getLevelData((float) $qaScore);
                $qaResolvedLevel = is_numeric($qaLevelData['level'] ?? null)
                    ? (int) $qaLevelData['level']
                    : null;
                $qaDescription = $this->resolveSubtopicLevelDescription(
                    $slug,
                    $qaResolvedLevel,
                    $qaScore,
                    (string) ($qaLevelData['description'] ?? '')
                );
            }
        }

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
            'qa_score' => $qaScore,
            'qa_level' => $qaLevelData['level'],
            'qa_predikat' => (string) ($qaLevelData['predikat'] ?? 'Belum Dinilai'),
            'qa_description' => $qaDescription,
            'qa_level_note' => $this->compactLevelDescription($qaDescription),
            'has_qa_data' => $qaHasData,
            'is_qa_verified' => $rowsTotal > 0 && $qaRowsVerified === $rowsTotal,
            'rows_total' => $rowsTotal,
            'rows_verified' => $rowsVerified,
            'rows_qa_verified' => $qaRowsVerified,
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
            'qa_score' => null,
            'qa_level' => null,
            'qa_predikat' => 'Belum Dinilai',
            'qa_description' => 'Belum ada data verifikasi final QA pada topik ini.',
            'qa_level_note' => 'Belum ada data verifikasi final QA pada topik ini.',
            'has_qa_data' => false,
            'is_qa_verified' => false,
            'rows_total' => 0,
            'rows_verified' => 0,
            'rows_qa_verified' => 0,
            'source' => 'assessment',
        ];
    }

    private function calculateElementScore(
        array $subtopics,
        array $summaryConfig,
        string $scoreKey = 'score',
        string $weightedScoreKey = 'weighted_score'
    ): array
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
            $hasItemData = is_numeric($item[$scoreKey] ?? null);
            $score = $hasItemData ? (float) $item[$scoreKey] : 0.0;
            $weightedScore = (float) number_format($score * $weight, 2, '.', '');

            if ($hasItemData) {
                $hasData = true;
            }

            $rawScore += ($score * $weight);

            $item['weight'] = $weight;
            $item[$weightedScoreKey] = $weightedScore;
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

        return 'Belum ada deskripsi level topik.';
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

        return 'Belum ada deskripsi level elemen.';
    }

    private function compactLevelDescription(string $description): string
    {
        $normalized = trim((string) preg_replace('/\s+/u', ' ', $description));
        if ($normalized === '') {
            return 'Belum ada deskripsi level topik.';
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

    private function normalizeLevelValidationState(mixed $state): array
    {
        if (is_string($state)) {
            $decoded = json_decode($state, true);
            $state = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($state)) {
            return [];
        }

        return collect($state)
            ->mapWithKeys(function ($value, $key) {
                $level = (int) $key;
                if ($level < 1 || $level > 5) {
                    return [];
                }

                return [(string) $level => ((int) $value === 1 ? 1 : 0)];
            })
            ->all();
    }

    private function maxValidatedLevelFromState(mixed $state): ?int
    {
        $normalizedState = $this->normalizeLevelValidationState($state);
        $validatedLevels = collect($normalizedState)
            ->filter(fn ($value) => (int) $value === 1)
            ->keys()
            ->map(fn ($level) => (int) $level)
            ->filter(fn ($level) => $level >= 1 && $level <= 5)
            ->values();

        if ($validatedLevels->isEmpty()) {
            return null;
        }

        return (int) $validatedLevels->max();
    }

    private function filterAoiOnlyRows(Collection $rows): Collection
    {
        return $rows
            ->reject(fn ($row) => $this->isAoiOnlyRow($row))
            ->values();
    }

    private function isAoiOnlyRow(mixed $row): bool
    {
        $qaNote = trim((string) data_get($row, 'qa_verify_note', ''));
        $qaFollowUp = trim((string) data_get($row, 'qa_follow_up_recommendation', ''));
        if ($qaNote === '' && $qaFollowUp === '') {
            return false;
        }

        return !is_numeric(data_get($row, 'level'))
            && !is_numeric(data_get($row, 'skor'))
            && $this->maxValidatedLevelFromState(data_get($row, 'level_validation_state')) === null;
    }

    private function hasTableCached(string $table): bool
    {
        $normalizedTable = trim($table);
        if ($normalizedTable === '') {
            return false;
        }

        if (array_key_exists($normalizedTable, $this->schemaTableExists)) {
            return $this->schemaTableExists[$normalizedTable];
        }

        $exists = $this->schemaMetadataCache->hasTable($normalizedTable);
        $this->schemaTableExists[$normalizedTable] = $exists;

        return $exists;
    }

    private function hasColumnCached(string $table, string $column): bool
    {
        $normalizedTable = trim($table);
        $normalizedColumn = trim($column);
        if ($normalizedTable === '' || $normalizedColumn === '') {
            return false;
        }

        $cacheKey = $normalizedTable.'::'.$normalizedColumn;
        if (array_key_exists($cacheKey, $this->schemaColumnExists)) {
            return $this->schemaColumnExists[$cacheKey];
        }

        $exists = $this->hasTableCached($normalizedTable) && $this->schemaMetadataCache->hasColumn($normalizedTable, $normalizedColumn);
        $this->schemaColumnExists[$cacheKey] = $exists;

        return $exists;
    }
}

