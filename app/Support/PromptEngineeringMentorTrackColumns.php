<?php

namespace App\Support;

use App\Models\ConvertedStudentMentorDetail;

class PromptEngineeringMentorTrackColumns
{
    /**
     * Ordered mentor-list columns (fields 15–59) after base student columns and mentor flag.
     *
     * @return array<int, array{label: string, field: string, type: string, storage?: string}>
     */
    public static function trackColumns(): array
    {
        return [
            ['label' => 'Total Class Days', 'field' => 'total_class_days', 'type' => 'text'],
            ['label' => 'Orientation Status', 'field' => 'pe_mentor_orientation_status', 'type' => 'text'],
            ['label' => 'Attendance – Day 1', 'field' => 'pe_m_attendance_day_1', 'type' => 'text'],
            ['label' => 'Practical Work – Day 2', 'field' => 'pe_m_practical_day_2', 'type' => 'text'],
            ['label' => 'Attendance – Day 3', 'field' => 'pe_m_attendance_day_3', 'type' => 'text'],
            ['label' => 'Practical Work – Day 4', 'field' => 'pe_m_practical_day_4', 'type' => 'text'],
            ['label' => 'Attendance – Day 5', 'field' => 'pe_m_attendance_day_5', 'type' => 'text'],
            ['label' => 'Call Status – Day 5', 'field' => 'pe_m_call_status_day_5', 'type' => 'text'],
            ['label' => 'Practical Work – Day 6', 'field' => 'pe_m_practical_day_6', 'type' => 'text'],
            ['label' => 'Attendance – Day 7', 'field' => 'pe_m_attendance_day_7', 'type' => 'text'],
            ['label' => 'Practical Work – Day 8', 'field' => 'pe_m_practical_day_8', 'type' => 'text'],
            ['label' => 'Attendance – Day 9', 'field' => 'pe_m_attendance_day_9', 'type' => 'text'],
            ['label' => 'Call Status – Day 9', 'field' => 'pe_m_call_status_day_9', 'type' => 'text'],
            ['label' => 'Practical Work – Day 10', 'field' => 'pe_m_practical_day_10', 'type' => 'text'],
            ['label' => 'First Periodical Test', 'field' => 'pe_first_periodical_test', 'type' => 'text'],
            ['label' => 'Attendance – Day 11', 'field' => 'pe_m_attendance_day_11', 'type' => 'text'],
            ['label' => 'Practical Work – Day 12', 'field' => 'pe_m_practical_day_12', 'type' => 'text'],
            ['label' => 'Attendance – Day 13', 'field' => 'pe_m_attendance_day_13', 'type' => 'text'],
            ['label' => 'Practical Work – Day 14', 'field' => 'pe_m_practical_day_14', 'type' => 'text'],
            ['label' => 'Attendance – Day 15', 'field' => 'pe_m_attendance_day_15', 'type' => 'text'],
            ['label' => 'Call Status – Day 15', 'field' => 'pe_m_call_status_day_15', 'type' => 'text'],
            ['label' => 'Practical Work – Day 16', 'field' => 'pe_m_practical_day_16', 'type' => 'text'],
            ['label' => 'Attendance – Day 17', 'field' => 'pe_m_attendance_day_17', 'type' => 'text'],
            ['label' => 'Practical Work – Day 18', 'field' => 'pe_m_practical_day_18', 'type' => 'text'],
            ['label' => 'Attendance – Day 19', 'field' => 'pe_m_attendance_day_19', 'type' => 'text'],
            ['label' => 'Practical Work – Day 20', 'field' => 'pe_m_practical_day_20', 'type' => 'text'],
            ['label' => 'Call Status – Day 20', 'field' => 'pe_m_call_status_day_20', 'type' => 'text'],
            ['label' => 'Second Periodical Test', 'field' => 'pe_second_periodical_test', 'type' => 'text'],
            ['label' => 'Attendance – Day 21', 'field' => 'pe_m_attendance_day_21', 'type' => 'text'],
            ['label' => 'Practical Work – Day 22', 'field' => 'pe_m_practical_day_22', 'type' => 'text'],
            ['label' => 'Attendance – Day 23', 'field' => 'pe_m_attendance_day_23', 'type' => 'text'],
            ['label' => 'Practical Work – Day 24', 'field' => 'pe_m_practical_day_24', 'type' => 'text'],
            ['label' => 'Attendance – Day 25', 'field' => 'pe_m_attendance_day_25', 'type' => 'text'],
            ['label' => 'Call Status – Day 25', 'field' => 'pe_m_call_status_day_25', 'type' => 'text'],
            ['label' => 'Practical Work – Day 26', 'field' => 'pe_m_practical_day_26', 'type' => 'text'],
            ['label' => 'Attendance – Day 27', 'field' => 'pe_m_attendance_day_27', 'type' => 'text'],
            ['label' => 'Practical Work – Day 28', 'field' => 'pe_m_practical_day_28', 'type' => 'text'],
            ['label' => 'Attendance – Day 29', 'field' => 'pe_m_attendance_day_29', 'type' => 'text'],
            ['label' => 'Call Status – Day 29', 'field' => 'pe_m_call_status_day_29', 'type' => 'text'],
            ['label' => 'Practical Work – Day 30', 'field' => 'pe_m_practical_day_30', 'type' => 'text'],
            ['label' => 'Final Examination', 'field' => 'pe_final_examination', 'type' => 'text'],
            ['label' => 'Course Completion Date', 'field' => 'class_ending_date', 'type' => 'date', 'storage' => 'student'],
            ['label' => 'Course Status', 'field' => 'pe_course_status', 'type' => 'text'],
            ['label' => 'Student Feedback', 'field' => 'pe_student_feedback', 'type' => 'text'],
            ['label' => 'Remarks', 'field' => 'remarks', 'type' => 'text'],
        ];
    }

