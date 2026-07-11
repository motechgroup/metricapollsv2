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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_request_id')
                ->nullable()
                ->constrained('research_requests')
                ->nullOnDelete();
            $table->foreignId('client_organization_id')
                ->constrained('client_organizations')
                ->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('project_manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->decimal('budget', 12, 2)->nullable();
            $table->string('status')->default('planning'); // planning, live, paused, completed
            $table->integer('target_quota')->default(1000);
            $table->integer('current_responses')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
