<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Add new fields
            $table->string('full_name')->after('id');
            $table->string('fax', 100)->nullable()->after('phone');
            
            // Rename cv_text to bio
            $table->renameColumn('cv_text', 'bio');
            
            // Drop old fields
            $table->dropColumn(['first_name', 'last_name', 'honorific_title', 'academic_title']);
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Restore old fields
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('honorific_title')->nullable()->after('last_name');
            $table->string('academic_title')->nullable()->after('honorific_title');
            
            // Rename bio back to cv_text
            $table->renameColumn('bio', 'cv_text');
            
            // Drop new fields
            $table->dropColumn(['full_name', 'fax']);
        });
    }
};
