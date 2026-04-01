<?php

namespace Tests\Unit;

use App\Support\DashboardShellPayloadNormalizer;
use PHPUnit\Framework\TestCase;

class DashboardShellPayloadNormalizerTest extends TestCase
{
    public function test_sanitize_headnav_crumbs_sets_last_item_as_current(): void
    {
        $crumbs = [
            ['label' => ' Dashboard ', 'url' => ' /dashboard '],
            ['label' => '  ', 'url' => '/ignored'],
            ['label' => 'Element 1', 'url' => '/elements/element1', 'is_current' => true],
        ];

        $sanitized = DashboardShellPayloadNormalizer::sanitizeHeadnavCrumbs($crumbs);

        $this->assertCount(2, $sanitized);
        $this->assertSame('Dashboard', $sanitized[0]['label']);
        $this->assertSame('/dashboard', $sanitized[0]['url']);
        $this->assertFalse($sanitized[0]['is_current']);
        $this->assertSame('Element 1', $sanitized[1]['label']);
        $this->assertNull($sanitized[1]['url']);
        $this->assertTrue($sanitized[1]['is_current']);
    }

    public function test_sanitize_notification_realtime_channels_deduplicates_and_trims(): void
    {
        $channels = [' private-notifications.all ', '', 'private-notifications.all', 'private-notifications.element.element2'];

        $sanitized = DashboardShellPayloadNormalizer::sanitizeNotificationRealtimeChannels($channels);

        $this->assertSame(
            ['private-notifications.all', 'private-notifications.element.element2'],
            $sanitized
        );
    }

    public function test_sanitize_toast_queue_normalizes_type_and_filters_empty(): void
    {
        $toasts = [
            ['type' => 'SUCCESS', 'title' => '', 'message' => 'Saved'],
            ['type' => 'unknown', 'title' => 'Info', 'message' => 'Hello'],
            ['type' => 'error', 'title' => '', 'message' => ''],
            'invalid',
        ];

        $sanitized = DashboardShellPayloadNormalizer::sanitizeToastQueue($toasts);

        $this->assertCount(2, $sanitized);
        $this->assertSame('success', $sanitized[0]['type']);
        $this->assertSame('Berhasil', $sanitized[0]['title']);
        $this->assertSame('Saved', $sanitized[0]['message']);
        $this->assertSame('info', $sanitized[1]['type']);
        $this->assertSame('Info', $sanitized[1]['title']);
        $this->assertSame('Hello', $sanitized[1]['message']);
    }

    public function test_sanitize_notification_items_accepts_array_and_object(): void
    {
        $rawItems = collect([
            ['id' => 1, 'statement' => 'abc'],
            new class
            {
                public function toArray(): array
                {
                    return ['id' => 2, 'statement' => 'def'];
                }
            },
            (object) ['id' => 3, 'statement' => 'ghi'],
            null,
        ]);

        $sanitized = DashboardShellPayloadNormalizer::sanitizeNotificationItems($rawItems)->all();

        $this->assertCount(3, $sanitized);
        $this->assertTrue(is_array($sanitized[0]));
        $this->assertTrue(is_object($sanitized[1]));
        $this->assertTrue(is_object($sanitized[2]));
    }
}
