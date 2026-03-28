<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnActionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Roll Dice', 'slug' => 'roll_dice'],
            ['name' => 'Trade', 'slug' => 'trade'],
            ['name' => 'Purchase Card', 'slug' => 'purchase_card'],
            ['name' => 'Return Tokens', 'slug' => 'return_tokens'],
        ];

        foreach ($types as $type) {
            DB::table('turn_action_types')->updateOrInsert(
                ['slug' => $type['slug']],
                [...$type, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
