<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchQuotationCard extends Model
{
    protected $fillable = [
        'match_id',
        'quotation_card_id',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function quotationCard(): BelongsTo
    {
        return $this->belongsTo(QuotationCard::class);
    }
}
