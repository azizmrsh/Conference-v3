#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FINAL FIX - REMOVING ALL JSON CONSTRAINTS ===\n\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0');

// Get all constraints
$constraints = DB::select("
    SELECT 
        tc.TABLE_NAME,
        cc.CONSTRAINT_NAME,
        cc.CHECK_CLAUSE
    FROM information_schema.CHECK_CONSTRAINTS cc
    JOIN information_schema.TABLE_CONSTRAINTS tc 
        ON cc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
    WHERE tc.TABLE_SCHEMA = 'conf2' 
    AND tc.CONSTRAINT_TYPE = 'CHECK'
    AND cc.CHECK_CLAUSE LIKE '%json_valid%'
");

echo "Found " . count($constraints) . " JSON constraints\n\n";

// Group by table
$byTable = [];
foreach ($constraints as $c) {
    $byTable[$c->TABLE_NAME][] = $c;
}

foreach ($byTable as $table => $tableConstraints) {
    echo "Table: {$table}\n";

    // Backup data
    $data = DB::table($table)->get();
    echo "  Backed up " . count($data) . " rows\n";

    // Get CREATE statement
    $result = DB::select("SHOW CREATE TABLE {$table}");
    $createStmt = $result[0]->{'Create Table'};

    // Remove ALL CONSTRAINT clauses with json_valid
    $createStmt = preg_replace('/,\s*CONSTRAINT\s+`[^`]+`\s+CHECK\s*\([^)]*json_valid[^)]*\)/i', '', $createStmt);

    // Also try without backticks
    $createStmt = preg_replace('/,\s*CONSTRAINT\s+\w+\s+CHECK\s*\([^)]*json_valid[^)]*\)/i', '', $createStmt);

    // Drop and recreate
    DB::statement("DROP TABLE {$table}");
    DB::statement($createStmt);

    // Restore data
    if (count($data) > 0) {
        foreach ($data as $row) {
            DB::table($table)->insert((array) $row);
        }
    }

    echo "  ✓ Fixed\n\n";
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "=== COMPLETE ===\n";

// Verify
$remaining = DB::select("
    SELECT COUNT(*) as count
    FROM information_schema.CHECK_CONSTRAINTS cc
    JOIN information_schema.TABLE_CONSTRAINTS tc 
        ON cc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
    WHERE tc.TABLE_SCHEMA = 'conf2' 
    AND cc.CHECK_CLAUSE LIKE '%json_valid%'
");

echo "Remaining JSON constraints: " . $remaining[0]->count . "\n";
