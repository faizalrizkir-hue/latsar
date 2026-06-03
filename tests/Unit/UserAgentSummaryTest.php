<?php

namespace Tests\Unit;

use App\Support\UserAgentSummary;
use Tests\TestCase;

class UserAgentSummaryTest extends TestCase
{
    public function test_it_summarizes_chrome_on_windows(): void
    {
        $summary = UserAgentSummary::summarize(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36'
        );

        $this->assertTrue($summary['available']);
        $this->assertSame('Google Chrome 125', $summary['browser_label']);
        $this->assertSame('Desktop', $summary['device']);
        $this->assertSame('Windows 10/11', $summary['os']);
    }

    public function test_it_summarizes_edge_before_chrome(): void
    {
        $summary = UserAgentSummary::summarize(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.2535.67'
        );

        $this->assertSame('Microsoft Edge 125', $summary['browser_label']);
        $this->assertSame('Windows 10/11', $summary['os']);
    }

    public function test_it_summarizes_mobile_safari_on_iphone(): void
    {
        $summary = UserAgentSummary::summarize(
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1'
        );

        $this->assertSame('Safari 17.5', $summary['browser_label']);
        $this->assertSame('iPhone', $summary['device']);
        $this->assertSame('iOS 17.5', $summary['os']);
    }

    public function test_empty_user_agent_is_unavailable(): void
    {
        $summary = UserAgentSummary::summarize('');

        $this->assertFalse($summary['available']);
        $this->assertSame('-', $summary['browser_label']);
    }
}
