<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\CourseMail;
use App\Models\ConvertedStudentMentorDetail;
use App\Models\ConvertedStudentSupportDetail;
use App\Models\SupportFeedbackHistory;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Models\LeadDetail;
use App\Models\ClassTime;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Services\MailService;
use App\Services\WatiService;
use App\Support\ConvertedLeadWhatsAppSupport;
use App\Support\CourseMailResolver;
use App\Support\SupportFlagFieldSupport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SupportConvertedLeadController extends Controller
{
    /**
     * Display a listing of Board of Open Schooling and Skill Education converted leads for support
     */
    public function index(Request $request)
    {
        $query = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'supportDetails',
            'supportFlag',
            'subject',
            'batch',
            'admissionBatch'
        ])->where('course_id', 2) // Board of Open Schooling and Skill Education course
          ->where('is_academic_verified', 1);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('studentDetails', function($subQ) use ($search) {
                      $subQ->where('application_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('registration_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('registration_status', $request->registration_status);
            });
        }

        if ($request->filled('student_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('student_status', $request->student_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        SupportFlagFieldSupport::applyListingFilter($query, $request);

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', 2)->orderBy('title')->get();
        $subjects = Subject::where('course_id', 2)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $supportFlags = SupportFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.support-bosse-index', compact(
            'convertedLeads', 
            'batches', 
            'subjects', 
            'country_codes',
            'supportFlags'
        ));
    }

    private function canSendSupportCourseMail(): bool
    {
        if (! AuthHelper::isLoggedIn()) {
            return false;
        }

        return RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_super_admin()
            || RoleHelper::is_academic_assistant()
            || RoleHelper::is_admission_counsellor()
            || RoleHelper::is_support_team()
            || RoleHelper::is_team_lead()
            || (function_exists('has_permission') && has_permission('admin/support-bosse-converted-leads/index'));
    }

    private function courseMailJsonResponse(array $data, int $status = 200)
    {
        return response()->json($data, $status)->header('Content-Type', 'application/json');
    }

    /**
     * Send course mail form (loaded in ajax modal).
     */
    public function showSendCourseMailForm(Request $request, $id)
    {
        if (! $this->canSendSupportCourseMail()) {
            abort(403, 'Access denied.');
        }

        $convertedLead = ConvertedLead::with(['course', 'batch', 'admissionBatch'])->findOrFail($id);

        if (! filled($convertedLead->email)) {
            return $this->renderSendCourseMailForm($request, [
                'convertedLead' => $convertedLead,
                'error' => 'This converted lead does not have an email address.',
            ]);
        }

        $templates = CourseMailResolver::listForCourse((int) $convertedLead->course_id);
        $defaultMail = CourseMailResolver::resolveForConvertedLead($convertedLead);
        $defaultId = $defaultMail?->id;

        $templateOptions = $templates->map(function (CourseMail $mail) use ($defaultId) {
            return CourseMailResolver::templateToArray($mail, (int) $mail->id === (int) $defaultId);
        })->values();

        $contextParts = array_filter([
            $convertedLead->course?->title,
            $convertedLead->batch?->title,
            $convertedLead->admissionBatch?->title,
        ]);

        return $this->renderSendCourseMailForm($request, [
            'convertedLead' => $convertedLead,
            'templateOptions' => $templateOptions,
            'selectedTemplateId' => $defaultMail?->id,
            'subject' => CourseMailResolver::defaultSubject($convertedLead),
            'content' => $defaultMail?->content ?? '',
            'context' => $contextParts ? implode(' · ', $contextParts) : null,
        ]);
    }

    private function renderSendCourseMailForm(Request $request, array $data)
    {
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.converted-leads.send-course-mail-form', $data);
        }

        return view('admin.converted-leads.send-course-mail-page', $data);
    }

    /**
     * Send edited course mail to a support converted lead (does not update course_mails).
     */
    public function sendSupportCourseMail(Request $request, $id)
    {
        if (! $this->canSendSupportCourseMail()) {
            return $this->courseMailJsonResponse(['success' => false, 'error' => 'Access denied.']);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:50000',
        ]);

        $convertedLead = ConvertedLead::with(['course'])->findOrFail($id);

        if (! filled($convertedLead->email)) {
            return $this->courseMailJsonResponse([
                'success' => false,
                'error' => 'This converted lead does not have an email address.',
            ]);
        }

        try {
            $sendResult = MailService::sendConvertedLeadSupportMail(
                $convertedLead,
                $request->subject,
                $request->content
            );

            if (! $sendResult['success']) {
                $error = $sendResult['error'] ?? 'Failed to send mail.';

                Log::error('Support course mail send failed', [
                    'converted_lead_id' => $id,
                    'error' => $error,
                ]);

                return $this->courseMailJsonResponse([
                    'success' => false,
                    'error' => $error,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Support course mail send failed: '.$e->getMessage(), [
                'converted_lead_id' => $id,
            ]);

            return $this->courseMailJsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->courseMailJsonResponse([
            'success' => true,
            'message' => 'Mail sent to '.$convertedLead->email.'.',
        ]);
    }

    /**
     * Send WhatsApp message to a support converted lead via Wati.
     */
    public function sendSupportWhatsApp(Request $request, $id)
    {
        if (! $this->canSendSupportCourseMail()) {
            return response()->json(['success' => false, 'error' => 'Access denied.'], 403);
        }

        $convertedLead = ConvertedLead::with(['leadDetail'])->findOrFail($id);

        $recipient = ConvertedLeadWhatsAppSupport::resolveRecipient($convertedLead);
        if (! $recipient) {
            return response()->json([
                'success' => false,
                'error' => 'No WhatsApp or phone number with country code is available for this student.',
            ], 422);
        }

        $wati = app(WatiService::class);
        if (! $wati->canSendTemplate()) {
            return response()->json([
                'success' => false,
                'error' => 'WhatsApp (Wati) is not configured. Add WATI_ENABLED, WATI_API_ENDPOINT, WATI_API_TOKEN, and WATI_CHANNEL_PHONE_NUMBER to .env.',
            ], 503);
        }

        $parameters = ConvertedLeadWhatsAppSupport::resolveTemplateParameters($convertedLead);
        $templateName = (string) config('wati.template_name', 'support_desk');

        try {
            $result = $wati->sendTemplateMessage($recipient['number'], $parameters);
        } catch (\Throwable $e) {
            Log::error('Wati WhatsApp template send failed: '.$e->getMessage(), [
                'converted_lead_id' => $id,
                'recipient' => $recipient['number'],
                'template_name' => $templateName,
            ]);

            $errorMessage = trim((string) $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $errorMessage !== '' ? $errorMessage : 'Unable to send WhatsApp message right now. Please try again shortly.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp template "'.$templateName.'" sent to '.$recipient['display'].' ('.$recipient['source'].').',
            'data' => $result,
        ]);
    }

    /**
     * Show Board of Open Schooling and Skill Education support converted lead details
     */
    public function showBosse($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail.sslcCertificates.verifiedBy',
            'leadDetail.sslcVerifiedBy',
            'leadDetail.plustwoVerifiedBy',
            'leadDetail.ugVerifiedBy',
            'leadDetail.passportPhotoVerifiedBy',
            'leadDetail.adharFrontVerifiedBy',
            'leadDetail.adharBackVerifiedBy',
            'leadDetail.signatureVerifiedBy',
            'leadDetail.birthCertificateVerifiedBy',
            'leadDetail.otherDocumentVerifiedBy',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails',
            'supportDetails',
            'subject',
            'batch',
            'admissionBatch'
        ])->findOrFail($id);

        if ((int) ($convertedLead->course_id) !== 2) {
            abort(404);
        }

        return view('admin.converted-leads.support-bosse-show', compact('convertedLead'));
    }

    /**
     * Show NIOS support converted lead details
     */
    public function showNios($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail.sslcCertificates.verifiedBy',
            'leadDetail.sslcVerifiedBy',
            'leadDetail.plustwoVerifiedBy',
            'leadDetail.ugVerifiedBy',
            'leadDetail.passportPhotoVerifiedBy',
            'leadDetail.adharFrontVerifiedBy',
            'leadDetail.adharBackVerifiedBy',
            'leadDetail.signatureVerifiedBy',
            'leadDetail.birthCertificateVerifiedBy',
            'leadDetail.otherDocumentVerifiedBy',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails',
            'supportDetails',
            'subject',
            'batch',
            'admissionBatch'
        ])->findOrFail($id);

        if ((int) ($convertedLead->course_id) !== 1) {
            abort(404);
        }

        return view('admin.converted-leads.support-nios-show', compact('convertedLead'));
    }

    /**
     * Show unified support converted lead details (any course)
     */
    public function show($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail.sslcCertificates.verifiedBy',
            'leadDetail.sslcVerifiedBy',
            'leadDetail.plustwoVerifiedBy',
            'leadDetail.ugVerifiedBy',
            'leadDetail.passportPhotoVerifiedBy',
            'leadDetail.adharFrontVerifiedBy',
            'leadDetail.adharBackVerifiedBy',
            'leadDetail.signatureVerifiedBy',
            'leadDetail.birthCertificateVerifiedBy',
            'leadDetail.otherDocumentVerifiedBy',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails',
            'supportDetails',
            'supportFeedbackHistory.createdBy',
            'subject',
            'batch',
            'admissionBatch.mentor'
        ])->findOrFail($id);

        return view('admin.converted-leads.support-show', compact('convertedLead'));
    }

    /**
     * Display a listing of NIOS converted leads for support
     */
    public function niosIndex(Request $request)
    {
        $query = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'supportDetails',
            'supportFlag',
            'subject',
            'batch',
            'admissionBatch'
        ])->where('course_id', 1) // NIOS course
          ->where('is_academic_verified', 1);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('studentDetails', function($subQ) use ($search) {
                      $subQ->where('application_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('registration_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('registration_status', $request->registration_status);
            });
        }

        if ($request->filled('student_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('student_status', $request->student_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        SupportFlagFieldSupport::applyListingFilter($query, $request);

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', 1)->orderBy('title')->get();
        $subjects = Subject::where('course_id', 1)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $supportFlags = SupportFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.support-nios-index', compact(
            'convertedLeads', 
            'batches', 
            'subjects', 
            'country_codes',
            'supportFlags'
        ));
    }

    /**
     * Display a listing of UG/PG converted leads for support
     */
    public function ugpgIndex(Request $request)
    {
        $query = ConvertedLead::with([
            'lead',
            'leadDetail.university',
            'leadDetail.universityCourse',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'supportDetails',
            'supportFlag',
            'batch',
            'admissionBatch'
        ])->where('course_id', 9) // UG/PG course
          ->where('is_academic_verified', 1);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('register_number', 'like', "%{$search}%")
                  ->orWhereHas('studentDetails', function($subQ) use ($search) {
                      $subQ->where('application_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('university_id')) {
            $query->whereHas('leadDetail', function($q) use ($request) {
                $q->where('university_id', $request->university_id);
            });
        }

        if ($request->filled('course_type')) {
            $query->whereHas('leadDetail', function($q) use ($request) {
                $q->where('course_type', $request->course_type);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        SupportFlagFieldSupport::applyListingFilter($query, $request);

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', 9)->orderBy('title')->get();
        $universities = \App\Models\University::where('is_active', 1)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $supportFlags = SupportFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.support-ugpg-index', compact(
            'convertedLeads', 
            'batches', 
            'universities', 
            'country_codes',
            'supportFlags'
        ));
    }

    /**
     * Display a listing of EduMaster converted leads for support
     */
    public function edumasterIndex(Request $request)
    {
        $query = ConvertedLead::with([
            'lead',
            'leadDetail.university',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'supportDetails',
            'supportFlag',
            'batch',
            'admissionBatch'
        ])->where('course_id', 23) // EduMaster course
          ->where('is_academic_verified', 1);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('register_number', 'like', "%{$search}%")
                  ->orWhereHas('leadDetail', function($subQ) use ($search) {
                      $subQ->where('whatsapp_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('university_id')) {
            $query->whereHas('leadDetail', function($q) use ($request) {
                $q->where('university_id', $request->university_id);
            });
        }

        if ($request->filled('course_type')) {
            $query->whereHas('leadDetail', function($q) use ($request) {
                $q->where('course_type', $request->course_type);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        SupportFlagFieldSupport::applyListingFilter($query, $request);

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', 23)->orderBy('title')->get();
        $universities = \App\Models\University::where('is_active', 1)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $supportFlags = SupportFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.support-edumaster-index', compact(
            'convertedLeads', 
            'batches', 
            'universities', 
            'country_codes',
            'supportFlags'
        ));
    }

    /**
     * Update support details inline
     */
    public function updateSupportDetails(Request $request, $id)
    {
        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            $field = $request->field;
            $value = $request->value;

            if ($field === 'support_flag_id') {
                return SupportFlagFieldSupport::supportFlagUpdateJsonResponse($convertedLead, $value);
            }

            // Validate the field and value
            $validationRules = $this->getValidationRules($field, $convertedLead->course_id);
            if ($validationRules) {
                $validator = Validator::make([$field => $value], [$field => $validationRules]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()->first($field)
                    ], 422);
                }
            }

            $courseId = (int) $convertedLead->course_id;
            $convertedLeadFields = ['register_number', 'name', 'phone', 'batch_id', 'admission_batch_id'];
            $leadDetailFields = ['whatsapp_number', 'whatsapp_code', 'class_time_id', 'parents_number', 'parents_code'];

            if ($courseId === 25 && in_array($field, $convertedLeadFields)) {
                $oldAdmissionBatchId = $field === 'admission_batch_id'
                    ? $convertedLead->admission_batch_id
                    : null;

                if ($field === 'phone') {
                    $convertedLead->phone = $value;
                    if ($request->has('code')) {
                        $convertedLead->code = $request->code;
                    }
                } else {
                    $convertedLead->$field = $value;
                }

                if ($field === 'admission_batch_id' && (string) $oldAdmissionBatchId !== (string) $value) {
                    $convertedLead->admission_batch_assigned_at = $value ? now() : null;
                }

                $convertedLead->save();
                $responseValue = $this->formatSupportResponseValue($field, $value, $convertedLead);
            } elseif ($courseId === 25 && in_array($field, $leadDetailFields)) {
                $leadDetail = LeadDetail::where('lead_id', $convertedLead->lead_id)->where('course_id', 25)->first();
                if (!$leadDetail) {
                    $leadDetail = new LeadDetail();
                    $leadDetail->lead_id = $convertedLead->lead_id;
                    $leadDetail->course_id = 25;
                }
                $leadDetail->$field = $value;
                $leadDetail->save();
                $responseValue = $this->formatSupportResponseValue($field, $value, $convertedLead);
            } else {
                // Handle all other fields in converted_student_support_details
                $supportDetails = $convertedLead->supportDetails;
                if (!$supportDetails) {
                    $supportDetails = new ConvertedStudentSupportDetail();
                    $supportDetails->converted_student_id = $id;
                }
                $supportDetails->$field = $value;
                $supportDetails->save();
                $responseValue = $this->formatResponseValue($field, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $responseValue
            ]);

        } catch (\Exception $e) {
            Log::error('SupportConvertedLeadController updateSupportDetails error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating the record'
            ], 500);
        }
    }

    /**
     * Format response value for JV support fields (batch, admission batch, phone, class time)
     */
    private function formatSupportResponseValue($field, $value, ConvertedLead $convertedLead)
    {
        if ($field === 'batch_id' && $value) {
            $batch = Batch::find($value);
            return $batch ? $batch->title : $value;
        }
        if ($field === 'admission_batch_id' && $value) {
            $ab = AdmissionBatch::find($value);
            return $ab ? $ab->title : $value;
        }
        if ($field === 'phone' && $convertedLead) {
            return \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone);
        }
        if ($field === 'class_time_id' && $value) {
            $ct = ClassTime::find($value);
            if ($ct) {
                return \Carbon\Carbon::parse($ct->from_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($ct->to_time)->format('h:i A');
            }
        }
        if (in_array($field, ['whatsapp_number', 'parents_number']) && $value) {
            $jv = $convertedLead->lead->juniorVloggerStudentDetails ?? null;
            $code = $jv ? ($field === 'whatsapp_number' ? ($jv->whatsapp_code ?? '') : ($jv->parents_code ?? '')) : '';
            return \App\Helpers\PhoneNumberHelper::display($code, $value);
        }
        return $value;
    }

    /**
     * Get validation rules for specific fields
     */
    private function getValidationRules($field, $courseId = null)
    {
        $rules = [
            'registration_status' => 'nullable|string|max:255',
            'technology_side' => 'nullable|string|max:255',
            'student_status' => 'nullable|string|max:255',
            'call_1' => 'nullable|string|max:255',
            'app' => 'nullable|in:Provided app,OTP Problem,Task Completed,Not Respond',
            'whatsapp_group' => 'nullable|string|max:255',
            'telegram_group' => 'nullable|string|max:255',
            'problems' => 'nullable|string|max:500',
            'support_notes' => 'nullable|string|max:1000',
            'support_status' => 'nullable|string|max:255',
            'support_priority' => 'nullable|string|max:255',
            'register_number' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'batch_id' => 'nullable|exists:batches,id',
            'admission_batch_id' => 'nullable|exists:admission_batches,id',
            'whatsapp_number' => 'nullable|string|max:50',
            'class_time_id' => 'nullable|exists:class_times,id',
            'parents_number' => 'nullable|string|max:50',
            'support_flag_id' => SupportFlagFieldSupport::validationRule(),
        ];

        return $rules[$field] ?? null;
    }

    /**
     * Submit feedback for a converted lead
     */
    public function submitFeedback(Request $request, $id)
    {
        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'feedback_type' => 'required|string|max:255',
                'feedback_content' => 'required|string|max:10000',
                'feedback_status' => 'nullable|string|max:255',
                'priority' => 'nullable|string|max:255',
                'follow_up_date' => 'nullable|date',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create feedback history record
            $feedback = SupportFeedbackHistory::create([
                'converted_student_id' => $id,
                'created_by' => AuthHelper::getCurrentUserId(),
                'feedback_type' => $request->feedback_type,
                'feedback_content' => $request->feedback_content,
                'feedback_status' => $request->feedback_status ?? 'pending',
                'priority' => $request->priority ?? 'medium',
                'follow_up_date' => $request->follow_up_date,
                'notes' => $request->notes,
            ]);

            // Update last_feedback timestamp in support details
            $supportDetails = $convertedLead->supportDetails;
            if (!$supportDetails) {
                $supportDetails = new ConvertedStudentSupportDetail();
                $supportDetails->converted_student_id = $id;
            }
            $supportDetails->last_feedback = now();
            $supportDetails->save();

            // Send email notification to CAO (From CRM)
            try {
                $subject = 'Support Feedback - ' . ($convertedLead->name ?? 'Student') . ' (#' . $convertedLead->id . ')';
                $body = view('emails.support-feedback-cao', [
                    'convertedLead' => $convertedLead,
                    'feedback' => $feedback
                ])->render();
                
                // Send email only to cao@natdemy.com with "From CRM" as sender name
                if (function_exists('send_email')) {
                    send_email('cao@natdemy.com', 'CAO', $subject, $body, [], 'CRM');
                }
            } catch (\Exception $mailEx) {
                Log::error('Support feedback CAO mail failed: ' . $mailEx->getMessage(), [
                    'lead_id' => $convertedLead->id,
                    'feedback_id' => $feedback->id ?? null
                ]);
                // Do not block user flow on email failure
            }

            // Send aligned feedback mail to student if email available
            if (!empty($convertedLead->email)) {
                try {
                    $studentSubject = 'Your Support Feedback Update - ' . ($convertedLead->course?->title ?? 'Course');
                    $studentBody = view('emails.support-feedback-student', [
                        'convertedLead' => $convertedLead,
                        'feedback' => $feedback
                    ])->render();

                    if (function_exists('send_email')) {
                        // Send from support team (no CRM label for student-facing mail)
                        send_email($convertedLead->email, $convertedLead->name ?? 'Student', $studentSubject, $studentBody, [], 'Support Team');
                    }
                } catch (\Exception $studentMailEx) {
                    Log::error('Support feedback student mail failed: ' . $studentMailEx->getMessage(), [
                        'lead_id' => $convertedLead->id,
                        'feedback_id' => $feedback->id ?? null
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully',
                'feedback' => $feedback->load('createdBy')
            ]);

        } catch (\Exception $e) {
            Log::error('SupportConvertedLeadController submitFeedback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while submitting feedback'
            ], 500);
        }
    }

    /**
     * Toggle support verification (Support team only)
     */
    public function toggleSupportVerification(Request $request, $id)
    {
        if (!(RoleHelper::is_support_team() || RoleHelper::is_admin_or_super_admin())) {
            return response()->json(['success' => false, 'message' => 'Access denied. Support team or Admin only.'], 403);
        }

        $convertedLead = ConvertedLead::findOrFail($id);
        $currently = (bool) $convertedLead->is_support_verified;
        if ($currently) {
            $convertedLead->is_support_verified = 0;
            $convertedLead->support_verified_by = null;
            $convertedLead->support_verified_at = null;
        } else {
            $convertedLead->is_support_verified = 1;
            $convertedLead->support_verified_by = AuthHelper::getCurrentUserId();
            $convertedLead->support_verified_at = now();
        }
        $convertedLead->save();

        return response()->json([
            'success' => true,
            'message' => $currently ? 'Support verification removed.' : 'Support verification completed.',
            'is_support_verified' => (bool) $convertedLead->is_support_verified,
        ]);
    }


    /**
     * Display a listing of Hotel Management converted leads for support
     */
    public function hotelManagementIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 8, 'Hotel Management Converted Support List', 'admin.converted-leads.support-hotel-management-index');
    }

    /**
     * Display a listing of Grameen Mukt Vidhyalayi Shiksha Sansthan converted leads for support
     */
    public function gmvssIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 16, 'Grameen Mukt Vidhyalayi Shiksha Sansthan Converted Support List', 'admin.converted-leads.support-gmvss-index');
    }

    /**
     * Display a listing of AI with Python converted leads for support
     */
    public function aiPythonIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 10, 'AI with Python Converted Support List', 'admin.converted-leads.support-ai-python-index');
    }

    /**
     * Display a listing of AI Integrated Digital Marketing converted leads for support
     */
    public function digitalMarketingIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 11, 'AI Integrated Digital Marketing Converted Support List', 'admin.converted-leads.support-digital-marketing-index');
    }

    /**
     * Display a listing of AI Automation converted leads for support
     */
    public function aiAutomationIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 12, 'Diploma in Data Science Converted Support List', 'admin.converted-leads.support-diploma-in-data-science-index');
    }

    /**
     * Display a listing of Web Development converted leads for support
     */
    public function webDevelopmentIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 13, 'Web Development & Designing Converted Support List', 'admin.converted-leads.support-web-development-index');
    }

    /**
     * Display a listing of Vibe Coding converted leads for support
     */
    public function vibeCodingIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 14, 'Vibe Coding Converted Support List', 'admin.converted-leads.support-vibe-coding-index');
    }

    /**
     * Display a listing of Diploma in Graphic Designing converted leads for support
     */
    public function graphicDesigningIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 15, 'Diploma in Graphic Designing Converted Support List', 'admin.converted-leads.support-graphic-designing-index');
    }

    public function aiIntegratedVideoEditingIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 30, 'AI-Integrated Video Editing Converted Support List', 'admin.converted-leads.support-ai-integrated-video-editing-index');
    }

    public function aiIntegratedVideographyIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 31, 'AI-Integrated Videography Converted Support List', 'admin.converted-leads.support-ai-integrated-videography-index');
    }

    public function aiIntegratedPhotographyIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 32, 'AI-Integrated Photography Converted Support List', 'admin.converted-leads.support-ai-integrated-photography-index');
    }

    /**
     * Display a listing of Diploma in Machine Learning converted leads for support
     */
    public function machineLearningIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 20, 'Diploma in Machine Learning Converted Support List', 'admin.converted-leads.support-machine-learning-index');
    }

    /**
     * Display Flutter converted leads for support
     */
    public function flutterIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 21, 'Flutter Converted Support List', 'admin.converted-leads.support-flutter-index');
    }

    /**
     * Display Eduthanzeel converted leads for support
     */
    public function eduthanzeelIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 6, 'Eduthanzeel Converted Support List', 'admin.converted-leads.support-eduthanzeel-index');
    }

    /**
     * Display E-School converted leads for support
     */
    public function eSchoolIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 5, 'E-School Converted Support List', 'admin.converted-leads.support-e-school-index');
    }

    /**
     * Display CreateX AI – Course Support list (course_id = 25)
     */
    public function juniorVloggerIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 25, 'CreateX AI – Course Support List', 'admin.converted-leads.support-junior-vlogger-index');
    }

    public function roboVibeIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 33, 'Robo Vibe – Course Support List', 'admin.converted-leads.support-robo-vibe-index');
    }

    public function promptEngineeringIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 34, 'Prompt Engineering – Course Support List', 'admin.converted-leads.support-prompt-engineering-index');
    }

    /**
     * Get course support index
     */
    private function getCourseSupportIndex(Request $request, $courseId, $pageTitle, $viewName)
    {
        $query = ConvertedLead::with([
            'lead',
            'lead.team',
            'lead.team.detail',
            'lead.juniorVloggerStudentDetails.classTime',
            'leadDetail',
            'course',
            'academicAssistant',
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'supportDetails',
            'supportFlag',
            'subject',
            'batch',
            'admissionBatch'
        ])->where('course_id', $courseId)
          ->where('is_academic_verified', 1);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('register_number', 'like', "%{$search}%")
                  ->orWhereHas('studentDetails', function($subQ) use ($search) {
                      $subQ->where('application_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('registration_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('registration_status', $request->registration_status);
            });
        }

        if ($request->filled('student_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('student_status', $request->student_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        SupportFlagFieldSupport::applyListingFilter($query, $request);

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', $courseId)->orderBy('title')->get();
        $subjects = Subject::where('course_id', $courseId)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $supportFlags = SupportFlagFieldSupport::forFilterSelect();

        return view($viewName, compact(
            'convertedLeads', 
            'batches', 
            'subjects', 
            'country_codes',
            'pageTitle',
            'supportFlags'
        ));
    }

    /**
     * Show support converted lead details for Ajax list
     */
    public function showAjax($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail.sslcCertificates.verifiedBy',
            'leadDetail.sslcVerifiedBy',
            'leadDetail.plustwoVerifiedBy',
            'leadDetail.ugVerifiedBy',
            'leadDetail.passportPhotoVerifiedBy',
            'leadDetail.adharFrontVerifiedBy',
            'leadDetail.adharBackVerifiedBy',
            'leadDetail.signatureVerifiedBy',
            'leadDetail.birthCertificateVerifiedBy',
            'leadDetail.otherDocumentVerifiedBy',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails',
            'supportDetails',
            'supportFeedbackHistory.createdBy',
            'subject',
            'batch',
            'admissionBatch.mentor'
        ])->findOrFail($id);

        $backUrl = route('admin.support-ajax-converted-leads.index');

        return view('admin.converted-leads.support-show', compact('convertedLead', 'backUrl'));
    }

    /**
     * Format response value for display
     */
    private function formatResponseValue($field, $value)
    {
        return $value;
    }
}