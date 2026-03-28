<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DifficultyTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['name' => 'Padrão Primário', 'slug' => 'padrao-primario', 'star_count' => 1, 'base_xp_reward' => 100, 'win_bonus_xp' => 50, 'sort_order' => 1],
            ['name' => 'Cadeia Cruzada', 'slug' => 'cadeia-cruzada', 'star_count' => 2, 'base_xp_reward' => 200, 'win_bonus_xp' => 100, 'sort_order' => 2],
            ['name' => 'Mestre do Caos', 'slug' => 'mestre-do-caos', 'star_count' => 3, 'base_xp_reward' => 350, 'win_bonus_xp' => 150, 'sort_order' => 3],
        ];

        foreach ($tiers as $tier) {
            DB::table('difficulty_tiers')->updateOrInsert(
                ['slug' => $tier['slug']],
                [...$tier, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
