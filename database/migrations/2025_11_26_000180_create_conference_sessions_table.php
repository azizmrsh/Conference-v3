<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conference_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->string('session_title');
            $table->dateTime('date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('hall_name')->nullable();
            $table->foreignId('chair_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->integer('session_order')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index(['conference_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_sessions');
    }
};

