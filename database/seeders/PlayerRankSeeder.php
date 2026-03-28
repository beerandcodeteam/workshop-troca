<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerRankSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = [
            ['name' => 'Bronze', 'slug' => 'bronze', 'min_xp' => 0, 'sort_order' => 1],
            ['name' => 'Silver', 'slug' => 'silver', 'min_xp' => 500, 'sort_order' => 2],
            ['name' => 'Gold', 'slug' => 'gold', 'min_xp' => 1500, 'sort_order' => 3],
            ['name' => 'Platinum', 'slug' => 'platinum', 'min_xp' => 3500, 'sort_order' => 4],
            ['name' => 'Diamond', 'slug' => 'diamond', 'min_xp' => 7000, 'sort_order' => 5],
        ];

        foreach ($ranks as $rank) {
            DB::table('player_ranks')->updateOrInsert(
                ['slug' => $rank['slug']],
                [...$rank, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
