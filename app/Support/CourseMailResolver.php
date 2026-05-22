<?php

namespace App\Support;

use App\Models\ConvertedLead;
use App\Models\CourseMail;
use Illuminate\Support\Collection;

class CourseMailResolver
{
    /**
     * All mail templates for a course (for template picker).
     */
    public static function listForCourse(int $courseId): Collection
    {
        if (! $courseId) {
            return collect();
        }

        return CourseMail::query()
            ->with(['course', 'batch', 'admissionBatch'])
            ->where('course_id', $courseId)
            ->orderBy('batch_id')
            ->orderByRaw('admission_batch_id IS NULL')
            ->orderBy('admission_batch_id')
            ->orderByDesc('updated_at')
            ->get();
    }

    public static function formatTemplateLabel(CourseMail $courseMail): string
    {
        $parts = array_filter([
            $courseMail->course?->title,
            $courseMail->batch?->title,
            $courseMail->admission_batch_id
                ? ($courseMail->admissionBatch?->title ?? 'Admission Batch #'.$courseMail->admission_batch_id)
                : 'All Admission Batches',
        ]);

        return $parts ? implode(' · ', $parts) : 'Mail template #'.$courseMail->id;
    }

    public static function templateToArray(CourseMail $courseMail, bool $isDefault = false): array
    {
        return [
            'id' => $courseMail->id,
            'label' => self::formatTemplateLabel($courseMail),
            'course_id' => $courseMail->course_id,
            'batch_id' => $courseMail->batch_id,
            'admission_batch_id' => $courseMail->admission_batch_id,
            'content' => $courseMail->content,
            'is_default' => $isDefault,
        ];
    }
    /**
     * Resolve course mail template for a converted lead (course + batch + admission batch).
     * Prefers an exact admission-batch match, then falls back to "all admission batches" (null).
     */
    public static function resolveForConvertedLead(ConvertedLead $convertedLead): ?CourseMail
    {
        $courseId = (int) $convertedLead->course_id;
        $batchId = (int) $convertedLead->batch_id;

        if (! $courseId || ! $batchId) {
            return null;
        }

        $baseQuery = CourseMail::query()
            ->where('course_id', $courseId)
            ->where('batch_id', $batchId);

        $admissionBatchId = $convertedLead->admission_batch_id
            ? (int) $convertedLead->admission_batch_id
            : null;

        if ($admissionBatchId) {
            $exact = (clone $baseQuery)
                ->where('admission_batch_id', $admissionBatchId)
                ->first();

            if ($exact) {
                return $exact;
            }
        }

        $allBatches = (clone $baseQuery)
            ->whereNull('admission_batch_id')
            ->first();

        if ($allBatches) {
            return $allBatches;
        }

        return (clone $baseQuery)
            ->whereNotNull('admission_batch_id')
            ->orderByDesc('updated_at')
            ->first();
    }

    public static function defaultSubject(ConvertedLead $convertedLead): string
    {
        $courseTitle = $convertedLead->course?->title ?? 'Course';

        return $courseTitle.' - Important Information';
    }
}
