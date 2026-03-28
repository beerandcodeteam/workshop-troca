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
        Schema::create('quotation_card_trade_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_card_trade_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trade_side_id')->constrained();
            $table->foreignId('token_color_id')->constrained();
            $table->smallInteger('quantity')->default(1);
            $table->timestamps();

            $table->index('quotation_card_trade_id', 'idx_qcti_trade_id');
            $table->index(['quotation_card_trade_id', 'trade_side_id'], 'idx_qcti_trade_side');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_card_trade_items');
    }
};
