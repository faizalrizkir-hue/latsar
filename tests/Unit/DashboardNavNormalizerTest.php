<?php

namespace Tests\Unit;

use App\Support\DashboardNavNormalizer;
use PHPUnit\Framework\TestCase;

class DashboardNavNormalizerTest extends TestCase
{
    public function test_sanitize_handles_collection_input_with_subtopic_collection(): void
    {
        $navElements = collect([
            [
                'slug' => 'element2',
                'title' => '',
                'nav_title' => '',
                'icon_label' => '',
                'coverage_percent' => 140,
                'custom_meta' => 'keep-me',
                'subtopics' => collect([
                    ['slug' => 'element2_pengembangan_informasi', 'title' => '', 'extra' => 'ok'],
                    ['slug' => '', 'title' => 'invalid'],
                ]),
            ],
        ]);

        $sanitized = DashboardNavNormalizer::sanitize($navElements)->all();

        $this->assertCount(1, $sanitized);
        $this->assertSame('element2', $sanitized[0]['slug']);
        $this->assertSame('2', $sanitized[0]['icon_label']);
        $this->assertSame(100, $sanitized[0]['coverage_percent']);
        $this->assertSame('keep-me', $sanitized[0]['custom_meta']);
        $this->assertCount(1, $sanitized[0]['subtopics']);
        $this->assertSame('element2_pengembangan_informasi', $sanitized[0]['subtopics'][0]['slug']);
        $this->assertNotSame('', $sanitized[0]['subtopics'][0]['title']);
        $this->assertSame('ok', $sanitized[0]['subtopics'][0]['extra']);
    }

    public function test_has_renderable_requires_element_slug_and_subtopic_slug(): void
    {
        $withoutSubtopics = [
            ['slug' => 'element1', 'subtopics' => []],
        ];
        $withSubtopics = [
            ['slug' => 'element1', 'subtopics' => [['slug' => 'element1_kegiatan_asurans']]],
        ];

        $this->assertFalse(DashboardNavNormalizer::hasRenderable($withoutSubtopics));
        $this->assertTrue(DashboardNavNormalizer::hasRenderable($withSubtopics));
    }

    public function test_sanitize_accepts_arrayable_object_items(): void
    {
        $arrayableElement = new class
        {
            public function toArray(): array
            {
                return [
                    'slug' => 'element3',
                    'subtopics' => [
                        new class
                        {
                            public function toArray(): array
                            {
                                return [
                                    'slug' => 'element3_perencanaan_pengawasan',
                                    'title' => 'Perencanaan Pengawasan',
                                ];
                            }
                        },
                    ],
                ];
            }
        };

        $sanitized = DashboardNavNormalizer::sanitize([$arrayableElement])->all();

        $this->assertCount(1, $sanitized);
        $this->assertSame('element3', $sanitized[0]['slug']);
        $this->assertCount(1, $sanitized[0]['subtopics']);
        $this->assertSame('element3_perencanaan_pengawasan', $sanitized[0]['subtopics'][0]['slug']);
    }
}
