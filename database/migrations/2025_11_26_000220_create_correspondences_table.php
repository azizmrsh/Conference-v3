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
            $table->enum('direction', ['outgoing','incoming'])->default('outgoing');
            $table->enum('category', ['general','royal','diplomatic','press','internal'])->default('general');
            $table->string('ref_number')->nullable();
            $table->date('correspondence_date')->nullable();
            $table->string('recipient_entity')->nullable();
            $table->text('subject')->nullable();
            $table->mediumText('content')->nullable();
            $table->string('file_path', 1024)->nullable();
            $table->boolean('response_received')->default(false);
            $table->date('response_date')->nullable();
            $table->enum('status', ['draft','sent','received','replied','approved','rejected','pending'])->default('draft');
            $table->integer('priority')->default(3);
            $table->boolean('requires_follow_up')->default(true);
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('ref_number');
            $table->index(['category','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correspondences');
    }
};

