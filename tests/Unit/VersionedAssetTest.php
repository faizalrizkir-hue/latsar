<?php

namespace Tests\Unit;

use App\Support\VersionedAsset;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class VersionedAssetTest extends TestCase
{
    public function test_uses_forced_asset_version_when_configured(): void
    {
        Config::set('app.asset_version', 'release-20260406');

        $url = VersionedAsset::url('css/login.css');

        $this->assertStringContainsString('/css/login.css', $url);
        $this->assertStringContainsString('v=release-20260406', $url);
    }

    public function test_returns_plain_asset_url_when_file_missing_and_no_forced_version(): void
    {
        Config::set('app.asset_version', '');

        $url = VersionedAsset::url('css/__missing-test-file.css');

        $this->assertStringContainsString('/css/__missing-test-file.css', $url);
        $this->assertStringNotContainsString('v=', $url);
    }
}
