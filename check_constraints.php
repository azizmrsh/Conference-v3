<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Get check constraints
$constraints = DB::select("
    SELECT CONSTRAINT_NAME, CHECK_CLAUSE 
    FROM information_schema.CHECK_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'conf2' 
    AND TABLE_NAME = 'conferences'
");

echo "Check Constraints:\n";
print_r($constraints);

// Get table structure
$columns = DB::select("DESCRIBE conferences");
echo "\n\nTable Structure:\n";
print_r($columns);
