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
        Schema::create('field_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->foreignId('field_agent_id')
                ->constrained('users')
                ->cascadeOnDelete(); // Field agent account
            $table->integer('target_submissions')->default(100);
            $table->integer('completed_submissions')->default(0);
            $table->string('status')->default('assigned'); // assigned, active, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_assignments');
    }
};
