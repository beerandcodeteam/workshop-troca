<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending', 'slug' => 'pending'],
            ['name' => 'In Progress', 'slug' => 'in_progress'],
            ['name' => 'Completed', 'slug' => 'completed'],
            ['name' => 'Abandoned', 'slug' => 'abandoned'],
        ];

        foreach ($statuses as $status) {
            DB::table('match_statuses')->updateOrInsert(
                ['slug' => $status['slug']],
                [...$status, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
