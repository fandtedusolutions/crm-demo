<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addon_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('addon_course_id')->constrained('courses')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['course_id', 'addon_course_id']);
            $table->index(['course_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_courses');
    }
};
