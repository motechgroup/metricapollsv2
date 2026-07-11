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
        Schema::table('surveys', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false);
            $table->decimal('payout_amount', 10, 2)->default(0.00); // Amount paid in KES
            $table->boolean('is_qualification')->default(false);
            $table->string('min_badge_level')->default('Bronze'); // Bronze, Silver, Gold
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'payout_amount', 'is_qualification', 'min_badge_level']);
        });
    }
};
