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
        Schema::create('match_turns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('turn_number');
            $table->foreignId('participant_type_id')->constrained();
            $table->foreignId('turn_action_type_id')->constrained();
            $table->json('action_data')->nullable();
            $table->timestamps();

            $table->index('match_id', 'idx_mt_match_id');
            $table->index(['match_id', 'turn_number'], 'idx_mt_match_turn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_turns');
    }
};
