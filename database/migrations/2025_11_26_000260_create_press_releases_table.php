<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('press_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_campaign_id')->constrained('media_campaigns')->cascadeOnDelete();
            $table->string('title');
            $table->mediumText('content');
            $table->enum('release_type', ['announcement','invitation','daily_coverage','final_statement','follow_up']);
            $table->dateTime('scheduled_release_time')->nullable();
            $table->dateTime('actual_release_time')->nullable();
            $table->enum('status', ['draft','approved','sent','published'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('press_releases');
    }
};

