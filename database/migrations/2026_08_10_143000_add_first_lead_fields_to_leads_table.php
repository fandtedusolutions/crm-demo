<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('first_lead_source_id')->nullable()->after('lead_source_id');
            $table->unsignedBigInteger('first_lead_course_id')->nullable()->after('course_id');
            $table->unsignedBigInteger('first_lead_status_id')->nullable()->after('lead_status_id');

            $table->foreign('first_lead_source_id')->references('id')->on('lead_sources')->nullOnDelete();
            $table->foreign('first_lead_course_id')->references('id')->on('courses')->nullOnDelete();
            $table->foreign('first_lead_status_id')->references('id')->on('lead_statuses')->nullOnDelete();
        });

        // Backfill existing leads from their current values
        DB::table('leads')->whereNull('first_lead_source_id')->update([
            'first_lead_source_id' => DB::raw('lead_source_id'),
        ]);
        DB::table('leads')->whereNull('first_lead_course_id')->update([
            'first_lead_course_id' => DB::raw('course_id'),
        ]);
        DB::table('leads')->whereNull('first_lead_status_id')->update([
            'first_lead_status_id' => DB::raw('lead_status_id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['first_lead_source_id']);
            $table->dropForeign(['first_lead_course_id']);
            $table->dropForeign(['first_lead_status_id']);
            $table->dropColumn(['first_lead_source_id', 'first_lead_course_id', 'first_lead_status_id']);
        });
    }
};
