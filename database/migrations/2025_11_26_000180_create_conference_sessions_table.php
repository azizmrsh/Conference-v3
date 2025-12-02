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
            $table->string('title');
            $table->string('theme')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('hall_name')->nullable();
            $table->foreignId('chair_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->integer('order_number')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['conference_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_sessions');
    }
};

