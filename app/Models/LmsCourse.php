<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LmsCourse extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'title',
    ];

    public function mapping(): HasOne
    {
        return $this->hasOne(LmsCourseMapping::class, 'lms_course_id');
    }

    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            LmsCourseMapping::class,
            'lms_course_id',
            'id',
            'id',
            'course_id'
        );
    }
}
