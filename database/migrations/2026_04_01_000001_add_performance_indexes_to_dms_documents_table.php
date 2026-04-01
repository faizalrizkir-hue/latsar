<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'dms_documents';

    /**
     * @var array<string, array<int, string>>
     */
    private array $indexMap = [
        'idx_dms_deleted_status_updated' => ['deleted_at', 'status', 'updated_at'],
        'idx_dms_status_deleted' => ['status', 'deleted_at'],
        'idx_dms_tag' => ['tag'],
    ];

    public function up(): void
    {
        if (!Schema::hasTable($this->tableName)) {
            return;
        }

        $existingIndexes = $this->existingIndexes();

        Schema::table($this->tableName, function (Blueprint $table) use ($existingIndexes): void {
            foreach ($this->indexMap as $indexName => $columns) {
                if (in_array($indexName, $existingIndexes, true) || !$this->hasAllColumns($columns)) {
                    continue;
                }

                $table->index($columns, $indexName);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable($this->tableName)) {
            return;
        }

        $existingIndexes = $this->existingIndexes();

        Schema::table($this->tableName, function (Blueprint $table) use ($existingIndexes): void {
            foreach (array_keys($this->indexMap) as $indexName) {
                if (in_array($indexName, $existingIndexes, true)) {
                    $table->dropIndex($indexName);
                }
            }
        });
    }

    /**
     * @return array<int, string>
     */
    private function existingIndexes(): array
    {
        $rows = DB::select(sprintf('SHOW INDEX FROM `%s`', $this->tableName));
        $indexNames = [];

        foreach ($rows as $row) {
            if (isset($row->Key_name)) {
                $indexNames[] = (string) $row->Key_name;
            }
        }

        return array_values(array_unique($indexNames));
    }

    /**
     * @param array<int, string> $columns
     */
    private function hasAllColumns(array $columns): bool
    {
        foreach ($columns as $column) {
            if (!Schema::hasColumn($this->tableName, $column)) {
                return false;
            }
        }

        return true;
    }
};
