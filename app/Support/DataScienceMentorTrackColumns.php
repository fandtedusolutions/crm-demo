<?php

namespace App\Support;

use App\Models\ConvertedStudentMentorDetail;
use Carbon\Carbon;

class DataScienceMentorTrackColumns
{
    /**
     * @return array<int, array{label: string, field: string, type: string, options?: array<string, string>}>
     */
    public static function trackColumns(): array
    {
        return [
            [
                'label' => 'Current Month',
                'field' => 'current_month',
                'type' => 'select',
                'options' => [
                    'Not Started' => 'Not Started',
                    'Month 1' => 'Month 1',
                    'Month 2' => 'Month 2',
                    'Month 3' => 'Month 3',
                    'Month 4' => 'Month 4',
                    'Month 5' => 'Month 5',
                    'Month 6' => 'Month 6',
                    'Completed' => 'Completed',
                ],
            ],
            [
                'label' => 'Current Module',
                'field' => 'current_module',
                'type' => 'select',
                'options' => [
                    'Orientation' => 'Orientation',
                    'Python Programming Fundamentals' => 'Python Programming Fundamentals',
                    'SQL Databases & Excel' => 'SQL Databases & Excel',
                    'Data Analysis & Visualization' => 'Data Analysis & Visualization',
                    'Machine Learning' => 'Machine Learning',
                    'Deep Learning & Generative AI' => 'Deep Learning & Generative AI',
                    'Capstone Project & Placement' => 'Capstone Project & Placement',
                    'Completed' => 'Completed',
                ],
            ],
            ['label' => 'Current Academic Stage', 'field' => 'current_academic_stage', 'type' => 'text'],
            [
                'label' => 'Current Status',
                'field' => 'current_status',
                'type' => 'select',
                'options' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                    'On Hold' => 'On Hold',
                    'Dropped' => 'Dropped',
                ],
            ],
            ['label' => 'Mentor Remarks', 'field' => 'mentor_remarks', 'type' => 'text'],
            ['label' => 'M1 Exam Date', 'field' => 'm1_exam_date', 'type' => 'date'],
            ['label' => 'M1 Marks', 'field' => 'm1_marks', 'type' => 'text'],
            ['label' => 'M2 Exam Date', 'field' => 'm2_exam_date', 'type' => 'date'],
            ['label' => 'M2 Marks', 'field' => 'm2_marks', 'type' => 'text'],
            ['label' => 'M3 Exam Date', 'field' => 'm3_exam_date', 'type' => 'date'],
            ['label' => 'M3 Marks', 'field' => 'm3_marks', 'type' => 'text'],
            ['label' => 'M4 Exam Date', 'field' => 'm4_exam_date', 'type' => 'date'],
            ['label' => 'M4 Marks', 'field' => 'm4_marks', 'type' => 'text'],
            ['label' => 'M5 Exam Date', 'field' => 'm5_exam_date', 'type' => 'date'],
            ['label' => 'M5 Marks', 'field' => 'm5_marks', 'type' => 'text'],
            ['label' => 'M6 / Final Exam Date', 'field' => 'm6_final_exam_date', 'type' => 'date'],
            ['label' => 'M6 Marks', 'field' => 'm6_marks', 'type' => 'text'],
            ['label' => 'M6 Feedback', 'field' => 'm6_feedback', 'type' => 'text'],
            [
                'label' => 'M1 Project',
                'field' => 'm1_project',
                'type' => 'select',
                'options' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                ],
            ],
            [
                'label' => 'M2 Project',
                'field' => 'm2_project',
                'type' => 'select',
                'options' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                ],
            ],
            [
                'label' => 'M3 EDA Project',
                'field' => 'm3_eda_project',
                'type' => 'select',
                'options' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                ],
            ],
            [
                'label' => 'M4 ML Project',
                'field' => 'm4_ml_project',
                'type' => 'select',
                'options' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                ],
            ],
            [
                'label' => 'M5 GenAI Project',
                'field' => 'm5_genai_project',
                'type' => 'select',
                'options' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                ],
            ],
            [
                'label' => 'Capstone Project',
                'field' => 'capstone_project',
                'type' => 'select',
                'options' => [
                    'Not Started' => 'Not Started',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                ],
            ],
            ['label' => 'Mock Test Date', 'field' => 'mock_test_date', 'type' => 'date'],
            ['label' => 'Mock Test Marks', 'field' => 'mock_test_marks', 'type' => 'text'],
            ['label' => 'Mock Test Feedback', 'field' => 'mock_test_feedback', 'type' => 'text'],
            ['label' => 'Mock Interview Date', 'field' => 'mock_interview_date', 'type' => 'date'],
            ['label' => 'Mock Interview Marks', 'field' => 'mock_interview_marks', 'type' => 'text'],
            ['label' => 'Mock Interview Feedback', 'field' => 'mock_interview_feedback', 'type' => 'text'],
            ['label' => 'Internship Status', 'field' => 'internship_status', 'type' => 'text'],
            ['label' => 'Final Interview Date', 'field' => 'final_interview_date', 'type' => 'date'],
            ['label' => 'Interview Marks', 'field' => 'interview_marks', 'type' => 'text'],
            ['label' => 'Certificate Examination Date', 'field' => 'final_certificate_examination_date', 'type' => 'date'],
            ['label' => 'Certificate Examination Marks', 'field' => 'certificate_examination_marks', 'type' => 'text'],
            ['label' => 'Certificate Distribution Date', 'field' => 'certificate_distribution_date', 'type' => 'date'],
            ['label' => 'Experience Certificate Distribution Date', 'field' => 'experience_certificate_distribution_date', 'type' => 'date'],
            ['label' => 'Cancelled Date', 'field' => 'cancelled_date', 'type' => 'date'],
            ['label' => 'Final Remarks', 'field' => 'final_remarks', 'type' => 'text'],
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

    /** @return string[] */
    public static function selectFields(): array
    {
        return array_values(array_map(
            fn ($col) => $col['field'],
            array_filter(self::trackColumns(), fn ($col) => ($col['type'] ?? '') === 'select')
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

        $track = $mentorDetails->ds_mentor_track ?? [];

        if (isset($track[$field]) && $track[$field] !== '') {
            return (string) $track[$field];
        }

        // Shared faculty track fallbacks
        $facultyTrack = $mentorDetails->ds_faculty_track ?? [];
        $facultyMap = [
            'current_month' => 'current_academic_month',
            'current_module' => 'current_module',
            'm1_marks' => 'm1_exam_mark',
            'm2_marks' => 'm2_exam_mark',
            'm3_marks' => 'm3_exam_mark',
            'm4_marks' => 'm4_exam_mark',
            'm5_marks' => 'm5_exam_mark',
            'm6_marks' => 'm6_exam_mark',
        ];

        if (isset($facultyMap[$field])) {
            $facKey = $facultyMap[$field];
            if (isset($facultyTrack[$facKey]) && $facultyTrack[$facKey] !== '') {
                return (string) $facultyTrack[$facKey];
            }
        }

        // Direct database column fallbacks
        $fallbacks = [
            'm1_exam_date' => 'first_month_exam_date',
            'm1_marks' => 'first_month_marks',
            'm2_exam_date' => 'second_month_exam_date',
            'm2_marks' => 'second_month_marks',
            'm3_exam_date' => 'third_month_exam_date',
            'm3_marks' => 'third_month_marks',
            'm4_exam_date' => 'fourth_month_exam_date',
            'm4_marks' => 'fourth_month_marks',
            'mock_test_date' => 'mock_test_date',
            'mock_test_marks' => 'mock_test_marks',
            'mock_test_feedback' => 'mock_test_feedback',
            'mock_interview_date' => 'mock_interview_date',
            'mock_interview_marks' => 'mock_interview_marks',
            'mock_interview_feedback' => 'mock_interview_feedback',
            'final_interview_date' => 'final_interview_date',
            'interview_marks' => 'interview_marks',
            'final_certificate_examination_date' => 'final_certificate_examination_date',
            'certificate_examination_marks' => 'certificate_examination_marks',
            'certificate_distribution_date' => 'certificate_distribution_date',
            'experience_certificate_distribution_date' => 'experience_certificate_distribution_date',
            'cancelled_date' => 'cancelled_date',
            'final_remarks' => 'remarks',
            'mentor_remarks' => 'remarks',
        ];

        if (isset($fallbacks[$field])) {
            $directProp = $fallbacks[$field];
            $val = $mentorDetails->$directProp;
            if ($val !== null && $val !== '') {
                if ($val instanceof \DateTimeInterface) {
                    return $val->format('Y-m-d');
                }
                return (string) $val;
            }
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

        if ($field === 'current_month') {
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
        $track = $mentorDetails->ds_mentor_track ?? [];
        if ($value === null || $value === '') {
            unset($track[$field]);
        } else {
            $track[$field] = $value;
        }
        $mentorDetails->ds_mentor_track = $track;

        // Synchronize shared fields into ds_faculty_track
        $facultyTrack = $mentorDetails->ds_faculty_track ?? [];
        $facultyMap = [
            'current_month' => 'current_academic_month',
            'current_module' => 'current_module',
            'm1_marks' => 'm1_exam_mark',
            'm2_marks' => 'm2_exam_mark',
            'm3_marks' => 'm3_exam_mark',
            'm4_marks' => 'm4_exam_mark',
            'm5_marks' => 'm5_exam_mark',
            'm6_marks' => 'm6_exam_mark',
        ];

        if (isset($facultyMap[$field])) {
            $facKey = $facultyMap[$field];
            if ($value === null || $value === '') {
                unset($facultyTrack[$facKey]);
            } else {
                $facultyTrack[$facKey] = $value;
            }
            $mentorDetails->ds_faculty_track = $facultyTrack;
        }

        // Synchronize with direct database columns if they exist
        $directMap = [
            'm1_marks' => 'first_month_marks',
            'm2_marks' => 'second_month_marks',
            'm3_marks' => 'third_month_marks',
            'm4_marks' => 'fourth_month_marks',
            'mock_test_date' => 'mock_test_date',
            'mock_test_marks' => 'mock_test_marks',
            'mock_test_feedback' => 'mock_test_feedback',
            'mock_interview_date' => 'mock_interview_date',
            'mock_interview_marks' => 'mock_interview_marks',
            'mock_interview_feedback' => 'mock_interview_feedback',
            'final_interview_date' => 'final_interview_date',
            'interview_marks' => 'interview_marks',
            'final_certificate_examination_date' => 'final_certificate_examination_date',
            'certificate_examination_marks' => 'certificate_examination_marks',
            'certificate_distribution_date' => 'certificate_distribution_date',
            'experience_certificate_distribution_date' => 'experience_certificate_distribution_date',
            'cancelled_date' => 'cancelled_date',
            'final_remarks' => 'remarks',
        ];

        if (isset($directMap[$field])) {
            $col = $directMap[$field];
            $mentorDetails->$col = ($value === '' ? null : $value);
        }
    }

    /** @return array<string, string> */
    public static function validationRules(): array
    {
        $rules = [
            'current_month' => 'nullable|in:Not Started,Month 1,Month 2,Month 3,Month 4,Month 5,Month 6,Completed',
            'current_module' => 'nullable|in:Orientation,Python Programming Fundamentals,SQL Databases & Excel,Data Analysis & Visualization,Machine Learning,Deep Learning & Generative AI,Capstone Project & Placement,Completed',
            'current_status' => 'nullable|in:Not Started,In Progress,Completed,On Hold,Dropped',
            'm1_project' => 'nullable|in:Not Started,In Progress,Completed',
            'm2_project' => 'nullable|in:Not Started,In Progress,Completed',
            'm3_eda_project' => 'nullable|in:Not Started,In Progress,Completed',
            'm4_ml_project' => 'nullable|in:Not Started,In Progress,Completed',
            'm5_genai_project' => 'nullable|in:Not Started,In Progress,Completed',
            'capstone_project' => 'nullable|in:Not Started,In Progress,Completed',
            'current_academic_stage' => 'nullable|string|max:255',
            'mentor_remarks' => 'nullable|string|max:2000',
            'm1_marks' => 'nullable|string|max:255',
            'm2_marks' => 'nullable|string|max:255',
            'm3_marks' => 'nullable|string|max:255',
            'm4_marks' => 'nullable|string|max:255',
            'm5_marks' => 'nullable|string|max:255',
            'm6_marks' => 'nullable|string|max:255',
            'm6_feedback' => 'nullable|string|max:2000',
            'mock_test_marks' => 'nullable|string|max:255',
            'mock_test_feedback' => 'nullable|string|max:2000',
            'mock_interview_marks' => 'nullable|string|max:255',
            'mock_interview_feedback' => 'nullable|string|max:2000',
            'internship_status' => 'nullable|string|max:255',
            'interview_marks' => 'nullable|string|max:255',
            'certificate_examination_marks' => 'nullable|string|max:255',
            'final_remarks' => 'nullable|string|max:2000',
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
