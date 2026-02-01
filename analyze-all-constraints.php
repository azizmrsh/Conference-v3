#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== COMPREHENSIVE DATABASE CONSTRAINTS FIX ===\n\n";

// Get all CHECK constraints with json_valid
echo "Step 1: Finding all JSON validation constraints...\n";
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
    ORDER BY tc.TABLE_NAME, cc.CONSTRAINT_NAME
");

echo "Found " . count($constraints) . " JSON validation constraints:\n\n";

// Group by table
$tableGroups = [];
foreach ($constraints as $constraint) {
    if (!isset($tableGroups[$constraint->TABLE_NAME])) {
        $tableGroups[$constraint->TABLE_NAME] = [];
    }
    $tableGroups[$constraint->TABLE_NAME][] = $constraint;
}

foreach ($tableGroups as $tableName => $tableConstraints) {
    echo "Table: {$tableName}\n";
    foreach ($tableConstraints as $c) {
        echo "  - {$c->CONSTRAINT_NAME}: {$c->CHECK_CLAUSE}\n";
    }
    echo "\n";
}

echo "\nStep 2: Analyzing which constraints should be removed...\n";
echo "The following constraints appear to be on regular text fields:\n\n";

// Constraints that are likely wrong (title, session_title, header, etc.)
$suspectConstraints = [];
foreach ($constraints as $constraint) {
    $clause = strtolower($constraint->CHECK_CLAUSE);
    // If it's checking json_valid on fields like title, name, header, value (standalone)
    if (preg_match('/json_valid\(`?(title|name|header|value|description|content)`?\)/', $clause)) {
        $suspectConstraints[] = $constraint;
    }
}

foreach ($suspectConstraints as $c) {
    echo "  {$c->TABLE_NAME}.{$c->CONSTRAINT_NAME}\n";
}

echo "\nStep 3: Rebuilding tables without problematic constraints...\n";
echo "Do you want to proceed? (This will rebuild the affected tables)\n";
echo "Tables to rebuild:\n";

$tablesToRebuild = array_unique(array_map(function ($c) {
    return $c->TABLE_NAME;
}, $suspectConstraints));

foreach ($tablesToRebuild as $table) {
    echo "  - {$table}\n";
}

echo "\n";
