<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            if (! Schema::hasColumn('converted_student_mentor_details', 'ha_mentor_track')) {
                $table->json('ha_mentor_track')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            if (Schema::hasColumn('converted_student_mentor_details', 'ha_mentor_track')) {
                $table->dropColumn('ha_mentor_track');
            }
        });
    }
};
