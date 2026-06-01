<?php

namespace App\Services;

use App\Models\ConvertedLead;
use App\Models\Lead;
use App\Support\CourseMailBodyFormatter;
use Illuminate\Support\Facades\Log;

class MailService
{
    public static function sendStudentRegistrationEmail($student, $courseType)
    {
        $subject = "Registration Confirmation – {$courseType} Course";

        // Build different content for student and CAO
        $studentBody = self::buildStudentRegistrationEmailBody($student, $courseType);
        $caoBody = self::buildCAORegistrationEmailBody($student, $courseType);

        // Get attachments
        $attachments = self::getStudentAttachments($student);

        // CRITICAL: Use email from LeadDetail (form submission), NOT from Lead table
        // The email must come from the leads_details table (form submission)
        // Do NOT use $student->lead->email as that would be from the leads table
        $studentEmail = $student->getAttribute('email'); // Explicitly get from LeadDetail

        // Log both emails for debugging
        $leadEmail = $student->lead ? $student->lead->email : null;
        \Log::info('Email source check in MailService::sendStudentRegistrationEmail', [
            'lead_detail_id' => $student->id ?? null,
            'lead_id' => $student->lead_id ?? null,
            'email_from_lead_detail' => $studentEmail, // This is what we should use
            'email_from_lead_table' => $leadEmail, // This should NOT be used
            'student_name' => $student->student_name ?? 'N/A',
            'course_type' => $courseType,
        ]);

        // Validate email format - must be from LeadDetail, not Lead
        if (empty($studentEmail) || ! filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
            \Log::error('Invalid or empty student email in MailService::sendStudentRegistrationEmail', [
                'lead_detail_id' => $student->id ?? null,
                'lead_id' => $student->lead_id ?? null,
                'email_from_lead_detail' => $studentEmail,
                'email_from_lead_table' => $leadEmail,
            ]);

            return; // Don't send email if email is invalid
        }

        // Send to student with full content including terms and conditions
        // IMPORTANT: This email MUST come from leads_details table (form submission), not leads table
        send_email($studentEmail, $student->student_name ?? 'Student', $subject, $studentBody, $attachments, 'Support Team');

        // Send to CAO with only details and files, no terms and conditions
        send_email('cao@natdemy.com', 'CAO', $subject, $caoBody, $attachments, 'Support Team');
    }

    /**
     * Send support course mail to a converted lead (edited content; does not change course_mails template).
     *
     * @return array{success: bool, error: ?string}
     */
    public static function sendConvertedLeadSupportMail(ConvertedLead $convertedLead, string $subject, string $body): array
    {
        $email = $convertedLead->email;

        if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::error('Invalid converted lead email in MailService::sendConvertedLeadSupportMail', [
                'converted_lead_id' => $convertedLead->id,
                'email' => $email,
            ]);

            return [
                'success' => false,
                'error' => 'This converted lead does not have a valid email address.',
            ];
        }

        if (! function_exists('send_email_detailed')) {
            Log::error('send_email_detailed helper not available for converted lead support mail');

            return [
                'success' => false,
                'error' => 'Mail helper is not available.',
            ];
        }

