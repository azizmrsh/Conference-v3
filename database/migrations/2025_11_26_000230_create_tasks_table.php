<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->nullable()->constrained('conferences')->cascadeOnDelete();
            $table->enum('category', ['pre_conference','invitation_management','paper_management','logistics','protocol','media_campaign','financial','during_conference','post_conference']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('department_responsible')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->integer('priority')->default(3);
            $table->integer('estimated_hours')->nullable();
            $table->enum('status', ['not_started','in_progress','waiting','completed','overdue','cancelled'])->default('not_started');
            $table->integer('completion_percentage')->default(0);
            $table->dateTime('completed_at')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('deliverables')->nullable();
            $table->text('success_criteria')->nullable();
            $table->boolean('auto_reminder')->default(true);
            $table->string('reminder_schedule')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['conference_id','status','due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

