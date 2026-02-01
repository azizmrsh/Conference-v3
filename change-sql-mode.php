#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Changing SQL mode to disable JSON validation...\n";

try {
    DB::statement('SET GLOBAL sql_mode=""');
    echo "✓ SQL mode changed successfully\n";
    echo "✓ JSON validation constraints will no longer be added\n\n";
    echo "Now rebuild the tables by running:\n";
    echo "  php artisan migrate:fresh --seed\n\n";
    echo "Or just rebuild specific table:\n";
    echo "  php rebuild-single-table.php\n";
} catch (\Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n";
    echo "\nYou need to manually edit C:\\xampp\\mysql\\bin\\my.ini\n";
    echo "Add this line under [mysqld]:\n";
    echo "  sql_mode=\"\"\n";
    echo "Then restart MySQL from XAMPP.\n";
}
