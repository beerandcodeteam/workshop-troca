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
        Schema::create('match_quotation_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_card_id')->constrained();
            $table->timestamps();

            $table->index('match_id', 'idx_mqc_match_id');
            $table->unique(['match_id', 'quotation_card_id'], 'uniq_mqc_match_quotation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_quotation_cards');
    }
};
