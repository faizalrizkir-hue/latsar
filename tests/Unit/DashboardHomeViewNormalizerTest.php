<?php

namespace Tests\Unit;

use App\Support\DashboardHomeViewNormalizer;
use PHPUnit\Framework\TestCase;

class DashboardHomeViewNormalizerTest extends TestCase
{
    public function test_build_ui_meta_maps_levels_hints_and_meter_segments(): void
    {
        $meta = DashboardHomeViewNormalizer::buildUiMeta(
            ['element1' => 0.40, 'element2' => 0.20],
            ['level' => 3, 'predikat' => 'Memadai', 'description' => 'Kondisi mandiri.'],
            ['level' => 4, 'predikat' => 'Terintegrasi', 'description' => 'Kondisi QA.']
        );

        $this->assertSame('Dashboard Kapabilitas APIP', $meta['page_title']);
        $this->assertSame(3, $meta['overall_level']);
        $this->assertSame('is-level-3', $meta['overall_level_class']);
        $this->assertSame('Level 3', $meta['overall_level_label']);
        $this->assertSame('Memadai', $meta['overall_predikat']);
        $this->assertStringContainsString('Element 1: 40%', (string) $meta['weight_hint_text']);
        $this->assertStringContainsString('Element 2: 20%', (string) $meta['weight_hint_text']);
        $this->assertCount(5, (array) $meta['segment_offsets']);
        $this->assertSame('is-level-4', $meta['overall_level_qa_class']);
    }

    public function test_enrich_elements_adds_open_state_classes_and_aggregates(): void
    {
        $elements = [
            [
                'slug' => 'element1',
                'title' => 'Element 1',
                'level' => 3,
                'qa_level' => 2,
                'description' => '',
                'qa_description' => '',
                'subtopic_count' => 2,
                'subtopics' => [
                    [
                        'slug' => 'element1_kegiatan_asurans',
                        'title' => 'Sub Topik 1',
                        'rows_total' => 2,
                        'has_data' => true,
                        'weight' => 0.80,
                        'weighted_score' => 0.40,
                        'qa_weighted_score' => 0.35,
                        'level' => 3,
                        'qa_level' => 2,
                        'description' => '',
                        'qa_description' => '',
                    ],
                    [
                        'slug' => 'element1_jasa_konsultansi',
                        'title' => 'Sub Topik 2',
                        'rows_total' => 0,
                        'has_data' => true,
                        'weight' => 0.20,
                        'weighted_score' => 0.10,
                        'qa_weighted_score' => 0.05,
                        'level' => null,
                        'qa_level' => null,
                    ],
                ],
            ],
            [
                'slug' => 'element2',
                'title' => 'Element 2',
                'subtopics' => [],
            ],
        ];

        $normalized = DashboardHomeViewNormalizer::enrichElements($elements, ['element1']);

        $this->assertCount(2, $normalized);

        $element1 = $normalized[0];
        $this->assertTrue((bool) $element1['can_open']);
        $this->assertSame('is-level-3', $element1['level_class']);
        $this->assertSame('L3', $element1['level_label']);
        $this->assertSame('Belum ada keterangan level untuk element ini.', $element1['description']);
        $this->assertSame(3, (int) $element1['statement_count']);
        $this->assertSame('100', (string) $element1['subtopic_weight_total_percent']);
        $this->assertSame(0.50, (float) $element1['subtopic_contribution_total']);
        $this->assertSame(0.40, (float) $element1['subtopic_qa_contribution_total']);
        $this->assertSame('Belum ada deskripsi level sub topik.', (string) $element1['subtopics'][0]['level_note']);
        $this->assertSame('L2', (string) $element1['subtopics'][0]['qa_level_label']);

        $element2 = $normalized[1];
        $this->assertFalse((bool) $element2['can_open']);
        $this->assertSame(0, (int) $element2['statement_count']);
    }
}

