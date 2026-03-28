<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DifficultyTier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'star_count',
        'base_xp_reward',
        'win_bonus_xp',
        'sort_order',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }
}
