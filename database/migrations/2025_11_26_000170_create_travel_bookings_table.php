<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained('invitations')->cascadeOnDelete();
            $table->enum('type', ['arrival_flight','departure_flight','hotel','transfer']);
            $table->foreignId('airline_id')->nullable()->constrained('airlines')->nullOnDelete();
            $table->string('flight_number', 50)->nullable();
            $table->date('flight_date')->nullable();
            $table->time('flight_time')->nullable();
            $table->foreignId('airport_from_id')->nullable()->constrained('airports')->nullOnDelete();
            $table->foreignId('airport_to_id')->nullable()->constrained('airports')->nullOnDelete();
            $table->string('ticket_number')->nullable();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
            $table->enum('room_type', ['standard','suite','royal_suite'])->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('ticket_path', 1024)->nullable();
            $table->enum('delivery_method', ['email','fax','hand','courier'])->nullable();
            $table->dateTime('delivery_confirmed_at')->nullable();
            $table->enum('booking_status', ['pending','booked','issued','cancelled'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('type');
            $table->index('booking_status');
            $table->index('flight_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_bookings');
    }
};

