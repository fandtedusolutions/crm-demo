<?php

namespace App\Http\Controllers;

use App\Models\ConvertedLead;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SupportAjaxController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)->get();
        $batches = Batch::where('is_active', true)->get();

        return view('admin.converted-leads.support-ajax-index', compact('courses', 'batches'));
    }

    public function getData(Request $request)
    {
        // 1. Base Query
        $query = ConvertedLead::select('converted_leads.*')
        ->with([
            'lead',
            'leadDetail',
            'supportDetails',
            'admissionBatch',
            'studentDetails'
        ])
        ->withCount('supportFeedbackHistory')
        ->where('converted_leads.is_academic_verified', 1);

        // 2. Role-based Filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function ($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                }
                else {
                    $query->whereHas('lead', function ($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            }
            elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function ($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // 3. Apply Filters
        if ($request->filled('course_id')) {
            $query->forCourseListing($request->course_id);
        }
        if ($request->filled('batch_id')) {
            $query->where('converted_leads.batch_id', $request->batch_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('converted_leads.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('converted_leads.created_at', '<=', $request->date_to);
        }

        // Search Filter
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('converted_leads.name', 'like', "%{$search}%")
                    ->orWhere('converted_leads.phone', 'like', "%{$search}%")
                    ->orWhereHas('studentDetails', function ($subQ) use ($search) {
                    $subQ->where('application_number', 'like', "%{$search}%")
                        ->orWhere('register_number', 'like', "%{$search}%");
                }
                );
            });
        }

        // 4. Sorting - Default sort by last_feedback (most recent feedback first)
        // Join with support details to enable sorting by last_feedback
        $query->leftJoin('converted_student_support_details', 'converted_leads.id', '=', 'converted_student_support_details.converted_student_id');
        
        $columns = [
            0 => 'converted_leads.created_at',
            1 => 'converted_leads.name',
            2 => 'converted_leads.is_b2b',
            3 => 'converted_leads.phone',
            4 => 'whatsapp', // mapped manually
            5 => 'batch_id', // approximation
            6 => 'support_feedback_history_count', // feedback count
            7 => 'converted_leads.id'
        ];

        $orderColumn = 'converted_student_support_details.last_feedback';
        $orderDir = 'desc';

        if ($request->has('order')) {
            $order = $request->input('order.0');
            $columnIdx = $order['column'];
            $dir = $order['dir'];
            $columnName = $columns[$columnIdx] ?? 'converted_leads.created_at';

            // Only sort by direct columns on the main table or simple relations
            if (in_array($columnName, ['converted_leads.created_at', 'converted_leads.name', 'converted_leads.is_b2b', 'converted_leads.phone'])) {
                $orderColumn = $columnName;
                $orderDir = $dir;
            }
        }

        // Primary sort by last_feedback (nulls last), secondary sort by selected column
        $query->orderByRaw('converted_student_support_details.last_feedback IS NULL')
              ->orderBy($orderColumn, $orderDir);

        // 5. Pagination
        $filteredRecords = $query->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $length = $length == -1 ? $filteredRecords : $length;

        $data = $query->skip($start)->take($length)->get();

        // 6. Format Data
        $formattedData = [];
        foreach ($data as $index => $row) {
            $slNo = $start + $index + 1;
            $convertedDate = $row->created_at->format('d-m-Y');
            if ($row->studentDetails && $row->studentDetails->converted_date) {
                try {
                    $convertedDate = Carbon::parse($row->studentDetails->converted_date)->format('d-m-Y');
                }
                catch (\Exception $e) {
                // Keep created_at if parse fails
                }
            }

            $whatsapp = 'N/A';
            if ($row->leadDetail && $row->leadDetail->whatsapp_number) {
                $whatsapp = \App\Helpers\PhoneNumberHelper::display($row->leadDetail->whatsapp_code, $row->leadDetail->whatsapp_number);
            }

            $phone = \App\Helpers\PhoneNumberHelper::display($row->code, $row->phone);

            $action = '
                <div class="d-flex gap-2">
                    <a href="' . route('admin.support-ajax-converted-leads.details', $row->id) . '" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                        <i class="ti ti-eye"></i>
                    </a>
                </div>
            ';

            $admissionBatchTitle = $row->admissionBatch ? $row->admissionBatch->title : 'N/A';
            
            $feedbackCount = $row->support_feedback_history_count ?? 0;

            $formattedData[] = [
                $slNo, // Sl No
                $convertedDate,
                $row->name,
                $row->is_b2b == 1 ? 'B2B' : 'In House',
                $phone,
                $whatsapp,
                $admissionBatchTitle,
                $feedbackCount,
                $action
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => ConvertedLead::where('is_academic_verified', 1)->count(),
            'recordsFiltered' => $filteredRecords,
            'data' => $formattedData
        ]);
    }
}
