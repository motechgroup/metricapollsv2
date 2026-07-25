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
        Schema::table('public_opinions', function (Blueprint $table) {
            $table->boolean('allow_public_voting')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_opinions', function (Blueprint $table) {
            $table->dropColumn('allow_public_voting');
        });
    }
};
