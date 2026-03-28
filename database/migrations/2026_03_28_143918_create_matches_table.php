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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('difficulty_tier_id')->constrained();
            $table->foreignId('match_status_id')->constrained();
            $table->foreignId('match_result_type_id')->nullable()->constrained();
            $table->smallInteger('current_turn_number')->default(0);
            $table->foreignId('current_participant_type_id')->nullable()->constrained('participant_types');
            $table->boolean('has_acted_this_turn')->default(false);
            $table->integer('player_score')->default(0);
            $table->integer('ai_score')->default(0);
            $table->smallInteger('player_cards_purchased')->default(0);
            $table->smallInteger('ai_cards_purchased')->default(0);
            $table->smallInteger('compartments_emptied')->default(0);
            $table->integer('xp_earned')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_matches_user_id');
            $table->index('match_status_id', 'idx_matches_status_id');
            $table->index(['user_id', 'match_status_id'], 'idx_matches_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
