<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->string('title');
            $table->enum('campaign_type', ['pre_conference', 'during_conference', 'post_conference']);
            $table->text('plan_text')->nullable();
            $table->text('target_outlets')->nullable();
            $table->text('press_kit_notes')->nullable();
            $table->dateTime('press_conference_date')->nullable();
            $table->decimal('estimated_budget', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->default(0.00);
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['campaign_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_campaigns');
    }
};