        return send_email_detailed(
            $email,
            $convertedLead->name ?? 'Student',
            $subject,
            CourseMailBodyFormatter::toHtml($body),
            [],
            config('mail.from.name'),
            config('mail.from.address')
        );
    }

    public static function sendNiosStudentVerificationEmail($student, $verifier)
    {
        $subject = '🎓 NIOS Student Verified: '.($student->student_name ?? 'Student');

        $body = self::buildVerificationEmailBody($student, $verifier);

        // Get attachments
        $attachments = self::getStudentAttachments($student);

        // Send to verifier
        send_email($verifier->email, $verifier->name, $subject, $body, $attachments, 'Support Team');

        // Send copy to CAO
        send_email('cao@natdemy.com', 'CAO', $subject, $body, $attachments, 'Support Team');
    }

    private static function buildCAORegistrationEmailBody($student, $courseType)
    {
        // Build Basic Info list with only available fields
        $basicItems = [];
        if (! empty($student->student_name)) {
            $basicItems[] = "<li><b>Name:</b> {$student->student_name}</li>";
        }
        if (! empty($student->father_name)) {
            $basicItems[] = "<li><b>Father Name:</b> {$student->father_name}</li>";
        }
        if (! empty($student->mother_name)) {
            $basicItems[] = "<li><b>Mother Name:</b> {$student->mother_name}</li>";
        }
        if (! empty($student->date_of_birth)) {
            $basicItems[] = '<li><b>Date of Birth:</b> '.\Carbon\Carbon::parse($student->date_of_birth)->format('d-m-Y').'</li>';
        }
        if (! empty($student->email)) {
            $basicItems[] = "<li><b>Email:</b> {$student->email}</li>";
        }
        if (! empty($student->personal_number)) {
            $basicItems[] = "<li><b>Personal Number:</b> {$student->personal_number}</li>";
        }
        if (! empty($student->parents_number)) {
            $basicItems[] = "<li><b>Parents Number:</b> {$student->parents_number}</li>";
        }
        if (! empty($student->whatsapp_number)) {
            $basicItems[] = "<li><b>WhatsApp Number:</b> {$student->whatsapp_number}</li>";
        }
        $basicHtml = implode("\n", $basicItems);

        // Resolve subject and batch names from relation or string fields
        $subjectName = null;
        if (isset($student->subject)) {
            $subjectName = $student->subject->title ?? $student->subject->name ?? null;
        }
        if (empty($subjectName) && ! empty($student->subject_name)) {
            $subjectName = $student->subject_name;
        }

        $batchName = null;
        if (isset($student->batch)) {
            $batchName = $student->batch->title ?? $student->batch->name ?? null;
        }
        if (empty($batchName) && ! empty($student->batch_name)) {
            $batchName = $student->batch_name;
        }

        // Build Course Info list with only available fields
        $courseItems = [];
        if (! empty($courseType)) {
            $courseItems[] = "<li><b>Course:</b> {$courseType}</li>";
        }
        if (! empty($subjectName)) {
            $courseItems[] = "<li><b>Subject:</b> {$subjectName}</li>";
        }
        if (! empty($batchName)) {
            $courseItems[] = "<li><b>Batch:</b> {$batchName}</li>";
        }
        if (! empty($student->second_language)) {
            $courseItems[] = "<li><b>Second Language:</b> {$student->second_language}</li>";
        }
        $courseHtml = implode("\n", $courseItems);

        // Build Address list with only available fields
        $addressItems = [];
        if (! empty($student->street)) {
            $addressItems[] = "<li><b>Street:</b> {$student->street}</li>";
        }
        if (! empty($student->locality)) {
            $addressItems[] = "<li><b>Locality:</b> {$student->locality}</li>";
        }
        if (! empty($student->post_office)) {
            $addressItems[] = "<li><b>Post Office:</b> {$student->post_office}</li>";
        }
        if (! empty($student->district)) {
            $addressItems[] = "<li><b>District:</b> {$student->district}</li>";
        }
        if (! empty($student->state)) {
            $addressItems[] = "<li><b>State:</b> {$student->state}</li>";
        }
        if (! empty($student->pin_code)) {
            $addressItems[] = "<li><b>PIN Code:</b> {$student->pin_code}</li>";
        }
        $addressHtml = implode("\n", $addressItems);

        // Build uploaded files list
        $uploadedFiles = self::getUploadedFilesList($student);

        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 700px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                
                <h2 style='color: #2c3e50; text-align: center;'>New Student Registration – {$courseType} Course</h2>
                <p>Dear CAO Team,</p>

                <p>A new student has completed their registration for the {$courseType} course. Please find the submitted details and uploaded documents below.</p>

                <hr style='margin:20px 0;'>

                <h3 style='color: #2c3e50;'>📌 Student Details</h3>
                <ul>
                    {$basicHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Course Information</h3>
                <ul>
                    {$courseHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Address</h3>
                <ul>
                    {$addressHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Uploaded Documents</h3>
                {$uploadedFiles}

                <p><b>Registration Date:</b> ".now()->format('d-m-Y h:i A')."</p>

                <hr style='margin:20px 0;'>
                <p>
                    Please review the submitted documents and process the student's application accordingly.
                </p>

                <p style='margin-top:30px;'>
                    Best regards,<br>
                    <b>Registration System</b>
                </p>
            </div>
        </body>
        </html>";
    }

    private static function buildStudentRegistrationEmailBody($student, $courseType)
    {
        // Build Basic Info list with only available fields
        $basicItems = [];
        if (! empty($student->student_name)) {
            $basicItems[] = "<li><b>Name:</b> {$student->student_name}</li>";
        }
        if (! empty($student->father_name)) {
            $basicItems[] = "<li><b>Father Name:</b> {$student->father_name}</li>";
        }
        if (! empty($student->mother_name)) {
            $basicItems[] = "<li><b>Mother Name:</b> {$student->mother_name}</li>";
        }
        if (! empty($student->date_of_birth)) {
            $basicItems[] = '<li><b>Date of Birth:</b> '.\Carbon\Carbon::parse($student->date_of_birth)->format('d-m-Y').'</li>';
        }
        if (! empty($student->email)) {
            $basicItems[] = "<li><b>Email:</b> {$student->email}</li>";
        }
        if (! empty($student->personal_number)) {
            $basicItems[] = "<li><b>Personal Number:</b> {$student->personal_number}</li>";
        }
        if (! empty($student->parents_number)) {
            $basicItems[] = "<li><b>Parents Number:</b> {$student->parents_number}</li>";
        }
        if (! empty($student->whatsapp_number)) {
            $basicItems[] = "<li><b>WhatsApp Number:</b> {$student->whatsapp_number}</li>";
        }
        $basicHtml = implode("\n", $basicItems);

        // Resolve subject and batch names from relation or string fields
        $subjectName = null;
        if (isset($student->subject)) {
            $subjectName = $student->subject->title ?? $student->subject->name ?? null;
        }
        if (empty($subjectName) && ! empty($student->subject_name)) {
            $subjectName = $student->subject_name;
        }

        $batchName = null;
        if (isset($student->batch)) {
            $batchName = $student->batch->title ?? $student->batch->name ?? null;
        }
        if (empty($batchName) && ! empty($student->batch_name)) {
            $batchName = $student->batch_name;
        }

        // Build Course Info list with only available fields
        $courseItems = [];
        if (! empty($courseType)) {
            $courseItems[] = "<li><b>Course:</b> {$courseType}</li>";
        }
        if (! empty($subjectName)) {
            $courseItems[] = "<li><b>Subject:</b> {$subjectName}</li>";
        }
        if (! empty($batchName)) {
            $courseItems[] = "<li><b>Batch:</b> {$batchName}</li>";
        }
        if (! empty($student->second_language)) {
            $courseItems[] = "<li><b>Second Language:</b> {$student->second_language}</li>";
        }
        $courseHtml = implode("\n", $courseItems);

        // Build Address list with only available fields
        $addressItems = [];
        if (! empty($student->street)) {
            $addressItems[] = "<li><b>Street:</b> {$student->street}</li>";
        }
        if (! empty($student->locality)) {
            $addressItems[] = "<li><b>Locality:</b> {$student->locality}</li>";
        }
        if (! empty($student->post_office)) {
            $addressItems[] = "<li><b>Post Office:</b> {$student->post_office}</li>";
        }
        if (! empty($student->district)) {
            $addressItems[] = "<li><b>District:</b> {$student->district}</li>";
        }
        if (! empty($student->state)) {
            $addressItems[] = "<li><b>State:</b> {$student->state}</li>";
        }
        if (! empty($student->pin_code)) {
            $addressItems[] = "<li><b>PIN Code:</b> {$student->pin_code}</li>";
        }
        $addressHtml = implode("\n", $addressItems);

        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 700px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                
                <h2 style='color: #2c3e50; text-align: center;'>Registration Confirmation – {$courseType} Course</h2>
                <p>Dear <b>{$student->student_name}</b>,</p>

                <p>Thank you for registering with us! We have received your application and documents for the {$courseType} course.</p>

                <hr style='margin:20px 0;'>

                <h3 style='color: #2c3e50;'>📌 Your Registration Details</h3>
                <ul>
                    {$basicHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Course Information</h3>
                <ul>
                    {$courseHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Address</h3>
                <ul>
                    {$addressHtml}
                </ul>

                <p><b>Registration Date:</b> ".now()->format('d-m-Y h:i A')."</p>

                <hr style='margin:20px 0;'>
                <p>
                    We have received all your documents and will review them within 24 hours. 
                    Our team will contact you soon regarding the next steps in your admission process.
                </p>

                ".self::getTermsAndConditions($courseType, $student)."

                <p>
                    If you have any questions, please don't hesitate to contact us at 
                    <a href='mailto:support@skill-park.com'>support@skill-park.com</a>
                </p>

                <p style='margin-top:30px;'>
                    We are delighted to have you on board. Please follow instructions carefully, utilize your mentor's guidance, and take full advantage of the learning resources provided. Your success is our mission.
                </p>
                
                <p style='text-align: center; margin: 20px 0;'>
                    <a href='https://chat.whatsapp.com/LpNUsxNbGPq4eFgVgFGSL2' style='background-color: #25D366; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;'>Join Our WhatsApp Group</a>
                </p>
                
                <p style='margin-top:30px;'>
                    Warm regards,<br>
                    <b>Support Team</b><br>
                    <b>Academic Operations Department</b><br>
                    <a href='tel:+919207666614'>+91 9207666614</a>, <a href='tel:+919207666615'>+91 9207666615</a>
                </p>
            </div>
        </body>
        </html>";
    }

    private static function buildVerificationEmailBody($student, $verifier)
    {
        // Build Basic Info list with only available fields
        $basicItems = [];
        if (! empty($student->student_name)) {
            $basicItems[] = "<li><b>Name:</b> {$student->student_name}</li>";
        }
        if (! empty($student->father_name)) {
            $basicItems[] = "<li><b>Father Name:</b> {$student->father_name}</li>";
        }
        if (! empty($student->mother_name)) {
            $basicItems[] = "<li><b>Mother Name:</b> {$student->mother_name}</li>";
        }
        if (! empty($student->date_of_birth)) {
            $basicItems[] = '<li><b>Date of Birth:</b> '.\Carbon\Carbon::parse($student->date_of_birth)->format('d-m-Y').'</li>';
        }
        if (! empty($student->email)) {
            $basicItems[] = "<li><b>Email:</b> {$student->email}</li>";
        }
        if (! empty($student->personal_number)) {
            $basicItems[] = "<li><b>Personal Number:</b> {$student->personal_number}</li>";
        }
        if (! empty($student->parents_number)) {
            $basicItems[] = "<li><b>Parents Number:</b> {$student->parents_number}</li>";
        }
        if (! empty($student->whatsapp_number)) {
            $basicItems[] = "<li><b>WhatsApp Number:</b> {$student->whatsapp_number}</li>";
        }
        $basicHtml = implode("\n", $basicItems);

        // Resolve subject and batch names from relation or string fields
        $subjectName = null;
        if (isset($student->subject)) {
            $subjectName = $student->subject->title ?? $student->subject->name ?? null;
        }
        if (empty($subjectName) && ! empty($student->subject_name)) {
            $subjectName = $student->subject_name;
        }

        $batchName = null;
        if (isset($student->batch)) {
            $batchName = $student->batch->title ?? $student->batch->name ?? null;
        }
        if (empty($batchName) && ! empty($student->batch_name)) {
            $batchName = $student->batch_name;
        }

        // Build Educational Details list with only available fields
        $educationItems = [];
        $educationItems[] = '<li><b>Course:</b> NIOS</li>';
        if (! empty($subjectName)) {
            $educationItems[] = "<li><b>Subject:</b> {$subjectName}</li>";
        }
        if (! empty($batchName)) {
            $educationItems[] = "<li><b>Batch:</b> {$batchName}</li>";
        }
        if (! empty($student->second_language)) {
            $educationItems[] = "<li><b>Second Language:</b> {$student->second_language}</li>";
        }
        $educationHtml = implode("\n", $educationItems);

        // Build Address list with only available fields
        $addressItems = [];
        if (! empty($student->street)) {
            $addressItems[] = "<li><b>Street:</b> {$student->street}</li>";
        }
        if (! empty($student->locality)) {
            $addressItems[] = "<li><b>Locality:</b> {$student->locality}</li>";
        }
        if (! empty($student->post_office)) {
            $addressItems[] = "<li><b>Post Office:</b> {$student->post_office}</li>";
        }
        if (! empty($student->district)) {
            $addressItems[] = "<li><b>District:</b> {$student->district}</li>";
        }
        if (! empty($student->state)) {
            $addressItems[] = "<li><b>State:</b> {$student->state}</li>";
        }
        if (! empty($student->pin_code)) {
            $addressItems[] = "<li><b>PIN Code:</b> {$student->pin_code}</li>";
        }
        $addressHtml = implode("\n", $addressItems);

        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 700px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                
                <h2 style='color: #2c3e50; text-align: center;'>Admission Verification Confirmation – Official Record Copy</h2>
                <p>Dear <b>{$verifier->name}</b>,</p>

                <p>This is to officially confirm that the student data and supporting documents verified by you 
                have now been recorded in the admission system.</p>

                <hr style='margin:20px 0;'>

                <h3 style='color: #2c3e50;'>📌 Step 1: Basic Info</h3>
                <ul>
                    {$basicHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Step 2: Educational Details</h3>
                <ul>
                    {$educationHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Step 3: Address</h3>
                <ul>
                    {$addressHtml}
                </ul>

                <p><b>Verified By:</b> {$verifier->name}<br>
                <b>Verified At:</b> ".now()->format('d-m-Y H:i:s')."</p>

                <hr style='margin:20px 0;'>
                <p>
                    This is to officially confirm that the student data and supporting documents
                    verified by you have now been recorded in the admission system. The details contained
                    in this record are based on the submissions made by the student and your verification
                    as the responsible officer.
                </p>

                <p>
                    By completing this task, you acknowledge that all the student's information, including personal details,
                    identification proof, academic certificates, and any other required documents,
                    have been thoroughly checked by you with due diligence. You further confirm that the 
                    verification has been carried out in accordance with the institutional guidelines and 
                    standards of accuracy.
                </p>

                <p>
                    Please be reminded that the responsibility for this verification rests entirely with you as the verifying
                    officer. In the event of any discrepancies, errors, or issues arising in the future regarding
                    this student's data or documents, accountability will remain under your role as the 
                    verifier.
                </p>

                <p>
                    We request you to retain this confirmation as part of your official record for future reference. 
                    Your diligence and professionalism in performing this responsibility are greatly appreciated by the Academic 
                    Admission Department.
                </p>

                <p style='margin-top:30px;'>
                    Sincerely,<br>
                    <b>Academic Admission Department</b>
                </p>
            </div>
        </body>
        </html>";
    }

    private static function getStudentAttachments($student)
    {
        $attachments = [];

        // All document fields from leads_details table
        $documentFields = [
            'birth_certificate',
            'passport_photo',
            'adhar_front',
            'adhar_back',
            'signature',
            'plustwo_certificate',
            'ug_certificate',
            'sslc_certificate',
        ];

        foreach ($documentFields as $field) {
            if (! empty($student->$field)) {
                $filePath = storage_path('app/public/'.$student->$field);
                if (file_exists($filePath)) {
                    $attachments[] = $filePath;
                }
            }
        }

        // Include multiple SSLC certificates if present via relation
        if (isset($student->sslcCertificates) && $student->sslcCertificates->count() > 0) {
            foreach ($student->sslcCertificates as $cert) {
                if (! empty($cert->certificate_path)) {
                    $filePath = storage_path('app/public/'.$cert->certificate_path);
                    if (file_exists($filePath)) {
                        $attachments[] = $filePath;
                    }
                }
            }
        }

        return $attachments;
    }

    private static function getUploadedFilesList($student)
    {
        $fileList = [];

        // Document fields with their display names
        $documentFields = [
            'birth_certificate' => 'Birth Certificate',
            'passport_photo' => 'Passport Photo',
            'adhar_front' => 'Aadhar Card (Front)',
            'adhar_back' => 'Aadhar Card (Back)',
            'signature' => 'Signature',
            'plustwo_certificate' => 'Plus Two Certificate',
            'ug_certificate' => 'UG Certificate',
            'sslc_certificate' => 'SSLC Certificate',
        ];

        foreach ($documentFields as $field => $displayName) {
            if (! empty($student->$field)) {
                $fileList[] = "<li><b>{$displayName}:</b> Uploaded</li>";
            }
        }

        if (empty($fileList)) {
            return '<p>No documents uploaded.</p>';
        }

        return '<ul>'.implode("\n", $fileList).'</ul>';
    }

    private static function getFieldName($field)
    {
        if (is_string($field)) {
            return $field;
        } elseif (is_object($field)) {
            // Handle Eloquent models or objects
            if (isset($field->title)) {
                return $field->title;
            } elseif (isset($field->name)) {
                return $field->name;
            } elseif (method_exists($field, 'toArray')) {
                $array = $field->toArray();

                return $array['title'] ?? $array['name'] ?? 'N/A';
            }
        } elseif (is_array($field)) {
            return $field['title'] ?? $field['name'] ?? 'N/A';
        }

        return 'N/A';
    }

    private static function getTermsAndConditions($courseType, $student)
    {
        // Only add terms and conditions for NIOS and BOSSE courses
        if (! in_array(strtoupper($courseType), ['NIOS', 'BOSSE'])) {
            return '';
        }

        return "
        <hr style='margin:30px 0;'>
        
        <h2 style='color: #2c3e50; text-align: center; margin-bottom: 30px;'>Welcome To World Of Edu Fantasy</h2>
        
        <p style='text-align: center; font-weight: bold; margin-bottom: 20px;'>From: Academic Operations Department</p>
        
        <p>Dear {$student->student_name},</p>
        
        <p>We are pleased to officially confirm your admission to our institution and extend to you a warm welcome to our academic family. This marks a significant milestone in your educational journey, and we are honored to be part of your growth and success.</p>
        
        <p>By choosing to study with us, you are joining a vibrant community of learners who share a strong passion for knowledge, skill development, and personal growth. Your admission reflects both your potential and our confidence in your ability to achieve excellence.</p>
        
        <p>Our commitment is to ensure that your academic journey with us is enriched with:</p>
        <ul>
            <li>Meaningful learning experiences</li>
            <li>Valuable academic opportunities</li>
            <li>Dedicated mentorship and guidance</li>
            <li>Technology-enabled resources</li>
            <li>A supportive and inspiring environment</li>
        </ul>
        
        <p>We have carefully designed a structured learning system that combines academic excellence with real-world application of knowledge, ensuring that you are well-prepared for both your academic pursuits and your future career path.</p>
        
        <p>Once again, congratulations on your admission. We look forward to witnessing your achievements and welcoming you to an inspiring and successful academic experience.</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Admission Confirmation:</h3>
        <p>We are pleased to officially confirm your admission to our institution. We extend a warm welcome to you as a new member of our academic community. Securing admission to your chosen course marks the beginning of a new chapter in your academic journey. In this regard, we request you to carefully read and understand the important instructions provided below.</p>
        
        <p>The Admission Number allotted to you will serve as your primary identification number for all academic and administrative purposes throughout your course duration.</p>
        
        <p>This number must be compulsorily mentioned in the following cases:</p>
        <ul>
            <li>All official communications</li>
            <li>When submitting fee payment slips</li>
            <li>During exam submissions and when providing academic records</li>
            <li>When applying for any special requests or services</li>
        </ul>
        
        <p>Using your Admission Number enables quick retrieval of your records in our system, facilitating efficient service and avoiding any delays or errors in processing your requests. This number will be issued to you by the Support Team.</p>
        
        <p>Please note that any discrepancies or errors in the documents you submitted will only be identified after the registration process is completed. Hence, you are advised to carefully monitor all updates shared in your respective student groups to avoid last-minute issues, especially during sudden changes or requirements from the educational boards.</p>
        
        <p>We also remind you to make timely fee payments and stay attentive to all official instructions communicated through our approved channels.</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Batch Change Policy & Associated Fee</h3>
        <p><strong>Re No.: Academic/policy/2023/08</strong></p>
        <p>As per the academic policies of our institution, all students are required to complete their classes and examinations within the batch in which they were originally admitted. Batch changes will only be considered under exceptional and justified circumstances. Therefore, students are expected to strictly adhere to the academic schedule and plan of their respective batches.</p>
        
        <p>Students who wish to apply for a batch change must submit an official request. Upon approval, a non-refundable Batch Change Fee of ₹2000/- must be paid. Access to the new batch's WhatsApp group and mobile application will be granted only after successful payment of this fee.</p>
        
        <p>We strongly encourage students to plan their academic activities in advance and avoid submitting unnecessary batch change requests.</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Mobile Learning App – Access Guidelines</h3>
        <p>As part of your academic onboarding, you will be granted access to our official mobile learning platform within 48 hours of your admission confirmation. This app is an essential tool designed to support your academic journey and ensure continuous learning from anywhere and any time at your convenience.</p>
        
        <p><strong>Key Features of the Learning App:</strong></p>
        <ul>
            <li><strong>Live and Recorded Classes:</strong> Attend sessions in real time or watch them later at your own pace.</li>
            <li><strong>Subject-wise PDF Notes and Study Materials:</strong> Downloadable PDF content for each subject.</li>
            <li><strong>Practice Tests, Mock Exams & Assignment Upload:</strong> Tools to test your knowledge and submit academic work efficiently.</li>
            <li><strong>Daily Announcements and Updates:</strong> Stay informed about important dates, academic schedules, and notifications.</li>
            <li><strong>Attendance Tracking:</strong> Monitor your class participation and maintain consistency.</li>
            <li><strong>Performance Reports:</strong> Review your academic progress regularly</li>
        </ul>
        
        <p><strong>How to Access the App:</strong></p>
        <ol>
            <li>You will receive a WhatsApp or Email containing your login credentials and a secure download link for the app.</li>
            <li>Ensure your registered mobile number and email are active to receive the credentials.</li>
            <li>Use the provided Login ID and Password to access your account.</li>
            <li>For security reasons, do not share your credentials with anyone.</li>
        </ol>
        
        <p>If you do not receive your login details within 48 hours, please contact the Student Support (9207666614, 9207666615) Team immediately using the official contact list.</p>
        
        <p>Stay connected, stay informed, and make the most of your digital learning experience!</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Academic Communication via WhatsApp / Telegram Groups</h3>
        <p>We are pleased to welcome you to our academic platform. As part of our structured academic communication and support system, students who have successfully completed their app login and document submission will be added to the institution's official communication groups.</p>
        
        <p>These groups serve as the primary channels for academic coordination, updates, and support throughout your course.</p>
        
        <p>Once your onboarding process is complete, you will receive access to the following official groups:</p>
        <ul>
            <li><strong>Public Community WhatsApp Group</strong> – Course-specific group where updates related to your enrolled program will be shared.</li>
            <li><strong>Mentoring & Guidance Group</strong> – Direct communication group between you and your assigned mentor(s) for academic assistance, queries, and regular follow-ups.</li>
            <li><strong>Telegram Group</strong> – A supplementary communication platform (if applicable) used for additional announcements and file sharing.</li>
        </ul>
        
        <p>These platforms are not casual chat groups but structured communication mediums designed to ensure that every student receives timely academic information, institutional announcements, and individual guidance from the respective teaching and support teams.</p>
        
        <p><strong>Group Communication Guidelines:</strong></p>
        <p>To maintain professionalism and ensure effective communication, the following guidelines must be strictly followed by all students:</p>
        <ol>
            <li>Only official academic updates, announcements, and instructions will be posted by the administration or faculty members.</li>
            <li>Students are expected to regularly check the group messages and respond or act when required.</li>
            <li>If you have any doubt regarding any communication posted in the group, you must contact your assigned mentor directly for clarification. Do not reply in the group unless specifically instructed to do so.</li>
            <li>Avoid posting personal messages, unrelated content, or questions in the group chat. Any misuse of the group may result in disciplinary action or removal from the group.</li>
            <li>Ensure that notifications are turned on and that you do not mute the group. Missing critical information due to negligence will not be considered a valid excuse.</li>
        </ol>
        
        <p><strong>Why These Groups Are Important:</strong></p>
        <p>All academic-related instructions—including class schedules, exam updates, model exams, mentoring follow-ups, content updates, certification processes, and institutional notices—will be communicated only through these official groups. Therefore, active participation and compliance with communication protocols are mandatory.</p>
        
        <p>Your cooperation in maintaining a professional and focused academic environment is highly appreciated.</p>
        
        <p>We advise all students to treat these groups as part of their formal academic space and adhere strictly to the guidelines for the smooth functioning of academic operations.</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Academic Mentorship Program</h3>
        <p>As part of our commitment to ensuring academic excellence and student success, each enrolled student will be assigned a dedicated Mentorship Officer. This officer will serve as your primary point of academic support throughout the duration of your course. The goal of the Academic Mentorship Program is to help you overcome challenges, enhance your performance, and develop effective learning strategies and time management skills.</p>
        
        <p>Your mentor is here to guide, motivate, and monitor you, ensuring that you stay on track with your academic responsibilities and achieve your goals in a structured and disciplined manner.</p>
        
        <p><strong>Roles and Responsibilities of the Mentorship Officer:</strong></p>
        <ul>
            <li><strong>Monitor Academic Progress & Attendance:</strong> Mentors will closely track your class attendance, assignment submissions, exam participation, and overall academic involvement. Any irregularities or gaps in participation will be promptly addressed through feedback and follow-up.</li>
            <li><strong>Provide Study Support, Guidance & Motivation:</strong> Based on your performance and learning style, your mentor will suggest effective study strategies and time management techniques. They will also encourage and motivate you to stay consistent and overcome any academic obstacles.</li>
            <li><strong>Conduct One-on-One Communication:</strong> If necessary, your mentor may schedule individual calls to better understand your challenges, clarify doubts, and provide customized academic advice. These calls are an essential part of the personalized support offered to you.</li>
            <li><strong>Share Feedback Reports & Improvement Suggestions:</strong> You will receive regular feedback on your academic progress. These updates may include strengths, areas for improvement, and actionable steps to enhance your performance. Feedback will be constructive and designed to help you grow academically.</li>
        </ul>
        
        <p><strong>Communication Protocol with Mentors:</strong></p>
        <p>Once your admission and onboarding processes are complete, your mentor will initiate contact through official communication platforms such as WhatsApp, Telegram, or app-based messages.</p>
        
        <p>Students are expected to:</p>
        <ul>
            <li>Respond to mentor messages politely, respectfully, and in a timely manner.</li>
            <li>Cooperate with instructions or guidance provided by the mentor.</li>
            <li>Maintain professionalism in all mentor-student communications.</li>
            <li>Inform the mentor in case of any difficulty that might affect academic performance.</li>
        </ul>
        
        <p>Remember, your mentor is here to support your success, not to supervise you. The relationship is built on mutual respect, open communication, and a shared goal of academic achievement.</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Your Success is a Shared Responsibility</h3>
        <p>The mentorship program is designed to offer you structured academic support. However, its effectiveness depends on your active participation and willingness to engage. Students who maintain regular communication with their mentors typically experience better academic performance, greater confidence, and improved time management skills.</p>
        
        <p>Take full advantage of this opportunity by being responsive, responsible, and receptive to your mentor's guidance.</p>
        
        <p>If you have any issues or concerns related to mentorship, please contact the Academic Support Team for assistance.</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Academic Calendar & Course Plan</h3>
        <p><strong>A Structured and Student-Friendly Roadmap for Your Academic Journey</strong></p>
        <p>After the successful completion of your admission and onboarding process, you will receive your personalized Course Schedule and Academic Calendar. This calendar serves as a comprehensive academic roadmap, outlining the complete timeline and structure of your course.</p>
        
        <p>The primary objective of the Academic Calendar is to ensure that every student follows a systematic learning path and remains informed about each academic milestone. Therefore, it is important that students read this calendar carefully and follow it consistently throughout the course. The calendar will be issued and explained by your assigned Mentor.</p>
        
        <p><strong>Key Components of the Academic Calendar:</strong></p>
        <ul>
            <li><strong>Mentoring Sessions and Doubt-Clearing Classes:</strong> In addition to regular subject classes, dedicated mentoring sessions and doubt-clearing discussions will be scheduled at regular intervals. These sessions aim to support students in resolving academic challenges and improving performance with guided mentorship.</li>
            <li><strong>Assignment & Project Submission Deadlines:</strong> All academic tasks such as assignments and projects will have specific submission dates. Students must adhere strictly to these deadlines, as late submissions will not be accepted under any circumstance.</li>
            <li><strong>Mock Exams & Review Tests:</strong> Regular mock tests and review assessments will be conducted to monitor your academic progress and gauge your understanding of the syllabus. These tests are vital for performance tracking and exam preparation.</li>
            <li><strong>Final Evaluation Dates:</strong> The calendar will also include clear timelines for final exams, oral assessments (if applicable), and course completion evaluations. These dates are crucial for certification eligibility.</li>
        </ul>
        
        <p><strong>App Notifications & Schedule Tracking:</strong></p>
        <p>All updates related to your academic schedule will be available through the official Learning App. You are required to check the app regularly for class schedules, exam dates, assignment deadlines, and any academic updates.</p>
        
        <p>Failure to follow the schedule—such as repeated absences or missed deadlines—without prior intimation may result in academic probation, mentor follow-up, or further disciplinary action.</p>
        
        <p><strong>Student Responsibilities:</strong></p>
        <ul>
            <li>Be proactive in preparing for classes, assessments, and assignments.</li>
            <li>Check notifications and updates on the Learning App regularly.</li>
            <li>Align your personal schedule with the Academic Calendar to avoid conflicts.</li>
            <li>Communicate directly with your mentor in case of difficulties or unavoidable absences.</li>
        </ul>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Privacy Policy</h3>
        <p>At our institution, the privacy of students who take admission with us is of utmost importance. To ensure confidentiality, all official WhatsApp and Telegram groups involving students will be structured in a restricted format. Students will be able to view only the group admins; other participants will remain hidden.</p>
        
        <p>All documents submitted by the students will be forwarded exclusively to the Registration Department. No other department will have access to personal documents. However, to ensure proper academic coordination, only limited details such as the student's name, course, and mobile number will be shared with relevant departments.</p>
        
        <p>Please note that staff members from other departments will not have access to any additional personal information. This policy is strictly followed to protect the privacy and security of our students and to avoid any misuse of sensitive data.</p>
        
        <p>Your understanding and cooperation are greatly appreciated.</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Office Working Hours</h3>
        <p>Our official office hours are Monday to Friday, from 9:00 AM to 5:00 PM. During this time, all WhatsApp messages, phone calls, and emails received will be promptly reviewed and responded to by our team.</p>
        
        <p>Any communications received outside these working hours will be addressed on the next official working day. We kindly request your patience and cooperation, as responses to queries submitted beyond office hours may take additional time to process.</p>
        
        <p>Please note that our office remains closed on Saturdays and Sundays. Messages or inquiries received during weekends or public holidays will be attended to only once regular operations resume.</p>
        
        <p>We sincerely apologize for any inconvenience this may cause and appreciate your understanding and continued support.</p>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Department Officers:</h3>
        <p>Since your admission, various departments have been assigned to provide you with the necessary support and follow-up. Below are the primary contact numbers for assistance related to your studies:</p>
        
        <ol>
            <li><strong>Support Officer</strong> - 9207666614, 9207666615<br>Handles technical support, app/link inquiries, and general academic matters.</li>
            <li><strong>Mentoring Officer</strong> – 9207666723<br>Provides mentoring support tailored to each student's needs.</li>
            <li><strong>Finance Officer</strong> – 8289916067<br>Manages registration fees, exam fees, and financial matters.</li>
            <li><strong>Post-Sales</strong> – 8089649255<br>Collects tuition fees, exam fees, and registration fees through phone calls and follow-up apps.</li>
            <li><strong>Enquiry Officer</strong> – 8129811884<br>Handles new admissions and student inquiries.</li>
        </ol>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Feedback & Grievance Mechanism</h3>
        <p>If you encounter any unresolved issues or delays, please follow this escalation path:</p>
        <ol>
            <li>Initially, raise the issue with the respective WhatsApp group admin.</li>
            <li>If the issue remains unresolved, contact the assigned officer directly (Support Team, Mentor Officer).</li>
            <li>For urgent unresolved cases, please email: <a href='mailto:cao@natdemy.com'>cao@natdemy.com</a><br>Subject: Student Grievance –Your Name –Issue Type</li>
        </ol>
        
        <h3 style='color: #2c3e50; margin-top: 30px;'>Frequently Asked Questions (FAQs)</h3>
        <p>We have answered below the most common questions students ask after admission—please read carefully before reaching out to the support team.</p>
        
        <p><strong>Q1: When will I receive access to the learning app?</strong><br>
        A: You will receive app access within 48 working hours of your admission confirmation. Please check your email and SMS regularly for login details.</p>
        
        <p><strong>Q3: How will I be informed about exam dates?</strong><br>
        A: Exam schedules will be communicated through your official WhatsApp group and directly by your assigned mentor.</p>
        
        <p><strong>Q4: Is it possible to change my course after admission?</strong><br>
        A: No. Once admission is confirmed, course changes are strictly not permitted.</p>
        
        <p><strong>Q5: Can I pause, defer, or extend my course duration?</strong><br>
        A: No. All courses must be completed within the assigned batch timeline without extensions.</p>
        
        <p><strong>Q6: When will I receive my academic calendar and class schedule?</strong><br>
        A: Your Course Plan and Academic Calendar will be shared within 3–5 working days after onboarding is completed.</p>
        
        <p><strong>Q7: How can I communicate with my mentor or faculty?</strong><br>
        A: You can communicate through your designated WhatsApp mentoring group or directly during live sessions and scheduled doubt-clearing hours.</p>
        
        <p><strong>Q8: What is the process for submitting assignments and projects?</strong><br>
        A: All assignments and project submissions must be uploaded via the official app before the deadline. Late submissions may not be evaluated.</p>
        
        <p><strong>Q9: Will I receive reminders about deadlines and exams?</strong><br>
        A: Yes. Notifications and reminders will be sent via the app, WhatsApp group, and mentor announcements.</p>
        
        <p><strong>Q10: How do I resolve technical issues with the app or login?</strong><br>
        A: Please contact the Student Support Team via WhatsApp or email. Most issues are resolved within 24–48 working hours.</p>
        
        <p><strong>Q11: Will I get a certificate upon course completion?</strong><br>
        A: Yes. Upon successful completion of your course and final evaluation, a digital certificate will be issued as per institutional policy.</p>
        
        <p><strong>Q12: What happens if I am absent for multiple classes?</strong><br>
        A: Regular absences without valid reason may result in mentor follow-up and may affect your academic status.</p>
        
        <p><strong>Q13: Can I access course materials offline?</strong><br>
        A: Most resources are available only through the app, which requires internet access. However, downloadable PDFs or notes may be provided by mentors in some cases.</p>
        
        <p><strong>Q14: Is attendance mandatory for all sessions?</strong><br>
        A: Yes. Active participation and attendance are mandatory. Repeated absenteeism may lead to academic warnings.</p>
        
        <p><strong>Q15: How often are mentoring sessions held?</strong><br>
        A: Mentoring and doubt-clearing sessions are typically held on a weekly or bi-weekly basis, depending on your course.</p>
        
        <p><strong>Q17: Are there any mock tests or practice exams?</strong><br>
        A: Yes. Regular mock exams and practice tests will be scheduled and notified through your academic calendar.</p>
        
        <p><strong>Q18: Will I be informed of performance updates or academic reviews?</strong><br>
        A: Yes. Periodic performance updates will be shared by your mentor, especially after internal assessments and mock exams.</p>
        
        <p><strong>Q19: Can I request a batch change if my schedule conflicts?</strong><br>
        A: Batch changes are allowed only in exceptional cases with valid documentation and prior approval from the academic office.</p>
        
        <p><strong>Q20: Who do I contact for fee-related or administrative queries?</strong><br>
        A: For all financial and administrative concerns, please contact the Accounts Department via the official communication channels.</p>
        ";
    }
}
