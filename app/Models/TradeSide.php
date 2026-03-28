<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TradeSide extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function quotationCardTradeItems(): HasMany
    {
        return $this->hasMany(QuotationCardTradeItem::class);
    }
}
