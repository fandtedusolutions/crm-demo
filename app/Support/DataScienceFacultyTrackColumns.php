<?php

namespace App\Support;

use App\Models\ConvertedStudentMentorDetail;
use Carbon\Carbon;

class DataScienceFacultyTrackColumns
{
    /**
     * @return array<int, array{label: string, field: string, type: string, options?: array<string, string>}>
     */
    public static function trackColumns(): array
    {
        return [
            ['label' => 'Current Academic Month', 'field' => 'current_academic_month', 'type' => 'month'],
            ['label' => 'Current Module', 'field' => 'current_module', 'type' => 'text'],

            // Month 1
            ['label' => 'M1 Class Start Date', 'field' => 'm1_class_start_date', 'type' => 'date'],
            ['label' => 'M1 Assignment Date', 'field' => 'm1_assignment_date', 'type' => 'date'],
            ['label' => 'M1 Project Start Date', 'field' => 'm1_project_start_date', 'type' => 'date'],
            ['label' => 'M1 Exam Mark', 'field' => 'm1_exam_mark', 'type' => 'text'],
            ['label' => 'M1 Total Working/Present Days', 'field' => 'm1_total_working_days', 'type' => 'text'],
            ['label' => 'M1 Attendance %', 'field' => 'm1_attendance_percentage', 'type' => 'text'],

            // Month 2
            ['label' => 'M2 Class Start Date', 'field' => 'm2_class_start_date', 'type' => 'date'],
            ['label' => 'M2 Assignment Date', 'field' => 'm2_assignment_date', 'type' => 'date'],
            ['label' => 'M2 Project Start Date', 'field' => 'm2_project_start_date', 'type' => 'date'],
            ['label' => 'M2 Exam Mark', 'field' => 'm2_exam_mark', 'type' => 'text'],
            ['label' => 'M2 Total Working/Present Days', 'field' => 'm2_total_working_days', 'type' => 'text'],
            ['label' => 'M2 Attendance %', 'field' => 'm2_attendance_percentage', 'type' => 'text'],

            // Month 3
            ['label' => 'M3 Class Start Date', 'field' => 'm3_class_start_date', 'type' => 'date'],
            ['label' => 'M3 Assignment Date', 'field' => 'm3_assignment_date', 'type' => 'date'],
            ['label' => 'M3 Project Start Date', 'field' => 'm3_project_start_date', 'type' => 'date'],
            ['label' => 'M3 Exam Mark', 'field' => 'm3_exam_mark', 'type' => 'text'],
            ['label' => 'M3 Total Working/Present Days', 'field' => 'm3_total_working_days', 'type' => 'text'],
            ['label' => 'M3 Attendance %', 'field' => 'm3_attendance_percentage', 'type' => 'text'],

            // Month 4
            ['label' => 'M4 Class Start Date', 'field' => 'm4_class_start_date', 'type' => 'date'],
            ['label' => 'M4 Assignment Date', 'field' => 'm4_assignment_date', 'type' => 'date'],
            ['label' => 'M4 Project Start Date', 'field' => 'm4_project_start_date', 'type' => 'date'],
            ['label' => 'M4 Exam Mark', 'field' => 'm4_exam_mark', 'type' => 'text'],
            ['label' => 'M4 Total Working/Present Days', 'field' => 'm4_total_working_days', 'type' => 'text'],
            ['label' => 'M4 Attendance %', 'field' => 'm4_attendance_percentage', 'type' => 'text'],

            // Month 5
            ['label' => 'M5 Class Start Date', 'field' => 'm5_class_start_date', 'type' => 'date'],
            ['label' => 'M5 Assignment Date', 'field' => 'm5_assignment_date', 'type' => 'date'],
            ['label' => 'M5 Project Start Date', 'field' => 'm5_project_start_date', 'type' => 'date'],
            ['label' => 'M5 Exam Mark', 'field' => 'm5_exam_mark', 'type' => 'text'],
            ['label' => 'M5 Total Working/Present Days', 'field' => 'm5_total_working_days', 'type' => 'text'],
            ['label' => 'M5 Attendance %', 'field' => 'm5_attendance_percentage', 'type' => 'text'],

            // Month 6
            ['label' => 'M6 Class Start Date', 'field' => 'm6_class_start_date', 'type' => 'date'],
            ['label' => 'M6 Assignment Date', 'field' => 'm6_assignment_date', 'type' => 'date'],
            ['label' => 'M6 Project Start Date', 'field' => 'm6_project_start_date', 'type' => 'date'],
            ['label' => 'M6 Exam Mark', 'field' => 'm6_exam_mark', 'type' => 'text'],
            ['label' => 'M6 Total Working/Present Days', 'field' => 'm6_total_working_days', 'type' => 'text'],
            ['label' => 'M6 Attendance %', 'field' => 'm6_attendance_percentage', 'type' => 'text'],

            // Overall Attendance
            ['label' => 'Total Scheduled Classes', 'field' => 'total_scheduled_classes', 'type' => 'text'],
            ['label' => 'Total Classes Conducted', 'field' => 'total_classes_conducted', 'type' => 'text'],
            ['label' => 'Total Present', 'field' => 'total_present', 'type' => 'text'],
            ['label' => 'Total Absent', 'field' => 'total_absent', 'type' => 'text'],
            ['label' => 'Attendance %', 'field' => 'attendance_percentage', 'type' => 'text'],

            // Faculty Remarks & Action
            ['label' => 'Faculty Overall Remarks', 'field' => 'faculty_overall_remarks', 'type' => 'text'],
            ['label' => 'Pending Academic Activities', 'field' => 'pending_academic_activities', 'type' => 'text'],
            ['label' => 'Remarks', 'field' => 'faculty_remarks', 'type' => 'text'],
        ];
    }

