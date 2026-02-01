<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->string('title');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('conference_id');
        });
        
        // Add topic_id to conference_sessions after topics table is created
        Schema::table('conference_sessions', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->after('conference_id')->constrained('conference_topics')->nullOnDelete();
            $table->index('topic_id');
        });
    }

    public function down(): void
    {
        // Drop topic_id from conference_sessions first
        Schema::table('conference_sessions', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropIndex(['topic_id']);
            $table->dropColumn('topic_id');
        });
        
        Schema::dropIfExists('conference_topics');
    }
};
