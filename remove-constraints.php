#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== REMOVING JSON CONSTRAINTS ===\n\n";

// Get all CHECK constraints
$checkConstraints = DB::select("
    SELECT cc.CONSTRAINT_NAME, cc.CHECK_CLAUSE
    FROM information_schema.CHECK_CONSTRAINTS cc
    JOIN information_schema.TABLE_CONSTRAINTS tc 
        ON cc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
    WHERE tc.TABLE_SCHEMA = 'conf2' 
    AND tc.TABLE_NAME = 'conferences'
    AND tc.CONSTRAINT_TYPE = 'CHECK'
    AND (cc.CHECK_CLAUSE LIKE '%json_valid%')
");

echo "Found " . count($checkConstraints) . " JSON validation constraint(s):\n\n";

foreach ($checkConstraints as $constraint) {
    echo "Constraint: {$constraint->CONSTRAINT_NAME}\n";
    echo "Clause: {$constraint->CHECK_CLAUSE}\n";

    // Try different methods to drop the constraint
    $dropped = false;

    // Method 1: DROP CONSTRAINT
    try {
        DB::statement("ALTER TABLE conferences DROP CONSTRAINT `{$constraint->CONSTRAINT_NAME}`");
        echo "✓ Successfully dropped using DROP CONSTRAINT\n\n";
        $dropped = true;
    } catch (\Exception $e) {
        echo "✗ DROP CONSTRAINT failed: {$e->getMessage()}\n";
    }

    if (!$dropped) {
        // Method 2: DROP CHECK
        try {
            DB::statement("ALTER TABLE conferences DROP CHECK `{$constraint->CONSTRAINT_NAME}`");
            echo "✓ Successfully dropped using DROP CHECK\n\n";
            $dropped = true;
        } catch (\Exception $e) {
            echo "✗ DROP CHECK failed: {$e->getMessage()}\n";
        }
    }

    if (!$dropped) {
        // Method 3: Without backticks
        try {
            DB::statement("ALTER TABLE conferences DROP CONSTRAINT {$constraint->CONSTRAINT_NAME}");
            echo "✓ Successfully dropped without backticks\n\n";
            $dropped = true;
        } catch (\Exception $e) {
            echo "✗ Without backticks failed: {$e->getMessage()}\n\n";
        }
    }
}

echo "=== DONE ===\n";
