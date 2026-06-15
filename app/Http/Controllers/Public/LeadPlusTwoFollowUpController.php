<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\PlusTwoFollowUpQuestionnaire;
use Illuminate\Http\Request;

class LeadPlusTwoFollowUpController extends Controller
{
    public function showForm($leadId = null)
    {
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);

            if ($lead && $lead->plusTwoFollowUpQuestionnaire) {
                return view('public.plus-two-follow-up-success');
            }
        }

        return view('public.plus-two-follow-up', compact('lead'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'received_plus_two_result' => 'required|in:yes,no',
            'result_outcome' => 'nullable|in:passed,failed,improvement',
            'stream_completed' => 'required|in:science,commerce,humanities',
            'current_plan' => 'required|in:degree,professional_course,government_exam,job,abroad_studies,business,not_decided',
            'college_selection' => 'required|in:finalized,shortlisted,not_decided',
            'planned_course' => 'nullable|string|max:255',
            'course_selection_reason' => 'nullable|string|max:1000',
            'admission_started' => 'required|in:yes,no',
            'decision_maker' => 'required|in:self,parents,both_together,guardian',
            'career_clarity_level' => 'required|in:yes,somewhat,no',
            'biggest_challenge' => 'nullable|string|max:1000',
            'guidance_interested_level' => 'required|in:yes,maybe,no',
            'counseling_preference' => 'required|in:online,direct,either',
            'best_contact_time' => 'nullable|string|max:255',
            'result_status' => 'nullable|string|max:255',
            'stream' => 'nullable|string|max:255',
            'future_plan' => 'nullable|string|max:255',
            'course_interested' => 'nullable|string|max:255',
            'college_selected' => 'nullable|string|max:255',
            'decision_maker_summary' => 'nullable|string|max:255',
            'career_clarity' => 'nullable|string|max:255',
            'main_challenge' => 'nullable|string|max:1000',
            'guidance_interested' => 'nullable|string|max:255',
            'followup_date' => 'nullable|date',
            'followup_time' => 'nullable|date_format:H:i',
        ], [
            'lead_id.required' => 'Lead reference is required.',
            'lead_id.exists' => 'Invalid lead reference.',
            'name.required' => 'Name is required.',
            'mobile_number.required' => 'Mobile number is required.',
            'received_plus_two_result.required' => 'Please indicate if you have received your Plus Two result.',
            'stream_completed.required' => 'Please select your stream.',
            'current_plan.required' => 'Please select your current plan after Plus Two.',
            'college_selection.required' => 'Please indicate your college selection status.',
            'admission_started.required' => 'Please indicate if you have started the admission process.',
            'decision_maker.required' => 'Please select who will make the final decision.',
            'career_clarity_level.required' => 'Please indicate your career clarity.',
            'guidance_interested_level.required' => 'Please indicate if you are interested in career guidance.',
            'counseling_preference.required' => 'Please select your counseling preference.',
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        if ($lead->plusTwoFollowUpQuestionnaire) {
            return response()->json([
                'success' => false,
                'message' => 'This questionnaire has already been submitted.',
            ], 422);
        }

        try {
            $summary = $this->buildSummaryFields($request);

            $questionnaire = PlusTwoFollowUpQuestionnaire::create(array_merge(
                $request->only([
                    'lead_id',
                    'name',
                    'mobile_number',
                    'received_plus_two_result',
                    'result_outcome',
                    'stream_completed',
                    'current_plan',
                    'college_selection',
                    'planned_course',
                    'course_selection_reason',
                    'admission_started',
                    'decision_maker',
                    'career_clarity_level',
                    'biggest_challenge',
                    'guidance_interested_level',
                    'counseling_preference',
                    'best_contact_time',
                    'followup_date',
                    'followup_time',
                ]),
                $summary
            ));

            $lead->update([
                'title' => $request->name,
                'phone' => $request->mobile_number,
                'followup_date' => $request->followup_date ?: $lead->followup_date,
            ]);

            try {
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'activity_type' => 'plus_two_questionnaire_submitted',
                    'description' => 'Plus Two Student Follow-Up Questionnaire submitted',
                    'remarks' => 'iPhone Challenge questionnaire submitted on ' . now()->format('d-m-Y') . ' at ' . now()->format('h:i A'),
                    'created_by' => $lead->telecaller_id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create lead activity for Plus Two questionnaire: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your questionnaire has been submitted successfully.',
                'redirect' => route('public.lead.plus-two-follow-up.success', $lead->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the questionnaire. Please try again.',
            ], 500);
        }
    }

    public function showSuccess($leadId)
    {
        return view('public.plus-two-follow-up-success');
    }

    private function buildSummaryFields(Request $request): array
    {
        return [
            'result_status' => $request->input('result_status') ?: $this->formatResultStatus($request),
            'stream' => $request->input('stream') ?: $this->labelMap('stream_completed', $request->stream_completed),
            'future_plan' => $request->input('future_plan') ?: $this->labelMap('current_plan', $request->current_plan),
            'course_interested' => $request->input('course_interested') ?: $request->planned_course,
            'college_selected' => $request->input('college_selected') ?: $this->labelMap('college_selection', $request->college_selection),
            'decision_maker_summary' => $request->input('decision_maker_summary') ?: $this->labelMap('decision_maker', $request->decision_maker),
            'career_clarity' => $request->input('career_clarity') ?: $this->labelMap('career_clarity_level', $request->career_clarity_level),
            'main_challenge' => $request->input('main_challenge') ?: $request->biggest_challenge,
            'guidance_interested' => $request->input('guidance_interested') ?: $this->labelMap('guidance_interested_level', $request->guidance_interested_level),
        ];
    }

    private function formatResultStatus(Request $request): string
    {
        if ($request->filled('result_outcome')) {
            return $this->labelMap('result_outcome', $request->result_outcome);
        }

        if ($request->received_plus_two_result === 'no') {
            return 'Result Not Received';
        }

        if ($request->received_plus_two_result === 'yes') {
            return 'Result Received';
        }

        return '';
    }

    private function labelMap(string $field, ?string $value): string
    {
        $maps = [
            'result_outcome' => [
                'passed' => 'Passed',
                'failed' => 'Failed',
                'improvement' => 'Improvement',
            ],
            'stream_completed' => [
                'science' => 'Science',
                'commerce' => 'Commerce',
                'humanities' => 'Humanities',
            ],
            'current_plan' => [
                'degree' => 'Degree',
                'professional_course' => 'Professional Course',
                'government_exam' => 'Government Exam Preparation',
                'job' => 'Job',
                'abroad_studies' => 'Abroad Studies',
                'business' => 'Business',
                'not_decided' => 'Not Decided Yet',
            ],
            'college_selection' => [
                'finalized' => 'Finalized',
                'shortlisted' => 'Shortlisted',
                'not_decided' => 'Not Decided',
            ],
            'decision_maker' => [
                'self' => 'Self',
                'parents' => 'Parents',
                'both_together' => 'Both Together',
                'guardian' => 'Guardian',
            ],
            'career_clarity_level' => [
                'yes' => 'Yes',
                'somewhat' => 'Somewhat',
                'no' => 'No',
            ],
            'guidance_interested_level' => [
                'yes' => 'Yes',
                'maybe' => 'Maybe',
                'no' => 'No',
            ],
        ];

        return $maps[$field][$value] ?? (string) $value;
    }
}
