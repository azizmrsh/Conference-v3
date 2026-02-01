#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ULTIMATE FIX - Using Raw SQL ===\n\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0');

// List of tables and their problematic columns
$fixes = [
    'conference_topics' => [
        'backup' => true,
        'sql' => "
            CREATE TABLE conference_topics_new (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                conference_id bigint(20) unsigned NOT NULL,
                title VARCHAR(500) NOT NULL,
                `order` int(11) NOT NULL DEFAULT 0,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY conference_topics_conference_id_index (conference_id),
                CONSTRAINT conference_topics_conference_id_foreign FOREIGN KEY (conference_id) REFERENCES conferences (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        "
    ]
];

foreach ($fixes as $table => $config) {
    echo "Fixing {$table}...\n";

    // Backup
    if ($config['backup']) {
        $data = DB::table($table)->get();
        echo "  Backed up " . count($data) . " rows\n";
    }

    // Create new table
    DB::statement($config['sql']);
    echo "  Created new table\n";

    // Copy data
    if ($config['backup'] && count($data) > 0) {
        DB::statement("INSERT INTO {$table}_new SELECT * FROM {$table}");
        echo "  Copied data\n";
    }

    // Swap tables
    DB::statement("DROP TABLE {$table}");
    DB::statement("RENAME TABLE {$table}_new TO {$table}");
    echo "  Swapped tables\n\n";
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "=== DONE ===\n";
echo "Try creating a conference topic now.\n";
