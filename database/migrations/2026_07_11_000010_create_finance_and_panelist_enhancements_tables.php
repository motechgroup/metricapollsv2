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
        // 1. Add Experience Points and Badges to PanelistProfiles
        Schema::table('panelists', function (Blueprint $table) {
            $table->integer('experience_points')->default(0);
            $table->string('badge_level')->default('Bronze'); // Bronze, Silver, Gold
        });

        // 2. Add Fraud metrics to SurveyResponses
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->integer('duration_seconds')->default(0);
            $table->boolean('is_fraudulent')->default(false);
            $table->string('fraud_reason')->nullable();
        });

        // 3. Create Invoices Table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->foreignId('client_organization_id')
                ->constrained('client_organizations')
                ->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->string('status')->default('pending'); // pending, paid, cancelled
            $table->timestamps();
        });

        // 4. Create Gateway Settings Table
        Schema::create('gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // M-Pesa, Card
            $table->json('credentials'); // key, secret, passkey, env, status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_settings');
        Schema::dropIfExists('invoices');

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropColumn(['duration_seconds', 'is_fraudulent', 'fraud_reason']);
        });

        Schema::table('panelists', function (Blueprint $table) {
            $table->dropColumn(['experience_points', 'badge_level']);
        });
    }
};
