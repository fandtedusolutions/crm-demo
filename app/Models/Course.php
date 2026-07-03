<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'code',
        'amount',
        'hod_number',
        'hod_id',
        'is_active',
        'needs_time',
        'is_online',
        'is_offline',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'needs_time' => 'boolean',
        'is_online' => 'boolean',
        'is_offline' => 'boolean',
        'amount' => 'double',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function classTimes()
    {
        return $this->hasMany(ClassTime::class);
    }

    public function offlinePlaces()
    {
        return $this->belongsToMany(OfflinePlace::class, 'course_offline_place');
    }

    public function activeOfflinePlaces()
    {
        return $this->offlinePlaces()
            ->where('offline_places.is_active', true)
            ->orderBy('offline_places.name');
    }

    public function courseTypes()
    {
        return $this->hasMany(CourseType::class);
    }

    public function activeCourseTypes()
    {
        return $this->courseTypes()
            ->where('is_active', true)
            ->orderBy('title');
    }

    public function hod()
    {
        return $this->belongsTo(User::class , 'hod_id');
    }

    public function academicDeliveryStructures()
    {
        return $this->hasMany(AcademicDeliveryStructure::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
