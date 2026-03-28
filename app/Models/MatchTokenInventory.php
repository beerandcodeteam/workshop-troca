<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchTokenInventory extends Model
{
    protected $fillable = [
        'match_id',
        'participant_type_id',
        'token_color_id',
        'quantity',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function participantType(): BelongsTo
    {
        return $this->belongsTo(ParticipantType::class);
    }

    public function tokenColor(): BelongsTo
    {
        return $this->belongsTo(TokenColor::class);
    }
}
