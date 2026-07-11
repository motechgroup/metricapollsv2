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
        Schema::create('admin_polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Brand, Politician, Product, Media
            $table->json('options'); // [{name: '...', votes: 100, logo: '...'}]
            $table->boolean('is_public')->default(false);
            $table->text('ai_report')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_polls');
    }
};
