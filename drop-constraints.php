#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== REMOVING JSON CONSTRAINTS USING ALTER TABLE ===\n\n";

// Get all JSON constraints
$constraints = DB::select("
    SELECT 
        tc.TABLE_NAME,
        tc.CONSTRAINT_NAME
    FROM information_schema.TABLE_CONSTRAINTS tc
    JOIN information_schema.CHECK_CONSTRAINTS cc
        ON tc.CONSTRAINT_NAME = cc.CONSTRAINT_NAME
    WHERE tc.TABLE_SCHEMA = 'conf2'
    AND tc.CONSTRAINT_TYPE = 'CHECK'
    AND cc.CHECK_CLAUSE LIKE '%json_valid%'
");

echo "Found " . count($constraints) . " JSON constraints to remove\n\n";

$success = 0;
$failed = 0;

foreach ($constraints as $constraint) {
    echo "Removing: {$constraint->TABLE_NAME}.{$constraint->CONSTRAINT_NAME}\n";

    $attempts = [
        "ALTER TABLE `{$constraint->TABLE_NAME}` DROP CONSTRAINT `{$constraint->CONSTRAINT_NAME}`",
        "ALTER TABLE `{$constraint->TABLE_NAME}` DROP CHECK `{$constraint->CONSTRAINT_NAME}`",
        "ALTER TABLE {$constraint->TABLE_NAME} DROP CONSTRAINT {$constraint->CONSTRAINT_NAME}",
        "ALTER TABLE {$constraint->TABLE_NAME} DROP CHECK {$constraint->CONSTRAINT_NAME}",
    ];

    $removed = false;
    foreach ($attempts as $sql) {
        try {
            DB::statement($sql);
            echo "  ✓ Removed\n";
            $success++;
            $removed = true;
            break;
        } catch (\Exception $e) {
            // Try next method
        }
    }

    if (!$removed) {
        echo "  ✗ Failed (constraint might be built-in)\n";
        $failed++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Successfully removed: {$success}\n";
echo "Failed: {$failed}\n";

// Check remaining
$remaining = DB::select("
    SELECT COUNT(*) as count
    FROM information_schema.TABLE_CONSTRAINTS tc
    JOIN information_schema.CHECK_CONSTRAINTS cc
        ON tc.CONSTRAINT_NAME = cc.CONSTRAINT_NAME
    WHERE tc.TABLE_SCHEMA = 'conf2'
    AND tc.CONSTRAINT_TYPE = 'CHECK'
    AND cc.CHECK_CLAUSE LIKE '%json_valid%'
");

echo "Remaining constraints: " . $remaining[0]->count . "\n";
