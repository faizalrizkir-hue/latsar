<?php

namespace App\Services;

use App\Models\ElementPreference;
use App\Models\ElementProgressArchive;
use App\Models\ElementProgressArchiveLoadLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ElementPreferenceService
{
    public function __construct(
        private readonly SchemaMetadataCache $schemaMetadataCache
    ) {
    }

    private ?array $cachedStructure = null;

    private ?array $cachedSummaryModules = null;

    private ?array $cachedSubtopicModules = null;

    public function hasPreferencesTable(): bool
    {
        return $this->schemaMetadataCache->hasTable('element_preferences');
    }

    public function hasProgressArchiveTable(): bool
    {
        return $this->schemaMetadataCache->hasTable('element_progress_archives');
    }

    public function hasProgressArchiveLoadLogTable(): bool
    {
        return $this->schemaMetadataCache->hasTable('element_progress_archive_load_logs');
    }

    public function structure(): array
    {
        if ($this->cachedStructure !== null) {
            return $this->cachedStructure;
        }

        $defaults = $this->buildDefaultStructure();
        if (!$this->hasPreferencesTable()) {
            $this->cachedStructure = $defaults;

            return $this->cachedStructure;
        }

        $savedPreference = ElementPreference::query()
            ->latest('id')
            ->first();
        $savedPayload = $savedPreference?->payload;
        if (!is_array($savedPayload)) {
            $this->cachedStructure = $defaults;

            return $this->cachedStructure;
        }

        $this->cachedStructure = $this->mergeStructureWithPayload($defaults, $savedPayload);

        return $this->cachedStructure;
    }

    public function buildStructureFromInput(array $inputElements): array
    {
        $baseStructure = $this->structure();
        $baseElements = array_values(array_filter((array) ($baseStructure['elements'] ?? []), 'is_array'));
        $baseBySlug = $this->elementsBySlug($baseElements);

        $elements = [];
        $usedElementSlugs = [];
        $usedSubtopicSlugs = [];
        $elementSequence = 1;

        foreach ($inputElements as $elementKey => $rawElement) {
            if (!is_array($rawElement)) {
                continue;
            }

            $requestedSlug = (string) ($rawElement['slug'] ?? $elementKey);
            $requestedSlug = $this->sanitizeElementSlug($requestedSlug);
            $elementFallbackSlug = 'element'.$elementSequence;
            $elementSlug = $this->ensureUniqueSlug(
                $requestedSlug !== '' ? $requestedSlug : $elementFallbackSlug,
                $usedElementSlugs,
                $elementFallbackSlug
            );

            $baseElement = $this->arrayOrEmpty($baseBySlug[$elementSlug] ?? null);
            $baseSubtopics = array_values(array_filter((array) ($baseElement['subtopics'] ?? []), 'is_array'));
            $baseSubtopicBySlug = $this->subtopicsBySlug($baseSubtopics);

            $element = [
                'slug' => $elementSlug,
                'title' => trim((string) ($rawElement['title'] ?? ($baseElement['title'] ?? Str::headline($elementSlug)))),
                'active' => $this->toBool($rawElement['active'] ?? ($baseElement['active'] ?? true)),
                'weight' => $this->parsePercentWeight(
                    $rawElement['weight'] ?? ($this->asFloat($baseElement['weight'] ?? 0) * 100),
                    $this->asFloat($baseElement['weight'] ?? 0)
                ),
                'info_levels' => $this->normalizeLevelDescriptions(
                    $rawElement['info_levels'] ?? null,
                    $this->normalizeLevelDescriptions($baseElement['info_levels'] ?? null)
                ),
                'subtopics' => [],
            ];

            if ($element['title'] === '') {
                $element['title'] = Str::headline($elementSlug);
            }

            $subtopicSequence = 1;
            foreach ((array) ($rawElement['subtopics'] ?? []) as $subtopicKey => $rawSubtopic) {
                if (!is_array($rawSubtopic)) {
                    continue;
                }

                $requestedSubtopicSlug = (string) ($rawSubtopic['slug'] ?? $subtopicKey);
                $requestedSubtopicSlug = $this->sanitizeSubtopicSlug($requestedSubtopicSlug, $elementSlug);
                $subtopicFallbackSlug = $elementSlug.'_subtopik_'.$subtopicSequence;
                $subtopicSlug = $this->ensureUniqueSlug(
                    $requestedSubtopicSlug !== '' ? $requestedSubtopicSlug : $subtopicFallbackSlug,
                    $usedSubtopicSlugs,
                    $subtopicFallbackSlug
                );

                $baseSubtopic = $this->arrayOrEmpty($baseSubtopicBySlug[$subtopicSlug] ?? null);

                $subtopic = [
                    'slug' => $subtopicSlug,
                    'title' => trim((string) ($rawSubtopic['title'] ?? ($baseSubtopic['title'] ?? Str::headline($subtopicSlug)))),
                    'active' => $this->toBool($rawSubtopic['active'] ?? ($baseSubtopic['active'] ?? true)),
                    'weight' => $this->parsePercentWeight(
                        $rawSubtopic['weight'] ?? ($this->asFloat($baseSubtopic['weight'] ?? 0) * 100),
                        $this->asFloat($baseSubtopic['weight'] ?? 0)
                    ),
                    'info_levels' => $this->normalizeLevelDescriptions(
                        $rawSubtopic['info_levels'] ?? null,
                        $this->normalizeLevelDescriptions($baseSubtopic['info_levels'] ?? null)
                    ),
                    'rows' => $this->rowsFromInputPercent(
                        (array) ($rawSubtopic['rows'] ?? []),
                        array_values(array_filter((array) ($baseSubtopic['rows'] ?? []), 'is_array'))
                    ),
                ];

                if ($subtopic['title'] === '') {
                    $subtopic['title'] = Str::headline($subtopicSlug);
                }

                $element['subtopics'][] = $subtopic;
                $subtopicSequence++;
            }

            if (count($element['subtopics']) === 0) {
                $fallbackSubtopicSlug = $this->ensureUniqueSlug(
                    $elementSlug.'_subtopik_1',
                    $usedSubtopicSlugs,
                    $elementSlug.'_subtopik_1'
                );
                $element['subtopics'][] = [
                    'slug' => $fallbackSubtopicSlug,
                    'title' => 'Sub Topik 1',
                    'active' => true,
                    'weight' => 1.0,
                    'rows' => [
                        [
                            'label' => 'Pernyataan 1',
                            'active' => true,
                            'weight' => 1.0,
                            'level_hints' => $this->normalizeLevelDescriptions(null),
                        ],
                    ],
                ];
            }

            $elements[] = $element;
            $elementSequence++;
        }

        if (count($elements) === 0) {
            $elements = $baseElements;
        }

        if (count($elements) === 0) {
            $elements = $this->defaultSingleElement();
        }

        return [
            'elements' => $this->finalizeElements($elements),
        ];
    }

    public function saveStructure(array $structure, ?string $updatedBy = null): void
    {
        if (!$this->hasPreferencesTable()) {
            return;
        }

        $payload = [
            'elements' => $this->finalizeElements(array_values(array_filter((array) ($structure['elements'] ?? []), 'is_array'))),
        ];

        $record = ElementPreference::query()->first();
        if ($record === null) {
            ElementPreference::query()->create([
                'payload' => $payload,
                'updated_by' => $this->normalizeUpdatedBy($updatedBy),
            ]);
        } else {
            $record->fill([
                'payload' => $payload,
                'updated_by' => $this->normalizeUpdatedBy($updatedBy),
            ]);
            $record->save();
        }

        $this->clearCache();
    }

    public function resetToDefaults(?string $updatedBy = null): array
    {
        $defaults = $this->buildDefaultStructure();
        $this->saveStructure($defaults, $updatedBy);

        return $defaults;
    }

    /**
     * @return array{
     *     tables: array<int, string>,
     *     deleted_by_table: array<string, int>,
     *     deleted_total: int
     * }
     */
    public function resetElementDataAndHistory(): array
    {
        $subtopicModules = array_merge(
            array_values(array_filter((array) config('element_subtopic_modules.modules', []), 'is_array')),
            array_values(array_filter($this->subtopicModules(false), 'is_array'))
        );
        $dataTables = [];
        $historyTables = [];

        foreach ($subtopicModules as $module) {
            $dataTable = $this->tableFromModelClass((string) ($module['model'] ?? ''));
            if ($dataTable !== null) {
                $dataTables[$dataTable] = true;
            }

            $historyTable = $this->tableFromModelClass((string) ($module['edit_log_model'] ?? ''));
            if ($historyTable !== null) {
                $historyTables[$historyTable] = true;
            }
        }

        foreach ([
            'element_assessments',
            'element1_kegiatan_asurans_doc_selections',
            'element1_kegiatan_asurans_row_doc_selections',
        ] as $table) {
            if ($this->schemaMetadataCache->hasTable($table)) {
                $dataTables[$table] = true;
            }
        }

        if ($this->schemaMetadataCache->hasTable('notifications')) {
            $historyTables['notifications'] = true;
        }

        $targetTables = array_values(array_unique(array_merge(
            array_keys($dataTables),
            array_keys($historyTables)
        )));

        if (count($targetTables) === 0) {
            return [
                'tables' => [],
                'deleted_by_table' => [],
                'deleted_total' => 0,
            ];
        }

        $deletedByTable = [];
        DB::transaction(function () use ($targetTables, &$deletedByTable): void {
            foreach ($targetTables as $table) {
                if (!$this->schemaMetadataCache->hasTable($table)) {
                    continue;
                }

                $deletedByTable[$table] = (int) DB::table($table)->count();
                DB::table($table)->delete();
            }
        });

        $this->seedDefaultSubtopicRows();

        return [
            'tables' => array_keys($deletedByTable),
            'deleted_by_table' => $deletedByTable,
            'deleted_total' => (int) array_sum($deletedByTable),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function progressArchives(): array
    {
        if (!$this->hasProgressArchiveTable()) {
            return [];
        }

        return ElementProgressArchive::query()
            ->orderByDesc('budget_year')
            ->orderByDesc('updated_at')
            ->get()
            ->map(static function (ElementProgressArchive $archive): array {
                return [
                    'id' => (int) $archive->id,
                    'budget_year' => (int) $archive->budget_year,
                    'total_rows' => (int) $archive->total_rows,
                    'archived_by' => trim((string) ($archive->archived_by ?? '')),
                    'loaded_by' => trim((string) ($archive->loaded_by ?? '')),
                    'updated_at' => $archive->updated_at,
                    'last_loaded_at' => $archive->last_loaded_at,
                ];
            })
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function progressArchiveLoadLogs(int $limit = 20): array
    {
        if (!$this->hasProgressArchiveLoadLogTable()) {
            return [];
        }

        $safeLimit = max(1, min(100, $limit));

        return ElementProgressArchiveLoadLog::query()
            ->orderByDesc('id')
            ->limit($safeLimit)
            ->get()
            ->map(static function (ElementProgressArchiveLoadLog $log): array {
                return [
                    'id' => (int) $log->id,
                    'archive_id' => (int) $log->archive_id,
                    'budget_year' => (int) $log->budget_year,
                    'restored_tables' => (int) $log->restored_tables,
                    'restored_total' => (int) $log->restored_total,
                    'restored_by_table' => is_array($log->restored_by_table) ? $log->restored_by_table : [],
                    'loaded_by' => trim((string) ($log->loaded_by ?? '')),
                    'created_at' => $log->created_at,
                ];
            })
            ->all();
    }

    /**
     * @return array{
     *     archive_id: int,
     *     budget_year: int,
     *     total_tables: int,
     *     total_rows: int,
     *     replaced: bool
     * }
     */
    public function archiveProgressByBudgetYear(int $budgetYear, ?string $archivedBy = null): array
    {
        if (!$this->hasProgressArchiveTable()) {
            throw new RuntimeException('Tabel arsip progress belum tersedia.');
        }

        $normalizedYear = max(2000, min(2100, $budgetYear));
        $snapshot = $this->buildProgressSnapshot();
        $normalizedBy = $this->normalizeUpdatedBy($archivedBy);

        $archive = ElementProgressArchive::query()->firstOrNew([
            'budget_year' => $normalizedYear,
        ]);
        $isReplacingExisting = $archive->exists;

        $archive->budget_year = $normalizedYear;
        $archive->snapshot = $snapshot;
        $archive->total_rows = (int) ($snapshot['total_rows'] ?? 0);
        $archive->archived_by = $normalizedBy;
        if (!$isReplacingExisting) {
            $archive->loaded_by = null;
            $archive->last_loaded_at = null;
        }
        $archive->save();

        return [
            'archive_id' => (int) $archive->id,
            'budget_year' => $normalizedYear,
            'total_tables' => (int) count((array) ($snapshot['tables'] ?? [])),
            'total_rows' => (int) ($snapshot['total_rows'] ?? 0),
            'replaced' => $isReplacingExisting,
        ];
    }

    /**
     * @return array{
     *     archive_id: int,
     *     budget_year: int,
     *     restored_tables: int,
     *     restored_total: int,
     *     restored_by_table: array<string, int>
     * }
     */
    public function loadProgressArchive(int $archiveId, ?string $loadedBy = null): array
    {
        if (!$this->hasProgressArchiveTable()) {
            throw new RuntimeException('Tabel arsip progress belum tersedia.');
        }

        $archive = ElementProgressArchive::query()->find($archiveId);
        if (!$archive) {
            throw new RuntimeException('Arsip progress tidak ditemukan.');
        }

        $snapshot = is_array($archive->snapshot) ? $archive->snapshot : [];
        $snapshotTables = is_array($snapshot['tables'] ?? null)
            ? (array) $snapshot['tables']
            : [];
        $excludedTables = array_flip($this->progressArchiveExcludedTables());
        $snapshotTableNames = array_values(array_filter(
            array_map(
                static fn (string $table): string => trim($table),
                array_map('strval', array_keys($snapshotTables))
            ),
            static fn (string $table): bool => $table !== '' && !isset($excludedTables[$table])
        ));

        $tablesToClear = array_values(array_unique(array_merge(
            $this->progressArchiveTargetTables(),
            $snapshotTableNames
        )));

        $restoredByTable = [];

        DB::transaction(function () use ($tablesToClear, $snapshotTables, $excludedTables, &$restoredByTable): void {
            foreach ($tablesToClear as $table) {
                $tableName = trim((string) $table);
                if ($tableName === '' || isset($excludedTables[$tableName]) || !$this->schemaMetadataCache->hasTable($tableName)) {
                    continue;
                }

                DB::table($tableName)->delete();
                $restoredByTable[$tableName] = 0;
            }

            foreach ($snapshotTables as $table => $tableSnapshot) {
                $tableName = trim((string) $table);
                if ($tableName === '' || isset($excludedTables[$tableName]) || !$this->schemaMetadataCache->hasTable($tableName)) {
                    continue;
                }

                $allowedColumns = array_flip($this->schemaMetadataCache->columnListing($tableName));
                if (count($allowedColumns) === 0) {
                    continue;
                }

                $rows = is_array($tableSnapshot) ? ($tableSnapshot['rows'] ?? []) : [];
                if (!is_array($rows) || count($rows) === 0) {
                    continue;
                }

                $insertRows = [];
                foreach ($rows as $row) {
                    if (!is_array($row) || count($row) === 0) {
                        continue;
                    }

                    $normalizedRow = array_intersect_key($row, $allowedColumns);
                    if (count($normalizedRow) === 0) {
                        continue;
                    }

                    $insertRows[] = $normalizedRow;
                }

                if (count($insertRows) === 0) {
                    continue;
                }

                foreach (array_chunk($insertRows, 250) as $chunk) {
                    DB::table($tableName)->insert($chunk);
                }

                $restoredByTable[$tableName] = count($insertRows);
            }
        });

        $archive->loaded_by = $this->normalizeUpdatedBy($loadedBy);
        $archive->last_loaded_at = now();
        $archive->save();

        $restoredTables = (int) count(array_filter($restoredByTable, static fn (int $count): bool => $count > 0));
        $restoredTotal = (int) array_sum($restoredByTable);

        if ($this->hasProgressArchiveLoadLogTable()) {
            ElementProgressArchiveLoadLog::query()->create([
                'archive_id' => (int) $archive->id,
                'budget_year' => (int) $archive->budget_year,
                'restored_tables' => $restoredTables,
                'restored_total' => $restoredTotal,
                'restored_by_table' => $restoredByTable,
                'loaded_by' => $this->normalizeUpdatedBy($loadedBy),
            ]);
        }

        $this->clearCache();

        return [
            'archive_id' => (int) $archive->id,
            'budget_year' => (int) $archive->budget_year,
            'restored_tables' => $restoredTables,
            'restored_total' => $restoredTotal,
            'restored_by_table' => $restoredByTable,
        ];
    }

    public function summaryModules(bool $activeOnly = false): array
    {
        $this->ensureMergedModules();
        $modules = $this->cachedSummaryModules ?? [];

        if (!$activeOnly) {
            return $modules;
        }

        return array_filter($modules, static fn (array $module): bool => (bool) ($module['is_active'] ?? true));
    }

    public function subtopicModules(bool $activeOnly = false): array
    {
        $this->ensureMergedModules();
        $modules = $this->cachedSubtopicModules ?? [];

        if (!$activeOnly) {
            return $modules;
        }

        return array_filter($modules, static fn (array $module): bool => (bool) ($module['is_active'] ?? true));
    }

    public function summaryModule(string $slug): ?array
    {
        $modules = $this->summaryModules();
        $module = $modules[$slug] ?? null;

        return is_array($module) ? $module : null;
    }

    public function subtopicModule(string $slug): ?array
    {
        $modules = $this->subtopicModules();
        $module = $modules[$slug] ?? null;

        return is_array($module) ? $module : null;
    }

    public function activeElementSlugs(): array
    {
        return array_values(array_map(
            static fn (array $element): string => (string) ($element['slug'] ?? ''),
            array_filter(
                array_values(array_filter((array) ($this->structure()['elements'] ?? []), 'is_array')),
                static fn (array $element): bool => (bool) ($element['active'] ?? false)
            )
        ));
    }

    public function activeSubtopicSlugsByElement(): array
    {
        $map = [];
        $elements = array_values(array_filter((array) ($this->structure()['elements'] ?? []), 'is_array'));

        foreach ($elements as $element) {
            $elementSlug = (string) ($element['slug'] ?? '');
            if ($elementSlug === '' || !(bool) ($element['active'] ?? false)) {
                continue;
            }

            $subtopics = array_values(array_filter((array) ($element['subtopics'] ?? []), 'is_array'));
            $map[$elementSlug] = array_values(array_map(
                static fn (array $subtopic): string => (string) ($subtopic['slug'] ?? ''),
                array_filter($subtopics, static fn (array $subtopic): bool => (bool) ($subtopic['active'] ?? false))
            ));
        }

        return $map;
    }

    private function ensureMergedModules(): void
    {
        if ($this->cachedSummaryModules !== null && $this->cachedSubtopicModules !== null) {
            return;
        }

        $summaryBase = $this->summaryModulesConfig();
        $subtopicBase = $this->subtopicModulesConfig();
        $structure = $this->structure();

        $structureElements = array_values(array_filter((array) ($structure['elements'] ?? []), 'is_array'));
        if (count($structureElements) === 0) {
            $this->cachedSummaryModules = [];
            $this->cachedSubtopicModules = [];

            return;
        }

        $summaryModules = [];
        $subtopicModules = [];

        foreach ($structureElements as $elementIndex => $element) {
            $elementSlug = (string) ($element['slug'] ?? '');
            if ($elementSlug === '') {
                continue;
            }

            $elementTitle = trim((string) ($element['title'] ?? ''));
            if ($elementTitle === '') {
                $elementTitle = Str::headline($elementSlug);
            }

            $baseSummaryModule = $this->arrayOrEmpty($summaryBase[$elementSlug] ?? null);
            $summaryModule = array_merge(
                $this->defaultSummaryModule($elementSlug, $elementTitle, $elementIndex + 1),
                $baseSummaryModule
            );

            $isElementActive = (bool) ($element['active'] ?? true);
            $subtopics = array_values(array_filter((array) ($element['subtopics'] ?? []), 'is_array'));
            $activeSubtopics = $isElementActive
                ? array_values(array_filter($subtopics, static fn (array $subtopic): bool => (bool) ($subtopic['active'] ?? false)) )
                : [];

            $summaryModule['title'] = $elementTitle;
            $summaryModule['is_active'] = $isElementActive;
            $summaryModule['element_weight'] = $isElementActive
                ? $this->normalizeWeight($element['weight'] ?? 0)
                : 0.0;
            $summaryModule['info_levels'] = $this->mergeInfoLevelsWithDescriptions(
                (array) ($summaryModule['info_levels'] ?? []),
                $this->normalizeLevelDescriptions(
                    $element['info_levels'] ?? null,
                    $this->infoLevelDescriptionsFromModule((array) ($summaryModule['info_levels'] ?? []))
                )
            );
            $summaryModule['subtopic_slugs'] = array_values(array_map(
                static fn (array $subtopic): string => (string) ($subtopic['slug'] ?? ''),
                $activeSubtopics
            ));
            $summaryModule['subtopic_weights'] = $this->weightMapBySlug($activeSubtopics);

            if (!$isElementActive) {
                $summaryModule['subtopic_slugs'] = [];
                $summaryModule['subtopic_weights'] = [];
            }

            $summaryModules[$elementSlug] = $summaryModule;

            foreach ($subtopics as $subtopicIndex => $subtopic) {
                $subtopicSlug = (string) ($subtopic['slug'] ?? '');
                if ($subtopicSlug === '') {
                    continue;
                }

                $subtopicTitle = trim((string) ($subtopic['title'] ?? ''));
                if ($subtopicTitle === '') {
                    $subtopicTitle = Str::headline($subtopicSlug);
                }

                $baseSubtopicModule = $this->arrayOrEmpty($subtopicBase[$subtopicSlug] ?? null);
                $subtopicModule = array_merge(
                    $this->defaultSubtopicModule($elementTitle, $subtopicTitle, $subtopicIndex + 1),
                    $baseSubtopicModule
                );

                $subtopicMode = trim((string) ($subtopicModule['mode'] ?? ''));
                if ($subtopicMode === '') {
                    $hasModel = trim((string) ($subtopicModule['model'] ?? '')) !== '';
                    $subtopicMode = $hasModel ? 'kegiatan' : 'assessment';
                }

                $rows = array_values(array_filter((array) ($subtopic['rows'] ?? []), 'is_array'));
                $activeRows = array_values(array_filter($rows, static fn (array $row): bool => (bool) ($row['active'] ?? false)));
                if (count($activeRows) === 0) {
                    $activeRows = $rows;
                }
                if (count($activeRows) === 0) {
                    $activeRows[] = ['label' => 'Pernyataan 1', 'weight' => 1.0, 'active' => true];
                }

                $activeRows = $this->normalizeRowWeights($activeRows);
                $rowMap = [];
                $weightMap = [];
                $rowId = 1;
                foreach ($activeRows as $row) {
                    $label = trim((string) ($row['label'] ?? ''));
                    if ($label === '') {
                        $label = 'Pernyataan '.$rowId;
                    }

                    $rowMap[$rowId] = $label;
                    $weightMap[$rowId] = $this->normalizeWeight($row['weight'] ?? 0);
                    $rowId++;
                }

                $subtopicModule['mode'] = $subtopicMode;
                $subtopicModule['subtopic_title'] = $subtopicTitle;
                $subtopicModule['page_title'] = $elementTitle;
                $subtopicModule['rows'] = $rowMap;
                $subtopicModule['weights'] = $weightMap;
                $subtopicModule['info_levels'] = $this->mergeInfoLevelsWithDescriptions(
                    (array) ($subtopicModule['info_levels'] ?? []),
                    $this->normalizeLevelDescriptions(
                        $subtopic['info_levels'] ?? null,
                        $this->infoLevelDescriptionsFromModule((array) ($subtopicModule['info_levels'] ?? []))
                    )
                );
                $subtopicModule['is_active'] = $isElementActive && (bool) ($subtopic['active'] ?? false);
                $subtopicModule['statement_level_hints'] = [];

                foreach ($activeRows as $activeRow) {
                    $rowLabel = trim((string) ($activeRow['label'] ?? ''));
                    if ($rowLabel === '') {
                        continue;
                    }

                    $subtopicModule['statement_level_hints'][$rowLabel] = $this->normalizeLevelDescriptions(
                        $activeRow['level_hints'] ?? null
                    );
                }

                $subtopicModules[$subtopicSlug] = $subtopicModule;
            }
        }

        $activeElements = array_values(array_filter(
            $summaryModules,
            static fn (array $module): bool => (bool) ($module['is_active'] ?? false)
        ));
        $totalElementWeight = array_reduce(
            $activeElements,
            static fn (float $carry, array $module): float => $carry + (float) ($module['element_weight'] ?? 0),
            0.0
        );

        if ($totalElementWeight > 0) {
            foreach ($summaryModules as $elementSlug => $module) {
                if (!(bool) ($module['is_active'] ?? false)) {
                    $summaryModules[$elementSlug]['element_weight'] = 0.0;
                    continue;
                }

                $summaryModules[$elementSlug]['element_weight'] = round(
                    $this->normalizeWeight(($module['element_weight'] ?? 0) / $totalElementWeight),
                    6
                );
            }
        }

        $this->cachedSummaryModules = $summaryModules;
        $this->cachedSubtopicModules = $subtopicModules;
    }

    private function buildDefaultStructure(): array
    {
        $summaryModules = $this->summaryModulesConfig();
        $subtopicModules = $this->subtopicModulesConfig();

        if (count($summaryModules) === 0 && count($subtopicModules) > 0) {
            $groupedSubtopics = [];
            foreach ($subtopicModules as $subtopicSlug => $subtopicConfig) {
                if (!is_array($subtopicConfig)) {
                    continue;
                }

                $resolvedSubtopicSlug = (string) $subtopicSlug;
                if ($resolvedSubtopicSlug === '') {
                    continue;
                }

                $elementSlug = $this->topLevelElementSlug($resolvedSubtopicSlug);
                if ($elementSlug === '') {
                    continue;
                }

                if (!isset($groupedSubtopics[$elementSlug])) {
                    $groupedSubtopics[$elementSlug] = [];
                }

                $groupedSubtopics[$elementSlug][] = $resolvedSubtopicSlug;
            }

            if (count($groupedSubtopics) > 0) {
                ksort($groupedSubtopics);
                $equalElementWeight = 1 / count($groupedSubtopics);

                foreach ($groupedSubtopics as $elementSlug => $subtopicSlugs) {
                    if (count($subtopicSlugs) === 0) {
                        continue;
                    }

                    $firstSubtopicConfig = is_array($subtopicModules[$subtopicSlugs[0]] ?? null)
                        ? (array) $subtopicModules[$subtopicSlugs[0]]
                        : [];
                    $elementTitle = trim((string) ($firstSubtopicConfig['page_title'] ?? ''));
                    if ($elementTitle === '') {
                        $elementTitle = Str::headline($elementSlug);
                    }

                    $equalSubtopicWeight = 1 / count($subtopicSlugs);
                    $summaryModules[$elementSlug] = [
                        'title' => $elementTitle,
                        'element_weight' => $equalElementWeight,
                        'subtopic_slugs' => $subtopicSlugs,
                        'subtopic_weights' => array_fill_keys($subtopicSlugs, $equalSubtopicWeight),
                        'info_levels' => [],
                    ];
                }
            }
        }

        $elements = [];
        foreach ($summaryModules as $elementSlug => $summaryConfig) {
            if (!is_array($summaryConfig)) {
                continue;
            }

            $elementSlug = (string) $elementSlug;
            $subtopicSlugs = array_values(array_filter(
                array_map('strval', (array) ($summaryConfig['subtopic_slugs'] ?? [])),
                static fn (string $slug): bool => $slug !== ''
            ));

            if (count($subtopicSlugs) === 0) {
                $subtopicSlugs = array_values(array_keys(array_filter(
                    $subtopicModules,
                    static fn ($config, $slug): bool => is_array($config) && Str::startsWith((string) $slug, $elementSlug.'_'),
                    ARRAY_FILTER_USE_BOTH
                )));
            }

            $subtopicWeightMap = $this->normalizeSlugWeightMap(
                (array) ($summaryConfig['subtopic_weights'] ?? []),
                $subtopicSlugs
            );

            $subtopics = [];
            foreach ($subtopicSlugs as $subtopicSlug) {
                $subtopicConfig = $subtopicModules[$subtopicSlug] ?? [];
                $rowsMap = is_array($subtopicConfig['rows'] ?? null)
                    ? (array) ($subtopicConfig['rows'] ?? [])
                    : [];
                $statementLevelHints = is_array($subtopicConfig['statement_level_hints'] ?? null)
                    ? (array) ($subtopicConfig['statement_level_hints'] ?? [])
                    : [];

                $rowIds = array_values(array_map('intval', array_keys($rowsMap)));
                if (count($rowIds) === 0) {
                    $rowIds = [1];
                    $rowsMap = [1 => 'Pernyataan 1'];
                }

                $rowWeightMap = $this->normalizeNumericWeightMap(
                    is_array($subtopicConfig['weights'] ?? null) ? (array) ($subtopicConfig['weights'] ?? []) : [],
                    $rowIds
                );

                $rows = [];
                foreach ($rowIds as $rowId) {
                    $rowLabel = trim((string) ($rowsMap[$rowId] ?? ('Pernyataan '.$rowId)));
                    if ($rowLabel === '') {
                        $rowLabel = 'Pernyataan '.$rowId;
                    }

                    $rows[] = [
                        'label' => $rowLabel,
                        'active' => true,
                        'weight' => $this->normalizeWeight($rowWeightMap[$rowId] ?? 0),
                        'level_hints' => $this->normalizeLevelDescriptions($statementLevelHints[$rowLabel] ?? null),
                    ];
                }

                $subtopics[] = [
                    'slug' => $subtopicSlug,
                    'title' => (string) ($subtopicConfig['subtopic_title'] ?? Str::headline($subtopicSlug)),
                    'active' => true,
                    'weight' => $this->normalizeWeight($subtopicWeightMap[$subtopicSlug] ?? 0),
                    'info_levels' => $this->normalizeLevelDescriptions($subtopicConfig['info_levels'] ?? null),
                    'rows' => $rows,
                ];
            }

            $elements[] = [
                'slug' => $elementSlug,
                'title' => (string) ($summaryConfig['title'] ?? Str::headline($elementSlug)),
                'active' => true,
                'weight' => $this->normalizeWeight($summaryConfig['element_weight'] ?? 0),
                'info_levels' => $this->normalizeLevelDescriptions($summaryConfig['info_levels'] ?? null),
                'subtopics' => $subtopics,
            ];
        }

        if (count($elements) === 0) {
            $elements = $this->defaultSingleElement();
        }

        return [
            'elements' => $this->finalizeElements($elements),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function summaryModulesConfig(): array
    {
        return $this->configModules('element_summary_modules.modules', 'element_summary_modules.php');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function subtopicModulesConfig(): array
    {
        return $this->configModules('element_subtopic_modules.modules', 'element_subtopic_modules.php');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function configModules(string $configKey, string $fileName): array
    {
        $modules = config($configKey, []);
        if (is_array($modules) && count($modules) > 0) {
            return $modules;
        }

        $filePath = config_path($fileName);
        if (!is_file($filePath)) {
            return [];
        }

        $raw = require $filePath;
        $fileModules = is_array($raw) ? ($raw['modules'] ?? []) : [];

        return is_array($fileModules) ? $fileModules : [];
    }

    private function mergeStructureWithPayload(array $defaults, array $payload): array
    {
        $defaultElements = array_values(array_filter((array) ($defaults['elements'] ?? []), 'is_array'));
        $payloadElements = array_values(array_filter((array) ($payload['elements'] ?? []), 'is_array'));

        if (count($payloadElements) === 0) {
            return [
                'elements' => $this->finalizeElements($defaultElements),
            ];
        }

        $defaultBySlug = $this->elementsBySlug($defaultElements);

        $elements = [];
        $usedElementSlugs = [];
        $usedSubtopicSlugs = [];
        $elementSequence = 1;

        foreach ($payloadElements as $payloadElement) {
            $requestedSlug = $this->sanitizeElementSlug((string) ($payloadElement['slug'] ?? ''));
            $elementFallbackSlug = 'element'.$elementSequence;
            $elementSlug = $this->ensureUniqueSlug(
                $requestedSlug !== '' ? $requestedSlug : $elementFallbackSlug,
                $usedElementSlugs,
                $elementFallbackSlug
            );

            $defaultElement = $this->arrayOrEmpty($defaultBySlug[$elementSlug] ?? null);
            $defaultSubtopicBySlug = $this->subtopicsBySlug(array_values(array_filter((array) ($defaultElement['subtopics'] ?? []), 'is_array')));

            $element = [
                'slug' => $elementSlug,
                'title' => trim((string) ($payloadElement['title'] ?? ($defaultElement['title'] ?? Str::headline($elementSlug)))),
                'active' => $this->toBool($payloadElement['active'] ?? ($defaultElement['active'] ?? true)),
                'weight' => $this->normalizeWeight($payloadElement['weight'] ?? ($defaultElement['weight'] ?? 0)),
                'info_levels' => $this->normalizeLevelDescriptions(
                    $payloadElement['info_levels'] ?? null,
                    $this->normalizeLevelDescriptions($defaultElement['info_levels'] ?? null)
                ),
                'subtopics' => [],
            ];

            if ($element['title'] === '') {
                $element['title'] = Str::headline($elementSlug);
            }

            $payloadSubtopics = array_values(array_filter((array) ($payloadElement['subtopics'] ?? []), 'is_array'));
            if (count($payloadSubtopics) === 0) {
                $payloadSubtopics = array_values(array_filter((array) ($defaultElement['subtopics'] ?? []), 'is_array'));
            }

            $subtopicSequence = 1;
            foreach ($payloadSubtopics as $payloadSubtopic) {
                $requestedSubtopicSlug = $this->sanitizeSubtopicSlug((string) ($payloadSubtopic['slug'] ?? ''), $elementSlug);
                $subtopicFallbackSlug = $elementSlug.'_subtopik_'.$subtopicSequence;
                $subtopicSlug = $this->ensureUniqueSlug(
                    $requestedSubtopicSlug !== '' ? $requestedSubtopicSlug : $subtopicFallbackSlug,
                    $usedSubtopicSlugs,
                    $subtopicFallbackSlug
                );

                $defaultSubtopic = $this->arrayOrEmpty($defaultSubtopicBySlug[$subtopicSlug] ?? null);
                $payloadRows = array_values(array_filter((array) ($payloadSubtopic['rows'] ?? []), 'is_array'));
                $defaultRows = array_values(array_filter((array) ($defaultSubtopic['rows'] ?? []), 'is_array'));
                $rows = $this->rowsFromPersistedPayload($payloadRows, $defaultRows);

                $subtopic = [
                    'slug' => $subtopicSlug,
                    'title' => trim((string) ($payloadSubtopic['title'] ?? ($defaultSubtopic['title'] ?? Str::headline($subtopicSlug)))),
                    'active' => $this->toBool($payloadSubtopic['active'] ?? ($defaultSubtopic['active'] ?? true)),
                    'weight' => $this->normalizeWeight($payloadSubtopic['weight'] ?? ($defaultSubtopic['weight'] ?? 0)),
                    'info_levels' => $this->normalizeLevelDescriptions(
                        $payloadSubtopic['info_levels'] ?? null,
                        $this->normalizeLevelDescriptions($defaultSubtopic['info_levels'] ?? null)
                    ),
                    'rows' => $rows,
                ];

                if ($subtopic['title'] === '') {
                    $subtopic['title'] = Str::headline($subtopicSlug);
                }

                $element['subtopics'][] = $subtopic;
                $subtopicSequence++;
            }

            if (count($element['subtopics']) === 0) {
                $fallbackSubtopicSlug = $this->ensureUniqueSlug(
                    $elementSlug.'_subtopik_1',
                    $usedSubtopicSlugs,
                    $elementSlug.'_subtopik_1'
                );
                $element['subtopics'][] = [
                    'slug' => $fallbackSubtopicSlug,
                    'title' => 'Sub Topik 1',
                    'active' => true,
                    'weight' => 1.0,
                    'rows' => [
                        [
                            'label' => 'Pernyataan 1',
                            'active' => true,
                            'weight' => 1.0,
                            'level_hints' => $this->normalizeLevelDescriptions(null),
                        ],
                    ],
                ];
            }

            $elements[] = $element;
            $elementSequence++;
        }

        if (count($elements) === 0) {
            $elements = $defaultElements;
        }

        if (count($elements) === 0) {
            $elements = $this->defaultSingleElement();
        }

        return [
            'elements' => $this->finalizeElements($elements),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $inputRows
     * @param array<int, array<string, mixed>> $baseRows
     * @return array<int, array<string, mixed>>
     */
    private function rowsFromInputPercent(array $inputRows, array $baseRows): array
    {
        $rows = [];

        foreach ($inputRows as $rawRow) {
            if (!is_array($rawRow)) {
                continue;
            }

            $label = trim((string) ($rawRow['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $rows[] = [
                'label' => $label,
                'active' => $this->toBool($rawRow['active'] ?? true),
                'weight' => $this->parsePercentWeight($rawRow['weight'] ?? 0, 0),
                'level_hints' => $this->normalizeLevelDescriptions($rawRow['level_hints'] ?? null),
            ];
        }

        if (count($rows) > 0) {
            return $rows;
        }

        foreach ($baseRows as $baseRow) {
            $label = trim((string) ($baseRow['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $rows[] = [
                'label' => $label,
                'active' => $this->toBool($baseRow['active'] ?? true),
                'weight' => $this->normalizeWeight($baseRow['weight'] ?? 0),
                'level_hints' => $this->normalizeLevelDescriptions($baseRow['level_hints'] ?? null),
            ];
        }

        if (count($rows) === 0) {
            $rows[] = [
                'label' => 'Pernyataan 1',
                'active' => true,
                'weight' => 1.0,
                'level_hints' => $this->normalizeLevelDescriptions(null),
            ];
        }

        return $rows;
    }

    /**
     * @param array<int, array<string, mixed>> $payloadRows
     * @param array<int, array<string, mixed>> $defaultRows
     * @return array<int, array<string, mixed>>
     */
    private function rowsFromPersistedPayload(array $payloadRows, array $defaultRows): array
    {
        $rows = [];

        foreach ($payloadRows as $payloadRow) {
            $label = trim((string) ($payloadRow['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $rows[] = [
                'label' => $label,
                'active' => $this->toBool($payloadRow['active'] ?? true),
                'weight' => $this->normalizeWeight($payloadRow['weight'] ?? 0),
                'level_hints' => $this->normalizeLevelDescriptions($payloadRow['level_hints'] ?? null),
            ];
        }

        if (count($rows) > 0) {
            return $rows;
        }

        foreach ($defaultRows as $defaultRow) {
            $label = trim((string) ($defaultRow['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $rows[] = [
                'label' => $label,
                'active' => $this->toBool($defaultRow['active'] ?? true),
                'weight' => $this->normalizeWeight($defaultRow['weight'] ?? 0),
                'level_hints' => $this->normalizeLevelDescriptions($defaultRow['level_hints'] ?? null),
            ];
        }

        if (count($rows) === 0) {
            $rows[] = [
                'label' => 'Pernyataan 1',
                'active' => true,
                'weight' => 1.0,
                'level_hints' => $this->normalizeLevelDescriptions(null),
            ];
        }

        return $rows;
    }

    /**
     * @param array<int, array<string, mixed>> $elements
     * @return array<string, array<string, mixed>>
     */
    private function elementsBySlug(array $elements): array
    {
        $map = [];

        foreach ($elements as $element) {
            $slug = (string) ($element['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $map[$slug] = $element;
        }

        return $map;
    }

    /**
     * @param array<int, array<string, mixed>> $subtopics
     * @return array<string, array<string, mixed>>
     */
    private function subtopicsBySlug(array $subtopics): array
    {
        $map = [];

        foreach ($subtopics as $subtopic) {
            $slug = (string) ($subtopic['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $map[$slug] = $subtopic;
        }

        return $map;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function defaultSingleElement(): array
    {
        return [
            [
                'slug' => 'element1',
                'title' => 'Element 1',
                'active' => true,
                'weight' => 1.0,
                'info_levels' => $this->normalizeLevelDescriptions(null),
                'subtopics' => [
                    [
                        'slug' => 'element1_subtopik_1',
                        'title' => 'Sub Topik 1',
                        'active' => true,
                        'weight' => 1.0,
                        'info_levels' => $this->normalizeLevelDescriptions(null),
                        'rows' => [
                            [
                                'label' => 'Pernyataan 1',
                                'active' => true,
                                'weight' => 1.0,
                                'level_hints' => $this->normalizeLevelDescriptions(null),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $elements
     * @return array<int, array<string, mixed>>
     */
    private function finalizeElements(array $elements): array
    {
        $finalElements = [];

        foreach ($elements as $element) {
            $elementSlug = $this->sanitizeElementSlug((string) ($element['slug'] ?? ''));
            if ($elementSlug === '') {
                continue;
            }

            $subtopics = array_values(array_filter((array) ($element['subtopics'] ?? []), 'is_array'));
            $finalSubtopics = [];
            foreach ($subtopics as $subtopic) {
                $subtopicSlug = $this->sanitizeSubtopicSlug((string) ($subtopic['slug'] ?? ''), $elementSlug);
                if ($subtopicSlug === '') {
                    continue;
                }

                $rows = array_values(array_filter((array) ($subtopic['rows'] ?? []), 'is_array'));
                $finalRows = [];
                foreach ($rows as $row) {
                    $label = trim((string) ($row['label'] ?? ''));
                    if ($label === '') {
                        continue;
                    }

                    $finalRows[] = [
                        'label' => $label,
                        'active' => $this->toBool($row['active'] ?? true),
                        'weight' => $this->normalizeWeight($row['weight'] ?? 0),
                        'level_hints' => $this->normalizeLevelDescriptions($row['level_hints'] ?? null),
                    ];
                }

                if (count($finalRows) === 0) {
                    $finalRows[] = [
                        'label' => 'Pernyataan 1',
                        'active' => true,
                        'weight' => 1.0,
                        'level_hints' => $this->normalizeLevelDescriptions(null),
                    ];
                }

                $this->ensureAtLeastOneActive($finalRows);
                $this->normalizeActiveWeights($finalRows);

                $subtopicTitle = trim((string) ($subtopic['title'] ?? ''));
                if ($subtopicTitle === '') {
                    $subtopicTitle = Str::headline($subtopicSlug);
                }

                $finalSubtopics[] = [
                    'slug' => $subtopicSlug,
                    'title' => $subtopicTitle,
                    'active' => $this->toBool($subtopic['active'] ?? true),
                    'weight' => $this->normalizeWeight($subtopic['weight'] ?? 0),
                    'info_levels' => $this->normalizeLevelDescriptions($subtopic['info_levels'] ?? null),
                    'rows' => $finalRows,
                ];
            }

            if (count($finalSubtopics) === 0) {
                $finalSubtopics[] = [
                    'slug' => $elementSlug.'_subtopik_1',
                    'title' => 'Sub Topik 1',
                    'active' => true,
                    'weight' => 1.0,
                    'info_levels' => $this->normalizeLevelDescriptions(null),
                    'rows' => [
                        [
                            'label' => 'Pernyataan 1',
                            'active' => true,
                            'weight' => 1.0,
                            'level_hints' => $this->normalizeLevelDescriptions(null),
                        ],
                    ],
                ];
            }

            $elementTitle = trim((string) ($element['title'] ?? ''));
            if ($elementTitle === '') {
                $elementTitle = Str::headline($elementSlug);
            }

            if ($this->toBool($element['active'] ?? true)) {
                $this->ensureAtLeastOneActive($finalSubtopics);
                $this->normalizeActiveWeights($finalSubtopics);
            } else {
                foreach ($finalSubtopics as $index => $finalSubtopic) {
                    $finalSubtopics[$index]['weight'] = 0.0;
                }
            }

            $finalElements[] = [
                'slug' => $elementSlug,
                'title' => $elementTitle,
                'active' => $this->toBool($element['active'] ?? true),
                'weight' => $this->normalizeWeight($element['weight'] ?? 0),
                'info_levels' => $this->normalizeLevelDescriptions($element['info_levels'] ?? null),
                'subtopics' => $finalSubtopics,
            ];
        }

        if (count($finalElements) === 0) {
            return [];
        }

        $this->ensureAtLeastOneActive($finalElements);
        $this->normalizeActiveWeights($finalElements);

        return $finalElements;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function normalizeRowWeights(array $rows): array
    {
        $normalizedRows = [];
        foreach ($rows as $row) {
            $normalizedRows[] = [
                'label' => trim((string) ($row['label'] ?? '')),
                'active' => $this->toBool($row['active'] ?? true),
                'weight' => $this->normalizeWeight($row['weight'] ?? 0),
                'level_hints' => $this->normalizeLevelDescriptions($row['level_hints'] ?? null),
            ];
        }

        $this->ensureAtLeastOneActive($normalizedRows);
        $this->normalizeActiveWeights($normalizedRows);

        return $normalizedRows;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function ensureAtLeastOneActive(array &$items): void
    {
        if (count($items) === 0) {
            return;
        }

        $hasActive = false;
        foreach ($items as $item) {
            if ((bool) ($item['active'] ?? false)) {
                $hasActive = true;
                break;
            }
        }

        if ($hasActive) {
            return;
        }

        $items[0]['active'] = true;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function normalizeActiveWeights(array &$items): void
    {
        $activeIndexes = [];
        foreach ($items as $index => $item) {
            if ((bool) ($item['active'] ?? false)) {
                $activeIndexes[] = $index;
                continue;
            }

            $items[$index]['weight'] = 0.0;
        }

        if (count($activeIndexes) === 0) {
            return;
        }

        $sum = 0.0;
        foreach ($activeIndexes as $index) {
            $sum += $this->normalizeWeight($items[$index]['weight'] ?? 0);
        }

        if ($sum <= 0) {
            $equalWeight = round(1 / count($activeIndexes), 6);
            foreach ($activeIndexes as $index) {
                $items[$index]['weight'] = $equalWeight;
            }

            return;
        }

        foreach ($activeIndexes as $index) {
            $items[$index]['weight'] = round(
                $this->normalizeWeight(($items[$index]['weight'] ?? 0) / $sum),
                6
            );
        }
    }

    /**
     * @param array<int, array<string, mixed>> $subtopics
     * @return array<string, float>
     */
    private function weightMapBySlug(array $subtopics): array
    {
        $map = [];
        $sum = 0.0;

        foreach ($subtopics as $subtopic) {
            $slug = (string) ($subtopic['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $weight = $this->normalizeWeight($subtopic['weight'] ?? 0);
            $map[$slug] = $weight;
            $sum += $weight;
        }

        if ($sum <= 0 || count($map) === 0) {
            return $map;
        }

        foreach ($map as $slug => $weight) {
            $map[$slug] = round($this->normalizeWeight($weight / $sum), 6);
        }

        return $map;
    }

    /**
     * @param array<int|string, mixed> $weights
     * @param array<int, string> $slugs
     * @return array<string, float>
     */
    private function normalizeSlugWeightMap(array $weights, array $slugs): array
    {
        $normalized = [];

        foreach ($slugs as $slug) {
            $normalized[$slug] = $this->normalizeWeight($weights[$slug] ?? 0);
        }

        $sum = array_sum($normalized);
        if ($sum <= 0 && count($normalized) > 0) {
            $equalWeight = round(1 / count($normalized), 6);
            foreach ($normalized as $slug => $weight) {
                $normalized[$slug] = $equalWeight;
            }

            return $normalized;
        }

        if ($sum > 0) {
            foreach ($normalized as $slug => $weight) {
                $normalized[$slug] = round($this->normalizeWeight($weight / $sum), 6);
            }
        }

        return $normalized;
    }

    /**
     * @param array<int|string, mixed> $weights
     * @param array<int, int> $rowIds
     * @return array<int, float>
     */
    private function normalizeNumericWeightMap(array $weights, array $rowIds): array
    {
        $normalized = [];

        foreach ($rowIds as $rowId) {
            $normalized[$rowId] = $this->normalizeWeight($weights[$rowId] ?? $weights[(string) $rowId] ?? 0);
        }

        $sum = array_sum($normalized);
        if ($sum <= 0 && count($normalized) > 0) {
            $equalWeight = round(1 / count($normalized), 6);
            foreach ($normalized as $rowId => $weight) {
                $normalized[$rowId] = $equalWeight;
            }

            return $normalized;
        }

        if ($sum > 0) {
            foreach ($normalized as $rowId => $weight) {
                $normalized[$rowId] = round($this->normalizeWeight($weight / $sum), 6);
            }
        }

        return $normalized;
    }

    private function defaultSummaryModule(string $elementSlug, string $elementTitle, int $position): array
    {
        $headerCode = 'E'.$position;
        if (preg_match('/^element(\d+)$/', $elementSlug, $matches)) {
            $headerCode = 'E'.((string) ($matches[1] ?? $position));
        }

        return [
            'view' => 'elements.element1-summary',
            'title' => $elementTitle,
            'header_code' => $headerCode,
            'header_subtitle' => 'Rekap skor tertimbang dan level dari sub topik '.$elementTitle,
            'level_label' => 'Level '.$elementTitle,
            'info_modal_title' => 'Informasi Level '.$elementTitle,
            'styles' => [
                'css/element1-kegiatan-asurans.css',
                'css/element1-summary.css',
            ],
            'info_levels' => [],
            'element_weight' => 0.0,
            'subtopic_slugs' => [],
            'subtopic_weights' => [],
        ];
    }

    private function defaultSubtopicModule(string $elementTitle, string $subtopicTitle, int $position): array
    {
        return [
            'view' => 'elements.element1-kegiatan-asurans',
            'mode' => 'kegiatan',
            'model' => null,
            'edit_log_model' => null,
            'page_title' => $elementTitle,
            'subtopic_code' => 'S'.$position,
            'subtopic_title' => $subtopicTitle,
            'info_modal_title' => 'Informasi Level '.$subtopicTitle,
            'notification_title' => $elementTitle.' - '.$subtopicTitle,
            'rows' => [
                1 => 'Pernyataan 1',
            ],
            'weights' => [
                1 => 1.0,
            ],
            'info_levels' => [],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function normalizeLevelDescriptions(mixed $value, ?array $fallback = null): array
    {
        $normalized = [
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
        ];

        if (is_array($fallback)) {
            foreach ($fallback as $level => $description) {
                if (!is_numeric($level)) {
                    continue;
                }

                $levelNumber = (int) $level;
                if ($levelNumber < 1 || $levelNumber > 5) {
                    continue;
                }

                $normalized[$levelNumber] = trim((string) $description);
            }
        }

        if (!is_array($value)) {
            return $normalized;
        }

        foreach ($value as $key => $item) {
            $level = null;
            $description = '';

            if (is_array($item)) {
                $rawLevel = $item['level'] ?? $key;
                if (is_numeric($rawLevel)) {
                    $level = (int) $rawLevel;
                }

                $description = trim((string) ($item['description'] ?? ''));
            } else {
                if (is_numeric($key)) {
                    $level = (int) $key;
                }

                $description = trim((string) $item);
            }

            if ($level === null || $level < 1 || $level > 5) {
                continue;
            }

            $normalized[$level] = $description;
        }

        return $normalized;
    }

    /**
     * @param array<int, array<string, mixed>> $infoLevels
     * @return array<int, string>
     */
    private function infoLevelDescriptionsFromModule(array $infoLevels): array
    {
        return $this->normalizeLevelDescriptions($infoLevels);
    }

    /**
     * @param array<int, array<string, mixed>> $baseInfoLevels
     * @param array<int, string> $descriptions
     * @return array<int, array<string, mixed>>
     */
    private function mergeInfoLevelsWithDescriptions(array $baseInfoLevels, array $descriptions): array
    {
        $baseByLevel = [];
        foreach ($baseInfoLevels as $item) {
            if (!is_array($item)) {
                continue;
            }

            $level = is_numeric($item['level'] ?? null) ? (int) $item['level'] : null;
            if ($level === null || $level < 1 || $level > 5) {
                continue;
            }

            $baseByLevel[$level] = $item;
        }

        $merged = [];
        for ($level = 1; $level <= 5; $level++) {
            $baseItem = is_array($baseByLevel[$level] ?? null)
                ? $baseByLevel[$level]
                : ['level' => $level];

            $baseItem['level'] = $level;
            $baseItem['description'] = trim((string) ($descriptions[$level] ?? ''));
            $merged[] = $baseItem;
        }

        return $merged;
    }

    private function sanitizeElementSlug(string $value): string
    {
        $normalized = $this->sanitizeSlugValue($value);
        if ($normalized === '') {
            return '';
        }

        if (!Str::startsWith($normalized, 'element')) {
            $normalized = 'element_'.$normalized;
        }

        return $normalized;
    }

    private function sanitizeSubtopicSlug(string $value, string $elementSlug): string
    {
        $normalized = $this->sanitizeSlugValue($value);
        if ($normalized === '') {
            return '';
        }

        if ($elementSlug !== '' && !Str::startsWith($normalized, $elementSlug.'_')) {
            $normalized = $elementSlug.'_'.$normalized;
        }

        return $normalized;
    }

    private function sanitizeSlugValue(string $value): string
    {
        $normalized = (string) Str::of($value)
            ->lower()
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_');

        return $normalized;
    }

    /**
     * @param array<int, string> $used
     */
    private function ensureUniqueSlug(string $requestedSlug, array &$used, string $fallback): string
    {
        $baseSlug = trim($requestedSlug);
        if ($baseSlug === '') {
            $baseSlug = trim($fallback);
        }

        if ($baseSlug === '') {
            $baseSlug = 'item';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (in_array($slug, $used, true)) {
            $slug = $baseSlug.'_'.$counter;
            $counter++;
        }

        $used[] = $slug;

        return $slug;
    }

    private function parsePercentWeight(mixed $value, float $fallback): float
    {
        $parsed = $this->parseNumeric($value);
        if ($parsed === null) {
            return $this->normalizeWeight($fallback);
        }

        return $this->normalizeWeight($parsed / 100);
    }

    private function parseNumeric(mixed $value): ?float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $normalized = str_replace(',', '.', trim($value));
            if ($normalized === '' || !is_numeric($normalized)) {
                return null;
            }

            return (float) $normalized;
        }

        return null;
    }

    private function normalizeWeight(mixed $weight): float
    {
        $value = $this->asFloat($weight);
        if ($value > 1) {
            $value /= 100;
        }

        if ($value < 0) {
            $value = 0;
        }

        if ($value > 1) {
            $value = 1;
        }

        return round($value, 6);
    }

    private function normalizeUpdatedBy(?string $updatedBy): ?string
    {
        $value = trim((string) $updatedBy);

        return $value !== '' ? Str::limit($value, 100, '') : null;
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (int) $value === 1;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }

    private function asFloat(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $normalized = str_replace(',', '.', trim($value));
            if ($normalized === '' || !is_numeric($normalized)) {
                return 0.0;
            }

            return (float) $normalized;
        }

        return 0.0;
    }

    /**
     * @return array<string, mixed>
     */
    private function arrayOrEmpty(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private function topLevelElementSlug(string $slug): string
    {
        $value = trim($slug);
        if ($value === '') {
            return '';
        }

        if (preg_match('/^(element\d+)/', $value, $matches)) {
            return (string) ($matches[1] ?? '');
        }

        return Str::before($value, '_');
    }

    private function tableFromModelClass(string $modelClass): ?string
    {
        $className = trim($modelClass);
        if ($className === '' || !class_exists($className) || !is_subclass_of($className, Model::class)) {
            return null;
        }

        try {
            $table = trim((string) (new $className())->getTable());
        } catch (\Throwable) {
            return null;
        }

        return $table !== '' ? $table : null;
    }

    /**
     * @return array<int, string>
     */
    private function progressArchiveTargetTables(): array
    {
        $subtopicModules = array_merge(
            array_values(array_filter((array) config('element_subtopic_modules.modules', []), 'is_array')),
            array_values(array_filter($this->subtopicModules(false), 'is_array'))
        );

        $tables = [];
        foreach ($subtopicModules as $module) {
            $modelTable = $this->tableFromModelClass((string) ($module['model'] ?? ''));
            if ($modelTable !== null && $this->schemaMetadataCache->hasTable($modelTable)) {
                $tables[$modelTable] = true;
            }

            $editLogTable = $this->tableFromModelClass((string) ($module['edit_log_model'] ?? ''));
            if ($editLogTable !== null && $this->schemaMetadataCache->hasTable($editLogTable)) {
                $tables[$editLogTable] = true;
            }
        }

        foreach ([
            'element_preferences',
            'element_assessments',
            'element1_kegiatan_asurans_doc_selections',
            'element1_kegiatan_asurans_row_doc_selections',
        ] as $table) {
            if ($this->schemaMetadataCache->hasTable($table)) {
                $tables[$table] = true;
            }
        }

        return array_values(array_filter(
            array_keys($tables),
            fn (string $table): bool => !$this->isProgressArchiveExcludedTable($table)
        ));
    }

    /**
     * @return array<int, string>
     */
    private function progressArchiveExcludedTables(): array
    {
        return [
            'notifications',
            'notification_reads',
        ];
    }

    private function isProgressArchiveExcludedTable(string $table): bool
    {
        return in_array(trim($table), $this->progressArchiveExcludedTables(), true);
    }

    /**
     * @return array{
     *     version: int,
     *     captured_at: string,
     *     tables: array<string, array{columns: array<int, string>, rows: array<int, array<string, mixed>>, count: int}>,
     *     total_rows: int
     * }
     */
    private function buildProgressSnapshot(): array
    {
        $tables = $this->progressArchiveTargetTables();
        $snapshotTables = [];
        $totalRows = 0;

        foreach ($tables as $table) {
            if (!$this->schemaMetadataCache->hasTable($table)) {
                continue;
            }

            $columns = $this->schemaMetadataCache->columnListing($table);
            if (count($columns) === 0) {
                continue;
            }

            $query = DB::table($table);
            if (in_array('id', $columns, true)) {
                $query->orderBy('id');
            }

            $rows = $query
                ->get($columns)
                ->map(static fn ($row): array => (array) $row)
                ->all();

            $rowsCount = count($rows);
            $snapshotTables[$table] = [
                'columns' => $columns,
                'rows' => $rows,
                'count' => $rowsCount,
            ];
            $totalRows += $rowsCount;
        }

        return [
            'version' => 1,
            'captured_at' => now()->toIso8601String(),
            'tables' => $snapshotTables,
            'total_rows' => $totalRows,
        ];
    }

    private function seedDefaultSubtopicRows(): void
    {
        $subtopicModules = array_values(array_filter($this->subtopicModules(false), 'is_array'));
        if (count($subtopicModules) === 0) {
            return;
        }

        foreach ($subtopicModules as $module) {
            $table = $this->tableFromModelClass((string) ($module['model'] ?? ''));
            if ($table === null || !$this->schemaMetadataCache->hasTable($table)) {
                continue;
            }

            $rows = (array) ($module['rows'] ?? []);
            if (count($rows) === 0) {
                continue;
            }

            foreach ($rows as $rowId => $label) {
                $id = (int) $rowId;
                $statement = trim((string) $label);

                if ($id <= 0 || $statement === '') {
                    continue;
                }

                DB::table($table)->updateOrInsert(
                    ['id' => $id],
                    ['pernyataan' => $statement]
                );
            }
        }
    }

    private function clearCache(): void
    {
        $this->cachedStructure = null;
        $this->cachedSummaryModules = null;
        $this->cachedSubtopicModules = null;
    }
}

