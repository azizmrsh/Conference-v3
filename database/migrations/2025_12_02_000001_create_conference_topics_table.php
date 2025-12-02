<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->string('title');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('conference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_topics');
    }
};
