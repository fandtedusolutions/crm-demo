<?php

namespace App\Support;

use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class CourseCourseTypeSupport
{
    public static function typesFor(?Course $course): Collection
    {
        if (!$course) {
            return collect();
        }

        return $course->activeCourseTypes()->get();
    }

    public static function optionsForSelect(?Course $course): array
    {
        return static::typesFor($course)->pluck('title', 'id')->all();
    }

    public static function validationRules(?Course $course, int $courseId): array
    {
        if (static::typesFor($course)->isEmpty()) {
            return [];
        }

        return [
            'course_type_id' => [
                'required',
                Rule::exists('course_types', 'id')->where(
                    fn ($query) => $query->where('course_id', $courseId)->where('is_active', true)
                ),
            ],
        ];
    }
}
