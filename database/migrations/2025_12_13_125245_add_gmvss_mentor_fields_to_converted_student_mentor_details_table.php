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
            // Grameen Mukt Vidhyalayi Shiksha Sansthan Mentor specific fields
            $table->date('online_result_publication_date')->nullable()->after('courier_tracking_number');
            $table->date('certificate_publication_date')->nullable()->after('online_result_publication_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn(['online_result_publication_date', 'certificate_publication_date']);
        });
    }
};