    /** @return string[] */
    public static function trackDetailFields(): array
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

    /** @return string[] */
    public static function monthFields(): array
    {
        return array_values(array_map(
            fn ($col) => $col['field'],
            array_filter(self::trackColumns(), fn ($col) => ($col['type'] ?? '') === 'month')
        ));
    }

    public static function isTrackField(string $field): bool
    {
        return in_array($field, self::trackDetailFields(), true);
    }

    public static function trackValue(?ConvertedStudentMentorDetail $mentorDetails, string $field): ?string
    {
        if (! $mentorDetails || ! self::isTrackField($field)) {
            return null;
        }

        $track = $mentorDetails->ds_faculty_track ?? [];

        if (isset($track[$field]) && $track[$field] !== '') {
            return (string) $track[$field];
        }

        // Shared fields fallbacks to mentor track & direct columns
        $mentorTrack = $mentorDetails->ds_mentor_track ?? [];

        if ($field === 'current_academic_month' && isset($mentorTrack['current_month']) && $mentorTrack['current_month'] !== '') {
            return (string) $mentorTrack['current_month'];
        }

        if ($field === 'current_module' && isset($mentorTrack['current_module']) && $mentorTrack['current_module'] !== '') {
            return (string) $mentorTrack['current_module'];
        }

        if ($field === 'm1_exam_mark') {
            if (isset($mentorTrack['m1_marks']) && $mentorTrack['m1_marks'] !== '') {
                return (string) $mentorTrack['m1_marks'];
            }
            if ($mentorDetails->first_month_marks !== null && $mentorDetails->first_month_marks !== '') {
                return (string) $mentorDetails->first_month_marks;
            }
        }

        if ($field === 'm2_exam_mark') {
            if (isset($mentorTrack['m2_marks']) && $mentorTrack['m2_marks'] !== '') {
                return (string) $mentorTrack['m2_marks'];
            }
            if ($mentorDetails->second_month_marks !== null && $mentorDetails->second_month_marks !== '') {
                return (string) $mentorDetails->second_month_marks;
            }
        }

        if ($field === 'm3_exam_mark') {
            if (isset($mentorTrack['m3_marks']) && $mentorTrack['m3_marks'] !== '') {
                return (string) $mentorTrack['m3_marks'];
            }
            if ($mentorDetails->third_month_marks !== null && $mentorDetails->third_month_marks !== '') {
                return (string) $mentorDetails->third_month_marks;
            }
        }

        if ($field === 'm4_exam_mark') {
            if (isset($mentorTrack['m4_marks']) && $mentorTrack['m4_marks'] !== '') {
                return (string) $mentorTrack['m4_marks'];
            }
            if ($mentorDetails->fourth_month_marks !== null && $mentorDetails->fourth_month_marks !== '') {
                return (string) $mentorDetails->fourth_month_marks;
            }
        }

        if ($field === 'm5_exam_mark' && isset($mentorTrack['m5_marks']) && $mentorTrack['m5_marks'] !== '') {
            return (string) $mentorTrack['m5_marks'];
        }

        if ($field === 'm6_exam_mark' && isset($mentorTrack['m6_marks']) && $mentorTrack['m6_marks'] !== '') {
            return (string) $mentorTrack['m6_marks'];
        }

        if ($field === 'total_present' && $mentorDetails->total_present !== null && $mentorDetails->total_present !== '') {
            return (string) $mentorDetails->total_present;
        }

        if ($field === 'total_absent' && $mentorDetails->total_absent !== null && $mentorDetails->total_absent !== '') {
            return (string) $mentorDetails->total_absent;
        }

        if ($field === 'faculty_remarks' && $mentorDetails->remarks !== null && $mentorDetails->remarks !== '') {
            return (string) $mentorDetails->remarks;
        }

        return null;
    }

    public static function displayValue(?ConvertedStudentMentorDetail $mentorDetails, string $field): string
    {
        $val = self::trackValue($mentorDetails, $field);
        if ($val === null || $val === '') {
            return '-';
        }

        if (in_array($field, self::dateFields(), true)) {
            try {
                return Carbon::parse($val)->format('d-m-Y');
            } catch (\Exception $e) {
                return $val;
            }
        }

        if ($field === 'current_academic_month') {
            try {
                if (preg_match('/^\d{4}-\d{2}$/', $val)) {
                    return Carbon::createFromFormat('Y-m', $val)->format('M Y');
                }
            } catch (\Exception $e) {
                return $val;
            }
        }

        return $val;
    }

