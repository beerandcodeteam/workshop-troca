<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TokenColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Red', 'slug' => 'red', 'hex_code' => '#EF4444'],
            ['name' => 'Green', 'slug' => 'green', 'hex_code' => '#22C55E'],
            ['name' => 'White', 'slug' => 'white', 'hex_code' => '#F8FAFC'],
            ['name' => 'Yellow', 'slug' => 'yellow', 'hex_code' => '#EAB308'],
            ['name' => 'Blue', 'slug' => 'blue', 'hex_code' => '#3B82F6'],
        ];

        foreach ($colors as $color) {
            DB::table('token_colors')->updateOrInsert(
                ['slug' => $color['slug']],
                [...$color, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
