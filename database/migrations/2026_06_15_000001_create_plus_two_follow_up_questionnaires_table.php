<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plus_two_follow_up_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->string('name')->nullable();
            $table->string('mobile_number')->nullable();

            // Section 1: Result Status
            $table->enum('received_plus_two_result', ['yes', 'no'])->nullable();
            $table->enum('result_outcome', ['passed', 'failed', 'improvement'])->nullable();
            $table->enum('stream_completed', ['science', 'commerce', 'humanities'])->nullable();

            // Section 2: Future Plan
            $table->enum('current_plan', [
                'degree',
                'professional_course',
                'government_exam',
                'job',
                'abroad_studies',
                'business',
                'not_decided',
            ])->nullable();
            $table->enum('college_selection', ['finalized', 'shortlisted', 'not_decided'])->nullable();
            $table->string('planned_course')->nullable();
            $table->text('course_selection_reason')->nullable();

            // Section 3: Decision Stage
            $table->enum('admission_started', ['yes', 'no'])->nullable();
            $table->enum('decision_maker', ['self', 'parents', 'both_together', 'guardian'])->nullable();

            // Section 4: Pain Point Identification
            $table->enum('career_clarity_level', ['yes', 'somewhat', 'no'])->nullable();
            $table->text('biggest_challenge')->nullable();

            // Section 5: Opportunity Qualification
            $table->enum('guidance_interested_level', ['yes', 'maybe', 'no'])->nullable();
            $table->enum('counseling_preference', ['online', 'direct', 'either'])->nullable();
            $table->string('best_contact_time')->nullable();

            // Summary fields (editable, auto-populated from answers)
            $table->string('result_status')->nullable();
            $table->string('stream')->nullable();
            $table->string('future_plan')->nullable();
            $table->string('course_interested')->nullable();
            $table->string('college_selected')->nullable();
            $table->string('decision_maker_summary')->nullable();
            $table->string('career_clarity')->nullable();
            $table->string('main_challenge')->nullable();
            $table->string('guidance_interested')->nullable();
            $table->date('followup_date')->nullable();
            $table->time('followup_time')->nullable();

            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->unique('lead_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plus_two_follow_up_questionnaires');
    }
};
