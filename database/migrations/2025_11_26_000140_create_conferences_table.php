<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('session_number')->nullable();
            $table->string('location')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_address', 512)->nullable();
            $table->dateTime('hijri_date')->nullable();
            $table->dateTime('gregorian_date')->nullable();
            $table->integer('sessions_count')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['planning','active','completed','archived'])->default('planning');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index(['start_date','end_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};

