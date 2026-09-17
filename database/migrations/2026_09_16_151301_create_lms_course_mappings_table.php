<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_course_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lms_course_id');
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique('lms_course_id');
            $table->foreign('lms_course_id')
                ->references('id')
                ->on('lms_courses')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_course_mappings');
    }
};
