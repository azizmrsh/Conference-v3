<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type', 100);
            $table->unsignedBigInteger('attachable_id');
            $table->string('collection', 100);
            $table->string('disk', 50)->default('s3');
            $table->string('path', 1024);
            $table->string('filename')->nullable();
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['attachable_type','attachable_id']);
            $table->index('collection');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};

