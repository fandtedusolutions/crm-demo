<?php

namespace App\Helpers;

use App\Models\Course;
use Illuminate\Support\Facades\Schema;

class CourseTitleHelper
{
    /**
     * Used only when the courses table has no row for this id.
     */
    public const TITLES = [
        1 => 'National Institute of Open Schooling',
        2 => 'Board of Open Schooling and Skill Education',
        3 => 'Certificate Course in Medical Coding',
        4 => 'Diploma in Hospital Administration',
        5 => 'E-School',
        6 => 'Eduthanzeel',
        7 => 'TTC',
        8 => 'Hotel Management',
        9 => 'UG/PG',
        10 => 'Python',
        11 => 'AI Integrated Digital Marketing + Graphic Designing',
        12 => 'Diploma in Data Science',
        13 => 'Web Development & Designing',
        14 => 'Vibe Coding',
        15 => 'Diploma in Graphic Designing',
        16 => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
        20 => 'Diploma in Machine Learning',
        21 => 'Flutter',
        23 => 'EduMaster',
        25 => 'CreateX AI',
        27 => 'RPA',
        29 => 'AI-Integrated Sales & Marketing',
        30 => 'AI-Integrated Video Editing',
        31 => 'AI-Integrated Videography',
        32 => 'AI-Integrated Photography',
        33 => 'Robo Vibe',
        34 => 'Prompt Engineering',
        37 => 'Data Analytics with Gen AI',
    ];

    public static function title(int $courseId, ?string $fallback = null): string
    {
        $titles = self::titlesFromDatabase();
        if (isset($titles[$courseId]) && $titles[$courseId] !== '') {
            return $titles[$courseId];
        }

        if (isset(self::TITLES[$courseId]) && self::TITLES[$courseId] !== '') {
            return self::TITLES[$courseId];
        }

        return $fallback ?? '';
    }

    /**
     * @return array<int, string>
     */
    public static function titlesFromDatabase(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        try {
            if (! Schema::hasTable('courses')) {
                return $cache = [];
            }

            $cache = [];
            foreach (Course::query()->pluck('title', 'id') as $id => $title) {
                $cache[(int) $id] = trim((string) $title);
            }

            return $cache;
        } catch (\Throwable $e) {
            return $cache = [];
        }
    }
}
