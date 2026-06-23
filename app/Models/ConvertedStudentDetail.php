<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvertedStudentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'converted_lead_id',
        'reg_fee',
        'exam_fee',
        'enroll_no',
        'internship_id',
        'id_card',
        'tma',
        'deleted_by',
        'registration_number',
        'converted_date',
        'enrollment_number',
        'registration_link_id',
        'certificate_status',
        'certificate_received_date',
        'certificate_issued_date',
        'remarks',
        // Board of Open Schooling and Skill Education specific fields
        'application_number',
        'board_registration_number',
        'st',
        'phy',
        'che',
        'bio',
        // Hotel Management specific fields
        'app',
        'group',
        'interview',
        'howmany_interview',
        'teacher_id',
        'screening',
        // E-School and Eduthanzeel specific fields
        'continuing_studies',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'converted_date' => 'date',
        'certificate_received_date' => 'date',
        'certificate_issued_date' => 'date',
        'screening' => 'date',
        // Board of Open Schooling and Skill Education specific fields
        'st' => 'integer',
        'phy' => 'integer',
        'che' => 'integer',
        'bio' => 'integer',
        // Hotel Management specific fields
        'howmany_interview' => 'integer',
    ];

    // Relationships
    public function convertedLead()
    {
        return $this->belongsTo(ConvertedLead::class);
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function registrationLink()
    {
        return $this->belongsTo(RegistrationLink::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the SSLC certificates for this converted student detail.
     */
    public function sslcCertificates()
    {
        return $this->hasMany(SSLCertificate::class);
    }
}