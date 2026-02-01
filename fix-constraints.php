#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING CONFERENCES TABLE ===\n\n";

// Get all CHECK constraints on conferences table
echo "1. Finding CHECK constraints...\n";
$checkConstraints = DB::select("
    SELECT cc.CONSTRAINT_NAME, cc.CHECK_CLAUSE
    FROM information_schema.CHECK_CONSTRAINTS cc
    JOIN information_schema.TABLE_CONSTRAINTS tc 
        ON cc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
    WHERE tc.TABLE_SCHEMA = 'conf2' 
    AND tc.TABLE_NAME = 'conferences'
    AND tc.CONSTRAINT_TYPE = 'CHECK'
");

if (empty($checkConstraints)) {
    echo "   No CHECK constraints found.\n\n";
} else {
    echo "   Found " . count($checkConstraints) . " CHECK constraint(s):\n";
    foreach ($checkConstraints as $constraint) {
        echo "   - {$constraint->CONSTRAINT_NAME}\n";
        echo "     Clause: {$constraint->CHECK_CLAUSE}\n";
    }
    echo "\n";
}

// Drop all CHECK constraints
echo "2. Dropping CHECK constraints...\n";
foreach ($checkConstraints as $constraint) {
    try {
        $sql = "ALTER TABLE conferences DROP CONSTRAINT `{$constraint->CONSTRAINT_NAME}`";
        DB::statement($sql);
        echo "   ✓ Dropped: {$constraint->CONSTRAINT_NAME}\n";
    } catch (\Exception $e) {
        echo "   ✗ Failed to drop {$constraint->CONSTRAINT_NAME}: {$e->getMessage()}\n";
    }
}

echo "\n=== REPAIR COMPLETE ===\n";
echo "\nPlease try creating a conference again.\n";
