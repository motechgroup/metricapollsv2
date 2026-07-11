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
        Schema::create('panelists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('education_level')->nullable();
            $table->string('income_bracket')->nullable();
            $table->string('location_region')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->integer('points_balance')->default(0);
            $table->timestamps();
        });

        Schema::create('qualification_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('questions'); // Set of qualification questions (e.g. Yes/No matching demographic tags)
            $table->integer('reward_points')->default(50);
            $table->timestamps();
        });

        Schema::create('panelist_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('qualification_test_id')
                ->constrained('qualification_tests')
                ->cascadeOnDelete();
            $table->timestamp('passed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_cost');
            $table->string('type'); // airtime, mobile_money, voucher
            $table->integer('stock')->default(100);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('type'); // reward (survey response / test), withdrawal, transfer
            $table->decimal('amount', 10, 2)->default(0.00); // monetary equivalent in USD or local currency
            $table->integer('points'); // points credited or debited
            $table->string('description')->nullable();
            $table->string('reference')->nullable(); // M-Pesa reference code, voucher code, etc.
            $table->string('status')->default('completed'); // completed, pending, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('panelist_qualifications');
        Schema::dropIfExists('qualification_tests');
        Schema::dropIfExists('panelists');
    }
};
