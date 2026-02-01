<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('correspondences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->nullable()->constrained('conferences')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('direction', ['outgoing', 'incoming'])->default('outgoing');
            $table->enum('category', [
                'invitation',
                'member_consultation',
                'research',
                'attendance',
                'logistics',
                'finance',
                'royal_court',
                'diplomatic',
                'security',
                'press',
                'membership',
                'thanks',
                'general'
            ])->default('general');
            $table->enum('workflow_group', [
                'pre_conference',
                'scientific',
                'logistics',
                'media',
                'finance',
                'membership',
                'royal',
                'security',
                'general_ops'
            ])->default('general_ops');

            $table->string('ref_number')->nullable();
            $table->date('correspondence_date')->nullable();
            $table->string('recipient_entity')->nullable();
            $table->string('sender_entity')->nullable();

            $table->string('subject')->nullable();
            $table->text('content')->nullable();

            $table->json('header')->nullable();
            $table->string('file_path', 1024)->nullable();

            $table->boolean('response_received')->default(false);
            $table->date('response_date')->nullable();

            $table->enum('status', [
                'draft',
                'sent',
                'delivered',
                'received',
                'replied',
                'approved',
                'rejected',
                'pending',
                'archived'
            ])->default('draft');

            $table->integer('priority')->default(3);
            $table->boolean('requires_follow_up')->default(true);
            $table->dateTime('follow_up_at')->nullable();

            $table->boolean('last_of_type')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('ref_number');
            $table->index(['category', 'status']);
            $table->index(['category', 'last_of_type']);
            $table->index('follow_up_at');
            $table->index('workflow_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correspondences');
    }
};
