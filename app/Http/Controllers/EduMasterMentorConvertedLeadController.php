<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentMentorDetail;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Models\RegistrationLink;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EduMasterMentorConvertedLeadController extends Controller
{
    /**
     * Display a listing of EduMaster converted leads for mentoring
     */
    public function index(Request $request)
    {
        $query = ConvertedLead::with([
            
            'flag','lead', 
            'leadDetail.university',
            'leadDetail.universityCourse',
            'course', 
            'academicAssistant', 
            'createdBy',
            'cancelledBy',
            'studentDetails',
            'mentorDetails.sslcRegistrationLink',
            'mentorDetails.plustwoRegistrationLink',
            'subject',
            'batch',
            'admissionBatch'
        ])->where('course_id', 23) // EduMaster course
          ->where('is_support_verified', 1);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_admin_or_super_admin()) {
                // Admins and super admins can see all support verified leads
                // No additional filtering needed
            } elseif (RoleHelper::is_hod()) {
                // HOD: Only see leads for courses where they are assigned as HOD
                $hodCourseIds = \App\Models\Course::where('hod_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();
                
                // Check if current course (23) is in HOD's assigned courses
                if (!empty($hodCourseIds) && in_array(23, $hodCourseIds)) {
                    // HOD is assigned to this course, show data
                } else {
                    // HOD is not assigned to this course, return empty results
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_mentor_head()) {
                // Mentor Head: Can see all support verified leads
                // No additional filtering needed
            } elseif (RoleHelper::is_mentor()) {
                // Regular Mentor: Filter by admission_batch_id where mentor_id matches
                $mentorAdmissionBatchIds = AdmissionBatch::where('mentor_id', AuthHelper::getCurrentUserId())
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($mentorAdmissionBatchIds)) {
                    $query->whereIn('admission_batch_id', $mentorAdmissionBatchIds);
                } else {
                    // If mentor has no admission batches, return empty result
                    $query->whereRaw('1 = 0');
                }
            } elseif (RoleHelper::is_team_lead()) {
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
            } elseif (RoleHelper::is_senior_manager()) {
                // Senior Manager: Filter by their own leads or team leads if they have a team
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
            } elseif (RoleHelper::is_general_manager()) {
                // General Manager: Can see all leads
                // No additional filtering needed
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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%")
                    ->orWhereHas('leadDetail', function($q) use ($search) {
                        $q->where('whatsapp_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        \App\Support\MentorFlagFieldSupport::applyListingFilter($query, $request);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get filter data
        $batches = Batch::where('course_id', 23)->orderBy('is_active', 'desc')->orderBy('title')->get();
        $admissionBatches = AdmissionBatch::whereHas('batch', function($q) {
            $q->where('course_id', 23);
        })->orderBy('is_active', 'desc')->orderBy('title')->get();
        $registrationLinks = RegistrationLink::orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.mentor-edumaster-index', compact(
            'convertedLeads', 
            'batches',
            'admissionBatches',
            'registrationLinks',
            'country_codes',
            'flags'
        ));
    }

    /**
     * Update mentor details inline
     */
    public function updateMentorDetails(Request $request, $id)
    {
        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            $field = $request->field;
            $value = $request->value;

            if ($denied = \App\Support\MentorFlagFieldSupport::mentorLeadScopeDeniedJsonResponse($convertedLead)) {
                return $denied;
            }

            // Validate the field and value
            $validationRules = $this->getValidationRules($field);
            if ($validationRules) {
                $validator = Validator::make([$field => $value], [$field => $validationRules]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()->first($field)
                    ], 422);
                }
            }

            if ($field === 'flag_id') {
                return \App\Support\MentorFlagFieldSupport::flagUpdateJsonResponse($convertedLead, $value);
            }

            // Handle all fields - update in converted_student_mentor_details table
            $mentorDetails = $convertedLead->mentorDetails;
            if (!$mentorDetails) {
                $mentorDetails = new ConvertedStudentMentorDetail();
                $mentorDetails->converted_student_id = $id;
            }
            $mentorDetails->$field = $value;
            $mentorDetails->save();

            // Format the response value
            $responseValue = $this->formatResponseValue($field, $value, $mentorDetails);

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $responseValue
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating EduMaster mentor details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get validation rules for specific fields
     */
    private function getValidationRules($field)
    {
        $rules = [
            'sslc_enrollment_number' => 'nullable|string|max:255',
            'sslc_registration_link_id' => 'nullable|exists:registration_links,id',
            'sslc_online_result_publication_date' => 'nullable|date',
            'sslc_certificate_publication_date' => 'nullable|date',
            'sslc_certificate_issued_date' => 'nullable|date',
            'sslc_certificate_distribution_date' => 'nullable|date',
            'sslc_courier_tracking_number' => 'nullable|string|max:255',
            'sslc_remarks' => 'nullable|string|max:1000',
            'plustwo_subject_no' => 'nullable|string|max:255',
            'plustwo_enrollment_number' => 'nullable|string|max:255',
            'plustwo_registration_link_id' => 'nullable|exists:registration_links,id',
            'plustwo_online_result_publication_date' => 'nullable|date',
            'plustwo_certificate_publication_date' => 'nullable|date',
            'plustwo_certificate_issued_date' => 'nullable|date',
            'plustwo_certificate_distribution_date' => 'nullable|date',
            'plustwo_courier_tracking_number' => 'nullable|string|max:255',
            'plustwo_remarks' => 'nullable|string|max:1000',
            'degree_board_university' => 'nullable|string|max:255',
            'degree_course_type' => 'nullable|string|max:255',
            'degree_course_name' => 'nullable|string|max:255',
            'degree_back_year' => 'nullable|string|max:255',
            'degree_registration_start_date' => 'nullable|date',
            'degree_registration_form_summary_distribution_date' => 'nullable|date',
            'degree_registration_form_summary_submission_date' => 'nullable|date',
            'degree_id_card_issued_date' => 'nullable|date',
            'degree_first_year_result_date' => 'nullable|date',
            'degree_second_year_result_date' => 'nullable|date',
            'degree_third_year_result_date' => 'nullable|date',
            'degree_online_result_publication_date' => 'nullable|date',
            'degree_certificate_publication_date' => 'nullable|date',
            'degree_certificate_issued_date' => 'nullable|date',
            'degree_certificate_distribution_date' => 'nullable|date',
            'degree_courier_tracking_number' => 'nullable|string|max:255',
            'degree_remarks' => 'nullable|string|max:1000',
        ];

        return $rules[$field] ?? null;
    }

    /**
     * Format response value for display
     */
    private function formatResponseValue($field, $value, $mentorDetails)
    {
        // For date fields, format them
        $dateFields = [
            'sslc_online_result_publication_date',
            'sslc_certificate_publication_date',
            'sslc_certificate_issued_date',
            'sslc_certificate_distribution_date',
            'plustwo_online_result_publication_date',
            'plustwo_certificate_publication_date',
            'plustwo_certificate_issued_date',
            'plustwo_certificate_distribution_date',
            'degree_registration_start_date',
            'degree_registration_form_summary_distribution_date',
            'degree_registration_form_summary_submission_date',
            'degree_id_card_issued_date',
            'degree_first_year_result_date',
            'degree_second_year_result_date',
            'degree_third_year_result_date',
            'degree_online_result_publication_date',
            'degree_certificate_publication_date',
            'degree_certificate_issued_date',
            'degree_certificate_distribution_date',
        ];

        if (in_array($field, $dateFields) && $value) {
            try {
                return \Carbon\Carbon::parse($value)->format('d-m-Y');
            } catch (\Exception $e) {
                return $value;
            }
        }

        // For registration link fields, return the link title
        if ($field === 'sslc_registration_link_id' && $value) {
            $link = RegistrationLink::find($value);
            return $link ? $link->title : $value;
        }

        if ($field === 'plustwo_registration_link_id' && $value) {
            $link = RegistrationLink::find($value);
            return $link ? $link->title : $value;
        }

        return $value;
    }
}
