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
        Schema::create('card_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('token_color_id')->constrained();
            $table->smallInteger('quantity')->default(1);
            $table->timestamps();

            $table->index('card_id', 'idx_card_tokens_card_id');
            $table->unique(['card_id', 'token_color_id'], 'uniq_card_tokens_card_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_tokens');
    }
};
