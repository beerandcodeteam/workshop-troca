<?php

namespace Database\Factories;

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameMatch>
 */
class GameMatchFactory extends Factory
{
    protected $model = GameMatch::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'difficulty_tier_id' => DifficultyTier::first()?->id ?? 1,
            'match_status_id' => MatchStatus::first()?->id ?? 1,
        ];
    }
}
