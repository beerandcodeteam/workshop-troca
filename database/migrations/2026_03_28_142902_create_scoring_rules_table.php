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
        Schema::create('scoring_rules', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('min_remaining_tokens');
            $table->smallInteger('max_remaining_tokens')->nullable();
            $table->smallInteger('star_count');
            $table->smallInteger('points');
            $table->timestamps();

            $table->index(['star_count', 'min_remaining_tokens'], 'idx_scoring_star_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_rules');
    }
};
