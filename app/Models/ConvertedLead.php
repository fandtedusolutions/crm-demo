<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConvertedLead extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Course id of the listing currently being queried. Listing pages set this
     * so addon enrollments reuse the Batch and Admission Batch columns.
     */
    public static ?int $listingCourseId = null;

    protected bool $resolvingListingBatch = false;

    protected $fillable = [
        'lead_id',
        'name',
        'code',
        'phone',
        'email',
        'dob',
        'username',
        'password',
        'status',
        'is_b2b',
        'postsale_status',
        'is_cancelled',
        'cancelled_by',
        'cancelled_at',
        'cancel_remark',
        'postsale_followupdate',
        'postsale_followuptime',
        'paid_status',
        'call_status',
        'need_mobile',
        'asset_id',
        'called_date',
        'called_time',
        'post_sales_remarks',
        'post_sales_user_id',
        'ref_no',
        'register_number',
        'course_id',
        'sub_course_id',
        'university_id',
        'academic_assistant_id',
        'batch_id',
        'addon_batch_id',
        'board_id',
        'subject_id',
        'flag_id',
        'support_flag_id',
        'course_flag_id',
        'admission_batch_id',
        'addon_admission_batch_id',
        'admission_batch_assigned_at',
        'finance_approval',
        'faculty_id',
        'is_postpond_batch',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
        'reg_updated_by',
        'is_academic_verified',
        'academic_verified_by',
        'academic_verified_at',
        'is_support_verified',
        'support_verified_by',
        'support_verified_at',
        'is_course_changed',
        'course_changed_at',
        'course_changed_by',
        'is_shared_to_lms',
        'lms_stream_id',
        'shared_to_lms_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'reg_updated_at' => 'datetime',
        'academic_verified_at' => 'datetime',
        'support_verified_at' => 'datetime',
        'course_changed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'admission_batch_assigned_at' => 'datetime',
        'shared_to_lms_at' => 'datetime',
        'postsale_followupdate' => 'date',
        'called_date' => 'date',
        'called_time' => 'datetime:H:i:s',
        'is_course_changed' => 'boolean',
        'is_shared_to_lms' => 'boolean',
        'is_cancelled' => 'boolean',
        'is_postpond_batch' => 'boolean',
        'is_b2b' => 'boolean',
        'need_mobile' => 'boolean',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function leadDetail()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id', 'lead_id');
    }

    /**
     * Primary course rows plus students whose registration addon matches this course.
     */
    public function scopeForCourseListing($query, $courseId)
    {
        $courseId = (int) $courseId;
        static::$listingCourseId = $courseId > 0 ? $courseId : null;

        $query->with([
            'addonBatch:id,title',
            'addonAdmissionBatch:id,title',
        ]);

        return $query->where(function ($q) use ($courseId) {
            $q->where($this->qualifyColumn('course_id'), $courseId)
                ->orWhereHas('leadDetail', function ($detailQuery) use ($courseId) {
                    $detailQuery->where('addon_course_id', $courseId);
                });
        });
    }

    public function scopeWhereListingBatch($query, $batchId)
    {
        $batchId = (int) $batchId;
        $courseId = (int) static::$listingCourseId;
        if ($courseId <= 0) {
            return $query->where($this->qualifyColumn('batch_id'), $batchId);
        }

        $courseColumn = $this->qualifyColumn('course_id');
        $batchColumn = $this->qualifyColumn('batch_id');
        $addonBatchColumn = $this->qualifyColumn('addon_batch_id');

        return $query->where(function ($outer) use ($courseId, $batchId, $courseColumn, $batchColumn, $addonBatchColumn) {
            $outer->where(function ($primary) use ($courseId, $batchId, $courseColumn, $batchColumn) {
                $primary->where($courseColumn, $courseId)
                    ->where($batchColumn, $batchId);
            })->orWhere(function ($addon) use ($courseId, $batchId, $courseColumn, $addonBatchColumn) {
                $addon->where($courseColumn, '!=', $courseId)
                    ->where($addonBatchColumn, $batchId)
                    ->whereHas('leadDetail', function ($detailQuery) use ($courseId) {
                        $detailQuery->where('addon_course_id', $courseId);
                    });
            });
        });
    }

    public function scopeWhereListingAdmissionBatch($query, $admissionBatchId)
    {
        $admissionBatchId = (int) $admissionBatchId;
        $courseId = (int) static::$listingCourseId;
        if ($courseId <= 0) {
            return $query->where($this->qualifyColumn('admission_batch_id'), $admissionBatchId);
        }

        $courseColumn = $this->qualifyColumn('course_id');
        $admissionColumn = $this->qualifyColumn('admission_batch_id');
        $addonAdmissionColumn = $this->qualifyColumn('addon_admission_batch_id');

        return $query->where(function ($outer) use ($courseId, $admissionBatchId, $courseColumn, $admissionColumn, $addonAdmissionColumn) {
            $outer->where(function ($primary) use ($courseId, $admissionBatchId, $courseColumn, $admissionColumn) {
                $primary->where($courseColumn, $courseId)
                    ->where($admissionColumn, $admissionBatchId);
            })->orWhere(function ($addon) use ($courseId, $admissionBatchId, $courseColumn, $addonAdmissionColumn) {
                $addon->where($courseColumn, '!=', $courseId)
                    ->where($addonAdmissionColumn, $admissionBatchId)
                    ->whereHas('leadDetail', function ($detailQuery) use ($courseId) {
                        $detailQuery->where('addon_course_id', $courseId);
                    });
            });
        });
    }

    public function scopeForMentorAdmissionBatches($query, array $admissionBatchIds)
    {
        $admissionBatchIds = array_values(array_filter(array_map('intval', $admissionBatchIds)));
        if ($admissionBatchIds === []) {
            return $query->whereRaw('1 = 0');
        }

        $courseId = (int) static::$listingCourseId;
        if ($courseId <= 0) {
            return $query->whereIn($this->qualifyColumn('admission_batch_id'), $admissionBatchIds);
        }

        $courseColumn = $this->qualifyColumn('course_id');
        $admissionColumn = $this->qualifyColumn('admission_batch_id');
        $addonAdmissionColumn = $this->qualifyColumn('addon_admission_batch_id');

        return $query->where(function ($outer) use ($courseId, $admissionBatchIds, $courseColumn, $admissionColumn, $addonAdmissionColumn) {
            $outer->where(function ($primary) use ($courseId, $admissionBatchIds, $courseColumn, $admissionColumn) {
                $primary->where($courseColumn, $courseId)
                    ->whereIn($admissionColumn, $admissionBatchIds);
            })->orWhere(function ($addon) use ($courseId, $admissionBatchIds, $courseColumn, $addonAdmissionColumn) {
                $addon->where($courseColumn, '!=', $courseId)
                    ->whereIn($addonAdmissionColumn, $admissionBatchIds)
                    ->whereHas('leadDetail', function ($detailQuery) use ($courseId) {
                        $detailQuery->where('addon_course_id', $courseId);
                    });
            });
        });
    }

    /**
     * Same as forCourseListing, for a set of course IDs (e.g. HOD courses).
     */
    public function scopeForCourseIdsListing($query, array $courseIds)
    {
        $courseIds = array_values(array_filter(array_map('intval', $courseIds)));

        if ($courseIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($courseIds) {
            $q->whereIn($this->qualifyColumn('course_id'), $courseIds)
                ->orWhereHas('leadDetail', function ($detailQuery) use ($courseIds) {
                    $detailQuery->whereIn('addon_course_id', $courseIds);
                });
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subCourse()
    {
        return $this->belongsTo(SubCourse::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function addonBatch()
    {
        return $this->belongsTo(Batch::class, 'addon_batch_id');
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function subjectAreas()
    {
        return $this->belongsToMany(SubjectArea::class, 'converted_lead_subject_area')
            ->withTimestamps()
            ->withTrashed()
            ->orderBy('subject_areas.title');
    }

    public function flag()
    {
        return $this->belongsTo(Flag::class);
    }

    public function supportFlag()
    {
        return $this->belongsTo(SupportFlag::class);
    }

    public function courseFlag()
    {
        return $this->belongsTo(CourseFlag::class);
    }

    public function academicAssistant()
    {
        return $this->belongsTo(User::class, 'academic_assistant_id');
    }

    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function courseChangedBy()
    {
        return $this->belongsTo(User::class, 'course_changed_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function regUpdatedBy()
    {
        return $this->belongsTo(User::class, 'reg_updated_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function postSalesUser()
    {
        return $this->belongsTo(User::class, 'post_sales_user_id');
    }

    public function admissionBatch()
    {
        return $this->belongsTo(AdmissionBatch::class);
    }

    public function addonAdmissionBatch()
    {
        return $this->belongsTo(AdmissionBatch::class, 'addon_admission_batch_id');
    }


    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'student_id');
    }

    public function studentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class, 'converted_lead_id');
    }

    public function teacher()
    {
        return $this->hasOneThrough(User::class, ConvertedStudentDetail::class, 'converted_lead_id', 'id', 'id', 'teacher_id');
    }

    public function idCards()
    {
        return $this->hasMany(ConvertedLeadIdCard::class, 'converted_lead_id');
    }

    public function mentorDetails()
    {
        return $this->hasOne(ConvertedStudentMentorDetail::class, 'converted_student_id');
    }

    public function placementMockTestDetails()
    {
        return $this->hasMany(PlacementMockTestDetail::class, 'converted_lead_id')->orderByDesc('created_at');
    }

    public function placementScheduledInterviews()
    {
        return $this->hasMany(PlacementScheduledInterview::class, 'converted_lead_id')->orderByDesc('interview_date')->orderByDesc('created_at');
    }

    public function placementRemarkHistories()
    {
        return $this->hasMany(PlacementRemarkHistory::class, 'converted_lead_id')->orderByDesc('created_at');
    }

    /**
     * Placement stage: Placed if any interview is placed; else based on the latest (most recent) mock test only:
     * latest total >= 35 → Passed Mock Test, latest total < 35 → Need Mock Test; if no mock tests → Pending.
     * Requires placementScheduledInterviews and placementMockTestDetails to be loaded.
     */
    public function getPlacementStage(): string
    {
        if ($this->relationLoaded('placementScheduledInterviews') && $this->placementScheduledInterviews->where('status', PlacementScheduledInterview::STATUS_PLACED)->isNotEmpty()) {
            return 'Placed';
        }
        if ($this->relationLoaded('placementMockTestDetails') && $this->placementMockTestDetails->isNotEmpty()) {
            $latestMock = $this->placementMockTestDetails->first(); // relation already ordered by created_at desc
            $total = $latestMock->speaking_capacity + $latestMock->presentation_skill + $latestMock->character + $latestMock->dedication;
            return $total >= 35 ? 'Passed Mock Test' : 'Need Mock Test';
        }
        return 'Pending';
    }

    public function supportDetails()
    {
        return $this->hasOne(ConvertedStudentSupportDetail::class, 'converted_student_id');
    }

    public function supportFeedbackHistory()
    {
        return $this->hasMany(SupportFeedbackHistory::class, 'converted_student_id')->orderBy('created_at', 'desc');
    }

    public function convertedStudentActivities()
    {
        return $this->hasMany(ConvertedStudentActivity::class)->orderByDesc('activity_date')->orderByDesc('activity_time');
    }

    public function latestConvertedStudentActivity()
    {
        return $this->hasOne(ConvertedStudentActivity::class)->latestOfMany();
    }

    public function niosStudentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class)->where('course_id', 1);
    }

    public function bosseStudentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class)->where('course_id', 2);
    }

    public function medicalCodingStudentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class)->where('course_id', 3);
    }

    /**
     * Override the delete method to set deleted_by
     */
    public function delete()
    {
        $this->deleted_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->save();
        
        return parent::delete();
    }

    /**
     * Simple encryption for password
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = base64_encode($value);
        }
    }

    /**
     * Simple decryption for password
     */
    public function getPasswordAttribute($value)
    {
        if ($value) {
            return base64_decode($value);
        }
        return $value;
    }

    /**
     * Proxy methods to access fields from ConvertedStudentDetail
     */
    public function getRegFeeAttribute()
    {
        return $this->studentDetails?->reg_fee;
    }

    public function getExamFeeAttribute()
    {
        return $this->studentDetails?->exam_fee;
    }

    public function getEnrollNoAttribute()
    {
        return $this->studentDetails?->enroll_no;
    }

    public function getIdCardAttribute()
    {
        return $this->studentDetails?->id_card;
    }

    public function getTmaAttribute()
    {
        return $this->studentDetails?->tma;
    }

    public function getReModeAttribute()
    {
        return $this->studentDetails?->re_mode;
    }

    /**
     * Setter methods to update fields in ConvertedStudentDetail
     */
    public function setRegFeeAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['reg_fee' => $value]);
        }
    }

    public function setExamFeeAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['exam_fee' => $value]);
        }
    }

    public function setEnrollNoAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['enroll_no' => $value]);
        }
    }

    public function setIdCardAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['id_card' => $value]);
        }
    }

    public function setTmaAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['tma' => $value]);
        }
    }

    public function setReModeAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['re_mode' => $value]);
        }
    }

    public function getAttribute($key)
    {
        if ($this->listingUsesAddonFields()) {
            if ($key === 'batch_id') {
                return $this->attributes['addon_batch_id'] ?? null;
            }
            if ($key === 'admission_batch_id') {
                return $this->attributes['addon_admission_batch_id'] ?? null;
            }
            if ($key === 'batch') {
                return $this->addonBatch;
            }
            if ($key === 'admissionBatch') {
                return $this->addonAdmissionBatch;
            }
        }

        return parent::getAttribute($key);
    }

    public static function currentListingCourseId(): string
    {
        return static::$listingCourseId ? (string) static::$listingCourseId : '';
    }

    public function listingEditCourseId(): int
    {
        if ($this->listingUsesAddonFields()) {
            return (int) static::$listingCourseId;
        }

        return (int) ($this->attributes['course_id'] ?? 0);
    }

    public function listingUsesAddonFields(): bool
    {
        $courseId = (int) static::$listingCourseId;

        return $courseId > 0 && $this->isAddonEnrollmentFor($courseId);
    }

    public function isAddonEnrollmentFor(int $courseId): bool
    {
        if ($courseId <= 0 || $this->resolvingListingBatch) {
            return false;
        }
        if ((int) ($this->attributes['course_id'] ?? 0) === $courseId) {
            return false;
        }

        $this->resolvingListingBatch = true;
        try {
            $detail = $this->leadDetail;
        } finally {
            $this->resolvingListingBatch = false;
        }

        return (int) ($detail->addon_course_id ?? 0) === $courseId;
    }

    public function mentorScopeAdmissionBatchId()
    {
        $requestedCourseId = (int) request()->input('listing_course_id');
        if ($requestedCourseId > 0 && $this->isAddonEnrollmentFor($requestedCourseId)) {
            return $this->attributes['addon_admission_batch_id'] ?? null;
        }

        if ($this->listingUsesAddonFields()) {
            return $this->attributes['addon_admission_batch_id'] ?? null;
        }

        return $this->attributes['admission_batch_id'] ?? null;
    }

    public function shouldStoreListingBatchAsAddon(string $field, $value, int $listingCourseId): bool
    {
        if (! in_array($field, ['batch_id', 'admission_batch_id'], true)) {
            return false;
        }

        if ($listingCourseId > 0 && $this->isAddonEnrollmentFor($listingCourseId)) {
            return true;
        }

        if ($value === null || $value === '') {
            return false;
        }

        $addonCourseId = (int) ($this->leadDetail->addon_course_id ?? 0);
        if ($addonCourseId <= 0 || $addonCourseId === (int) ($this->attributes['course_id'] ?? 0)) {
            return false;
        }

        if ($field === 'batch_id') {
            $batch = Batch::find($value);

            return $batch && (int) $batch->course_id === $addonCourseId;
        }

        $admissionBatch = AdmissionBatch::with('batch')->find($value);
        $batchCourseId = (int) ($admissionBatch->batch->course_id ?? 0);

        return $admissionBatch && $batchCourseId === $addonCourseId;
    }

    /**
     * Write batch edits for an addon enrollment onto the addon columns.
     *
     * @return array{handled: bool, error: ?string, value: mixed}
     */
    public function applyListingBatchUpdate(string $field, $value, int $listingCourseId): array
    {
        if (! $this->shouldStoreListingBatchAsAddon($field, $value, $listingCourseId)) {
            return ['handled' => false, 'error' => null, 'value' => $value];
        }

        $error = $this->listingBatchUpdateError($field, $value, $listingCourseId);
        if ($error) {
            return ['handled' => true, 'error' => $error, 'value' => $value];
        }

        $storedValue = ($value === '' || $value === null) ? null : $value;
        if ($field === 'batch_id') {
            $this->addon_batch_id = $storedValue;
            if ($this->addon_admission_batch_id) {
                $stillMatches = AdmissionBatch::query()
                    ->where('id', $this->addon_admission_batch_id)
                    ->where('batch_id', $storedValue)
                    ->exists();
                if (! $stillMatches) {
                    $this->addon_admission_batch_id = null;
                }
            }
        } else {
            $this->addon_admission_batch_id = $storedValue;
        }

        return ['handled' => true, 'error' => null, 'value' => $storedValue];
    }

    public function listingBatchUpdateError(string $field, $value, int $listingCourseId): ?string
    {
        if (! $this->shouldStoreListingBatchAsAddon($field, $value, $listingCourseId)) {
            return null;
        }
        if ($value === null || $value === '') {
            return null;
        }

        $addonCourseId = (int) ($this->leadDetail->addon_course_id ?? 0);
        if ($field === 'batch_id') {
            $batch = Batch::find($value);
            if (! $batch || (int) $batch->course_id !== $addonCourseId) {
                return 'Select a batch for this addon course.';
            }

            return null;
        }

        $admissionBatch = AdmissionBatch::with('batch')->find($value);
        $batchCourseId = (int) ($admissionBatch->batch->course_id ?? 0);
        if (! $admissionBatch || $batchCourseId !== $addonCourseId) {
            return 'Select an admission batch for this addon course.';
        }

        $addonBatchId = (int) ($this->attributes['addon_batch_id'] ?? 0);
        if ($addonBatchId > 0 && (int) $admissionBatch->batch_id !== $addonBatchId) {
            return 'Select an admission batch for the addon batch.';
        }

        return null;
    }
}
