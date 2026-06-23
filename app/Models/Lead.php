<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\AuthHelper;
use App\Helpers\PhoneNumberHelper;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'gender',
        'age',
        'phone',
        'code',
        'whatsapp',
        'whatsapp_code',
        'email',
        'qualification',
        'country_id',
        'interest_status',
        'rating',
        'lead_status_id',
        'lead_source_id',
        'address',
        'telecaller_id',
        'team_id',
        'place',
        'created_by',
        'updated_by',
        'deleted_by',
        'course_id',
        'batch_id',
        'university_id',
        'by_meta',
        'meta_lead_id',
        'marketing_leads_id',
        'marketing_remarks',
        'followup_date',
        'remarks',
        'is_converted',
        'is_b2b',
        'is_pullbacked',
        'first_created_at',
    ];

    protected $casts = [
        'by_meta' => 'boolean',
        'is_converted' => 'boolean',
        'is_b2b' => 'boolean',
        'followup_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_pullbacked' => 'boolean',
        'first_created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope('exclude_pullbacked', function (Builder $builder) {
            $builder->where(function ($query) {
                $query->whereNull('leads.is_pullbacked')
                      ->orWhere('leads.is_pullbacked', 0);
            });
        });

        static::creating(function (Lead $lead) {
            if (empty($lead->first_created_at)) {
                $lead->first_created_at = now();
            }
        });
    }

    /**
     * Find an existing lead with the same code, phone, and course (matches add-lead form duplicate logic).
     */
    public static function findDuplicateByPhoneAndCourse(?string $code, ?string $phone, int $courseId, ?int $excludeLeadId = null): ?self
    {
        $normalized = PhoneNumberHelper::normalizeLeadParts($code, $phone);
        $code = $normalized['code'];
        $phone = $normalized['phone'];

        if ($code === '' || $phone === '') {
            return null;
        }

        $query = static::query()
            ->where('course_id', $courseId)
            ->whereNull('deleted_at')
            ->where(function (Builder $builder) use ($code, $phone) {
                $builder->where(function (Builder $exact) use ($code, $phone) {
                    $exact->where('code', $code)->where('phone', $phone);
                })->orWhere(function (Builder $full) use ($code, $phone) {
                    $full->where('code', $code)->where('phone', $code . $phone);
                });
            });

        if ($excludeLeadId !== null) {
            $query->where('id', '!=', $excludeLeadId);
        }

        return $query->first();
    }

    // Relationships
    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
    }

    public function telecaller()
    {
        return $this->belongsTo(User::class, 'telecaller_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function marketingLead()
    {
        return $this->belongsTo(MarketingLead::class, 'marketing_leads_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function leadActivities()
    {
        return $this->hasMany(LeadActivity::class, 'lead_id');
    }

    public function convertedLead()
    {
        return $this->hasOne(ConvertedLead::class, 'lead_id');
    }

    public function latestFollowupActivity()
    {
        return $this->hasOne(LeadActivity::class, 'lead_id')->latestOfMany('created_at');
    }

    public function studentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id');
    }

    public function niosStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 1);
    }

    public function gmvssStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 16);
    }

    public function bosseStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 2);
    }

    public function medicalCodingStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 3);
    }

    public function aiSalesMarketingStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 29);
    }

    public function hospitalAdminStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 4);
    }

    public function eSchoolStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 5);
    }

    public function eduthanzeelStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 6);
    }

    public function ttcStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 7);
    }

    public function hotelMgmtStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 8);
    }

    public function ugpgStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 9);
    }

    public function aiAutomationStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 12);
    }

    public function juniorVloggerStudentDetails()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id')->where('course_id', 25);
    }

    public function plusTwoFollowUpQuestionnaire()
    {
        return $this->hasOne(PlusTwoFollowUpQuestionnaire::class, 'lead_id');
    }

    // Scopes
    public function scopeWithStatusCount($query)
    {
        return $query->selectRaw('lead_status_id, COUNT(*) as count')
                    ->groupBy('lead_status_id');
    }

    // Static methods
    public static function statusWithCount()
    {
        return self::selectRaw('lead_status_id, COUNT(*) as count')
                  ->groupBy('lead_status_id')
                  ->get();
    }

    public function scopeByTelecaller($query, $telecallerId)
    {
        return $query->where('telecaller_id', $telecallerId);
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
    }

    public function scopeNotConverted($query)
    {
        return $query->where('is_converted', 0);
    }

    public function scopeNotDropped($query)
    {
        return $query->where('is_converted', '!=', 1);
    }

    // Accessors
    public function getFullPhoneAttribute()
    {
        return $this->code . $this->phone;
    }

    public function getWhatsappUrlAttribute()
    {
        if ($this->phone) {
            return "https://api.whatsapp.com/send/?phone={$this->code}{$this->phone}&text=Hi {$this->title}&type=phone_number&app_absent=0";
        }
        return 'javascript:void(0);';
    }

    // Methods
    public function updateLeadStatus($statusId, $remarks = null, $followupDate = null)
    {
        $this->update([
            'lead_status_id' => $statusId,
            'followup_date' => $followupDate,
            'remarks' => $remarks,
            'is_converted' => $statusId == 4 ? true : false,
            'updated_by' => AuthHelper::getCurrentUserId()
        ]);

        // Create lead activity
        LeadActivity::create([
            'lead_id' => $this->id,
            'lead_status_id' => $statusId,
            'remarks' => $remarks,
            'followup_date' => $followupDate,
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId()
        ]);
    }

    public function reassignToTelecaller($telecallerId, $fromTelecallerId = null)
    {
        $this->update([
            'telecaller_id' => $telecallerId,
            'lead_status_id' => 1, // Set status to 1 when reassigned
            'updated_by' => AuthHelper::getCurrentUserId()
        ]);

        // Get user names safely
        $fromTelecallerName = 'Unknown';
        if ($fromTelecallerId) {
            $fromUser = User::find($fromTelecallerId);
            $fromTelecallerName = $fromUser ? $fromUser->name : 'Unknown';
        }
        
        $toUser = User::find($telecallerId);
        $toTelecallerName = $toUser ? $toUser->name : 'Unknown';

        // Create activity log
        LeadActivity::create([
            'lead_id' => $this->id,
            'lead_status_id' => 1, // Set status to 1 when reassigned
            'remarks' => 'Lead has been reassigned from telecaller ' . $fromTelecallerName . 
                        ' to telecaller ' . $toTelecallerName . '.',
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId()
        ]);
    }

    /**
     * Check if lead is overdue (older than 7 days and not converted)
     */
    public function isOverdue()
    {
        return !$this->is_converted && $this->created_at->lt(now()->subDays(7));
    }

    /**
     * Check if lead is due today (created today)
     */
    public function isDueToday()
    {
        return $this->created_at->isToday();
    }

    /**
     * Check if lead is completed (converted)
     */
    public function isCompleted()
    {
        return $this->is_converted;
    }

    /**
     * Scope for overdue leads
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_converted', false)
                    ->where('created_at', '<', now()->subDays(7));
    }

    /**
     * Scope for leads due today
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Override the delete method to set deleted_by
     */
    public function delete()
    {
        $this->deleted_by = AuthHelper::getCurrentUserId();
        $this->save();
        
        return parent::delete();
    }

    /**
     * Calculate profile completeness percentage
     * Based on all basic lead details fields
     * Uses pre-calculated value if available for performance
     */
    public function getProfileCompletenessAttribute()
    {
        // Use pre-calculated value if available (set in controller for performance)
        if (isset($this->attributes['_profile_completeness'])) {
            return $this->attributes['_profile_completeness'];
        }
        
        $requiredFields = [
            'title', 'gender', 'age', 'phone', 'code', 'whatsapp', 'whatsapp_code',
            'email', 'qualification', 'country_id', 'interest_status', 'lead_status_id',
            'lead_source_id', 'address', 'telecaller_id', 'team_id', 'place'
        ];
        $completedFields = 0;
        
        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }
        
        return round(($completedFields / count($requiredFields)) * 100);
    }

    /**
     * Check if profile is incomplete
     * Uses pre-calculated value if available for performance
     */
    public function isProfileIncomplete()
    {
        $completeness = $this->attributes['_profile_completeness'] ?? $this->profile_completeness;
        return $completeness < 100;
    }

    /**
     * Get profile completeness status
     * Uses pre-calculated value if available for performance
     */
    public function getProfileStatusAttribute()
    {
        // Use pre-calculated value if available
        if (isset($this->attributes['_profile_status'])) {
            return $this->attributes['_profile_status'];
        }
        
        $completeness = $this->profile_completeness;
        if ($completeness == 100) {
            return 'complete';
        } elseif ($completeness >= 75) {
            return 'almost_complete';
        } elseif ($completeness >= 50) {
            return 'partial';
        } else {
            return 'incomplete';
        }
    }

    /**
     * Get detailed field completion status
     */
    public function getFieldCompletionStatus()
    {
        $fields = [
            'title' => 'Name',
            'gender' => 'Gender',
            'age' => 'Age',
            'phone' => 'Phone',
            'code' => 'Country Code',
            'whatsapp' => 'WhatsApp',
            'whatsapp_code' => 'WhatsApp Code',
            'email' => 'Email',
            'qualification' => 'Qualification',
            'country_id' => 'Country',
            'interest_status' => 'Interest Status',
            'lead_status_id' => 'Lead Status',
            'lead_source_id' => 'Lead Source',
            'address' => 'Address',
            'telecaller_id' => 'Telecaller',
            'team_id' => 'Team',
            'place' => 'Place'
        ];
        
        $completion = [];
        foreach ($fields as $field => $label) {
            $completion[$field] = [
                'label' => $label,
                'completed' => !empty($this->$field),
                'value' => $this->$field
            ];
        }
        
        return $completion;
    }

    /**
     * Get missing fields for profile completion
     * Uses pre-calculated value if available for performance
     */
    public function getMissingFields()
    {
        // Use pre-calculated value if available (set in controller for performance)
        if (isset($this->attributes['_missing_fields'])) {
            return $this->attributes['_missing_fields'];
        }
        
        $completion = $this->getFieldCompletionStatus();
        $missing = [];
        
        foreach ($completion as $field => $data) {
            if (!$data['completed']) {
                $missing[] = $data['label'];
            }
        }
        
        return $missing;
    }

    public function getInterestStatusLabelAttribute()
    {
        switch ($this->interest_status) {
            case 1:
                return 'Hot';
            case 2:
                return 'Warm';
            case 3:
                return 'Cold';
            default:
                return 'Not Set';
        }
    }

    public function getInterestStatusColorAttribute()
    {
        switch ($this->interest_status) {
            case 1:
                return 'danger'; // Red for Hot
            case 2:
                return 'warning'; // Yellow for Warm
            case 3:
                return 'info'; // Blue for Cold
            default:
                return 'secondary';
        }
    }
}