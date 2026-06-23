<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamSpecialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'course_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function leadDetails()
    {
        return $this->hasMany(LeadDetail::class, 'stream_specialization_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
