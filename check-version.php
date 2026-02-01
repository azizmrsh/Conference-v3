#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$version = DB::select('SELECT VERSION() as version');
echo "MySQL/MariaDB Version: " . $version[0]->version . "\n";

// Check for specific problematic constraints on conference_topics
$result = DB::select("SELECT CHECK_CLAUSE FROM information_schema.CHECK_CONSTRAINTS WHERE CONSTRAINT_NAME = 'title' AND CONSTRAINT_SCHEMA = 'conf2'");
if (count($result) > 0) {
    echo "Constraint on title: " . $result[0]->CHECK_CLAUSE . "\n";
}
