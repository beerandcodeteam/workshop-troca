<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            TokenColorSeeder::class,
            DifficultyTierSeeder::class,
            MatchStatusSeeder::class,
            MatchResultTypeSeeder::class,
            TurnActionTypeSeeder::class,
            ParticipantTypeSeeder::class,
            TradeSideSeeder::class,
            PlayerRankSeeder::class,
            ScoringRuleSeeder::class,
            QuotationCardSeeder::class,
            CardSeeder::class,
        ]);

        User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }
}
