<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conference_sessions', function (Blueprint $table) {
            // Add topic_id foreign key
            $table->foreignId('topic_id')->nullable()->after('conference_id')->constrained('conference_topics')->nullOnDelete();
            
            // Rename columns
            $table->renameColumn('title', 'session_title');
            $table->renameColumn('order_number', 'session_order');
            
            // Drop theme column
            $table->dropColumn('theme');
            
            // Convert date/time columns to datetime
            $table->dateTime('date')->change();
            $table->dateTime('start_time')->change();
            $table->dateTime('end_time')->change();
            
            // Add index
            $table->index('topic_id');
        });
    }

    public function down(): void
    {
        Schema::table('conference_sessions', function (Blueprint $table) {
            // Drop topic_id
            $table->dropForeign(['topic_id']);
            $table->dropColumn('topic_id');
            
            // Restore column names
            $table->renameColumn('session_title', 'title');
            $table->renameColumn('session_order', 'order_number');
            
            // Restore theme column
            $table->string('theme')->nullable()->after('title');
            
            // Revert datetime to date/time
            $table->date('date')->change();
            $table->time('start_time')->change();
            $table->time('end_time')->change();
        });
    }
};
