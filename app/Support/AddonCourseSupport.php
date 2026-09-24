<?php

namespace App\Support;

use App\Models\AddonCourse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AddonCourseSupport
{
    public static function forCourse(?int $courseId): Collection
    {
        if (!$courseId) {
            return collect();
        }

        return AddonCourse::query()
            ->with('addonCourse:id,title,is_active')
            ->where('course_id', $courseId)
            ->where('is_active', true)
            ->whereHas('addonCourse', fn ($query) => $query->where('is_active', true))
            ->get()
            ->sortBy(fn ($row) => mb_strtolower($row->addonCourse->title ?? ''))
            ->values();
    }

    public static function isValid(?int $courseId, $addonCourseId): bool
    {
        if (!$courseId || $addonCourseId === null || $addonCourseId === '') {
            return false;
        }

        return AddonCourse::query()
            ->where('course_id', $courseId)
            ->where('addon_course_id', $addonCourseId)
            ->where('is_active', true)
            ->whereHas('addonCourse', fn ($query) => $query->where('is_active', true))
            ->exists();
    }

    public static function applyConvertedLeadCourseListing(Builder $query, $courseId): Builder
    {
        return $query->forCourseListing($courseId);
    }
}
