<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchStatus extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('slug', 'pending');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('slug', 'in_progress');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('slug', 'completed');
    }

    public function scopeAbandoned(Builder $query): Builder
    {
        return $query->where('slug', 'abandoned');
    }
}
