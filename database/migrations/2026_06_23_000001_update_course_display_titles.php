<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $titles = [
            1 => 'National Institute of Open Schooling',
            2 => 'Board of Open Schooling and Skill Education',
            3 => 'Certificate Course in Medical Coding',
            4 => 'Diploma in Hospital Administration',
            11 => 'AI Integrated Digital Marketing',
            15 => 'Diploma in Graphic Designing',
            16 => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
            25 => 'CreateX AI',
        ];

        foreach ($titles as $id => $title) {
            DB::table('courses')->where('id', $id)->update(['title' => $title]);
        }
    }

    public function down(): void
    {
        $titles = [
            1 => 'NIOS',
            2 => 'BOSSE',
            3 => 'Medical Coding',
            4 => 'Hospital Administration',
            11 => 'Digital Marketing',
            15 => 'Graphic Designing',
            16 => 'GMVSS',
            25 => 'Junior Vlogger',
        ];

        foreach ($titles as $id => $title) {
            DB::table('courses')->where('id', $id)->update(['title' => $title]);
        }
    }
};
