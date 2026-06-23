<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            // AI Integrated Digital Marketing specific fields
            $table->enum('graphic_design_session_attendance', ['Attended', 'Not Attended'])->nullable()->after('third_month_feedback');
            $table->enum('copy_writing_session_attendance', ['Attended', 'Not Attended'])->nullable()->after('graphic_design_session_attendance');
            $table->date('completed_cancelled_date')->nullable()->after('cancelled_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'graphic_design_session_attendance',
                'copy_writing_session_attendance',
                'completed_cancelled_date',
            ]);
        });
    }
};

