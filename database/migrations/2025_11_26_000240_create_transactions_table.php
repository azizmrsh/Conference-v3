<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->enum('tx_type', ['budget_item','expense','payment']);
            $table->string('category')->nullable();
            $table->string('item_name')->nullable();
            $table->decimal('estimated_amount', 12, 2)->nullable();
            $table->decimal('actual_amount', 12, 2)->nullable();
            $table->date('incurred_at')->nullable();
            $table->enum('status', ['pending','paid','cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('tx_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

