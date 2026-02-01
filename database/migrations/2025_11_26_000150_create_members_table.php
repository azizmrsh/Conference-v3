<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->enum('type', ['working_member','correspondent_member','honorary_member','guest_speaker','staff','journalist']);
            $table->date('membership_date')->nullable();
            $table->foreignId('nationality_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('passport_number', 100)->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('fax', 100)->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path', 1024)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

