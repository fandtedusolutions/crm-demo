<?php

namespace App\Support;

use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class CourseOfflinePlaceSupport
{
    public static function placesFor(?Course $course): Collection
    {
        if (!$course || !$course->is_offline) {
            return collect();
        }

        return $course->activeOfflinePlaces()->get();
    }

    public static function placeNamesFor(?Course $course): array
    {
        return static::placesFor($course)->pluck('name')->all();
    }

    public static function optionsForSelect(?Course $course): array
    {
        return static::placesFor($course)->pluck('name', 'name')->all();
    }

    public static function locationValidationRules(?Course $course): array
    {
        $names = static::placeNamesFor($course);

        if ($names === []) {
            return ['location' => 'nullable|string|max:255'];
        }

        return [
            'location' => [
                'nullable',
                'required_if:programme_type,offline',
                Rule::in($names),
            ],
        ];
    }

    public static function isValidLocation(?Course $course, ?string $location): bool
    {
        if ($location === null || $location === '') {
            return true;
        }

        $names = static::placeNamesFor($course);

        if ($names === []) {
            return true;
        }

        return in_array($location, $names, true);
    }

    public static function syncForCourse(Course $course, ?array $offlinePlaceIds, bool $isOffline): void
    {
        if (!$isOffline) {
            $course->offlinePlaces()->detach();

            return;
        }

        $course->offlinePlaces()->sync($offlinePlaceIds ?? []);
    }
}
