<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoringRule extends Model
{
    protected $fillable = [
        'min_remaining_tokens',
        'max_remaining_tokens',
        'star_count',
        'points',
    ];

    public static function calculatePoints(int $remainingTokens, int $starCount): int
    {
        return static::query()
            ->where('star_count', $starCount)
            ->where('min_remaining_tokens', '<=', $remainingTokens)
            ->where(function ($query) use ($remainingTokens) {
                $query->whereNull('max_remaining_tokens')
                    ->orWhere('max_remaining_tokens', '>=', $remainingTokens);
            })
            ->value('points') ?? 0;
    }
}
