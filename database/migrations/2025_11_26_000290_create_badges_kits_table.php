<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('badges_kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->enum('item_type', ['staff_badge','member_badge','guest_badge','press_badge','bag','dvd']);
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('cost_per_item', 12, 2)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges_kits');
    }
};

