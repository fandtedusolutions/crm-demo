<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->foreignId('addon_course_id')
                ->nullable()
                ->after('course_id')
                ->constrained('courses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('addon_course_id');
        });
    }
};
