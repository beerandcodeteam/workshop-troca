<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $fillable = [
        'number',
        'star_count',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(CardToken::class);
    }
}
