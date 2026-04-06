<?php

namespace Tests\Unit;

use App\Services\SchemaMetadataCache;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaMetadataCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('schema_metadata_cache_test')) {
            Schema::create('schema_metadata_cache_test', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->nullable();
            });
        }
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('schema_metadata_cache_test');
        parent::tearDown();
    }

    public function test_schema_metadata_cache_reads_table_and_column_information(): void
    {
        Config::set('performance.schema_metadata_ttl_seconds', 600);

        /** @var SchemaMetadataCache $service */
        $service = app(SchemaMetadataCache::class);

        $this->assertTrue($service->hasTable('schema_metadata_cache_test'));
        $this->assertTrue($service->hasColumn('schema_metadata_cache_test', 'name'));
        $this->assertFalse($service->hasColumn('schema_metadata_cache_test', 'missing_column'));

        $columns = $service->columnListing('schema_metadata_cache_test');
        $this->assertContains('id', $columns);
        $this->assertContains('name', $columns);
    }

    public function test_bump_version_invalidates_cached_table_presence(): void
    {
        /** @var SchemaMetadataCache $service */
        $service = app(SchemaMetadataCache::class);

        $this->assertTrue($service->hasTable('schema_metadata_cache_test'));
        Schema::dropIfExists('schema_metadata_cache_test');

        // Cached value still true before bump.
        $this->assertTrue($service->hasTable('schema_metadata_cache_test'));

        $service->bumpVersion();
        $this->assertFalse($service->hasTable('schema_metadata_cache_test'));
    }
}
