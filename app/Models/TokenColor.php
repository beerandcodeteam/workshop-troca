<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TokenColor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'hex_code',
    ];

    public function cardTokens(): HasMany
    {
        return $this->hasMany(CardToken::class);
    }

    public function quotationCardTradeItems(): HasMany
    {
        return $this->hasMany(QuotationCardTradeItem::class);
    }

    public function matchTokenInventories(): HasMany
    {
        return $this->hasMany(MatchTokenInventory::class);
    }
}
