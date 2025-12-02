<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['hijri_year', 'gregorian_year', 'logo_path', 'themes']);
            
            // Add new datetime columns
            $table->dateTime('hijri_date')->nullable()->after('session_number');
            $table->dateTime('gregorian_date')->nullable()->after('hijri_date');
            
            // Add location and sessions_count
            $table->string('location')->nullable()->after('title');
            $table->integer('sessions_count')->default(0)->after('gregorian_date');
            
            // Modify existing date columns to datetime
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->change();
        });
    }

    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            // Restore old columns
            $table->string('hijri_year', 20)->nullable();
            $table->integer('gregorian_year')->nullable();
            $table->string('logo_path', 1024)->nullable();
            $table->text('themes')->nullable();
            
            // Drop new columns
            $table->dropColumn(['hijri_date', 'gregorian_date', 'location', 'sessions_count']);
            
            // Revert datetime to date
            $table->date('start_date')->change();
            $table->date('end_date')->change();
        });
    }
};
