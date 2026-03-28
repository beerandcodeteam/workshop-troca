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
        Schema::create('quotation_card_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_card_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('quotation_card_id', 'idx_qct_quotation_card_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_card_trades');
    }
};
