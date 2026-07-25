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
        Schema::create('political_parties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('party_color')->default('#0A58CA');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('politicians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo_path')->nullable();
            $table->foreignId('political_party_id')->nullable()->constrained('political_parties')->nullOnDelete();
            $table->string('level')->default('national'); // national, region, county, constituency
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('county_id')->nullable()->constrained('counties')->nullOnDelete();
            $table->foreignId('constituency_id')->nullable()->constrained('constituencies')->nullOnDelete();
            $table->string('position_title')->nullable(); // e.g. President, Governor, Woman Representative, Senator, MP
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('politicians');
        Schema::dropIfExists('political_parties');
    }
};
