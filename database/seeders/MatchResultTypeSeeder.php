<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchResultTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Player Win', 'slug' => 'player_win'],
            ['name' => 'AI Win', 'slug' => 'ai_win'],
            ['name' => 'Draw', 'slug' => 'draw'],
        ];

        foreach ($types as $type) {
            DB::table('match_result_types')->updateOrInsert(
                ['slug' => $type['slug']],
                [...$type, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
