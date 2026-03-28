<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 20)->nullable()->after('id');
            $table->integer('total_xp')->default(0)->after('password');
            $table->foreignId('player_rank_id')->nullable()->constrained()->after('total_xp');

            $table->index('total_xp', 'idx_users_total_xp');
            $table->index('player_rank_id', 'idx_users_player_rank_id');
        });

        DB::table('users')->whereNull('username')->update([
            'username' => DB::raw("LOWER(REPLACE(name, ' ', ''))"),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 20)->unique()->nullable(false)->change();
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        DB::table('users')->update([
            'name' => DB::raw('username'),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->dropForeign(['player_rank_id']);
            $table->dropIndex('idx_users_total_xp');
            $table->dropIndex('idx_users_player_rank_id');
            $table->dropColumn(['username', 'total_xp', 'player_rank_id']);
        });
    }
};
