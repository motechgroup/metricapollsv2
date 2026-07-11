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
        Schema::table('qualification_tests', function (Blueprint $table) {
            if (!Schema::hasColumn('qualification_tests', 'level')) {
                $table->string('level')->default('Level 1')->after('reward_points');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualification_tests', function (Blueprint $table) {
            if (Schema::hasColumn('qualification_tests', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
