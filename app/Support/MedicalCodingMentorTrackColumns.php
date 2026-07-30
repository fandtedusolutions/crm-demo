<?php

namespace App\Support;

use App\Models\ConvertedStudentMentorDetail;

class MedicalCodingMentorTrackColumns
{
    /**
     * @return array<int, array{label: string, field: string, type: string, options?: array<string, string>}>
     */
    public static function trackColumns(): array
    {
        return [
            ['label' => 'Total Course Days', 'field' => 'mc_total_course_days', 'type' => 'text'],
            ['label' => 'Orientation Status', 'field' => 'mc_orientation_status', 'type' => 'text'],
            ['label' => 'Module 1 Status Date', 'field' => 'mc_mod1_status_date', 'type' => 'text'],
            ['label' => 'Assessment – Module 1', 'field' => 'mc_mod1_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 1', 'field' => 'mc_mod1_mentor_call', 'type' => 'text'],
            ['label' => 'Module 2 Status', 'field' => 'mc_mod2_status', 'type' => 'text'],
            ['label' => 'Assessment – Module 2', 'field' => 'mc_mod2_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 2', 'field' => 'mc_mod2_mentor_call', 'type' => 'text'],
            ['label' => 'Module 3 Status', 'field' => 'mc_mod3_status', 'type' => 'text'],
            ['label' => 'Assessment – Module 3', 'field' => 'mc_mod3_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 3', 'field' => 'mc_mod3_mentor_call', 'type' => 'text'],
            ['label' => 'First Periodical Test Date', 'field' => 'mc_first_periodical_test_date', 'type' => 'text'],
            ['label' => 'Exam Mark', 'field' => 'mc_first_periodical_exam_mark', 'type' => 'text'],
            ['label' => 'Exam Remarks', 'field' => 'mc_first_periodical_exam_remarks', 'type' => 'text'],
            ['label' => 'Module 4 Status', 'field' => 'mc_mod4_status', 'type' => 'text'],
            ['label' => 'Assessment – Module 4', 'field' => 'mc_mod4_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 4', 'field' => 'mc_mod4_mentor_call', 'type' => 'text'],
            ['label' => 'Module 5 Status', 'field' => 'mc_mod5_status', 'type' => 'text'],
            ['label' => 'Assessment – Module 5', 'field' => 'mc_mod5_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 5', 'field' => 'mc_mod5_mentor_call', 'type' => 'text'],
            ['label' => 'Module 6 Status', 'field' => 'mc_mod6_status', 'type' => 'text'],
            ['label' => 'Assessment – Module 6', 'field' => 'mc_mod6_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 6', 'field' => 'mc_mod6_mentor_call', 'type' => 'text'],
            ['label' => 'Second Periodical Test Date', 'field' => 'mc_second_periodical_test_date', 'type' => 'date'],
            ['label' => 'Exam Mark', 'field' => 'mc_second_periodical_exam_mark', 'type' => 'text'],
            ['label' => 'Exam Remarks', 'field' => 'mc_second_periodical_exam_remarks', 'type' => 'text'],
            ['label' => 'Module 7 Status', 'field' => 'mc_mod7_status', 'type' => 'text'],
            ['label' => 'Assessment – Module 7', 'field' => 'mc_mod7_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 7', 'field' => 'mc_mod7_mentor_call', 'type' => 'text'],
            ['label' => 'Module 8 Status', 'field' => 'mc_mod8_status', 'type' => 'text'],
            ['label' => 'Assessment – Module 8', 'field' => 'mc_mod8_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 8', 'field' => 'mc_mod8_mentor_call', 'type' => 'text'],
            ['label' => 'Module 9 Status', 'field' => 'mc_mod9_status', 'type' => 'text'],
            ['label' => 'Assessment – Module 9', 'field' => 'mc_mod9_assessment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 9', 'field' => 'mc_mod9_mentor_call', 'type' => 'text'],
            ['label' => 'Module 10 Status', 'field' => 'mc_mod10_status', 'type' => 'text'],
            ['label' => 'AI Practical Status', 'field' => 'mc_mod10_ai_practical_status', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 10', 'field' => 'mc_mod10_mentor_call', 'type' => 'text'],
            ['label' => 'Module 11 Status', 'field' => 'mc_mod11_status', 'type' => 'text'],
            ['label' => 'Capstone Project Status', 'field' => 'mc_mod11_capstone_project_status', 'type' => 'text'],
            ['label' => 'Resume Preparation', 'field' => 'mc_mod11_resume_preparation', 'type' => 'text'],
            ['label' => 'Mock Interview Status', 'field' => 'mc_mod11_mock_interview_status', 'type' => 'text'],
            ['label' => 'Placement Preparation Status', 'field' => 'mc_mod11_placement_preparation_status', 'type' => 'text'],
            ['label' => 'Final Examination Date', 'field' => 'mc_final_exam_date', 'type' => 'date'],
            ['label' => 'Exam Mark', 'field' => 'mc_final_exam_mark', 'type' => 'text'],
            ['label' => 'Exam Remarks', 'field' => 'mc_final_exam_remarks', 'type' => 'text'],
            ['label' => 'Course Completion Date', 'field' => 'mc_course_completion_date', 'type' => 'date'],
            [
                'label' => 'Course Status',
                'field' => 'mc_course_status',
                'type' => 'select',
                'options' => [
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                    'Hold' => 'Hold',
                    'Dropped' => 'Dropped',
                ],
            ],
            ['label' => 'Attendance Percentage', 'field' => 'mc_attendance_pct', 'type' => 'text'],
            ['label' => 'Assessment Percentage', 'field' => 'mc_assessment_pct', 'type' => 'text'],
            ['label' => 'Module Completion Percentage', 'field' => 'mc_module_completion_pct', 'type' => 'text'],
            ['label' => 'Student Feedback', 'field' => 'mc_student_feedback', 'type' => 'text'],
            ['label' => 'Mentor Remarks', 'field' => 'mc_mentor_remarks', 'type' => 'text'],
        ];
    }

    /** @return string[] */
    public static function mentorDetailFields(): array
    {
        return array_column(self::trackColumns(), 'field');
    }

    /** @return string[] */
    public static function dateFields(): array
    {
        return array_values(array_map(
            fn ($col) => $col['field'],
            array_filter(self::trackColumns(), fn ($col) => ($col['type'] ?? '') === 'date')
        ));
    }

    public static function isTrackField(string $field): bool
    {
        return in_array($field, self::mentorDetailFields(), true);
    }

    public static function trackValue(?ConvertedStudentMentorDetail $mentorDetails, string $field): ?string
    {
        if (! $mentorDetails || ! self::isTrackField($field)) {
            return null;
        }
        $track = $mentorDetails->mc_mentor_track ?? [];

        return isset($track[$field]) && $track[$field] !== '' ? (string) $track[$field] : null;
    }

    public static function setTrackValue(ConvertedStudentMentorDetail $mentorDetails, string $field, $value): void
    {
        $track = $mentorDetails->mc_mentor_track ?? [];
        if ($value === null || $value === '') {
            unset($track[$field]);
        } else {
            $track[$field] = $value;
        }
        $mentorDetails->mc_mentor_track = $track;
    }

    /** @return array<string, string> */
    public static function validationRules(): array
    {
        $rules = [
            'mc_course_status' => 'nullable|in:In Progress,Completed,Hold,Dropped',
            'mc_student_feedback' => 'nullable|string|max:2000',
            'mc_mentor_remarks' => 'nullable|string|max:2000',
        ];

        foreach (self::dateFields() as $field) {
            $rules[$field] = 'nullable|date';
        }

        foreach (self::mentorDetailFields() as $field) {
            if (! isset($rules[$field])) {
                $rules[$field] = 'nullable|string|max:255';
            }
        }

        return $rules;
    }

    public static function validationRuleFor(string $field): ?string
    {
        return self::validationRules()[$field] ?? null;
    }
}
