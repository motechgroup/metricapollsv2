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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->json('settings')->nullable(); // configuration options
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')
                ->constrained('surveys')
                ->cascadeOnDelete();
            $table->string('type'); // text, single_choice, multiple_choice, number, gps, photo, signature
            $table->text('question_text');
            $table->json('options')->nullable(); // choices list for choice types
            $table->json('rules')->nullable(); // validation / skip / branching logic
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')
                ->constrained('surveys')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // Panelist user
            $table->foreignId('field_agent_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // Field Agent user
            $table->decimal('gps_latitude', 10, 8)->nullable();
            $table->decimal('gps_longitude', 11, 8)->nullable();
            $table->string('status')->default('completed'); // completed, pending_sync
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('response_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')
                ->constrained('survey_responses')
                ->cascadeOnDelete();
            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();
            $table->text('answer_value'); // Holds single answer or JSON for multiple choice
            $table->timestamps();
        });

        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // e.g. Health, Finance, Demographics
            $table->string('type');
            $table->text('question_text');
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_bank');
        Schema::dropIfExists('response_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('surveys');
    }
};
