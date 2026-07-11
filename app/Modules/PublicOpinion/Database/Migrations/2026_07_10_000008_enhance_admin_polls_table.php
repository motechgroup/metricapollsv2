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
        Schema::table('admin_polls', function (Blueprint $table) {
            $table->date('research_date')->nullable();
            $table->date('release_date')->nullable();
            $table->integer('initial_downloads')->default(0);
            $table->integer('download_count')->default(0);
            $table->integer('sample_size')->default(100);
            $table->string('region')->nullable();
            $table->text('methodology')->nullable();
        });

        Schema::create('report_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_poll_id')
                ->constrained('admin_polls')
                ->cascadeOnDelete();
            $table->string('email');
            $table->timestamp('downloaded_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_downloads');

        Schema::table('admin_polls', function (Blueprint $table) {
            $table->dropColumn([
                'research_date',
                'release_date',
                'initial_downloads',
                'download_count',
                'sample_size',
                'region',
                'methodology',
            ]);
        });
    }
};
