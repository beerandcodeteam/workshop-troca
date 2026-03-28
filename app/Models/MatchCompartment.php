<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchCompartment extends Model
{
    protected $fillable = [
        'match_id',
        'position',
        'is_star_bonus_active',
    ];

    protected function casts(): array
    {
        return [
            'is_star_bonus_active' => 'boolean',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(MatchCompartmentCard::class);
    }

    public function faceUpCard(): ?MatchCompartmentCard
    {
        return $this->cards()
            ->where('is_purchased', false)
            ->orderBy('position')
            ->first();
    }
}
