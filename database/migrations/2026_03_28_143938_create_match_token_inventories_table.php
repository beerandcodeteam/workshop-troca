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
        Schema::create('match_token_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_type_id')->constrained();
            $table->foreignId('token_color_id')->constrained();
            $table->smallInteger('quantity')->default(0);
            $table->timestamps();

            $table->index(['match_id', 'participant_type_id'], 'idx_mti_match_participant');
            $table->unique(['match_id', 'participant_type_id', 'token_color_id'], 'uniq_mti_match_participant_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_token_inventories');
    }
};
