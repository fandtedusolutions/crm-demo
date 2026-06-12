<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $role15 = DB::table('user_roles')->where('id', 15)->first();

        if ($role15 && stripos((string) $role15->title, 'faculty') !== false) {
            DB::table('user_roles')->updateOrInsert(
                ['id' => 99],
                [
                    'title' => '_swap_temp',
                    'description' => 'Temporary role for migration',
                    'is_active' => false,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            DB::table('users')->where('role_id', 15)->update(['role_id' => 99]);
            DB::table('users')->where('role_id', 16)->update(['role_id' => 15]);
            DB::table('users')->where('role_id', 99)->update(['role_id' => 16]);

            DB::table('user_roles')->where('id', 99)->delete();
        }

        DB::table('user_roles')->updateOrInsert(
            ['id' => 15],
            [
                'title' => 'Placement Manager',
                'description' => 'Placement Manager role with placement list access',
                'is_active' => true,
                'updated_at' => $now,
                'created_at' => $role15->created_at ?? $now,
            ]
        );

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
        $now = now();
        $role15 = DB::table('user_roles')->where('id', 15)->first();

        if ($role15 && stripos((string) $role15->title, 'placement') !== false) {
            DB::table('user_roles')->updateOrInsert(
                ['id' => 99],
                [
                    'title' => '_swap_temp',
                    'description' => 'Temporary role for migration',
                    'is_active' => false,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            DB::table('users')->where('role_id', 15)->update(['role_id' => 99]);
            DB::table('users')->where('role_id', 16)->update(['role_id' => 15]);
            DB::table('users')->where('role_id', 99)->update(['role_id' => 16]);

            DB::table('user_roles')->where('id', 99)->delete();
        }

        DB::table('user_roles')->updateOrInsert(
            ['id' => 15],
            [
                'title' => 'Faculty',
                'description' => 'Faculty role with mentor converted-leads access',
                'is_active' => true,
                'updated_at' => $now,
                'created_at' => $role15->created_at ?? $now,
            ]
        );

        DB::table('user_roles')->updateOrInsert(
            ['id' => 16],
            [
                'title' => 'Placement Officer',
                'description' => 'Placement Officer role',
                'is_active' => true,
                'updated_at' => $now,
                'created_at' => DB::table('user_roles')->where('id', 16)->value('created_at') ?? $now,
            ]
        );
    }
};
