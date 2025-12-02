<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained('invitations')->cascadeOnDelete();
            $table->string('title', 512);
            $table->text('abstract')->nullable();
            $table->string('theme')->nullable();
            $table->string('keywords', 512)->nullable();
            $table->string('file_word_path', 1024)->nullable();
            $table->string('file_pdf_path', 1024)->nullable();
            $table->string('final_print_path', 1024)->nullable();
            $table->enum('status', ['abstract_pending','abstract_accepted','full_paper_pending','under_review','modifications_required','accepted_final','sent_to_print'])->default('abstract_pending');
            $table->integer('word_count')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->integer('reviewers_count')->default(0);
            $table->integer('completed_reviews')->default(0);
            $table->date('review_deadline')->nullable();
            $table->text('review_summary')->nullable();
            $table->json('review_scores')->nullable();
            $table->boolean('requires_major_revision')->default(false);
            $table->boolean('requires_minor_revision')->default(false);
            $table->boolean('ready_for_print')->default(false);
            $table->dateTime('sent_to_print_at')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papers');
    }
};

