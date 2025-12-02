<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->foreignId('conference_session_id')->nullable()->constrained('conference_sessions')->nullOnDelete();
            $table->foreignId('invitation_id')->nullable()->constrained('invitations')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->cascadeOnDelete();
            $table->string('badge_code')->nullable();
            $table->enum('method', ['scan','manual'])->default('scan');
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['conference_id', 'conference_session_id']);
            $table->index(['invitation_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

