#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DATABASE DIAGNOSTIC TOOL ===\n\n";

// 1. Check database connection
echo "1. Testing database connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✓ Database connected successfully\n\n";
} catch (\Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Get all constraints
echo "2. Checking constraints on 'conferences' table...\n";
try {
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE, TABLE_NAME
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = 'conf2' 
        AND TABLE_NAME = 'conferences'
    ");

    if (empty($constraints)) {
        echo "   No constraints found\n\n";
    } else {
        foreach ($constraints as $constraint) {
            echo "   - {$constraint->CONSTRAINT_NAME} ({$constraint->CONSTRAINT_TYPE})\n";
        }
        echo "\n";
    }
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// 3. Get check constraints specifically
echo "3. Checking CHECK constraints...\n";
try {
    $checkConstraints = DB::select("
        SELECT CONSTRAINT_NAME, CHECK_CLAUSE
        FROM information_schema.CHECK_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = 'conf2'
    ");

    if (empty($checkConstraints)) {
        echo "   No CHECK constraints found\n\n";
    } else {
        foreach ($checkConstraints as $check) {
            echo "   - {$check->CONSTRAINT_NAME}: {$check->CHECK_CLAUSE}\n";
        }
        echo "\n";
    }
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// 4. Get table structure
echo "4. Table structure:\n";
try {
    $columns = DB::select("DESCRIBE conferences");
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) " .
            ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') .
            ($column->Default ? " DEFAULT {$column->Default}" : '') . "\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// 5. Get CREATE TABLE statement
echo "5. Full CREATE TABLE statement:\n";
try {
    $createTable = DB::select("SHOW CREATE TABLE conferences");
    echo "   " . $createTable[0]->{'Create Table'} . "\n\n";
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// 6. Check migrations status
echo "6. Migrations status:\n";
try {
    $migrations = DB::table('migrations')
        ->where('migration', 'like', '%conferences%')
        ->orderBy('id')
        ->get();

    foreach ($migrations as $migration) {
        echo "   - {$migration->migration} (batch: {$migration->batch})\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

echo "=== DIAGNOSTIC COMPLETE ===\n";
