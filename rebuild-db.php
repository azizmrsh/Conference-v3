#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== REBUILDING DATABASE FROM SCRATCH ===\n\n";

// Step 1: Drop database
echo "Step 1: Dropping database 'conf2'...\n";
try {
    DB::statement('DROP DATABASE IF EXISTS conf2');
    echo "  ✓ Database dropped\n\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Step 2: Create database
echo "Step 2: Creating database 'conf2'...\n";
try {
    DB::statement('CREATE DATABASE conf2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "  ✓ Database created\n\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Step 3: Use the new database
echo "Step 3: Switching to 'conf2'...\n";
try {
    DB::statement('USE conf2');
    echo "  ✓ Database selected\n\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "=== DATABASE READY ===\n\n";
echo "Now run: php artisan migrate --seed\n";
