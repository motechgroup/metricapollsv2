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
        Schema::create('public_opinion_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_opinion_id')->constrained('public_opinions')->cascadeOnDelete();
            $table->string('author_name');
            $table->text('comment_text');
            $table->integer('likes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_opinion_comments');
    }
};
