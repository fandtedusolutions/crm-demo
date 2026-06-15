@extends('layouts.mantis')

@section('title', 'Plus Two Questionnaire - ' . $lead->title)

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Plus Two Follow-Up Questionnaire</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item active">Questionnaire Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $lead->title }} <small class="text-muted">#{{ $lead->id }}</small></h5>
                <a href="{{ route('leads.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Back to Leads
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Name</small>
                        <strong>{{ $questionnaire->name }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Mobile Number</small>
                        <strong>{{ $questionnaire->mobile_number }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Submitted On</small>
                        <strong>{{ $questionnaire->created_at->format('d-m-Y h:i A') }}</strong>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3">Section 1: Result Status</h6>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q1. Received Plus Two Result?</small>
                        <strong>{{ ucfirst($questionnaire->received_plus_two_result) }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q2. Result</small>
                        <strong>{{ $questionnaire->result_outcome ? ucfirst($questionnaire->result_outcome) : '-' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q3. Stream</small>
                        <strong>{{ ucfirst($questionnaire->stream_completed) }}</strong>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3">Section 2: Future Plan</h6>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q4. Current Plan</small>
                        <strong>{{ str_replace('_', ' ', ucfirst($questionnaire->current_plan)) }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q5. College Selection</small>
                        <strong>{{ str_replace('_', ' ', ucfirst($questionnaire->college_selection)) }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q6. Planned Course</small>
                        <strong>{{ $questionnaire->planned_course ?: '-' }}</strong>
                    </div>
                    <div class="col-12 mt-2">
                        <small class="text-muted d-block">Q7. Reason for Choosing Course</small>
                        <strong>{{ $questionnaire->course_selection_reason ?: '-' }}</strong>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3">Section 3: Decision Stage</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Q8. Admission Process Started?</small>
                        <strong>{{ ucfirst($questionnaire->admission_started) }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Q9. Decision Maker</small>
                        <strong>{{ str_replace('_', ' ', ucfirst($questionnaire->decision_maker)) }}</strong>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3">Section 4: Pain Point Identification</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Q10. Career Clarity</small>
                        <strong>{{ ucfirst($questionnaire->career_clarity_level) }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Q11. Biggest Challenge</small>
                        <strong>{{ $questionnaire->biggest_challenge ?: '-' }}</strong>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3">Section 5: Opportunity Qualification</h6>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q12. Guidance Interested</small>
                        <strong>{{ ucfirst($questionnaire->guidance_interested_level) }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q13. Counseling Preference</small>
                        <strong>{{ ucfirst($questionnaire->counseling_preference) }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Q14. Best Contact Time</small>
                        <strong>{{ $questionnaire->best_contact_time ?: '-' }}</strong>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3">Summary Fields</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Result Status</small>
                        <strong>{{ $questionnaire->result_status ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Stream</small>
                        <strong>{{ $questionnaire->stream ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Future Plan</small>
                        <strong>{{ $questionnaire->future_plan ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Course Interested</small>
                        <strong>{{ $questionnaire->course_interested ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">College Selected</small>
                        <strong>{{ $questionnaire->college_selected ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Decision Maker</small>
                        <strong>{{ $questionnaire->decision_maker_summary ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Career Clarity</small>
                        <strong>{{ $questionnaire->career_clarity ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Main Challenge</small>
                        <strong>{{ $questionnaire->main_challenge ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Guidance Interested</small>
                        <strong>{{ $questionnaire->guidance_interested ?: '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Follow-up Date</small>
                        <strong>{{ $questionnaire->followup_date ? $questionnaire->followup_date->format('d-m-Y') : '-' }}</strong>
                    </div>
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Follow-up Time</small>
                        <strong>{{ $questionnaire->followup_time ? \Carbon\Carbon::parse($questionnaire->followup_time)->format('h:i A') : '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
