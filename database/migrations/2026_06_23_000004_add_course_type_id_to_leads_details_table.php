<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->foreignId('course_type_id')
                ->nullable()
                ->after('course_type')
                ->constrained('course_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropForeign(['course_type_id']);
            $table->dropColumn('course_type_id');
        });
    }
};
