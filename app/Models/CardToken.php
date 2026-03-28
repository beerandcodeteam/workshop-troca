<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardToken extends Model
{
    protected $fillable = [
        'card_id',
        'token_color_id',
        'quantity',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function tokenColor(): BelongsTo
    {
        return $this->belongsTo(TokenColor::class);
    }
}
