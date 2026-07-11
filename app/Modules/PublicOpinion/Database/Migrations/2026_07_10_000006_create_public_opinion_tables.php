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
        Schema::create('marketplace_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('file_path')->nullable();
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('academy_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('lessons'); // JSON list of lesson pages
            $table->integer('points_award')->default(50);
            $table->timestamps();
        });

        Schema::create('public_opinions', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->json('options'); // choices to vote on e.g. ["Option A", "Option B"]
            $table->string('status')->default('open'); // open, closed
            $table->integer('votes_count')->default(0);
            $table->timestamps();
        });

        Schema::create('public_opinion_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_opinion_id')
                ->constrained('public_opinions')
                ->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('voted_option');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_opinion_votes');
        Schema::dropIfExists('public_opinions');
        Schema::dropIfExists('academy_courses');
        Schema::dropIfExists('marketplace_reports');
    }
};
