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
        Schema::create('match_compartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('position');
            $table->boolean('is_star_bonus_active')->default(false);
            $table->timestamps();

            $table->index('match_id', 'idx_mc_match_id');
            $table->unique(['match_id', 'position'], 'uniq_mc_match_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_compartments');
    }
};
