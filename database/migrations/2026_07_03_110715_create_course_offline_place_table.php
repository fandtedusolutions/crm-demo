<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_offline_place', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offline_place_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'offline_place_id']);
        });

        $offlinePlaceIds = DB::table('offline_places')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->pluck('id');

        if ($offlinePlaceIds->isEmpty()) {
            return;
        }

        $offlineCourseIds = DB::table('courses')
            ->where('is_offline', true)
            ->whereNull('deleted_at')
            ->pluck('id');

        $now = now();
        $rows = [];

        foreach ($offlineCourseIds as $courseId) {
            foreach ($offlinePlaceIds as $offlinePlaceId) {
                $rows[] = [
                    'course_id' => $courseId,
                    'offline_place_id' => $offlinePlaceId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($rows !== []) {
            DB::table('course_offline_place')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offline_place');
    }
};
