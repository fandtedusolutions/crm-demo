<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            if (!Schema::hasColumn('converted_student_mentor_details', 'pe_mentor_daily_track')) {
                $table->json('pe_mentor_daily_track')->nullable()->after('pe_student_feedback');
            }
        });
    }

    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            if (Schema::hasColumn('converted_student_mentor_details', 'pe_mentor_daily_track')) {
                $table->dropColumn('pe_mentor_daily_track');
            }
        });
    }
};
