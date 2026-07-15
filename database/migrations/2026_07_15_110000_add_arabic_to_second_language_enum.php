<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('leads_details', 'second_language')) {
            DB::statement('ALTER TABLE leads_details MODIFY second_language VARCHAR(50) NULL');
        }
        if (Schema::hasColumn('converted_student_details', 'second_language')) {
            DB::statement('ALTER TABLE converted_student_details MODIFY second_language VARCHAR(50) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('leads_details', 'second_language')) {
            DB::statement("ALTER TABLE leads_details MODIFY second_language ENUM('malayalam', 'hindi') NULL");
        }
        if (Schema::hasColumn('converted_student_details', 'second_language')) {
            DB::statement("ALTER TABLE converted_student_details MODIFY second_language ENUM('malayalam', 'hindi') NULL");
        }
    }
};
