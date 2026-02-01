#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$constraints = DB::select("
    SELECT CONSTRAINT_NAME, CHECK_CLAUSE 
    FROM information_schema.CHECK_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'conf2'
");

echo count($constraints) . " CHECK constraints found in database\n\n";

if (count($constraints) > 0) {
    foreach ($constraints as $c) {
        echo "  - {$c->CONSTRAINT_NAME}: {$c->CHECK_CLAUSE}\n";
    }
} else {
    echo "✓ No CHECK constraints found - table is clean!\n";
}
