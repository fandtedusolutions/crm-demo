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
        Schema::table('converted_student_details', function (Blueprint $table) {
            // Add remarks field if it doesn't exist (it may already exist from Grameen Mukt Vidhyalayi Shiksha Sansthan migration)
            if (!Schema::hasColumn('converted_student_details', 'remarks')) {
                $table->text('remarks')->nullable()->after('screening');
            }
            
            // Add continuing_studies field (selectbox: yes, no)
            if (!Schema::hasColumn('converted_student_details', 'continuing_studies')) {
                $table->enum('continuing_studies', ['yes', 'no'])->nullable()->after('remarks');
            }
            
            // Add reason field (for inline editing)
            if (!Schema::hasColumn('converted_student_details', 'reason')) {
                $table->text('reason')->nullable()->after('continuing_studies');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_details', function (Blueprint $table) {
            if (Schema::hasColumn('converted_student_details', 'continuing_studies')) {
                $table->dropColumn('continuing_studies');
            }
            if (Schema::hasColumn('converted_student_details', 'reason')) {
                $table->dropColumn('reason');
            }
            // Note: We don't drop remarks as it might have been added by another migration
        });
    }
};
