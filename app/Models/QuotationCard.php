<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationCard extends Model
{
    protected $fillable = [
        'number',
        'name',
        'description',
    ];

    public function trades(): HasMany
    {
        return $this->hasMany(QuotationCardTrade::class);
    }
}
