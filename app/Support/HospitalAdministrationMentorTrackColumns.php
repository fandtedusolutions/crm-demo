<?php

namespace App\Support;

use App\Models\ConvertedStudentMentorDetail;

class HospitalAdministrationMentorTrackColumns
{
    /**
     * @return array<int, array{label: string, field: string, type: string, options?: array<string, string>}>
     */
    public static function trackColumns(): array
    {
        return [
            ['label' => 'Total Course Days', 'field' => 'ha_total_course_days', 'type' => 'text'],
            ['label' => 'Orientation Status', 'field' => 'ha_orientation_status', 'type' => 'text'],
            ['label' => 'Attendance – Module 1', 'field' => 'ha_mod1_attendance', 'type' => 'text'],
            ['label' => 'Assignment – Module 1', 'field' => 'ha_mod1_assignment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 1', 'field' => 'ha_mod1_mentor_call', 'type' => 'text'],
            ['label' => 'Attendance – Module 2', 'field' => 'ha_mod2_attendance', 'type' => 'text'],
            ['label' => 'Assignment – Module 2', 'field' => 'ha_mod2_assignment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 2', 'field' => 'ha_mod2_mentor_call', 'type' => 'text'],
            ['label' => 'First Periodical Test Date', 'field' => 'ha_first_periodical_test_date', 'type' => 'date'],
            ['label' => 'Exam Mark', 'field' => 'ha_first_periodical_exam_mark', 'type' => 'text'],
            ['label' => 'Exam Remarks', 'field' => 'ha_first_periodical_exam_remarks', 'type' => 'text'],
            ['label' => 'Attendance – Module 3', 'field' => 'ha_mod3_attendance', 'type' => 'text'],
            ['label' => 'Assignment – Module 3', 'field' => 'ha_mod3_assignment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 3', 'field' => 'ha_mod3_mentor_call', 'type' => 'text'],
            ['label' => 'Attendance – Module 4', 'field' => 'ha_mod4_attendance', 'type' => 'text'],
            ['label' => 'Assignment – Module 4', 'field' => 'ha_mod4_assignment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 4', 'field' => 'ha_mod4_mentor_call', 'type' => 'text'],
            ['label' => 'Second Periodical Test Date', 'field' => 'ha_second_periodical_test_date', 'type' => 'date'],
            ['label' => 'Exam Mark', 'field' => 'ha_second_periodical_exam_mark', 'type' => 'text'],
            ['label' => 'Exam Remarks', 'field' => 'ha_second_periodical_exam_remarks', 'type' => 'text'],
            ['label' => 'Attendance – Module 5', 'field' => 'ha_mod5_attendance', 'type' => 'text'],
            ['label' => 'Assignment – Module 5', 'field' => 'ha_mod5_assignment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 5', 'field' => 'ha_mod5_mentor_call', 'type' => 'text'],
            ['label' => 'Attendance – Module 6', 'field' => 'ha_mod6_attendance', 'type' => 'text'],
            ['label' => 'Assignment – Module 6', 'field' => 'ha_mod6_assignment', 'type' => 'text'],
            ['label' => 'Mentor Call – Module 6', 'field' => 'ha_mod6_mentor_call', 'type' => 'text'],
            ['label' => 'Practical / Case Study Status', 'field' => 'ha_practical_case_study_status', 'type' => 'text'],
            ['label' => 'Model Examination Date', 'field' => 'ha_model_exam_date', 'type' => 'date'],
            ['label' => 'Exam Mark', 'field' => 'ha_model_exam_mark', 'type' => 'text'],
            ['label' => 'Exam Remarks', 'field' => 'ha_model_exam_remarks', 'type' => 'text'],
            ['label' => 'Final Examination Date', 'field' => 'ha_final_exam_date', 'type' => 'date'],
            ['label' => 'Exam Mark', 'field' => 'ha_final_exam_mark', 'type' => 'text'],
            ['label' => 'Exam Remarks', 'field' => 'ha_final_exam_remarks', 'type' => 'text'],
            ['label' => 'Day 7 Follow-up', 'field' => 'ha_day7_followup', 'type' => 'text'],
            ['label' => 'Day 15 Follow-up', 'field' => 'ha_day15_followup', 'type' => 'text'],
            ['label' => 'Day 30 Follow-up', 'field' => 'ha_day30_followup', 'type' => 'text'],
            ['label' => 'Attendance %', 'field' => 'ha_attendance_pct', 'type' => 'text'],
            ['label' => 'Assignment Completion %', 'field' => 'ha_assignment_completion_pct', 'type' => 'text'],
            ['label' => 'Module Completion %', 'field' => 'ha_module_completion_pct', 'type' => 'text'],
            ['label' => 'Certificate Issued', 'field' => 'ha_certificate_issued', 'type' => 'text'],
            ['label' => 'Placement', 'field' => 'ha_placement', 'type' => 'text'],
            ['label' => 'Course Completion Date', 'field' => 'ha_course_completion_date', 'type' => 'date'],
            [
                'label' => 'Course Status',
                'field' => 'ha_course_status',
                'type' => 'select',
                'options' => [
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                    'Dropped' => 'Dropped',
                    'Hold' => 'Hold',
                ],
            ],
            ['label' => 'Student Feedback', 'field' => 'ha_student_feedback', 'type' => 'text'],
            ['label' => 'Mentor Remarks', 'field' => 'ha_mentor_remarks', 'type' => 'text'],
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
        $track = $mentorDetails->ha_mentor_track ?? [];

        return isset($track[$field]) && $track[$field] !== '' ? (string) $track[$field] : null;
    }

    public static function setTrackValue(ConvertedStudentMentorDetail $mentorDetails, string $field, $value): void
    {
        $track = $mentorDetails->ha_mentor_track ?? [];
        if ($value === null || $value === '') {
            unset($track[$field]);
        } else {
            $track[$field] = $value;
        }
        $mentorDetails->ha_mentor_track = $track;
    }

    /** @return array<string, string> */
    public static function validationRules(): array
    {
        $rules = [
            'ha_course_status' => 'nullable|in:In Progress,Completed,Dropped,Hold',
            'ha_student_feedback' => 'nullable|string|max:2000',
            'ha_mentor_remarks' => 'nullable|string|max:2000',
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
