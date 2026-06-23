<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;
use App\Models\Department;
use App\Models\OnlineTeachingFaculty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OnlineTeachingFacultyController extends Controller
{
    private function canAccessModule(): bool
    {
        // Keep it aligned with PermissionHelper sidebar gating:
        // Admin/Super Admin always, plus Admission Counsellor, HOD.
        return RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_admission_counsellor()
            || RoleHelper::is_hod()
            || RoleHelper::is_academic_assistant();
    }

    public function index()
    {
        if (!$this->canAccessModule()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        // DataTables columns (keep stable keys; values are rendered in getData()).
        $columns = [
            ['data' => 'index', 'name' => 'id', 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'id', 'orderable' => false, 'searchable' => false],
            ['data' => 'full_name', 'name' => 'full_name'],
            ['data' => 'primary_mobile_number', 'name' => 'primary_mobile_number'],
            ['data' => 'official_email_address', 'name' => 'official_email_address'],
            ['data' => 'department_name', 'name' => 'department_name'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'date_of_birth', 'name' => 'date_of_birth'],
            ['data' => 'teaching_experience', 'name' => 'teaching_experience'],

            // C fields (not on add form; inline editable)
            ['data' => 'faculty_id', 'name' => 'faculty_id'],
            ['data' => 'class_level', 'name' => 'class_level'],
            ['data' => 'employment_type', 'name' => 'employment_type'],
            ['data' => 'work_schedule_mode', 'name' => 'work_schedule_mode'],
            ['data' => 'candidate_status', 'name' => 'candidate_status'],
            ['data' => 'preferred_teaching_platform', 'name' => 'preferred_teaching_platform'],
            ['data' => 'technical_readiness_confirmation', 'name' => 'technical_readiness_confirmation'],
            ['data' => 'demo_class_date', 'name' => 'demo_class_date'],
            ['data' => 'demo_conducted_by', 'name' => 'demo_conducted_by'],
            ['data' => 'offer_letter_issued_date', 'name' => 'offer_letter_issued_date'],
            ['data' => 'joining_date', 'name' => 'joining_date'],
            ['data' => 'remarks', 'name' => 'remarks', 'orderable' => false],
            ['data' => 'offer_letter_upload', 'name' => 'offer_letter_upload', 'orderable' => false, 'searchable' => false],

            ['data' => 'created_at', 'name' => 'created_at'],
        ];

        return view('admin.online-teaching-faculties.index', compact('columns'));
    }

    public function ajax_add()
    {
        if (!$this->canAccessModule()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Load active departments
        $departments = Department::where('status', true)->orderBy('title')->get();
        return view('admin.online-teaching-faculties.add', compact('departments'));
    }

    public function submit(Request $request)
    {
        if (!$this->canAccessModule()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before_or_equal:today',
            'gender' => 'nullable|string|in:Male,Female',
            'primary_mobile_number' => 'required|string|max:30',
            'alternate_contact_number' => 'nullable|string|max:30',
            'official_email_address' => 'nullable|email|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'address_house_name_flat_no' => 'nullable|string|max:255',
            'address_area_locality' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:255',
            'address_district' => 'nullable|string|max:255',
            'address_state' => 'nullable|string|max:255',
            'address_pin_code' => 'nullable|string|max:20',
            'highest_educational_qualification' => 'nullable|string|max:255',
            'additional_certifications' => 'nullable|string|max:2000',
            'teaching_experience' => 'nullable|string|in:Yes,No',
            'department_id' => 'nullable|exists:departments,id',

            // Documents (optional)
            'document_resume_cv' => 'nullable|file|max:10240',
            'document_10th_certificate' => 'nullable|file|max:10240',
            'document_educational_qualification_certificates' => 'nullable|file|max:10240',
            'document_aadhaar_front' => 'nullable|file|max:10240',
            'document_aadhaar_back' => 'nullable|file|max:10240',
            'document_other_1' => 'nullable|file|max:10240',
            'document_other_2' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $data = $request->only([
            'full_name',
            'date_of_birth',
            'gender',
            'primary_mobile_number',
            'alternate_contact_number',
            'official_email_address',
            'father_name',
            'mother_name',
            'address_house_name_flat_no',
            'address_area_locality',
            'address_city',
            'address_district',
            'address_state',
            'address_pin_code',
            'highest_educational_qualification',
            'additional_certifications',
            'department_id',
        ]);

        // Normalize Yes/No to boolean
        $teachingExperience = $request->input('teaching_experience');
        if ($teachingExperience === 'Yes') {
            $data['teaching_experience'] = true;
        }
        elseif ($teachingExperience === 'No') {
            $data['teaching_experience'] = false;
        }
        else {
            $data['teaching_experience'] = null;
        }

        $faculty = OnlineTeachingFaculty::create($data);

        // Store documents
        $docFields = [
            'document_resume_cv',
            'document_10th_certificate',
            'document_educational_qualification_certificates',
            'document_aadhaar_front',
            'document_aadhaar_back',
            'document_other_1',
            'document_other_2',
        ];

        foreach ($docFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store("online-teaching-faculties/{$faculty->id}", 'public');
                $faculty->{ $field} = $path;
            }
        }

        $faculty->save();

        return redirect()->route('admin.online-teaching-faculties.index')->with('message_success', 'Online Teaching Faculty created successfully.');
    }

    public function show($id)
    {
        if (!$this->canAccessModule()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $faculty = OnlineTeachingFaculty::findOrFail($id);
        return view('admin.online-teaching-faculties.show', compact('faculty'));
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('admin.online-teaching-faculties.index')->with('message_danger', 'Access denied.');
        }

        $faculty = OnlineTeachingFaculty::findOrFail($id);

        $faculty->deleted_by = AuthHelper::getCurrentUserId();
        $faculty->save();

        $faculty->delete();

        return redirect()->route('admin.online-teaching-faculties.index')->with('message_success', 'Online Teaching Faculty deleted successfully.');
    }

    public function getData(Request $request): JsonResponse
    {
        if (!$this->canAccessModule()) {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        $query = OnlineTeachingFaculty::query();

        // If user is HOD, filter by their department
        if (RoleHelper::is_hod()) {
            $user = RoleHelper::is_logged_in() ? \App\Helpers\AuthHelper::getCurrentUser() : null;
            if ($user && $user->department_id) {
                $query->where('department_id', $user->department_id);
            }
        }

        // recordsTotal = total count with same role filter (no search)
        $totalRecords = (clone $query)->count();

        // DataTables global search
        if ($request->filled('search') && is_array($request->search) && !empty($request->search['value'])) {
            $sv = $request->search['value'];
            $query->where(function ($q) use ($sv) {
                $q->where('full_name', 'like', "%{$sv}%")
                    ->orWhere('primary_mobile_number', 'like', "%{$sv}%")
                    ->orWhere('official_email_address', 'like', "%{$sv}%")
                    ->orWhere('faculty_id', 'like', "%{$sv}%");
            });
        }

        $filteredCount = $query->count();

        // Column mapping for ordering (must match the index blade columns order)
        $columns = [
            0 => 'id', // index
            1 => 'id', // actions
            2 => 'full_name',
            3 => 'primary_mobile_number',
            4 => 'official_email_address',
            5 => 'department_name',
            6 => 'gender',
            7 => 'date_of_birth',
            8 => 'teaching_experience',
            9 => 'faculty_id',
            10 => 'class_level',
            11 => 'employment_type',
            12 => 'work_schedule_mode',
            13 => 'candidate_status',
            14 => 'preferred_teaching_platform',
            15 => 'technical_readiness_confirmation',
            16 => 'demo_class_date',
            17 => 'demo_conducted_by',
            18 => 'offer_letter_issued_date',
            19 => 'joining_date',
            20 => 'remarks',
            21 => 'offer_letter_upload',
            22 => 'created_at',
        ];

        $order = $request->get('order', []);
        $orderColumn = isset($order[0]['column']) ? (int)$order[0]['column'] : 22;
        $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';
        $orderColumnName = $columns[$orderColumn] ?? 'created_at';
        $query->orderBy($orderColumnName, $orderDir);

        $start = (int)$request->get('start', 0);
        $length = (int)$request->get('length', 25);
        if ($length < 0) {
            $length = $filteredCount;
        }
        $rows = $query->skip($start)->take($length)->get();

        // Load active departments for inline editing
        $departments = Department::where('status', true)->orderBy('title')->get();
        $departmentOptions = ['' => 'N/A'];
        foreach ($departments as $dept) {
            $departmentOptions[$dept->id] = $dept->title;
        }

        $data = [];
        foreach ($rows as $idx => $faculty) {
            /** @var \App\Models\OnlineTeachingFaculty $faculty */
            $data[] = [
                'DT_RowId' => 'online_teaching_faculty_' . $faculty->id,
                'index' => $start + $idx + 1,
                'actions' => view('admin.online-teaching-faculties.partials.actions', ['faculty' => $faculty])->render(),
                'full_name' => $this->renderInlineText($faculty, 'full_name'),
                'primary_mobile_number' => $this->renderInlineText($faculty, 'primary_mobile_number'),
                'official_email_address' => $this->renderInlineText($faculty, 'official_email_address'),
                'department_name' => $this->renderInlineSelect($faculty, 'department_id', $departmentOptions),
                'gender' => $this->renderInlineSelect($faculty, 'gender', [
                    '' => 'N/A',
                    'Male' => 'Male',
                    'Female' => 'Female',
                ]),
                'date_of_birth' => $this->renderInlineDate($faculty, 'date_of_birth'),
                'teaching_experience' => $this->renderInlineSelect($faculty, 'teaching_experience', [
                    '' => 'N/A',
                    '1' => 'Yes',
                    '0' => 'No',
                ], $faculty->teaching_experience === null ? '' : ($faculty->teaching_experience ? '1' : '0')),

                'faculty_id' => $this->renderInlineText($faculty, 'faculty_id'),
                'class_level' => $this->renderInlineSelect($faculty, 'class_level', [
                    '' => 'N/A',
                    'Basic' => 'Basic',
                    'LP (Lower Primary)' => 'LP (Lower Primary)',
                    'UP (Upper Primary)' => 'UP (Upper Primary)',
                    'Secondary' => 'Secondary',
                    'Higher Secondary' => 'Higher Secondary',
                    'IT' => 'IT',
                ]),
                'employment_type' => $this->renderInlineSelect($faculty, 'employment_type', [
                    '' => 'N/A',
                    'Full-Time' => 'Full-Time',
                    'Part-Time' => 'Part-Time',
                ]),
                'work_schedule_mode' => $this->renderInlineSelect($faculty, 'work_schedule_mode', [
                    '' => 'N/A',
                    'Day' => 'Day',
                    'Night' => 'Night',
                    'Full-Time' => 'Full-Time',
                ]),
                'candidate_status' => $this->renderInlineSelect($faculty, 'candidate_status', [
                    '' => 'N/A',
                    'New' => 'New',
                    'Shortlisted' => 'Shortlisted',
                    'Demo Completed' => 'Demo Completed',
                    'Selected' => 'Selected',
                    'Rejected' => 'Rejected',
                ]),
                'preferred_teaching_platform' => $this->renderInlineSelect($faculty, 'preferred_teaching_platform', [
                    '' => 'N/A',
                    'Google Meet' => 'Google Meet',
                    'Zoom' => 'Zoom',
                    'Both' => 'Both',
                ]),
                'technical_readiness_confirmation' => $this->renderInlineSelect($faculty, 'technical_readiness_confirmation', [
                    '' => 'N/A',
                    'Yes' => 'Yes',
                    'No' => 'No',
                ]),
                'demo_class_date' => $this->renderInlineDate($faculty, 'demo_class_date'),
                'demo_conducted_by' => $this->renderInlineText($faculty, 'demo_conducted_by'),
                'offer_letter_issued_date' => $this->renderInlineDate($faculty, 'offer_letter_issued_date'),
                'joining_date' => $this->renderInlineDate($faculty, 'joining_date'),
                'remarks' => $this->renderInlineTextarea($faculty, 'remarks'),
                'offer_letter_upload' => $this->renderInlineFileUpload($faculty, 'offer_letter_upload'),

                'created_at' => optional($faculty->created_at)->format('Y-m-d H:i'),
            ];
        }

        return response()->json([
            'draw' => (int)$request->get('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }

    public function inlineUpdate(Request $request, $id): JsonResponse
    {
        if (!$this->canAccessModule()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $faculty = OnlineTeachingFaculty::findOrFail($id);
        $field = (string)$request->input('field', '');

        $allowed = [
            // A fields
            'full_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before_or_equal:today',
            'gender' => 'nullable|string|in:Male,Female',
            'primary_mobile_number' => 'required|string|max:30',
            'alternate_contact_number' => 'nullable|string|max:30',
            'official_email_address' => 'nullable|email|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'address_house_name_flat_no' => 'nullable|string|max:255',
            'address_area_locality' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:255',
            'address_district' => 'nullable|string|max:255',
            'address_state' => 'nullable|string|max:255',
            'address_pin_code' => 'nullable|string|max:20',
            'highest_educational_qualification' => 'nullable|string|max:255',
            'additional_certifications' => 'nullable|string|max:2000',
            'teaching_experience' => 'nullable|string|in:1,0',
            'department_name' => 'nullable|string|in:E-School,EduThanzeel,Diploma in Graphic Designing,AI Integrated Digital Marketing,Data Science,Machine Learning',
            'department_id' => 'nullable|exists:departments,id',

            // C fields
            'faculty_id' => 'nullable|string|max:255',
            'class_level' => 'nullable|string|in:Basic,LP (Lower Primary),UP (Upper Primary),Secondary,Higher Secondary,IT',
            'employment_type' => 'nullable|string|in:Full-Time,Part-Time',
            'work_schedule_mode' => 'nullable|string|in:Day,Night,Full-Time',
            'candidate_status' => 'nullable|string|in:New,Shortlisted,Demo Completed,Selected,Rejected',
            'preferred_teaching_platform' => 'nullable|string|in:Google Meet,Zoom,Both',
            'technical_readiness_confirmation' => 'nullable|string|in:Yes,No',
            'demo_class_date' => 'nullable|date',
            'demo_conducted_by' => 'nullable|string|max:255',
            'offer_letter_issued_date' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:5000',
        ];

        if (!array_key_exists($field, $allowed)) {
            return response()->json(['error' => 'Field not allowed.'], 422);
        }

        $payload = ['value' => $request->input('value')];
        $validator = Validator::make($payload, ['value' => $allowed[$field]]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $value = $payload['value'];

        if ($field === 'teaching_experience') {
            if ($value === '' || $value === null) {
                $faculty->teaching_experience = null;
            }
            else {
                $faculty->teaching_experience = ($value === '1');
            }
        }
        else {
            $faculty->{ $field} = $value === '' ? null : $value;
        }

        $faculty->save();

        // Return display value
        $displayValue = $faculty->{ $field};
        if ($field === 'teaching_experience') {
            $displayValue = $faculty->teaching_experience === null ? 'N/A' : ($faculty->teaching_experience ? 'Yes' : 'No');
        }
        elseif ($field === 'department_id') {
            $displayValue = $faculty->department ? $faculty->department->title : 'N/A';
        }
        elseif ($field === 'date_of_birth' || $field === 'demo_class_date' || $field === 'offer_letter_issued_date' || $field === 'joining_date') {
            $displayValue = $faculty->{ $field} ? $faculty->{ $field}->format('Y-m-d') : 'N/A';
        }
        else {
            $displayValue = ($displayValue === null || $displayValue === '') ? 'N/A' : (string)$displayValue;
        }

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully.',
            'value' => $displayValue,
        ]);
    }

    public function uploadDocument(Request $request, $id): JsonResponse
    {
        if (!$this->canAccessModule()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $faculty = OnlineTeachingFaculty::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'field' => 'required|string|in:document_resume_cv,document_10th_certificate,document_educational_qualification_certificates,document_aadhaar_front,document_aadhaar_back,document_other_1,document_other_2,offer_letter_upload',
            'file' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $field = (string)$request->input('field');

        // Delete old file if exists
        $old = $faculty->{ $field};
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        $path = $request->file('file')->store("online-teaching-faculties/{$faculty->id}", 'public');
        $faculty->{ $field} = $path;
        $faculty->save();

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully.',
        ]);
    }

    private function renderInlineText(OnlineTeachingFaculty $faculty, string $field): string
    {
        $value = $faculty->{ $field};
        $display = ($value === null || $value === '') ? 'N/A' : e((string)$value);

        return sprintf(
            '<div class="inline-edit" data-field="%s" data-id="%d" data-current="%s"><span class="display-value">%s</span> <a href="#" class="edit-btn text-muted"><i class="ti ti-edit"></i></a></div>',
            e($field),
            (int)$faculty->id,
            e((string)($value ?? '')),
            $display
        );
    }

    private function renderInlineTextarea(OnlineTeachingFaculty $faculty, string $field): string
    {
        $value = $faculty->{ $field};
        $display = ($value === null || $value === '') ? 'N/A' : e((string)$value);

        return sprintf(
            '<div class="inline-edit" data-field="%s" data-id="%d" data-type="textarea" data-current="%s"><span class="display-value">%s</span> <a href="#" class="edit-btn text-muted"><i class="ti ti-edit"></i></a></div>',
            e($field),
            (int)$faculty->id,
            e((string)($value ?? '')),
            $display
        );
    }

    private function renderInlineDate(OnlineTeachingFaculty $faculty, string $field): string
    {
        $value = $faculty->{ $field};
        $display = $value ? $value->format('Y-m-d') : 'N/A';

        return sprintf(
            '<div class="inline-edit" data-field="%s" data-id="%d" data-type="date" data-current="%s"><span class="display-value">%s</span> <a href="#" class="edit-btn text-muted"><i class="ti ti-edit"></i></a></div>',
            e($field),
            (int)$faculty->id,
            e($value ? $value->format('Y-m-d') : ''),
            e($display)
        );
    }

    /**
     * @param array<string,string> $options value=>label (include ''=>'N/A')
     */
    private function renderInlineSelect(OnlineTeachingFaculty $faculty, string $field, array $options, ?string $overrideCurrentValue = null): string
    {
        $raw = $overrideCurrentValue !== null ? $overrideCurrentValue : ($faculty->{ $field} ?? '');
        $rawStr = ($raw === null) ? '' : (string)$raw;

        $label = $options[$rawStr] ?? (($rawStr === '' || $rawStr === null) ? 'N/A' : $rawStr);

        return sprintf(
            '<div class="inline-edit" data-field="%s" data-id="%d" data-type="select" data-current="%s" data-options-json="%s"><span class="display-value">%s</span> <a href="#" class="edit-btn text-muted"><i class="ti ti-edit"></i></a></div>',
            e($field),
            (int)$faculty->id,
            e($rawStr),
            e(json_encode($options, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)),
            e((string)$label)
        );
    }

    private function renderInlineFileUpload(OnlineTeachingFaculty $faculty, string $field): string
    {
        $value = $faculty->{ $field};
        $hasFile = !empty($value);

        $html = '<div class="inline-file-upload" data-field="' . e($field) . '" data-id="' . (int)$faculty->id . '">';

        if ($hasFile) {
            $html .= '<a href="' . e(asset('storage/' . $value)) . '" target="_blank" class="btn btn-outline-primary btn-sm me-1"><i class="ti ti-download"></i></a>';
        }
        else {
            $html .= '<span class="text-muted small me-1">No file</span>';
        }

        $html .= '<button type="button" class="btn btn-sm btn-outline-secondary js-inline-upload-btn"><i class="ti ti-upload"></i></button>';
        $html .= '<input type="file" class="d-none js-inline-file-input" data-field="' . e($field) . '" data-id="' . (int)$faculty->id . '" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">';
        $html .= '</div>';

        return $html;
    }

    /**
     * Generate form link for a faculty member
     */
    public function generateFormToken($id): string
    {
        return route('public.faculty.form', ['id' => $id]);
    }

    /**
     * Show public form for faculty to fill their details
     */
    public function publicForm($id)
    {
        $faculty = OnlineTeachingFaculty::findOrFail($id);

        // If form is already filled, show a message
        if ($faculty->form_filled_at) {
            return view('public.faculty-form-already-filled', compact('faculty'));
        }

        // Load active departments
        $departments = Department::where('status', true)->orderBy('title')->get();
        return view('public.faculty-form', compact('faculty', 'departments'));
    }

    /**
     * Handle public form submission
     */
    public function publicSubmit(Request $request, $id)
    {
        $faculty = OnlineTeachingFaculty::findOrFail($id);

        // If form is already filled, redirect with error
        if ($faculty->form_filled_at) {
            return redirect()->route('public.faculty.form', $id)
                ->with('error', 'This form has already been submitted.');
        }

        // All fields are required in public form
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'gender' => 'required|string|in:Male,Female',
            'primary_mobile_number' => 'required|string|max:30',
            'alternate_contact_number' => 'nullable|string|max:30',
            'official_email_address' => 'required|email|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'address_house_name_flat_no' => 'required|string|max:255',
            'address_area_locality' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_district' => 'required|string|max:255',
            'address_state' => 'required|string|max:255',
            'address_pin_code' => 'required|string|max:20',
            'highest_educational_qualification' => 'required|string|max:255',
            'additional_certifications' => 'nullable|string|max:2000',
            'teaching_experience' => 'required|string|in:Yes,No',
            'department_name' => 'required|string|in:E-School,EduThanzeel,Diploma in Graphic Designing,AI Integrated Digital Marketing,Data Science,Machine Learning',

            // Documents (required only if not already uploaded)
            'document_resume_cv' => ($faculty->document_resume_cv ? 'nullable' : 'required') . '|file|max:10240',
            'document_10th_certificate' => ($faculty->document_10th_certificate ? 'nullable' : 'required') . '|file|max:10240',
            'document_educational_qualification_certificates' => ($faculty->document_educational_qualification_certificates ? 'nullable' : 'required') . '|file|max:10240',
            'document_aadhaar_front' => ($faculty->document_aadhaar_front ? 'nullable' : 'required') . '|file|max:10240',
            'document_aadhaar_back' => ($faculty->document_aadhaar_back ? 'nullable' : 'required') . '|file|max:10240',
            'document_other_1' => 'nullable|file|max:10240',
            'document_other_2' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'full_name',
            'date_of_birth',
            'gender',
            'primary_mobile_number',
            'alternate_contact_number',
            'official_email_address',
            'father_name',
            'mother_name',
            'address_house_name_flat_no',
            'address_area_locality',
            'address_city',
            'address_district',
            'address_state',
            'address_pin_code',
            'highest_educational_qualification',
            'additional_certifications',
            'department_name',
        ]);

        // Normalize Yes/No to boolean
        $teachingExperience = $request->input('teaching_experience');
        if ($teachingExperience === 'Yes') {
            $data['teaching_experience'] = true;
        }
        elseif ($teachingExperience === 'No') {
            $data['teaching_experience'] = false;
        }

        // Mark form as filled
        $data['form_filled_at'] = now();

        // Update faculty data
        $faculty->update($data);

        // Store documents
        $docFields = [
            'document_resume_cv',
            'document_10th_certificate',
            'document_educational_qualification_certificates',
            'document_aadhaar_front',
            'document_aadhaar_back',
            'document_other_1',
            'document_other_2',
        ];

        foreach ($docFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store("online-teaching-faculties/{$faculty->id}", 'public');
                $faculty->{ $field} = $path;
            }
        }

        $faculty->save();

        // Send email to CAO with submitted details and documents
        if (function_exists('send_email')) {
            $subject = 'Online Teaching Faculty';
            $body = $this->buildFacultySubmissionEmailBody($faculty);
            $attachments = $this->getFacultyDocumentAttachments($faculty);
            send_email('cao@natdemy.com', 'CAO', $subject, $body, $attachments, 'CRM');
        }

        return view('public.faculty-form-success', compact('faculty'));
    }

    /**
     * Build HTML email body for CAO with faculty submission details.
     */
    private function buildFacultySubmissionEmailBody(OnlineTeachingFaculty $faculty): string
    {
        $dob = $faculty->date_of_birth ? \Carbon\Carbon::parse($faculty->date_of_birth)->format('d-m-Y') : '';
        $teachingExp = $faculty->teaching_experience === true ? 'Yes' : ($faculty->teaching_experience === false ? 'No' : '');
        $details = [
            'Full Name' => $faculty->full_name,
            'Date of Birth' => $dob,
            'Gender' => $faculty->gender,
            'Primary Mobile' => $faculty->primary_mobile_number,
            'Alternate Contact' => $faculty->alternate_contact_number,
            'Official Email' => $faculty->official_email_address,
            'Father Name' => $faculty->father_name,
            'Mother Name' => $faculty->mother_name,
            'House/Flat No.' => $faculty->address_house_name_flat_no,
            'Area/Locality' => $faculty->address_area_locality,
            'City' => $faculty->address_city,
            'District' => $faculty->address_district,
            'State' => $faculty->address_state,
            'PIN Code' => $faculty->address_pin_code,
            'Highest Qualification' => $faculty->highest_educational_qualification,
            'Additional Certifications' => $faculty->additional_certifications,
            'Teaching Experience' => $teachingExp,
            'Department' => $faculty->department_name,
        ];
        $cellStyle = "padding: 10px 14px; border: 1px solid #dee2e6; vertical-align: top;";
        $labelStyle = $cellStyle . " width: 38%; background-color: #f8f9fa; font-weight: 600; color: #495057;";
        $valueStyle = $cellStyle . " color: #212529;";
        $rows = '';
        foreach ($details as $label => $value) {
            if ((string) $value !== '') {
                $rows .= "<tr><td style='{$labelStyle}'>{$label}</td><td style='{$valueStyle}'>" . e($value) . "</td></tr>";
            }
        }
        $docLabels = [
            'document_resume_cv' => 'Resume/CV',
            'document_10th_certificate' => '10th Certificate',
            'document_educational_qualification_certificates' => 'Educational Qualification Certificates',
            'document_aadhaar_front' => 'Aadhaar (Front)',
            'document_aadhaar_back' => 'Aadhaar (Back)',
            'document_other_1' => 'Other Document 1',
            'document_other_2' => 'Other Document 2',
        ];
        $docRows = '';
        foreach ($docLabels as $field => $label) {
            if (!empty($faculty->$field)) {
                $docRows .= "<tr><td style='{$labelStyle}'>{$label}</td><td style='{$valueStyle}'>Uploaded</td></tr>";
            }
        }
        $docTable = $docRows
            ? "<table style='width:100%; border-collapse: collapse; margin: 0;'><tbody>{$docRows}</tbody></table>"
            : "<p style='margin: 0; padding: 10px 0; color: #6c757d;'>No documents attached.</p>";
        $submittedAt = $faculty->form_filled_at
            ? \Carbon\Carbon::parse($faculty->form_filled_at)->format('d-m-Y h:i A')
            : now()->format('d-m-Y h:i A');

        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 15px; line-height: 1.5; color: #333;'>
            <table role='presentation' style='width: 100%; max-width: 700px; margin: 0 auto; border-collapse: collapse;'>
                <tr>
                    <td style='padding: 24px; border: 1px solid #dee2e6; border-radius: 8px; background-color: #ffffff;'>
                        <h2 style='margin: 0 0 20px 0; color: #2c3e50; text-align: center; font-size: 22px;'>Online Teaching Faculty Form Submitted</h2>
                        <p style='margin: 0 0 12px 0;'>Dear CAO Team,</p>
                        <p style='margin: 0 0 20px 0;'>A faculty has submitted the online teaching faculty form. Details and uploaded documents are below.</p>
                        <hr style='margin: 24px 0; border: none; border-top: 1px solid #dee2e6;'/>
                        <h3 style='margin: 0 0 12px 0; color: #2c3e50; font-size: 17px;'>Submitted Details</h3>
                        <table style='width: 100%; border-collapse: collapse; margin: 0 0 24px 0;'>
                            <thead>
                                <tr>
                                    <th style='padding: 10px 14px; border: 1px solid #dee2e6; background-color: #e9ecef; text-align: left; font-weight: 600; color: #495057; width: 38%;'>Field</th>
                                    <th style='padding: 10px 14px; border: 1px solid #dee2e6; background-color: #e9ecef; text-align: left; font-weight: 600; color: #495057;'>Value</th>
                                </tr>
                            </thead>
                            <tbody>{$rows}</tbody>
                        </table>
                        <h3 style='margin: 0 0 12px 0; color: #2c3e50; font-size: 17px;'>Uploaded Documents</h3>
                        {$docTable}
                        <p style='margin: 20px 0 0 0; padding-top: 16px;'><strong>Submitted at:</strong> {$submittedAt}</p>
                        <hr style='margin: 24px 0; border: none; border-top: 1px solid #dee2e6;'/>
                        <p style='margin: 0;'>Best regards,<br/><strong>CRM</strong></p>
                    </td>
                </tr>
            </table>
        </body>
        </html>";
    }

    /**
     * Get full file paths of faculty documents for email attachments.
     */
    private function getFacultyDocumentAttachments(OnlineTeachingFaculty $faculty): array
    {
        $docFields = [
            'document_resume_cv',
            'document_10th_certificate',
            'document_educational_qualification_certificates',
            'document_aadhaar_front',
            'document_aadhaar_back',
            'document_other_1',
            'document_other_2',
        ];
        $attachments = [];
        foreach ($docFields as $field) {
            if (!empty($faculty->$field)) {
                $path = storage_path('app/public/' . $faculty->$field);
                if (file_exists($path)) {
                    $attachments[] = $path;
                }
            }
        }
        return $attachments;
    }
}