    public static function setTrackValue(ConvertedStudentMentorDetail $mentorDetails, string $field, $value): void
    {
        $track = $mentorDetails->ds_faculty_track ?? [];
        if ($value === null || $value === '') {
            unset($track[$field]);
        } else {
            $track[$field] = $value;
        }
        $mentorDetails->ds_faculty_track = $track;

        $mentorTrack = $mentorDetails->ds_mentor_track ?? [];
        $valOrNull = ($value === '' ? null : $value);

        if ($field === 'current_academic_month') {
            if ($valOrNull === null) {
                unset($mentorTrack['current_month']);
            } else {
                $mentorTrack['current_month'] = $value;
            }
        } elseif ($field === 'current_module') {
            if ($valOrNull === null) {
                unset($mentorTrack['current_module']);
            } else {
                $mentorTrack['current_module'] = $value;
            }
        } elseif ($field === 'm1_exam_mark') {
            if ($valOrNull === null) {
                unset($mentorTrack['m1_marks']);
            } else {
                $mentorTrack['m1_marks'] = $value;
            }
            $mentorDetails->first_month_marks = $valOrNull;
        } elseif ($field === 'm2_exam_mark') {
            if ($valOrNull === null) {
                unset($mentorTrack['m2_marks']);
            } else {
                $mentorTrack['m2_marks'] = $value;
            }
            $mentorDetails->second_month_marks = $valOrNull;
        } elseif ($field === 'm3_exam_mark') {
            if ($valOrNull === null) {
                unset($mentorTrack['m3_marks']);
            } else {
                $mentorTrack['m3_marks'] = $value;
            }
            $mentorDetails->third_month_marks = $valOrNull;
        } elseif ($field === 'm4_exam_mark') {
            if ($valOrNull === null) {
                unset($mentorTrack['m4_marks']);
            } else {
                $mentorTrack['m4_marks'] = $value;
            }
            $mentorDetails->fourth_month_marks = $valOrNull;
        } elseif ($field === 'm5_exam_mark') {
            if ($valOrNull === null) {
                unset($mentorTrack['m5_marks']);
            } else {
                $mentorTrack['m5_marks'] = $value;
            }
        } elseif ($field === 'm6_exam_mark') {
            if ($valOrNull === null) {
                unset($mentorTrack['m6_marks']);
            } else {
                $mentorTrack['m6_marks'] = $value;
            }
        } elseif ($field === 'total_present') {
            $mentorDetails->total_present = $valOrNull;
        } elseif ($field === 'total_absent') {
            $mentorDetails->total_absent = $valOrNull;
        } elseif ($field === 'faculty_remarks') {
            $mentorDetails->remarks = $valOrNull;
        }

        $mentorDetails->ds_mentor_track = $mentorTrack;
    }

    /** @return array<string, string> */
    public static function validationRules(): array
    {
        $rules = [
            'current_academic_month' => 'nullable|string|max:50',
            'current_module' => 'nullable|string|max:255',
            'm1_exam_mark' => 'nullable|string|max:255',
            'm1_total_working_days' => 'nullable|string|max:255',
            'm1_attendance_percentage' => 'nullable|string|max:255',
            'm2_exam_mark' => 'nullable|string|max:255',
            'm2_total_working_days' => 'nullable|string|max:255',
            'm2_attendance_percentage' => 'nullable|string|max:255',
            'm3_exam_mark' => 'nullable|string|max:255',
            'm3_total_working_days' => 'nullable|string|max:255',
            'm3_attendance_percentage' => 'nullable|string|max:255',
            'm4_exam_mark' => 'nullable|string|max:255',
            'm4_total_working_days' => 'nullable|string|max:255',
            'm4_attendance_percentage' => 'nullable|string|max:255',
            'm5_exam_mark' => 'nullable|string|max:255',
            'm5_total_working_days' => 'nullable|string|max:255',
            'm5_attendance_percentage' => 'nullable|string|max:255',
            'm6_exam_mark' => 'nullable|string|max:255',
            'm6_total_working_days' => 'nullable|string|max:255',
            'm6_attendance_percentage' => 'nullable|string|max:255',
            'total_scheduled_classes' => 'nullable|string|max:255',
            'total_classes_conducted' => 'nullable|string|max:255',
            'total_present' => 'nullable|string|max:255',
            'total_absent' => 'nullable|string|max:255',
            'attendance_percentage' => 'nullable|string|max:255',
            'faculty_overall_remarks' => 'nullable|string|max:2000',
            'pending_academic_activities' => 'nullable|string|max:2000',
            'faculty_remarks' => 'nullable|string|max:2000',
        ];

        foreach (self::dateFields() as $field) {
            $rules[$field] = 'nullable|date';
        }

        return $rules;
    }

    public static function validationRuleFor(string $field): ?string
    {
        return self::validationRules()[$field] ?? null;
    }
}
