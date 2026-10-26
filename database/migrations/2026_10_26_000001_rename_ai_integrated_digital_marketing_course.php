<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('courses')->where('id', 11)->update([
            'title' => 'AI Integrated Digital Marketing + Graphic Designing',
        ]);
    }

    public function down(): void
    {
        DB::table('courses')->where('id', 11)->update([
            'title' => 'AI Integrated Digital Marketing',
        ]);
    }
};
