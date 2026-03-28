<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerRank extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'min_xp',
        'sort_order',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeForXp(Builder $query, int $xp): Builder
    {
        return $query->where('min_xp', '<=', $xp)
            ->orderByDesc('min_xp')
            ->limit(1);
    }

    public static function findForXp(int $xp): ?self
    {
        return static::forXp($xp)->first();
    }
}
