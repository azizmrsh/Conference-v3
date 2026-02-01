<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media_has_models', function (Blueprint $table) {
            $table->unsignedInteger('order_column')->nullable()->after('media_id');
            $table->string('collection_name')->nullable()->after('order_column');
            $table->json('responsive_images')->nullable()->after('collection_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_has_models', function (Blueprint $table) {
            $table->dropColumn(['order_column', 'collection_name', 'responsive_images']);
        });
    }
};
