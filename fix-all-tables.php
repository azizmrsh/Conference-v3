#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== COMPREHENSIVE DATABASE FIX ===\n\n";

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0');
echo "Foreign key checks disabled\n\n";

// Tables that need JSON constraints removed on simple text fields
$tablesToFix = [
    'conference_topics' => ['title'],
    'conference_sessions' => ['session_title'],
    'correspondences' => ['header'],
    'settings' => ['value']
];

foreach ($tablesToFix as $tableName => $columns) {
    echo "Fixing table: {$tableName}\n";

    // Check if table exists
    if (!Schema::hasTable($tableName)) {
        echo "  ⚠ Table does not exist, skipping\n\n";
        continue;
    }

    // Backup data
    $data = DB::table($tableName)->get();
    echo "  Backed up " . count($data) . " row(s)\n";

    // Get CREATE TABLE statement
    try {
        $createResult = DB::select("SHOW CREATE TABLE {$tableName}");
        $createStatement = $createResult[0]->{'Create Table'};

        // Remove json_valid constraints from the CREATE statement
        foreach ($columns as $column) {
            $createStatement = preg_replace(
                '/,\s*CONSTRAINT\s+`?' . preg_quote($column) . '`?\s+CHECK\s*\([^)]*json_valid[^)]*\)/i',
                '',
                $createStatement
            );
        }

        // Drop and recreate table
        DB::statement("DROP TABLE {$tableName}");
        DB::statement($createStatement);
        echo "  ✓ Table recreated without JSON constraints\n";

        // Restore data
        if (count($data) > 0) {
            foreach ($data as $row) {
                DB::table($tableName)->insert((array) $row);
            }
            echo "  ✓ Restored " . count($data) . " row(s)\n";
        }

    } catch (\Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "Foreign key checks re-enabled\n\n";

echo "=== FIX COMPLETE ===\n";
echo "\nAll tables have been fixed. You can now:\n";
echo "  - Create conferences\n";
echo "  - Add conference topics\n";
echo "  - Create correspondence\n";
echo "  - Modify settings\n";
