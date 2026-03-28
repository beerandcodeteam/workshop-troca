<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchCompartmentCard extends Model
{
    protected $fillable = [
        'match_compartment_id',
        'card_id',
        'position',
        'is_purchased',
        'purchased_by_participant_type_id',
        'points_scored',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'is_purchased' => 'boolean',
            'purchased_at' => 'datetime',
        ];
    }

    public function compartment(): BelongsTo
    {
        return $this->belongsTo(MatchCompartment::class, 'match_compartment_id');
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function purchasedByParticipantType(): BelongsTo
    {
        return $this->belongsTo(ParticipantType::class, 'purchased_by_participant_type_id');
    }
}
