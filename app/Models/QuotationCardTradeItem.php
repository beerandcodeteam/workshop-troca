<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationCardTradeItem extends Model
{
    protected $fillable = [
        'quotation_card_trade_id',
        'trade_side_id',
        'token_color_id',
        'quantity',
    ];

    public function trade(): BelongsTo
    {
        return $this->belongsTo(QuotationCardTrade::class, 'quotation_card_trade_id');
    }

    public function tradeSide(): BelongsTo
    {
        return $this->belongsTo(TradeSide::class);
    }

    public function tokenColor(): BelongsTo
    {
        return $this->belongsTo(TokenColor::class);
    }
}
