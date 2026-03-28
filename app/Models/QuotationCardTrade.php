<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationCardTrade extends Model
{
    protected $fillable = [
        'quotation_card_id',
        'sort_order',
    ];

    public function quotationCard(): BelongsTo
    {
        return $this->belongsTo(QuotationCard::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationCardTradeItem::class);
    }

    public function leftItems(): HasMany
    {
        return $this->hasMany(QuotationCardTradeItem::class)
            ->whereHas('tradeSide', fn ($q) => $q->where('slug', 'left'));
    }

    public function rightItems(): HasMany
    {
        return $this->hasMany(QuotationCardTradeItem::class)
            ->whereHas('tradeSide', fn ($q) => $q->where('slug', 'right'));
    }
}
