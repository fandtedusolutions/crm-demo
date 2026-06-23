<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads_details';

    protected $fillable = [
        'lead_id',
        'course_id',
        'university_id',
        'university_course_id',
        'course_type',
        'course_type_id',
        'edumaster_course_name',
        'student_name',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'is_employed',
        'email',
        'personal_number',
        'personal_code',
        'parents_number',
        'parents_code',
        'father_contact_number',
        'father_contact_code',
        'mother_contact_number',
        'mother_contact_code',
        'whatsapp_number',
        'whatsapp_code',
        'residential_address',
        'selected_courses',
        'sslc_back_year',
        'plustwo_back_year',
        'plustwo_subject',
        'stream_specialization_id',
        'back_year',
        'degree_back_year',
        'subject_id',
        'batch_id',
        'sub_course_id',
        'class',
        'second_language',
        'medium_of_study',
        'previous_qualification',
        'technology_performance_category',
        'passed_year',
        'programme_type',
        'location',
        'class_time_id',
        'street',
        'locality',
        'post_office',
        'district',
        'state',
        'pin_code',
        'birth_certificate',
        'passport_photo',
        'adhar_front',
        'adhar_back',
        'signature',
        'other_document',
        'plustwo_certificate',
        'sslc_certificate',
        'ug_certificate',
        'post_graduation_certificate',
        'message',
        'status',
        'admin_remarks',
        'reviewed_by',
        'reviewed_at',
        // Document verification fields
        'sslc_verification_status',
        'sslc_verified_by',
        'sslc_verified_at',
        'plustwo_verification_status',
        'plustwo_verified_by',
        'plustwo_verified_at',
        'ug_verification_status',
        'ug_verified_by',
        'ug_verified_at',
        'post_graduation_certificate_verification_status',
        'post_graduation_certificate_verified_by',
        'post_graduation_certificate_verified_at',
        'passport_photo_verification_status',
        'passport_photo_verified_by',
        'passport_photo_verified_at',
        'adhar_front_verification_status',
        'adhar_front_verified_by',
        'adhar_front_verified_at',
        'adhar_back_verification_status',
        'adhar_back_verified_by',
        'adhar_back_verified_at',
        'signature_verification_status',
        'signature_verified_by',
        'signature_verified_at',
        'other_document_verification_status',
        'other_document_verified_by',
        'other_document_verified_at',
        'birth_certificate_verification_status',
        'birth_certificate_verified_by',
        'birth_certificate_verified_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_employed' => 'boolean',
        'reviewed_at' => 'datetime',
        'sslc_verified_at' => 'datetime',
        'plustwo_verified_at' => 'datetime',
        'ug_verified_at' => 'datetime',
        'post_graduation_certificate_verified_at' => 'datetime',
        'passport_photo_verified_at' => 'datetime',
        'adhar_front_verified_at' => 'datetime',
        'adhar_back_verified_at' => 'datetime',
        'signature_verified_at' => 'datetime',
        'other_document_verified_at' => 'datetime',
        'birth_certificate_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function courseTypeOption()
    {
        return $this->belongsTo(CourseType::class, 'course_type_id');
    }

    public function streamSpecialization()
    {
        return $this->belongsTo(StreamSpecialization::class, 'stream_specialization_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function universityCourse()
    {
        return $this->belongsTo(UniversityCourse::class);
    }

    public function subCourse()
    {
        return $this->belongsTo(SubCourse::class, 'sub_course_id');
    }

    public function classTime()
    {
        return $this->belongsTo(ClassTime::class, 'class_time_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Document verification relationships
    public function sslcVerifiedBy()
    {
        return $this->belongsTo(User::class, 'sslc_verified_by');
    }

    public function plustwoVerifiedBy()
    {
        return $this->belongsTo(User::class, 'plustwo_verified_by');
    }

    public function ugVerifiedBy()
    {
        return $this->belongsTo(User::class, 'ug_verified_by');
    }

    public function postGraduationCertificateVerifiedBy()
    {
        return $this->belongsTo(User::class, 'post_graduation_certificate_verified_by');
    }

    public function passportPhotoVerifiedBy()
    {
        return $this->belongsTo(User::class, 'passport_photo_verified_by');
    }

    public function adharFrontVerifiedBy()
    {
        return $this->belongsTo(User::class, 'adhar_front_verified_by');
    }

    public function adharBackVerifiedBy()
    {
        return $this->belongsTo(User::class, 'adhar_back_verified_by');
    }

    public function signatureVerifiedBy()
    {
        return $this->belongsTo(User::class, 'signature_verified_by');
    }

    public function otherDocumentVerifiedBy()
    {
        return $this->belongsTo(User::class, 'other_document_verified_by');
    }

    public function birthCertificateVerifiedBy()
    {
        return $this->belongsTo(User::class, 'birth_certificate_verified_by');
    }

    /**
     * Get the SSLC certificates for this lead detail.
     */
    public function sslcCertificates()
    {
        return $this->hasMany(SSLCertificate::class);
    }

    /**
     * Get document verification status
     * Returns 'verified' if all uploaded documents are verified, 'pending' otherwise
     */
    public function getDocumentVerificationStatus()
    {
        $documentTypes = [
            'sslc_certificate' => ['field' => 'sslc_certificate', 'status' => 'sslc_verification_status'],
            'plustwo_certificate' => ['field' => 'plustwo_certificate', 'status' => 'plustwo_verification_status'],
            'ug_certificate' => ['field' => 'ug_certificate', 'status' => 'ug_verification_status'],
            'post_graduation_certificate' => ['field' => 'post_graduation_certificate', 'status' => 'post_graduation_certificate_verification_status'],
            'birth_certificate' => ['field' => 'birth_certificate', 'status' => 'birth_certificate_verification_status'],
            'passport_photo' => ['field' => 'passport_photo', 'status' => 'passport_photo_verification_status'],
            'adhar_front' => ['field' => 'adhar_front', 'status' => 'adhar_front_verification_status'],
            'adhar_back' => ['field' => 'adhar_back', 'status' => 'adhar_back_verification_status'],
            'signature' => ['field' => 'signature', 'status' => 'signature_verification_status'],
            'other_document' => ['field' => 'other_document', 'status' => 'other_document_verification_status'],
        ];

        $uploadedDocuments = [];
        $hasPending = false;
        $hasVerified = false;

        foreach ($documentTypes as $type => $config) {
            $field = $config['field'];
            $statusField = $config['status'];
            
            // Check if document is uploaded
            if (!empty($this->$field)) {
                $uploadedDocuments[] = $type;
                $verificationStatus = $this->$statusField ?? 'pending';
                
                if ($verificationStatus === 'verified') {
                    $hasVerified = true;
                } else {
                    $hasPending = true;
                }
            }
        }

        // Check SSLC certificates (separate table)
        if ($this->sslcCertificates && $this->sslcCertificates->count() > 0) {
            foreach ($this->sslcCertificates as $certificate) {
                $uploadedDocuments[] = 'sslc_certificate';
                $verificationStatus = $certificate->verification_status ?? 'pending';
                
                if ($verificationStatus === 'verified') {
                    $hasVerified = true;
                } else {
                    $hasPending = true;
                }
            }
        }

        // If no documents uploaded, return null
        if (empty($uploadedDocuments)) {
            return null;
        }

        // If all uploaded documents are verified, return 'verified'
        // Otherwise return 'pending'
        return $hasPending ? 'pending' : 'verified';
    }
}