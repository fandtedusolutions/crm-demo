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
            if (! Schema::hasColumn('converted_student_details', 're_mode')) {
                $table->string('re_mode', 20)->nullable()->after('tma');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_details', function (Blueprint $table) {
            if (Schema::hasColumn('converted_student_details', 're_mode')) {
                $table->dropColumn('re_mode');
            }
        });
    }
};
