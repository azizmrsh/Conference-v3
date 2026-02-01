#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING CONFERENCES TABLE ===\n\n";

// Disable foreign key checks
echo "Step 0: Disabling foreign key checks...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');
echo "   Foreign key checks disabled\n\n";

echo "Step 1: Backing up data...\n";
$conferences = DB::table('conferences')->get();
echo "   Backed up " . count($conferences) . " conference(s)\n\n";

echo "Step 2: Dropping and recreating table...\n";
DB::statement('DROP TABLE IF EXISTS conferences_backup');
DB::statement('CREATE TABLE conferences_backup LIKE conferences');
if (count($conferences) > 0) {
    DB::statement('INSERT INTO conferences_backup SELECT * FROM conferences');
}
echo "   Backup table created\n\n";

echo "Step 3: Dropping original table...\n";
DB::statement('DROP TABLE conferences');
echo "   Original table dropped\n\n";

echo "Step 4: Creating new table without JSON constraints...\n";
DB::statement("
    CREATE TABLE `conferences` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `session_number` int(11) DEFAULT NULL,
        `hijri_date` datetime DEFAULT NULL,
        `gregorian_date` datetime DEFAULT NULL,
        `location` varchar(255) DEFAULT NULL,
        `venue_name` varchar(255) DEFAULT NULL,
        `venue_address` varchar(512) DEFAULT NULL,
        `start_date` datetime NOT NULL,
        `end_date` datetime NOT NULL,
        `sessions_count` int(11) NOT NULL DEFAULT 0,
        `status` enum('planning','active','completed','archived') NOT NULL DEFAULT 'planning',
        `description` text DEFAULT NULL,
        `created_by` bigint(20) unsigned DEFAULT NULL,
        `updated_by` bigint(20) unsigned DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `conferences_created_by_foreign` (`created_by`),
        KEY `conferences_updated_by_foreign` (`updated_by`),
        CONSTRAINT `conferences_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
        CONSTRAINT `conferences_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "   New table created\n\n";

echo "Step 5: Restoring data...\n";
if (count($conferences) > 0) {
    DB::statement('INSERT INTO conferences SELECT * FROM conferences_backup');
    echo "   Data restored\n\n";
} else {
    echo "   No data to restore\n\n";
}

echo "Step 6: Cleaning up...\n";
DB::statement('DROP TABLE conferences_backup');
echo "   Backup table removed\n\n";

echo "Step 7: Re-enabling foreign key checks...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "   Foreign key checks re-enabled\n\n";

echo "=== DONE ===\n";
echo "The conferences table has been recreated without JSON constraints.\n";
echo "You can now create conferences with regular text in title and location fields.\n";
