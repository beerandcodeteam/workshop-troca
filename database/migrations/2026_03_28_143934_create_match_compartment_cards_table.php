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
        Schema::create('match_compartment_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_compartment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('card_id')->constrained();
            $table->smallInteger('position');
            $table->boolean('is_purchased')->default(false);
            $table->foreignId('purchased_by_participant_type_id')->nullable()->constrained('participant_types');
            $table->smallInteger('points_scored')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->index('match_compartment_id', 'idx_mcc_compartment_id');
            $table->unique(['match_compartment_id', 'position'], 'uniq_mcc_compartment_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_compartment_cards');
    }
};
