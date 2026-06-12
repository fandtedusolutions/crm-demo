<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('user_roles')->updateOrInsert(
            ['id' => 16],
            [
                'title' => 'Faculty',
                'description' => 'Faculty role with mentor converted-leads access',
                'is_active' => true,
                'updated_at' => $now,
                'created_at' => DB::table('user_roles')->where('id', 16)->value('created_at') ?? $now,
            ]
        );
    }

    public function down(): void
    {
        DB::table('users')->where('role_id', 16)->update(['role_id' => 15]);
        DB::table('user_roles')->where('id', 16)->delete();
    }
};
