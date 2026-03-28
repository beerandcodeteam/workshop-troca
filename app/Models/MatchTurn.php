<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchTurn extends Model
{
    protected $fillable = [
        'match_id',
        'turn_number',
        'participant_type_id',
        'turn_action_type_id',
        'action_data',
    ];

    protected function casts(): array
    {
        return [
            'action_data' => 'array',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function participantType(): BelongsTo
    {
        return $this->belongsTo(ParticipantType::class);
    }

    public function turnActionType(): BelongsTo
    {
        return $this->belongsTo(TurnActionType::class);
    }
}
