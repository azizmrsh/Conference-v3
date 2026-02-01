#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== REBUILDING AFFECTED TABLES ===\n\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0');

$tables = ['conference_topics', 'conference_sessions', 'correspondences', 'settings'];

foreach ($tables as $table) {
    echo "Rebuilding {$table}...\n";

    // Backup
    $data = DB::table($table)->get();
    echo "  Backed up " . count($data) . " rows\n";

    // Drop and recreate using migrations
    Schema::dropIfExists($table);

    // Run specific migration based on table
    $migrationMap = [
        'conference_topics' => '2025_12_02_000001_create_conference_topics_table.php',
        'conference_sessions' => '2025_11_26_000180_create_conference_sessions_table.php',
        'correspondences' => '2025_11_27_000153_create_correspondences_table.php',
        'settings' => 'settings'
    ];

    if ($table == 'conference_topics') {
        Schema::create('conference_topics', function ($t) {
            $t->id();
            $t->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $t->string('title', 500);
            $t->integer('order')->default(0);
            $t->timestamps();
            $t->index('conference_id');
        });
    }

    // Restore data
    if (count($data) > 0) {
        foreach ($data as $row) {
            DB::table($table)->insert((array) $row);
        }
    }

    echo "  ✓ Done\n\n";
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "=== COMPLETE ===\n";
