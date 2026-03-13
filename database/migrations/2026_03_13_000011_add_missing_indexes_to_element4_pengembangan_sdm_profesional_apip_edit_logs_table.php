<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'element4_pengembangan_sdm_profesional_apip_edit_logs';

    private array $indexMap = [
        'e4_psdpa_el_row_idx' => 'row_id',
        'e4_psdpa_el_user_idx' => 'username',
        'e4_psdpa_el_created_idx' => 'created_at',
    ];

    public function up(): void
    {
        if (!Schema::hasTable($this->tableName)) {
            return;
        }

        $existingIndexes = $this->existingIndexes();

        Schema::table($this->tableName, function (Blueprint $table) use ($existingIndexes) {
            foreach ($this->indexMap as $indexName => $columnName) {
                if (!in_array($indexName, $existingIndexes, true) && Schema::hasColumn($this->tableName, $columnName)) {
                    $table->index($columnName, $indexName);
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable($this->tableName)) {
            return;
        }

        $existingIndexes = $this->existingIndexes();

        Schema::table($this->tableName, function (Blueprint $table) use ($existingIndexes) {
            foreach (array_keys($this->indexMap) as $indexName) {
                if (in_array($indexName, $existingIndexes, true)) {
                    $table->dropIndex($indexName);
                }
            }
        });
    }

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
};
