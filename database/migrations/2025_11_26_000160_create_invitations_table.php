<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('status', ['nominated','invited','confirmed_attendance','apologized_attendance','apologized_writing','no_response'])->default('nominated');
            $table->enum('role', ['researcher','listener','session_chair','media','guest_honor']);
            $table->dateTime('invitation_sent_at')->nullable();
            $table->dateTime('response_received_at')->nullable();
            $table->boolean('needs_visa')->default(true);
            $table->boolean('visa_issued')->default(false);
            $table->boolean('flight_booked')->default(false);
            $table->boolean('hotel_booked')->default(false);
            $table->boolean('bag_received')->default(false);
            $table->boolean('badge_printed')->default(false);
            $table->integer('reminders_sent')->default(0);
            $table->string('emergency_contact')->nullable();
            $table->text('dietary_requirements')->nullable();
            $table->text('accessibility_needs')->nullable();
            $table->text('documents_received')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('expected_arrival')->nullable();
            $table->dateTime('expected_departure')->nullable();
            $table->enum('preferred_communication', ['email','fax','phone','post'])->default('email');
            $table->timestamps();
            $table->index(['conference_id', 'status']);
            $table->index('member_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};