    /** @return string[] */
    public static function mentorDetailFields(): array
    {
        $fields = [];
        foreach (self::trackColumns() as $col) {
            if (($col['storage'] ?? 'mentor') === 'mentor') {
                $fields[] = $col['field'];
            }
        }

        return $fields;
    }

    /** @return string[] */
    public static function studentDetailFields(): array
    {
        return ['class_ending_date'];
    }

    public static function isDailyTrackField(string $field): bool
    {
        return $field === 'pe_mentor_orientation_status' || str_starts_with($field, 'pe_m_');
    }

    public static function dailyTrackValue(?ConvertedStudentMentorDetail $mentorDetails, string $field): ?string
    {
        if (!$mentorDetails || !self::isDailyTrackField($field)) {
            return null;
        }
        $track = $mentorDetails->pe_mentor_daily_track ?? [];

        return isset($track[$field]) && $track[$field] !== '' ? (string) $track[$field] : null;
    }

    public static function setDailyTrackValue(ConvertedStudentMentorDetail $mentorDetails, string $field, $value): void
    {
        $track = $mentorDetails->pe_mentor_daily_track ?? [];
        if ($value === null || $value === '') {
            unset($track[$field]);
        } else {
            $track[$field] = $value;
        }
        $mentorDetails->pe_mentor_daily_track = $track;
    }

    public static function mentorColumnValue(?ConvertedStudentMentorDetail $mentorDetails, string $field)
    {
        if (!$mentorDetails) {
            return null;
        }
        if (self::isDailyTrackField($field)) {
            return self::dailyTrackValue($mentorDetails, $field);
        }

        return $mentorDetails->$field;
    }

    public static function validationRuleFor(string $field): ?string
    {
        $rules = self::validationRules();

        return $rules[$field] ?? null;
    }

    /** @return array<string, string> */
    public static function validationRules(): array
    {
        $rules = [
            'total_class_days' => 'nullable|integer|min:0',
            'class_ending_date' => 'nullable|date',
            'pe_student_feedback' => 'nullable|string|max:2000',
            'remarks' => 'nullable|string|max:2000',
            'dob' => 'nullable|date|before_or_equal:today',
        ];

        foreach (self::mentorDetailFields() as $field) {
            if (isset($rules[$field])) {
                continue;
            }
            if ($field === 'pe_student_feedback') {
                continue;
            }
            $rules[$field] = 'nullable|string|max:255';
        }

        return $rules;
    }

    /** @return string[] */
    public static function dateFieldsForDisplay(): array
    {
        return ['class_ending_date', 'dob'];
    }
}
