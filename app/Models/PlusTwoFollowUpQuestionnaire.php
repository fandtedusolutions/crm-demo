<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlusTwoFollowUpQuestionnaire extends Model
{
    public const LEAD_SOURCE_ID = 13;

    protected $fillable = [
        'lead_id',
        'name',
        'mobile_number',
        'received_plus_two_result',
        'result_outcome',
        'stream_completed',
        'current_plan',
        'college_selection',
        'planned_course',
        'course_selection_reason',
        'admission_started',
        'decision_maker',
        'career_clarity_level',
        'biggest_challenge',
        'guidance_interested_level',
        'counseling_preference',
        'best_contact_time',
        'result_status',
        'stream',
        'future_plan',
        'course_interested',
        'college_selected',
        'decision_maker_summary',
        'career_clarity',
        'main_challenge',
        'guidance_interested',
        'followup_date',
        'followup_time',
    ];

    protected $casts = [
        'followup_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
