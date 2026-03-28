<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TradeSideSeeder extends Seeder
{
    public function run(): void
    {
        $sides = [
            ['name' => 'Left', 'slug' => 'left'],
            ['name' => 'Right', 'slug' => 'right'],
        ];

        foreach ($sides as $side) {
            DB::table('trade_sides')->updateOrInsert(
                ['slug' => $side['slug']],
                [...$side, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
