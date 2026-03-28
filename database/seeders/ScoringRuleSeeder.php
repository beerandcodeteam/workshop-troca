<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScoringRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            ['min_remaining_tokens' => 3, 'max_remaining_tokens' => null, 'star_count' => 0, 'points' => 1],
            ['min_remaining_tokens' => 3, 'max_remaining_tokens' => null, 'star_count' => 1, 'points' => 2],
            ['min_remaining_tokens' => 3, 'max_remaining_tokens' => null, 'star_count' => 2, 'points' => 3],
            ['min_remaining_tokens' => 2, 'max_remaining_tokens' => 2, 'star_count' => 0, 'points' => 2],
            ['min_remaining_tokens' => 2, 'max_remaining_tokens' => 2, 'star_count' => 1, 'points' => 3],
            ['min_remaining_tokens' => 2, 'max_remaining_tokens' => 2, 'star_count' => 2, 'points' => 5],
            ['min_remaining_tokens' => 1, 'max_remaining_tokens' => 1, 'star_count' => 0, 'points' => 3],
            ['min_remaining_tokens' => 1, 'max_remaining_tokens' => 1, 'star_count' => 1, 'points' => 5],
            ['min_remaining_tokens' => 1, 'max_remaining_tokens' => 1, 'star_count' => 2, 'points' => 8],
            ['min_remaining_tokens' => 0, 'max_remaining_tokens' => 0, 'star_count' => 0, 'points' => 5],
            ['min_remaining_tokens' => 0, 'max_remaining_tokens' => 0, 'star_count' => 1, 'points' => 8],
            ['min_remaining_tokens' => 0, 'max_remaining_tokens' => 0, 'star_count' => 2, 'points' => 12],
        ];

        foreach ($rules as $rule) {
            DB::table('scoring_rules')->updateOrInsert(
                ['min_remaining_tokens' => $rule['min_remaining_tokens'], 'star_count' => $rule['star_count']],
                [...$rule, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
