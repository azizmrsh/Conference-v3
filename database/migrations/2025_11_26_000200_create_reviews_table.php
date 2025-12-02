<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained('papers')->cascadeOnDelete();
            $table->foreignId('reviewer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('review_type', ['scientific_review','linguistic_review','technical_review','final_review']);
            $table->integer('review_round')->default(1);
            $table->dateTime('assigned_at');
            $table->date('due_date');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->enum('recommendation', ['accept','accept_with_minor','major_revision','reject'])->nullable();
            $table->tinyInteger('content_quality')->nullable();
            $table->tinyInteger('methodology_score')->nullable();
            $table->tinyInteger('language_quality')->nullable();
            $table->tinyInteger('formatting_score')->nullable();
            $table->tinyInteger('overall_score')->nullable();
            $table->text('general_comments')->nullable();
            $table->string('annotated_file_path', 1024)->nullable();
            $table->enum('status', ['assigned','in_progress','completed','overdue','cancelled'])->default('assigned');
            $table->boolean('confidential')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['paper_id','reviewer_user_id','review_round'], 'unique_paper_reviewer_round');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

