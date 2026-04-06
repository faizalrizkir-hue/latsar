<?php

namespace App\Support;

final class DashboardHomeViewNormalizer
{
    /**
     * @param array<string, float|int|string> $elementWeights
     * @param array<string, mixed> $overallLevelData
     * @param array<string, mixed> $overallLevelDataQa
     * @return array<string, mixed>
     */
    public static function buildUiMeta(
        array $elementWeights,
        array $overallLevelData,
        array $overallLevelDataQa
    ): array {
        $overallLevel = is_numeric($overallLevelData['level'] ?? null)
            ? (int) $overallLevelData['level']
            : null;
        $overallLevelQa = is_numeric($overallLevelDataQa['level'] ?? null)
            ? (int) $overallLevelDataQa['level']
            : null;

        $weightHintItems = collect($elementWeights)
            ->map(function ($elementWeight, $elementSlug) {
                $slug = (string) $elementSlug;
                $label = $slug;
                if (preg_match('/^element(\d+)$/', $slug, $matches)) {
                    $label = 'Element '.$matches[1];
                }

                return '- '.$label.': '.number_format((float) $elementWeight * 100, 0).'%';
            })
            ->values()
            ->implode("\n");

        $segmentArcLength = 314.16;
        $segmentLength = $segmentArcLength / 5;

        return [
            'page_title' => 'Dashboard Kapabilitas APIP',
            'overall_level' => $overallLevel,
            'overall_level_class' => $overallLevel !== null ? 'is-level-'.$overallLevel : 'pending',
            'overall_level_label' => $overallLevel !== null ? 'Level '.$overallLevel : 'Belum Dinilai',
            'overall_predikat' => (string) ($overallLevelData['predikat'] ?? 'Belum Dinilai'),
            'overall_description' => trim((string) ($overallLevelData['description'] ?? '')),
            'overall_level_qa' => $overallLevelQa,
            'overall_level_qa_class' => $overallLevelQa !== null ? 'is-level-'.$overallLevelQa : 'pending',
            'overall_level_qa_label' => $overallLevelQa !== null ? 'Level '.$overallLevelQa : 'Belum Dinilai',
            'overall_predikat_qa' => (string) ($overallLevelDataQa['predikat'] ?? 'Belum Dinilai'),
            'overall_description_qa' => trim((string) ($overallLevelDataQa['description'] ?? '')),
            'weight_hint_text' => trim("Bobot skor tertimbang:\n".$weightHintItems),
            'weighted_score_hint_text' => 'Rumus Skor Tertimbang: Bobot (%) x Skor',
            'level_predikat_hint_text' => trim(
                "Informasi Level Kapabilitas APIP:\n"
                ."Level 1 - Rintisan (0,00 - 1,98)\n"
                ."Level 2 - Terstruktur (1,99 - 2,98)\n"
                ."Level 3 - Memadai (2,99 - 3,98)\n"
                ."Level 4 - Terintegrasi (3,99 - 4,99)\n"
                ."Level 5 - Optimal (5,00)"
            ),
            'segment_arc_length' => $segmentArcLength,
            'segment_length' => $segmentLength,
            'segment_offsets' => [
                0,
                -$segmentLength,
                -$segmentLength * 2,
                -$segmentLength * 3,
                -$segmentLength * 4,
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $elements
     * @param array<int, string>|null $accessibleElementSlugs
     * @return array<int, array<string, mixed>>
     */
    public static function enrichElements(array $elements, ?array $accessibleElementSlugs): array
    {
        $isPublicAccess = $accessibleElementSlugs === null;
        $allowedElementSlugSet = [];
        if (is_array($accessibleElementSlugs)) {
            foreach ($accessibleElementSlugs as $allowedSlug) {
                $normalized = trim((string) $allowedSlug);
                if ($normalized !== '') {
                    $allowedElementSlugSet[$normalized] = true;
                }
            }
        }

        $normalizedElements = [];
        foreach ($elements as $element) {
            $item = is_array($element) ? $element : [];
            $elementSlug = trim((string) ($item['slug'] ?? ''));

            $level = is_numeric($item['level'] ?? null) ? (int) $item['level'] : null;
            $qaLevel = is_numeric($item['qa_level'] ?? null) ? (int) $item['qa_level'] : null;

            $subtopicsRaw = is_array($item['subtopics'] ?? null) ? $item['subtopics'] : [];
            $subtopics = [];
            $statementCount = 0;
            $subtopicWeightTotal = 0.0;
            $subtopicContributionTotal = 0.0;
            $subtopicQaContributionTotal = 0.0;

            foreach ($subtopicsRaw as $subtopic) {
                $subtopicItem = is_array($subtopic) ? $subtopic : [];
                $subtopicLevel = is_numeric($subtopicItem['level'] ?? null) ? (int) $subtopicItem['level'] : null;
                $subtopicQaLevel = is_numeric($subtopicItem['qa_level'] ?? null) ? (int) $subtopicItem['qa_level'] : null;

                $rowsTotal = (int) ($subtopicItem['rows_total'] ?? 0);
                if ($rowsTotal > 0) {
                    $statementCount += $rowsTotal;
                } elseif ((bool) ($subtopicItem['has_data'] ?? false)) {
                    $statementCount += 1;
                }

                $subtopicWeightTotal += (float) ($subtopicItem['weight'] ?? 0);
                $subtopicContributionTotal += (float) ($subtopicItem['weighted_score'] ?? 0);
                $subtopicQaContributionTotal += (float) ($subtopicItem['qa_weighted_score'] ?? 0);

                $subtopicItem['level_class'] = $subtopicLevel !== null ? 'is-level-'.$subtopicLevel : 'pending';
                $subtopicItem['qa_level_class'] = $subtopicQaLevel !== null ? 'is-level-'.$subtopicQaLevel : 'pending';
                $subtopicItem['level_label'] = $subtopicLevel !== null ? 'L'.$subtopicLevel : '-';
                $subtopicItem['qa_level_label'] = $subtopicQaLevel !== null ? 'L'.$subtopicQaLevel : '-';
                $subtopicItem['level_note'] = self::normalizeDescription(
                    (string) ($subtopicItem['level_note'] ?? $subtopicItem['description'] ?? ''),
                    'Belum ada deskripsi level sub topik.'
                );
                $subtopicItem['qa_level_note'] = self::normalizeDescription(
                    (string) ($subtopicItem['qa_level_note'] ?? $subtopicItem['qa_description'] ?? ''),
                    'Belum ada deskripsi level QA sub topik.'
                );

                $subtopics[] = $subtopicItem;
            }

            $item['can_open'] = $isPublicAccess || isset($allowedElementSlugSet[$elementSlug]);
            $item['level_class'] = $level !== null ? 'is-level-'.$level : 'pending';
            $item['qa_level_class'] = $qaLevel !== null ? 'is-level-'.$qaLevel : 'pending';
            $item['level_label'] = $level !== null ? 'L'.$level : '-';
            $item['qa_level_label'] = $qaLevel !== null ? 'L'.$qaLevel : '-';
            $item['description'] = self::normalizeDescription(
                (string) ($item['description'] ?? ''),
                'Belum ada keterangan level untuk element ini.'
            );
            $item['qa_description'] = self::normalizeDescription(
                (string) ($item['qa_description'] ?? ''),
                'Belum ada keterangan level QA untuk element ini.'
            );
            $item['subtopics'] = $subtopics;
            $item['subtopic_count'] = (int) ($item['subtopic_count'] ?? count($subtopics));
            $item['statement_count'] = $statementCount;
            $item['subtopic_weight_total'] = (float) number_format($subtopicWeightTotal, 6, '.', '');
            $item['subtopic_weight_total_percent'] = self::formatPercent($subtopicWeightTotal * 100);
            $item['subtopic_contribution_total'] = (float) number_format($subtopicContributionTotal, 2, '.', '');
            $item['subtopic_qa_contribution_total'] = (float) number_format($subtopicQaContributionTotal, 2, '.', '');

            $normalizedElements[] = $item;
        }

        return $normalizedElements;
    }

    private static function normalizeDescription(string $value, string $fallback): string
    {
        $normalized = trim((string) preg_replace('/\s+/u', ' ', $value));
        if ($normalized === '') {
            return $fallback;
        }

        return $normalized;
    }

    private static function formatPercent(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
