<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('courses')
            ->whereIn('id', [3, 4])
            ->update([
                'needs_time' => 1,
                'is_online' => 1,
                'is_offline' => 1,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('courses')
            ->where('id', 3)
            ->update([
                'needs_time' => 1,
                'is_online' => 1,
                'is_offline' => 1,
                'updated_at' => now(),
            ]);

        DB::table('courses')
            ->where('id', 4)
            ->update([
                'needs_time' => 0,
                'is_online' => 0,
                'is_offline' => 0,
                'updated_at' => now(),
            ]);
    }
};
