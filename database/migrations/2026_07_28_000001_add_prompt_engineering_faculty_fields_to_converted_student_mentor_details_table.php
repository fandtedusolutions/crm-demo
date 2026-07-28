<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('pe_attendance_days_1_10')->nullable()->after('jv_feedback_notes');
            $table->unsignedTinyInteger('pe_practical_work_1_5')->nullable()->after('pe_attendance_days_1_10');
            $table->string('pe_first_periodical_test')->nullable()->after('pe_practical_work_1_5');
            $table->unsignedTinyInteger('pe_attendance_days_11_20')->nullable()->after('pe_first_periodical_test');
            $table->string('pe_second_periodical_test')->nullable()->after('pe_attendance_days_11_20');
            $table->unsignedTinyInteger('pe_attendance_days_21_30')->nullable()->after('pe_second_periodical_test');
            $table->unsignedTinyInteger('pe_practical_work_11_15')->nullable()->after('pe_attendance_days_21_30');
            $table->string('pe_final_examination')->nullable()->after('pe_practical_work_11_15');
            $table->string('pe_course_status')->nullable()->after('pe_final_examination');
            $table->text('pe_student_feedback')->nullable()->after('pe_course_status');
        });
    }

    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'pe_attendance_days_1_10',
                'pe_practical_work_1_5',
                'pe_first_periodical_test',
                'pe_attendance_days_11_20',
                'pe_second_periodical_test',
                'pe_attendance_days_21_30',
                'pe_practical_work_11_15',
                'pe_final_examination',
                'pe_course_status',
                'pe_student_feedback',
            ]);
        });
    }
};
