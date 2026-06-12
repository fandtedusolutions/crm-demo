<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentMentorDetail;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UGPGMentorConvertedLeadController extends Controller
{
    /**
     * Display a listing of UG/PG converted leads for mentoring
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
            'mentorDetails',
            'subject',
            'batch',
            'admissionBatch'
        ])->where('course_id', 9) // UG/PG course
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
                
                // Check if current course (9) is in HOD's assigned courses
                if (!empty($hodCourseIds) && in_array(9, $hodCourseIds)) {
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

        if ($request->filled('student_status')) {
            $query->whereHas('mentorDetails', function($q) use ($request) {
                $q->where('student_status', $request->student_status);
            });
        }

        if ($request->filled('document_verification_status')) {
            $query->whereHas('mentorDetails', function($q) use ($request) {
                $q->where('document_verification_status', $request->document_verification_status);
            });
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
        $universities = \App\Models\University::where('is_active', 1)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $flags = \App\Support\MentorFlagFieldSupport::forFilterSelect();

        return view('admin.converted-leads.mentor-ugpg-index', compact(
            'convertedLeads', 
            'universities', 
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
            $responseValue = $this->formatResponseValue($field, $value);

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $responseValue
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating UG/PG mentor details: ' . $e->getMessage());
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
            'document_verification_status' => 'nullable|in:Not Verified,Verified',
            'certificate_distribution_mode' => 'nullable|in:In Person,Courier',
            'student_status' => 'nullable|in:Active,Completed,Discontinued',
            'online_registration_date' => 'nullable|date',
            'admission_form_issued_date' => 'nullable|date',
            'admission_form_returned_date' => 'nullable|date',
            'verification_completed_date' => 'nullable|date',
            'id_card_issued_date' => 'nullable|date',
            'first_year_result_declaration_date' => 'nullable|date',
            'second_year_result_declaration_date' => 'nullable|date',
            'third_year_result_declaration_date' => 'nullable|date',
            'all_online_result_publication_date' => 'nullable|date',
            'certificate_issued_date' => 'nullable|date',
            'courier_tracking_number' => 'nullable|string|max:255',
            'remarks_internal_notes' => 'nullable|string|max:1000',
        ];

        return $rules[$field] ?? null;
    }

    /**
     * Format response value for display
     */
    private function formatResponseValue($field, $value)
    {
        // For date fields, format them
        if (in_array($field, [
            'online_registration_date',
            'admission_form_issued_date',
            'admission_form_returned_date',
            'verification_completed_date',
            'id_card_issued_date',
            'first_year_result_declaration_date',
            'second_year_result_declaration_date',
            'third_year_result_declaration_date',
            'all_online_result_publication_date',
            'certificate_issued_date'
        ]) && $value) {
            try {
                return \Carbon\Carbon::parse($value)->format('d-m-Y');
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }
}
