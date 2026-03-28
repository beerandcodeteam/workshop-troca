<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Player', 'slug' => 'player'],
            ['name' => 'AI', 'slug' => 'ai'],
        ];

        foreach ($types as $type) {
            DB::table('participant_types')->updateOrInsert(
                ['slug' => $type['slug']],
                [...$type, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
