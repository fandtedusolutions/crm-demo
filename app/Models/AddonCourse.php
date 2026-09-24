<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'addon_course_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function addonCourse()
    {
        return $this->belongsTo(Course::class, 'addon_course_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
