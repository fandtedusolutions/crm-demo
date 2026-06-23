<?php

namespace App\Helpers;

class CourseTitleHelper
{
    public const TITLES = [
        1 => 'National Institute of Open Schooling',
        2 => 'Board of Open Schooling and Skill Education',
        3 => 'Certificate Course in Medical Coding',
        4 => 'Diploma in Hospital Administration',
        11 => 'AI Integrated Digital Marketing',
        15 => 'Diploma in Graphic Designing',
        16 => 'Grameen Mukt Vidhyalayi Shiksha Sansthan',
        25 => 'CreateX AI',
    ];

    public static function title(int $courseId, ?string $fallback = null): string
    {
        return self::TITLES[$courseId] ?? ($fallback ?? '');
    }
}
