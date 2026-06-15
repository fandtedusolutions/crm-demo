<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>iPhone Challenge - Plus Two Student Follow-Up Questionnaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .form-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
        }
        .form-header {
            background: linear-gradient(225deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: white;
            padding: 35px 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        .form-header h2 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .form-body {
            background: white;
            padding: 40px 35px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f3460;
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        .section-title:first-of-type {
            margin-top: 0;
        }
        .question-block {
            margin-bottom: 22px;
        }
        .question-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }
        .required { color: #dc3545; }
        .form-check {
            margin-bottom: 8px;
        }
        .form-check-label {
            cursor: pointer;
        }
        .summary-section {
            background: linear-gradient(135deg, #f8f9ff 0%, #eef2ff 100%);
            border: 1px solid #d0d7ff;
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
        }
        .summary-section .section-title {
            border-bottom-color: #c7d2fe;
            color: #3730a3;
        }
        .btn-submit {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
            border: none;
            padding: 14px 40px;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 52, 96, 0.3);
        }
        .pre-filled-banner {
            background: #e8f4fd;
            border: 1px solid #b6d4fe;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }
        .conditional-group {
            display: none;
            margin-top: 15px;
            padding-left: 15px;
            border-left: 3px solid #0f3460;
        }
        .conditional-group.show {
            display: block;
        }
        .alert { border-radius: 10px; }
        @media (max-width: 768px) {
            .form-body { padding: 25px 20px; }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-graduation-cap me-2"></i>iPhone Challenge</h2>
            <p class="mb-0">Plus Two Student Follow-Up Questionnaire</p>
        </div>

        <div class="form-body">
            @if($lead)
            <div class="pre-filled-banner">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                Your details have been pre-filled from our records. Please review and update if needed.
            </div>
            @endif

            <div id="alertContainer"></div>

            <form id="questionnaireForm">
                @csrf
                <input type="hidden" name="lead_id" value="{{ $lead->id ?? '' }}">

                <div class="section-title">Contact Information</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="question-block">
                            <label class="question-label" for="name">Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $lead->title ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="question-block">
                            <label class="question-label" for="mobile_number">Mobile Number <span class="required">*</span></label>
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $lead->phone ?? '') }}" required>
                        </div>
                    </div>
                </div>

                <div class="section-title">Section 1: Result Status</div>

                <div class="question-block">
                    <label class="question-label">Q1. Have you received your Plus Two result? <span class="required">*</span></label>
                    @foreach(['yes' => 'Yes', 'no' => 'No'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input sync-field" type="radio" name="received_plus_two_result" id="received_{{ $value }}" value="{{ $value }}" data-sync="result_status" data-label-map='{"yes":"Result Received","no":"Result Not Received"}' required>
                        <label class="form-check-label" for="received_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="question-block">
                    <label class="question-label">Q2. What was your result?</label>
                    @foreach(['passed' => 'Passed', 'failed' => 'Failed', 'improvement' => 'Improvement'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input sync-field" type="radio" name="result_outcome" id="outcome_{{ $value }}" value="{{ $value }}" data-sync="result_status" data-label="{{ $label }}">
                        <label class="form-check-label" for="outcome_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="question-block">
                    <label class="question-label">Q3. Which stream did you complete? <span class="required">*</span></label>
                    @foreach(['science' => 'Science', 'commerce' => 'Commerce', 'humanities' => 'Humanities'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input sync-field" type="radio" name="stream_completed" id="stream_{{ $value }}" value="{{ $value }}" data-sync="stream" data-label="{{ $label }}" required>
                        <label class="form-check-label" for="stream_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="section-title">Section 2: Future Plan</div>

                <div class="question-block">
                    <label class="question-label">Q4. What is your current plan after Plus Two? <span class="required">*</span></label>
                    @foreach([
                        'degree' => 'Degree',
                        'professional_course' => 'Professional Course',
                        'government_exam' => 'Government Exam Preparation',
                        'job' => 'Job',
                        'abroad_studies' => 'Abroad Studies',
                        'business' => 'Business',
                        'not_decided' => 'Not Decided Yet',
                    ] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input sync-field" type="radio" name="current_plan" id="plan_{{ $value }}" value="{{ $value }}" data-sync="future_plan" data-label="{{ $label }}" required>
                        <label class="form-check-label" for="plan_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="question-block">
                    <label class="question-label">Q5. Have you already selected a college or institution? <span class="required">*</span></label>
                    @foreach(['finalized' => 'Finalized', 'shortlisted' => 'Shortlisted', 'not_decided' => 'Not Decided'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input sync-field" type="radio" name="college_selection" id="college_{{ $value }}" value="{{ $value }}" data-sync="college_selected" data-label="{{ $label }}" required>
                        <label class="form-check-label" for="college_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="question-block">
                    <label class="question-label" for="planned_course">Q6. Which course are you planning to pursue?</label>
                    <input type="text" class="form-control sync-input" id="planned_course" name="planned_course" data-sync="course_interested">
                </div>

                <div class="question-block">
                    <label class="question-label" for="course_selection_reason">Q7. What is the main reason for choosing that course?</label>
                    <textarea class="form-control" id="course_selection_reason" name="course_selection_reason" rows="3"></textarea>
                </div>

                <div class="section-title">Section 3: Decision Stage</div>

                <div class="question-block">
                    <label class="question-label">Q8. Have you already started the admission process? <span class="required">*</span></label>
                    @foreach(['yes' => 'Yes', 'no' => 'No'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="admission_started" id="admission_{{ $value }}" value="{{ $value }}" required>
                        <label class="form-check-label" for="admission_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="question-block">
                    <label class="question-label">Q9. Who will be making the final decision regarding your higher studies? <span class="required">*</span></label>
                    @foreach(['self' => 'Self', 'parents' => 'Parents', 'both_together' => 'Both Together', 'guardian' => 'Guardian'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input sync-field" type="radio" name="decision_maker" id="decision_{{ $value }}" value="{{ $value }}" data-sync="decision_maker_summary" data-label="{{ $label }}" required>
                        <label class="form-check-label" for="decision_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="section-title">Section 4: Pain Point Identification</div>

                <div class="question-block">
                    <label class="question-label">Q10. Are you completely clear about your future career path? <span class="required">*</span></label>
                    @foreach(['yes' => 'Yes', 'somewhat' => 'Somewhat', 'no' => 'No'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input sync-field" type="radio" name="career_clarity_level" id="clarity_{{ $value }}" value="{{ $value }}" data-sync="career_clarity" data-label="{{ $label }}" required>
                        <label class="form-check-label" for="clarity_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="question-block">
                    <label class="question-label" for="biggest_challenge">Q11. What is the biggest challenge you are facing right now regarding your higher studies?</label>
                    <textarea class="form-control sync-input" id="biggest_challenge" name="biggest_challenge" rows="3" data-sync="main_challenge"></textarea>
                </div>

                <div class="section-title">Section 5: Opportunity Qualification</div>

                <div class="question-block">
                    <label class="question-label">Q12. Before taking admission, would you like to receive free career guidance regarding courses, career opportunities, placements, and future scope? <span class="required">*</span></label>
                    @foreach(['yes' => 'Yes', 'maybe' => 'Maybe', 'no' => 'No'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input sync-field" type="radio" name="guidance_interested_level" id="guidance_{{ $value }}" value="{{ $value }}" data-sync="guidance_interested" data-label="{{ $label }}" required>
                        <label class="form-check-label" for="guidance_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="question-block">
                    <label class="question-label">Q13. Would you prefer an online session or a direct counseling session? <span class="required">*</span></label>
                    @foreach(['online' => 'Online', 'direct' => 'Direct', 'either' => 'Either'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="counseling_preference" id="counseling_{{ $value }}" value="{{ $value }}" required>
                        <label class="form-check-label" for="counseling_{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="question-block">
                    <label class="question-label" for="best_contact_time">Q14. What would be the best time for our counselor to contact you?</label>
                    <input type="text" class="form-control" id="best_contact_time" name="best_contact_time" placeholder="e.g. Weekdays after 4 PM">
                </div>

                <div class="summary-section">
                    <div class="section-title">Summary Details</div>
                    <p class="text-muted small mb-3">These fields are auto-filled from your answers above. You can edit them if needed.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="result_status">Result Status</label>
                                <input type="text" class="form-control summary-field" id="result_status" name="result_status">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="stream">Stream</label>
                                <input type="text" class="form-control summary-field" id="stream" name="stream">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="future_plan">Future Plan</label>
                                <input type="text" class="form-control summary-field" id="future_plan" name="future_plan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="course_interested">Course Interested</label>
                                <input type="text" class="form-control summary-field" id="course_interested" name="course_interested">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="college_selected">College Selected</label>
                                <input type="text" class="form-control summary-field" id="college_selected" name="college_selected">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="decision_maker_summary">Decision Maker</label>
                                <input type="text" class="form-control summary-field" id="decision_maker_summary" name="decision_maker_summary">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="career_clarity">Career Clarity</label>
                                <input type="text" class="form-control summary-field" id="career_clarity" name="career_clarity">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="main_challenge">Main Challenge</label>
                                <input type="text" class="form-control summary-field" id="main_challenge" name="main_challenge">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="guidance_interested">Guidance Interested</label>
                                <input type="text" class="form-control summary-field" id="guidance_interested" name="guidance_interested">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="followup_date">Follow-up Date</label>
                                <input type="date" class="form-control" id="followup_date" name="followup_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="question-block">
                                <label class="question-label" for="followup_time">Follow-up Time</label>
                                <input type="time" class="form-control" id="followup_time" name="followup_time">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane me-2"></i>Submit Questionnaire
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const form = document.getElementById('questionnaireForm');
        const alertContainer = document.getElementById('alertContainer');
        const summaryTouched = {};

        document.querySelectorAll('input[name="received_plus_two_result"]').forEach(radio => {
            radio.addEventListener('change', function() {
                syncSummaryFromRadio(this);
            });
        });

        document.querySelectorAll('.sync-field').forEach(field => {
            field.addEventListener('change', function() {
                syncSummaryFromRadio(this);
            });
        });

        document.querySelectorAll('.sync-input').forEach(field => {
            field.addEventListener('input', function() {
                const target = this.dataset.sync;
                if (!summaryTouched[target]) {
                    document.getElementById(target).value = this.value;
                }
            });
        });

        document.querySelectorAll('.summary-field').forEach(field => {
            field.addEventListener('input', function() {
                summaryTouched[this.id] = true;
            });
        });

        function syncSummaryFromRadio(radio) {
            const target = radio.dataset.sync;
            if (!target || summaryTouched[target]) {
                return;
            }

            if (radio.name === 'received_plus_two_result' && radio.value === 'no') {
                document.getElementById('result_status').value = 'Result Not Received';
                return;
            }

            if (radio.name === 'received_plus_two_result' && radio.value === 'yes') {
                const outcome = document.querySelector('input[name="result_outcome"]:checked');
                if (outcome) {
                    document.getElementById('result_status').value = outcome.dataset.label || '';
                } else {
                    document.getElementById('result_status').value = 'Result Received';
                }
                return;
            }

            if (radio.name === 'result_outcome' && !summaryTouched['result_status']) {
                document.getElementById('result_status').value = radio.dataset.label || '';
                return;
            }

            document.getElementById(target).value = radio.dataset.label || '';
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const leadId = form.querySelector('[name="lead_id"]').value;
            if (!leadId) {
                showAlert('Lead reference is missing. Please use the link provided by our team.', 'danger');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

            const formData = new FormData(form);

            try {
                const response = await fetch('{{ route("public.lead.plus-two-follow-up.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    showAlert(data.message || 'Submission failed. Please try again.', 'danger');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Questionnaire';
                }
            } catch (error) {
                showAlert('An error occurred. Please check your connection and try again.', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Questionnaire';
            }
        });

        function showAlert(message, type) {
            alertContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    </script>
</body>
</html>
