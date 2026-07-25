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
            $table->string('target_level')->default('national')->after('topic'); // national, region, county, constituency
            $table->foreignId('region_id')->nullable()->after('target_level')->constrained('regions')->nullOnDelete();
            $table->foreignId('county_id')->nullable()->after('region_id')->constrained('counties')->nullOnDelete();
            $table->foreignId('constituency_id')->nullable()->after('county_id')->constrained('constituencies')->nullOnDelete();
            $table->string('position_title')->nullable()->after('constituency_id'); // e.g. Woman Representative, Governor, President
            $table->json('candidates_data')->nullable()->after('options'); // Rich array of options/candidates with politician_id, photo, party info
            $table->timestamp('expires_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_opinions', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['county_id']);
            $table->dropForeign(['constituency_id']);
            $table->dropColumn([
                'target_level',
                'region_id',
                'county_id',
                'constituency_id',
                'position_title',
                'candidates_data',
                'expires_at'
            ]);
        });
    }
};
