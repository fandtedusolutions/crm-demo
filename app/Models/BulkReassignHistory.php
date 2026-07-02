<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkReassignHistory extends Model
{
    protected $fillable = [
        'from_telecaller_id',
        'to_telecaller_id',
        'leads_count',
        'lead_source_id',
        'lead_status_id',
        'lead_from_date',
        'lead_to_date',
        'reassign_date',
        'reassign_time',
        'created_by',
    ];

    protected $casts = [
        'lead_from_date' => 'date',
        'lead_to_date' => 'date',
        'reassign_date' => 'date',
        'leads_count' => 'integer',
    ];

    public function fromTelecaller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_telecaller_id');
    }

    public function toTelecaller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_telecaller_id');
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function leadStatus(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
