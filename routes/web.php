<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\SubjectAreaController;
use App\Http\Controllers\CourseMailController;
use App\Http\Controllers\FlagController;
use App\Http\Controllers\SupportFlagController;
use App\Http\Controllers\CourseFlagController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UniversityCourseController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TelecallerController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ConvertedLeadController;
use App\Http\Controllers\VoxbayController;
use App\Http\Controllers\VoxbayCallLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MetaLeadController;
use App\Http\Controllers\VoxbayCallController;
use App\Http\Controllers\OnlineTeachingFacultyController;
use App\Http\Controllers\CallAnalyticsController;

// Public routes
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Online Teaching Faculty Form Routes 
Route::get('/faculty-form/{id}', [OnlineTeachingFacultyController::class, 'publicForm'])->name('public.faculty.form');
Route::post('/faculty-form/{id}', [OnlineTeachingFacultyController::class, 'publicSubmit'])->name('public.faculty.submit');

// Public Voxbay API routes (no authentication required)
Route::prefix('api/voxbay')->group(function () {
    Route::post('/outgoing-call', [VoxbayController::class, 'outgoingCall'])->name('voxbay.outgoing-call');
    Route::get('/telecaller/{id}/extension', [VoxbayController::class, 'getTelecallerExtension'])->name('voxbay.telecaller.extension');
    Route::get('/test-connection', [VoxbayController::class, 'testConnection'])->name('voxbay.test-connection');
    Route::post('/webhook', [VoxbayController::class, 'webhook'])->name('voxbay.webhook');
});

Route::prefix('voxbay-call')->group(function () {

    // Incoming call landed
    Route::post('/incoming-call', [VoxbayCallController::class, 'callcenterbridging']);

    // Outgoing call landed
    Route::post('/outgoing-call', [VoxbayCallController::class, 'outgoingCall']);

    // Click to call
    Route::get('/click-to-call', [VoxbayCallController::class, 'clickToCall']);

    // Incoming Call CDR push
    Route::post('/incoming-cdr-push', [VoxbayCallController::class, 'incomingcdrpush']);

    // Outgoing Call CDR push
    Route::post('/outgoing-cdr-push', [VoxbayCallController::class, 'outgoingcdrpush']);

    // Incoming connect event
    Route::post('/connect-incoming', [VoxbayCallController::class, 'connectincoming']);

    // Outgoing connect event
    Route::post('/connect-outgoing', [VoxbayCallController::class, 'connectoutgoing']);

    // Incoming disconnect event
    Route::post('/disconnect-incoming', [VoxbayCallController::class, 'disconnectincoming']);

    // Outgoing disconnect event
    Route::post('/disconnect-outgoing', [VoxbayCallController::class, 'disconnectoutgoing']);

    // Debug request endpoint
    Route::post('/debug', [VoxbayCallController::class, 'debugRequest']);
});

// Public Meta Leads API routes (no authentication required)
Route::prefix('api/meta-leads')->group(function () {
    Route::get('/fetch', [MetaLeadController::class, 'fetchLeads'])->name('api.meta-leads.fetch');
    Route::get('/push', [MetaLeadController::class, 'pushMetaLeads'])->name('api.meta-leads.push');
    Route::get('/test-token', [MetaLeadController::class, 'testToken'])->name('api.meta-leads.test-token');
    Route::get('/test-original-token', [MetaLeadController::class, 'testOriginalToken'])->name('api.meta-leads.test-original-token');
    Route::get('/try-token-exchange', [MetaLeadController::class, 'tryTokenExchange'])->name('api.meta-leads.try-token-exchange');
    Route::get('/debug-env', [MetaLeadController::class, 'debugEnv'])->name('api.meta-leads.debug-env');
    Route::get('/statistics', [MetaLeadController::class, 'statistics'])->name('api.meta-leads.statistics');
    Route::get('/list', [MetaLeadController::class, 'index'])->name('api.meta-leads.list');
});

// Public API routes for subjects and courses (no authentication required)
Route::get('/api/subjects/by-course/{courseId}', [App\Http\Controllers\SubjectController::class, 'getByCourse'])->name('api.subjects.by-course');
Route::get('/api/subject-areas', [App\Http\Controllers\SubjectAreaController::class, 'listActive'])->name('api.subject-areas');
Route::get('/api/flags', [App\Http\Controllers\FlagController::class, 'listActive'])->name('api.flags');
Route::get('/api/support-flags', [App\Http\Controllers\SupportFlagController::class, 'listActive'])->name('api.support-flags');
Route::get('/api/course-flags', [App\Http\Controllers\CourseFlagController::class, 'listActive'])->name('api.course-flags');
Route::get('/api/batches/by-course/{courseId}', [App\Http\Controllers\BatchController::class, 'getByCourse'])->name('api.batches.by-course');
Route::get('/api/sub-courses/by-course/{courseId}', [App\Http\Controllers\SubCourseController::class, 'getByCourse'])->name('api.sub-courses.by-course');
Route::get('/api/admission-batches/by-batch/{batchId}', [App\Http\Controllers\AdmissionBatchController::class, 'getByBatch'])->name('api.admission-batches.by-batch');
Route::get('/api/university-courses/by-university/{universityId}', [App\Http\Controllers\UniversityCourseController::class, 'getByUniversity'])->name('api.university-courses.by-university');
Route::get('/api/class-times/by-course/{courseId}', [App\Http\Controllers\ClassTimeController::class, 'getByCourse'])->name('api.class-times.by-course');
Route::get('/api/courses/{courseId}/needs-time', [App\Http\Controllers\CourseController::class, 'checkNeedsTime'])->name('api.courses.needs-time');
Route::post('/api/invoices/calculate-amount/{studentId}', [App\Http\Controllers\InvoiceController::class, 'calculateAmount'])->name('api.invoices.calculate-amount');

// Public Lead Registration Routes
Route::prefix('register')->group(function () {
    // NIOS Registration Routes
    Route::get('/nios/{leadId?}', [App\Http\Controllers\Public\LeadRegistrationController::class, 'showNiosForm'])->name('public.lead.nios.register');
    Route::post('/nios', [App\Http\Controllers\Public\LeadRegistrationController::class, 'store'])->name('public.lead.nios.store');
    Route::get('/nios/subjects', [App\Http\Controllers\Public\LeadRegistrationController::class, 'getSubjects'])->name('public.lead.nios.subjects');
    Route::get('/nios/batches', [App\Http\Controllers\Public\LeadRegistrationController::class, 'getBatches'])->name('public.lead.nios.batches');

    // BOSSE Registration Routes
    Route::get('/bosse/{leadId?}', [App\Http\Controllers\Public\LeadBosseRegistrationController::class, 'showBosseForm'])->name('public.lead.bosse.register');
    Route::post('/bosse', [App\Http\Controllers\Public\LeadBosseRegistrationController::class, 'store'])->name('public.lead.bosse.store');
    Route::get('/bosse/subjects', [App\Http\Controllers\Public\LeadBosseRegistrationController::class, 'getSubjects'])->name('public.lead.bosse.subjects');
    Route::get('/bosse/batches', [App\Http\Controllers\Public\LeadBosseRegistrationController::class, 'getBatches'])->name('public.lead.bosse.batches');

    // GMVSS Registration Routes
    Route::get('/gmvss/{leadId?}', [App\Http\Controllers\Public\LeadGmvssRegistrationController::class, 'showGmvssForm'])->name('public.lead.gmvss.register');
    Route::post('/gmvss', [App\Http\Controllers\Public\LeadGmvssRegistrationController::class, 'store'])->name('public.lead.gmvss.store');
    Route::get('/gmvss/subjects', [App\Http\Controllers\Public\LeadGmvssRegistrationController::class, 'getSubjects'])->name('public.lead.gmvss.subjects');
    Route::get('/gmvss/batches', [App\Http\Controllers\Public\LeadGmvssRegistrationController::class, 'getBatches'])->name('public.lead.gmvss.batches');

    // Medical Coding Registration Routes
    Route::get('/medical-coding/{leadId?}', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'showMedicalCodingForm'])->name('public.lead.medical-coding.register');
    Route::post('/medical-coding', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'store'])->name('public.lead.medical-coding.store');
    Route::get('/medical-coding/{leadId}/success', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'showSuccess'])->name('public.lead.medical-coding.register.success');
    Route::get('/medical-coding/subjects', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'getSubjects'])->name('public.lead.medical-coding.subjects');
    Route::get('/medical-coding/batches', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'getBatches'])->name('public.lead.medical-coding.batches');

    // Hospital Administration Registration Routes
    Route::get('/hospital-admin/{leadId?}', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'showHospitalAdminForm'])->name('public.lead.hospital-admin.register');
    Route::post('/hospital-admin', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'store'])->name('public.lead.hospital-admin.register.store');
    Route::get('/hospital-admin/{leadId}/success', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'showSuccess'])->name('public.lead.hospital-admin.register.success');
    Route::get('/hospital-admin/subjects', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'getSubjects'])->name('public.lead.hospital-admin.subjects');
    Route::get('/hospital-admin/batches', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'getBatches'])->name('public.lead.hospital-admin.batches');

    // E-School Registration Routes
    Route::get('/eschool/{leadId?}', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'showESchoolForm'])->name('public.lead.eschool.register');
    Route::post('/eschool', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'store'])->name('public.lead.eschool.register.store');
    Route::get('/eschool/{leadId}/success', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'showSuccess'])->name('public.lead.eschool.register.success');
    Route::get('/eschool/subjects', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'getSubjects'])->name('public.lead.eschool.subjects');
    Route::get('/eschool/batches', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'getBatches'])->name('public.lead.eschool.batches');

    // Eduthanzeel Registration Routes
    Route::get('/eduthanzeel/{leadId?}', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'showEduthanzeelForm'])->name('public.lead.eduthanzeel.register');
    Route::post('/eduthanzeel', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'store'])->name('public.lead.eduthanzeel.register.store');
    Route::get('/eduthanzeel/{leadId}/success', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'showSuccess'])->name('public.lead.eduthanzeel.register.success');
    Route::get('/eduthanzeel/subjects', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'getSubjects'])->name('public.lead.eduthanzeel.subjects');
    Route::get('/eduthanzeel/batches', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'getBatches'])->name('public.lead.eduthanzeel.batches');

    // TTC Registration Routes
    Route::get('/ttc/{leadId?}', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'showTTCForm'])->name('public.lead.ttc.register');
    Route::post('/ttc', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'store'])->name('public.lead.ttc.register.store');
    Route::get('/ttc/{leadId}/success', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'showSuccess'])->name('public.lead.ttc.register.success');
    Route::get('/ttc/subjects', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'getSubjects'])->name('public.lead.ttc.subjects');
    Route::get('/ttc/batches', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'getBatches'])->name('public.lead.ttc.batches');

    // Hotel Management Registration Routes
    Route::get('/hotel-mgmt/{leadId?}', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'showHotelMgmtForm'])->name('public.lead.hotel-mgmt.register');
    Route::post('/hotel-mgmt', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'store'])->name('public.lead.hotel-mgmt.register.store');
    Route::get('/hotel-mgmt/{leadId}/success', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'showSuccess'])->name('public.lead.hotel-mgmt.register.success');
    Route::get('/hotel-mgmt/subjects', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'getSubjects'])->name('public.lead.hotel-mgmt.subjects');
    Route::get('/hotel-mgmt/batches', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'getBatches'])->name('public.lead.hotel-mgmt.batches');

    // UG/PG Registration Routes
    Route::get('/ugpg/subjects', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'getSubjects'])->name('public.lead.ugpg.subjects');
    Route::get('/ugpg/batches', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'getBatches'])->name('public.lead.ugpg.batches');
    Route::get('/ugpg/courses', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'getCourses'])->name('public.lead.ugpg.courses');
    Route::get('/ugpg/{leadId?}', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'showUGPGForm'])->name('public.lead.ugpg.register');
    Route::post('/ugpg', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'store'])->name('public.lead.ugpg.register.store');
    Route::get('/ugpg/{leadId}/success', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'showSuccess'])->name('public.lead.ugpg.register.success');

    // EduMaster Registration Routes
    Route::get('/edumaster/subjects', [App\Http\Controllers\Public\LeadEduMasterRegistrationController::class, 'getSubjects'])->name('public.lead.edumaster.subjects');
    Route::get('/edumaster/batches', [App\Http\Controllers\Public\LeadEduMasterRegistrationController::class, 'getBatches'])->name('public.lead.edumaster.batches');
    Route::get('/edumaster/courses', [App\Http\Controllers\Public\LeadEduMasterRegistrationController::class, 'getCourses'])->name('public.lead.edumaster.courses');
    Route::get('/edumaster/{leadId?}', [App\Http\Controllers\Public\LeadEduMasterRegistrationController::class, 'showEduMasterForm'])->name('public.lead.edumaster.register');
    Route::post('/edumaster', [App\Http\Controllers\Public\LeadEduMasterRegistrationController::class, 'store'])->name('public.lead.edumaster.register.store');
    Route::get('/edumaster/{leadId}/success', [App\Http\Controllers\Public\LeadEduMasterRegistrationController::class, 'showSuccess'])->name('public.lead.edumaster.register.success');

    // Python Registration Routes
    Route::get('/python/{leadId?}', [App\Http\Controllers\Public\LeadPythonRegistrationController::class, 'showPythonForm'])->name('public.lead.python.register');
    Route::post('/python', [App\Http\Controllers\Public\LeadPythonRegistrationController::class, 'store'])->name('public.lead.python.register.store');
    Route::get('/python/subjects', [App\Http\Controllers\Public\LeadPythonRegistrationController::class, 'getSubjects'])->name('public.lead.python.subjects');
    Route::get('/python/batches', [App\Http\Controllers\Public\LeadPythonRegistrationController::class, 'getBatches'])->name('public.lead.python.batches');

    // Digital Marketing Registration Routes
    Route::get('/digital-marketing/{leadId?}', [App\Http\Controllers\Public\LeadDigitalMarketingRegistrationController::class, 'showDigitalMarketingForm'])->name('public.lead.digital-marketing.register');
    Route::post('/digital-marketing', [App\Http\Controllers\Public\LeadDigitalMarketingRegistrationController::class, 'store'])->name('public.lead.digital-marketing.register.store');
    Route::get('/digital-marketing/subjects', [App\Http\Controllers\Public\LeadDigitalMarketingRegistrationController::class, 'getSubjects'])->name('public.lead.digital-marketing.subjects');
    Route::get('/digital-marketing/batches', [App\Http\Controllers\Public\LeadDigitalMarketingRegistrationController::class, 'getBatches'])->name('public.lead.digital-marketing.batches');

    // Junior Vlogger Registration Routes (course_id = 25)
    Route::get('/junior-vlogger/{leadId?}', [App\Http\Controllers\Public\LeadJuniorVloggerRegistrationController::class, 'showForm'])->name('public.lead.junior-vlogger.register');
    Route::post('/junior-vlogger', [App\Http\Controllers\Public\LeadJuniorVloggerRegistrationController::class, 'store'])->name('public.lead.junior-vlogger.store');
    Route::get('/junior-vlogger/{leadId}/success', [App\Http\Controllers\Public\LeadJuniorVloggerRegistrationController::class, 'showSuccess'])->name('public.lead.junior-vlogger.register.success');

    // Plus Two Follow-Up Questionnaire (lead_source_id = 13)
    Route::get('/plus-two-follow-up/{leadId?}', [App\Http\Controllers\Public\LeadPlusTwoFollowUpController::class, 'showForm'])->name('public.lead.plus-two-follow-up.register');
    Route::post('/plus-two-follow-up', [App\Http\Controllers\Public\LeadPlusTwoFollowUpController::class, 'store'])->name('public.lead.plus-two-follow-up.store');
    Route::get('/plus-two-follow-up/{leadId}/success', [App\Http\Controllers\Public\LeadPlusTwoFollowUpController::class, 'showSuccess'])->name('public.lead.plus-two-follow-up.success');

    // Diploma in Data Science Registration Routes
    Route::get('/diploma-in-data-science/{leadId?}', [App\Http\Controllers\Public\LeadAIAutomationRegistrationController::class, 'showAIAutomationForm'])->name('public.lead.diploma-in-data-science.register');
    Route::post('/diploma-in-data-science', [App\Http\Controllers\Public\LeadAIAutomationRegistrationController::class, 'store'])->name('public.lead.diploma-in-data-science.register.store');
    Route::get('/diploma-in-data-science/subjects', [App\Http\Controllers\Public\LeadAIAutomationRegistrationController::class, 'getSubjects'])->name('public.lead.diploma-in-data-science.subjects');
    Route::get('/diploma-in-data-science/batches', [App\Http\Controllers\Public\LeadAIAutomationRegistrationController::class, 'getBatches'])->name('public.lead.diploma-in-data-science.batches');

    // Web Development & Designing Registration Routes
    Route::get('/web-dev/{leadId?}', [App\Http\Controllers\Public\LeadWebDevRegistrationController::class, 'showWebDevForm'])->name('public.lead.web-dev.register');
    Route::post('/web-dev', [App\Http\Controllers\Public\LeadWebDevRegistrationController::class, 'store'])->name('public.lead.web-dev.register.store');
    Route::get('/web-dev/subjects', [App\Http\Controllers\Public\LeadWebDevRegistrationController::class, 'getSubjects'])->name('public.lead.web-dev.subjects');
    Route::get('/web-dev/batches', [App\Http\Controllers\Public\LeadWebDevRegistrationController::class, 'getBatches'])->name('public.lead.web-dev.batches');

    // Vibe Coding Registration Routes
    Route::get('/vibe-coding/{leadId?}', [App\Http\Controllers\Public\LeadVibeCodingRegistrationController::class, 'showVibeCodingForm'])->name('public.lead.vibe-coding.register');
    Route::post('/vibe-coding', [App\Http\Controllers\Public\LeadVibeCodingRegistrationController::class, 'store'])->name('public.lead.vibe-coding.register.store');
    Route::get('/vibe-coding/subjects', [App\Http\Controllers\Public\LeadVibeCodingRegistrationController::class, 'getSubjects'])->name('public.lead.vibe-coding.subjects');
    Route::get('/vibe-coding/batches', [App\Http\Controllers\Public\LeadVibeCodingRegistrationController::class, 'getBatches'])->name('public.lead.vibe-coding.batches');

    // RPA Registration Routes (course_id = 27)
    Route::get('/rpa/{leadId?}', [App\Http\Controllers\Public\LeadRpaRegistrationController::class, 'showRpaForm'])->name('public.lead.rpa.register');
    Route::post('/rpa', [App\Http\Controllers\Public\LeadRpaRegistrationController::class, 'store'])->name('public.lead.rpa.register.store');

    // Graphic Designing Registration Routes
    Route::get('/graphic-designing/{leadId?}', [App\Http\Controllers\Public\LeadGraphicDesigningRegistrationController::class, 'showGraphicDesigningForm'])->name('public.lead.graphic-designing.register');
    Route::post('/graphic-designing', [App\Http\Controllers\Public\LeadGraphicDesigningRegistrationController::class, 'store'])->name('public.lead.graphic-designing.register.store');
    Route::get('/graphic-designing/subjects', [App\Http\Controllers\Public\LeadGraphicDesigningRegistrationController::class, 'getSubjects'])->name('public.lead.graphic-designing.subjects');
    Route::get('/graphic-designing/batches', [App\Http\Controllers\Public\LeadGraphicDesigningRegistrationController::class, 'getBatches'])->name('public.lead.graphic-designing.batches');
    
    // Machine Learning Registration Routes
    Route::get('/machine-learning/{leadId?}', [App\Http\Controllers\Public\LeadMachineLearningRegistrationController::class, 'showMachineLearningForm'])->name('public.lead.machine-learning.register');
    Route::post('/machine-learning', [App\Http\Controllers\Public\LeadMachineLearningRegistrationController::class, 'store'])->name('public.lead.machine-learning.register.store');
    Route::get('/machine-learning/subjects', [App\Http\Controllers\Public\LeadMachineLearningRegistrationController::class, 'getSubjects'])->name('public.lead.machine-learning.subjects');
    Route::get('/machine-learning/batches', [App\Http\Controllers\Public\LeadMachineLearningRegistrationController::class, 'getBatches'])->name('public.lead.machine-learning.batches');
    
    // Flutter Registration Routes
    Route::get('/flutter/{leadId?}', [App\Http\Controllers\Public\LeadFlutterRegistrationController::class, 'showFlutterForm'])->name('public.lead.flutter.register');
    Route::post('/flutter', [App\Http\Controllers\Public\LeadFlutterRegistrationController::class, 'store'])->name('public.lead.flutter.register.store');
    Route::get('/flutter/subjects', [App\Http\Controllers\Public\LeadFlutterRegistrationController::class, 'getSubjects'])->name('public.lead.flutter.subjects');
    Route::get('/flutter/batches', [App\Http\Controllers\Public\LeadFlutterRegistrationController::class, 'getBatches'])->name('public.lead.flutter.batches');
    
    // B2B Team Registration Routes
    Route::get('/team/{teamId}', [App\Http\Controllers\Public\TeamRegistrationController::class, 'showForm'])->name('public.team.register');
    Route::post('/team/{teamId}', [App\Http\Controllers\Public\TeamRegistrationController::class, 'store'])->name('public.team.register.store');
    Route::get('/team/{teamId}/success', [App\Http\Controllers\Public\TeamRegistrationController::class, 'showSuccess'])->name('public.team.register.success');
});


// Bulk upload form should be protected - moved back to protected routes

// Protected routes
Route::middleware(['custom.auth', 'telecaller.tracking'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index');
    Route::get('/revenue/team/{teamId}', [RevenueController::class, 'teamDetails'])->name('revenue.team.details');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Leads
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/data', [LeadController::class, 'getLeadsData'])->name('leads.data');
    Route::get('leads/duplicate', [LeadController::class, 'duplicateLeads'])->name('leads.duplicate');
    Route::get('leads/duplicate/data', [LeadController::class, 'getDuplicateLeadsData'])->name('leads.duplicate-data');
    Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::get('followup-leads', [LeadController::class, 'followupLeads'])->name('leads.followup');
    Route::get('registration-form-submitted-leads', [LeadController::class, 'registrationFormSubmittedLeads'])->name('leads.registration-form-submitted');
    Route::get('/leads-add', [LeadController::class, 'ajax_add'])->name('leads.add');
    Route::post('/leads-submit', [LeadController::class, 'submit'])->name('leads.submit');
    Route::get('/leads/bulk-upload-form', [LeadController::class, 'bulkUploadView'])->name('leads.bulk-upload.test');
    Route::get('/leads/bulk-upload-template', [LeadController::class, 'downloadTemplate'])->name('leads.bulk-upload.template');
    Route::post('/leads/bulk-upload', [LeadController::class, 'bulkUploadSubmit'])->name('leads.bulk-upload.submit');

    // Specific lead routes (must come before generic {lead} route)
    Route::get('leads/{lead}/ajax-show', [LeadController::class, 'ajax_show'])->name('leads.ajax-show');
    Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::get('leads/{lead}/ajax-edit', [LeadController::class, 'ajax_edit'])->name('leads.ajax-edit');
    Route::get('leads/{lead}/status-update', [LeadController::class, 'status_update'])->name('leads.status-update');
    Route::post('leads/{lead}/status-update', [LeadController::class, 'status_update_submit'])->name('leads.status-update-submit');
    Route::get('/leads/{lead}/history', [LeadController::class, 'history'])->name('leads.history');
    Route::get('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convertSubmit'])->name('leads.convert.submit');
    Route::get('leads/{lead}/call-logs', [VoxbayCallLogController::class, 'list'])->name('leads.call-logs');
    Route::get('/leads/{lead}/registration-details', [LeadController::class, 'getLeadRegistrationDetails'])->name('leads.registration-details');
    Route::get('/leads/{lead}/plus-two-questionnaire', [LeadController::class, 'plusTwoQuestionnaireDetails'])->name('leads.plus-two-questionnaire');
    Route::get('/leads/{lead}/approve-modal', [LeadController::class, 'showApproveModal'])->name('leads.approve-modal');
    Route::get('/leads/{lead}/reject-modal', [LeadController::class, 'showRejectModal'])->name('leads.reject-modal');
    Route::post('/leads/{lead}/registration-status', [LeadController::class, 'updateRegistrationStatus'])->name('leads.update-registration-status');

    // Generic lead routes (must come after specific routes)
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

    // Registration status update route
    Route::post('leads/update-registration-status', [LeadController::class, 'updateRegistrationStatus'])->name('leads.update-lead-registration-status');

    // Document verification route
    Route::post('leads/update-document-verification', [LeadController::class, 'updateDocumentVerification'])->name('leads.update-document-verification');

    // SSLC certificate verification route
    Route::post('leads/verify-sslc-certificate', [LeadController::class, 'verifySSLCertificate'])->name('leads.verify-sslc-certificate');
    Route::post('leads/update-registration-details', [LeadController::class, 'updateRegistrationDetails'])->name('leads.update-registration-details');
    Route::post('leads/remove-sslc-certificate', [LeadController::class, 'removeSSLCertificate'])->name('leads.remove-sslc-certificate');
    Route::post('leads/add-sslc-certificate', [LeadController::class, 'addSSLCCertificates'])->name('leads.add-sslc-certificate');

    // API routes for AJAX calls
    Route::prefix('api')->group(function () {
        Route::get('/leads/phone', [LeadController::class, 'getByPhone']);
        Route::get('/leads/telecallers-by-team', [LeadController::class, 'getTelecallersByTeam'])->name('leads.telecallers-by-team');
        Route::get('/batches/by-course/{courseId}', [App\Http\Controllers\BatchController::class, 'getByCourse'])->name('batches.by-course');
        Route::get('/academic-assistants', [App\Http\Controllers\AcademicAssistantController::class, 'getAll'])->name('academic-assistants.all');

        // Voxbay API routes (duplicates removed - already defined in public routes)

        // Call logs API routes
        Route::get('/call-logs', [VoxbayCallLogController::class, 'ajaxList'])->name('call-logs.ajax-list');
        Route::get('/call-logs/statistics', [VoxbayCallLogController::class, 'statistics'])->name('call-logs.statistics');

        // Telecaller Tracking API routes
        Route::prefix('telecaller-tracking')->group(function () {
            Route::post('/start-idle', [App\Http\Controllers\TelecallerTrackingController::class, 'startIdleTime'])->name('telecaller-tracking.start-idle');
            Route::post('/end-idle', [App\Http\Controllers\TelecallerTrackingController::class, 'endIdleTime'])->name('telecaller-tracking.end-idle');
            Route::post('/sync-idle', [App\Http\Controllers\TelecallerTrackingController::class, 'syncIdleTime'])->name('telecaller-tracking.sync-idle');
            Route::post('/log-activity', [App\Http\Controllers\TelecallerTrackingController::class, 'logActivity'])->name('telecaller-tracking.log-activity');
            Route::get('/current-session', [App\Http\Controllers\TelecallerTrackingController::class, 'getCurrentSession'])->name('telecaller-tracking.current-session');
            Route::post('/auto-logout', [App\Http\Controllers\TelecallerTrackingController::class, 'autoLogout'])->name('telecaller-tracking.auto-logout');
            Route::post('/working-hours-logout', [App\Http\Controllers\TelecallerTrackingController::class, 'workingHoursLogout'])->name('telecaller-tracking.working-hours-logout');
        });

        // API routes for universities
        Route::get('/api/universities/{id}', [UniversityController::class, 'getUniversityData']);
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::delete('/lead-statuses-delete/{id}', [LeadStatusController::class, 'delete'])->name('lead-statuses.delete');
        Route::resource('lead-statuses', LeadStatusController::class)->except(['create', 'edit']);
        Route::get('/lead-statuses-add', [LeadStatusController::class, 'ajax_add'])->name('lead-statuses.add');
        Route::get('/lead-statuses-edit/{id}', [LeadStatusController::class, 'ajax_edit'])->name('lead-statuses.edit');
        Route::post('/lead-statuses-submit', [LeadStatusController::class, 'submit'])->name('lead-statuses.submit');
        Route::put('/lead-statuses-update/{id}', [LeadStatusController::class, 'update'])->name('lead-statuses.update');

        Route::resource('lead-sources', LeadSourceController::class)->except(['create', 'edit']);
        Route::get('/lead-sources-add', [LeadSourceController::class, 'ajax_add'])->name('lead-sources.add');
        Route::get('/lead-sources-edit/{id}', [LeadSourceController::class, 'ajax_edit'])->name('lead-sources.edit');
        Route::post('/lead-sources-submit', [LeadSourceController::class, 'submit'])->name('lead-sources.submit');
        Route::put('/lead-sources-update/{leadSource}', [LeadSourceController::class, 'update'])->name('lead-sources.update');
        Route::delete('/lead-sources-delete/{id}', [LeadSourceController::class, 'delete'])->name('lead-sources.delete');

        Route::resource('subject-areas', SubjectAreaController::class)->except(['create', 'edit']);
        Route::get('/subject-areas-add', [SubjectAreaController::class, 'ajax_add'])->name('subject-areas.add');
        Route::get('/subject-areas-edit/{id}', [SubjectAreaController::class, 'ajax_edit'])->name('subject-areas.edit');
        Route::post('/subject-areas-submit', [SubjectAreaController::class, 'submit'])->name('subject-areas.submit');
        Route::put('/subject-areas-update/{subjectArea}', [SubjectAreaController::class, 'update'])->name('subject-areas.update');
        Route::delete('/subject-areas-delete/{id}', [SubjectAreaController::class, 'delete'])->name('subject-areas.delete');

        Route::get('/mails', [CourseMailController::class, 'index'])->name('mails.index');
        Route::get('/mails-add', [CourseMailController::class, 'ajax_add'])->name('mails.add');
        Route::get('/mails-edit/{id}', [CourseMailController::class, 'ajax_edit'])->name('mails.edit');
        Route::post('/mails-submit', [CourseMailController::class, 'submit'])->name('mails.submit');
        Route::put('/mails-update/{id}', [CourseMailController::class, 'update'])->name('mails.update');
        Route::delete('/mails-delete/{id}', [CourseMailController::class, 'delete'])->name('mails.delete');

        Route::get('/flags', [FlagController::class, 'index'])->name('flags.index');
        Route::get('/flags-add', [FlagController::class, 'ajax_add'])->name('flags.add');
        Route::get('/flags-edit/{id}', [FlagController::class, 'ajax_edit'])->name('flags.edit');
        Route::post('/flags-submit', [FlagController::class, 'submit'])->name('flags.submit');
        Route::put('/flags-update/{id}', [FlagController::class, 'update'])->name('flags.update');
        Route::delete('/flags-delete/{id}', [FlagController::class, 'delete'])->name('flags.delete');

        Route::get('/support-flags', [SupportFlagController::class, 'index'])->name('support-flags.index');
        Route::get('/support-flags-add', [SupportFlagController::class, 'ajax_add'])->name('support-flags.add');
        Route::get('/support-flags-edit/{id}', [SupportFlagController::class, 'ajax_edit'])->name('support-flags.edit');
        Route::post('/support-flags-submit', [SupportFlagController::class, 'submit'])->name('support-flags.submit');
        Route::put('/support-flags-update/{id}', [SupportFlagController::class, 'update'])->name('support-flags.update');
        Route::delete('/support-flags-delete/{id}', [SupportFlagController::class, 'delete'])->name('support-flags.delete');

        Route::get('/course-flags', [CourseFlagController::class, 'index'])->name('course-flags.index');
        Route::get('/course-flags-add', [CourseFlagController::class, 'ajax_add'])->name('course-flags.add');
        Route::get('/course-flags-edit/{id}', [CourseFlagController::class, 'ajax_edit'])->name('course-flags.edit');
        Route::post('/course-flags-submit', [CourseFlagController::class, 'submit'])->name('course-flags.submit');
        Route::put('/course-flags-update/{id}', [CourseFlagController::class, 'update'])->name('course-flags.update');
        Route::delete('/course-flags-delete/{id}', [CourseFlagController::class, 'delete'])->name('course-flags.delete');

        Route::resource('universities', UniversityController::class)->except(['create', 'edit']);
        Route::get('/universities-add', [UniversityController::class, 'ajax_add'])->name('universities.add');
        Route::get('/universities-edit/{id}', [UniversityController::class, 'ajax_edit'])->name('universities.edit');
        Route::post('/universities-submit', [UniversityController::class, 'submit'])->name('universities.submit');

        // Registration Links Routes
        Route::resource('registration-links', App\Http\Controllers\RegistrationLinkController::class)->except(['create', 'edit']);
        Route::get('/registration-links-add', [App\Http\Controllers\RegistrationLinkController::class, 'ajax_add'])->name('registration-links.add');
        Route::get('/registration-links-edit/{id}', [App\Http\Controllers\RegistrationLinkController::class, 'ajax_edit'])->name('registration-links.edit');
        Route::post('/registration-links-submit', [App\Http\Controllers\RegistrationLinkController::class, 'submit'])->name('registration-links.submit');
        Route::put('/registration-links-update/{registrationLink}', [App\Http\Controllers\RegistrationLinkController::class, 'update_registration_link'])->name('registration-links.update');
        Route::delete('/registration-links-delete/{id}', [App\Http\Controllers\RegistrationLinkController::class, 'delete'])->name('registration-links.delete');
        Route::put('/universities-update/{university}', [UniversityController::class, 'update'])->name('universities.update');
        Route::delete('/universities-delete/{id}', [UniversityController::class, 'delete'])->name('universities.delete');

        // Online Teaching Faculty module
        Route::get('/online-teaching-faculties', [OnlineTeachingFacultyController::class, 'index'])->name('online-teaching-faculties.index');
        Route::get('/online-teaching-faculties/data', [OnlineTeachingFacultyController::class, 'getData'])->name('online-teaching-faculties.data');
        Route::get('/online-teaching-faculties-add', [OnlineTeachingFacultyController::class, 'ajax_add'])->name('online-teaching-faculties.add');
        Route::post('/online-teaching-faculties-submit', [OnlineTeachingFacultyController::class, 'submit'])->name('online-teaching-faculties.submit');
        Route::get('/online-teaching-faculties/{id}', [OnlineTeachingFacultyController::class, 'show'])->name('online-teaching-faculties.show');
        Route::post('/online-teaching-faculties/{id}/inline-update', [OnlineTeachingFacultyController::class, 'inlineUpdate'])->name('online-teaching-faculties.inline-update');
        Route::post('/online-teaching-faculties/{id}/upload-document', [OnlineTeachingFacultyController::class, 'uploadDocument'])->name('online-teaching-faculties.upload-document');
        Route::get('/online-teaching-faculties/{id}/generate-form-link', [OnlineTeachingFacultyController::class, 'generateFormToken'])->name('online-teaching-faculties.generate-form-link');
        Route::delete('/online-teaching-faculties/{id}', [OnlineTeachingFacultyController::class, 'delete'])->name('online-teaching-faculties.delete');

        // Call Analytics (Call Tracker app data)
        Route::prefix('call-analytics')->name('call-analytics.')->group(function () {
            Route::get('/', [CallAnalyticsController::class, 'index'])->name('index');
            Route::get('/report', [CallAnalyticsController::class, 'report'])->name('report');
            Route::get('/{call}/recording/stream', [CallAnalyticsController::class, 'streamRecording'])->name('recording.stream')->whereNumber('call');
            Route::get('/{call}/recording/download', [CallAnalyticsController::class, 'downloadRecording'])->name('recording.download')->whereNumber('call');
            Route::get('/{call}', [CallAnalyticsController::class, 'show'])->name('show')->whereNumber('call');
        });

        // Department Routes
        Route::get('/departments', [App\Http\Controllers\DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments-add', [App\Http\Controllers\DepartmentController::class, 'add'])->name('departments.add');
        Route::post('/departments-submit', [App\Http\Controllers\DepartmentController::class, 'submit'])->name('departments.submit');
        Route::get('/departments-edit/{id}', [App\Http\Controllers\DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/departments-update/{id}', [App\Http\Controllers\DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments-delete/{id}', [App\Http\Controllers\DepartmentController::class, 'delete'])->name('departments.delete');

        // University Courses Routes
        Route::resource('university-courses', App\Http\Controllers\UniversityCourseController::class)->except(['create', 'edit']);
        Route::get('/university-courses-add', [App\Http\Controllers\UniversityCourseController::class, 'ajax_add'])->name('university-courses.add');
        Route::get('/university-courses-edit/{id}', [App\Http\Controllers\UniversityCourseController::class, 'ajax_edit'])->name('university-courses.edit');
        Route::post('/university-courses-submit', [App\Http\Controllers\UniversityCourseController::class, 'submit'])->name('university-courses.submit');
        Route::put('/university-courses-update/{id}', [App\Http\Controllers\UniversityCourseController::class, 'update'])->name('university-courses.update');
        Route::delete('/university-courses-delete/{id}', [App\Http\Controllers\UniversityCourseController::class, 'delete'])->name('university-courses.delete');

        Route::resource('countries', CountryController::class)->except(['create', 'edit']);
        Route::get('/countries-add', [CountryController::class, 'ajax_add'])->name('countries.add');
        Route::get('/countries-edit/{id}', [CountryController::class, 'ajax_edit'])->name('countries.edit');
        Route::post('/countries-submit', [CountryController::class, 'submit'])->name('countries.submit');
        Route::put('/countries-update/{id}', [CountryController::class, 'update'])->name('countries.update');
        Route::delete('/countries-delete/{id}', [CountryController::class, 'delete'])->name('countries.delete');

        Route::delete('/boards-delete/{id}', [App\Http\Controllers\BoardController::class, 'delete'])->name('boards.delete');
        Route::resource('boards', App\Http\Controllers\BoardController::class)->except(['create', 'edit']);
        Route::get('/boards-add', [App\Http\Controllers\BoardController::class, 'ajax_add'])->name('boards.add');
        Route::get('/boards-edit/{id}', [App\Http\Controllers\BoardController::class, 'ajax_edit'])->name('boards.edit');
        Route::post('/boards-submit', [App\Http\Controllers\BoardController::class, 'submit'])->name('boards.submit');
        Route::put('/boards-update/{id}', [App\Http\Controllers\BoardController::class, 'update'])->name('boards.update');

        Route::delete('/batches-delete/{id}', [App\Http\Controllers\BatchController::class, 'delete'])->name('batches.delete');
        Route::resource('batches', App\Http\Controllers\BatchController::class)->except(['create', 'edit']);
        Route::get('/batches-add', [App\Http\Controllers\BatchController::class, 'ajax_add'])->name('batches.add');
        Route::get('/batches-edit/{id}', [App\Http\Controllers\BatchController::class, 'ajax_edit'])->name('batches.edit');
        Route::get('/batches-postpone/{id}', [App\Http\Controllers\BatchController::class, 'ajax_postpone'])->name('batches.postpone');
        Route::post('/batches-postpone/{id}', [App\Http\Controllers\BatchController::class, 'postpone_submit'])->name('batches.postpone.submit');
        Route::post('/batches-submit', [App\Http\Controllers\BatchController::class, 'submit'])->name('batches.submit');
        Route::put('/batches-update/{id}', [App\Http\Controllers\BatchController::class, 'update'])->name('batches.update');

        Route::delete('/admission-batches-delete/{id}', [App\Http\Controllers\AdmissionBatchController::class, 'delete'])->name('admission-batches.delete');
        Route::resource('admission-batches', App\Http\Controllers\AdmissionBatchController::class)->except(['create', 'edit']);
        Route::get('/admission-batches-add', [App\Http\Controllers\AdmissionBatchController::class, 'ajax_add'])->name('admission-batches.add');
        Route::get('/admission-batches-edit/{id}', [App\Http\Controllers\AdmissionBatchController::class, 'ajax_edit'])->name('admission-batches.edit');
        Route::post('/admission-batches-submit', [App\Http\Controllers\AdmissionBatchController::class, 'submit'])->name('admission-batches.submit');
        Route::put('/admission-batches-update/{id}', [App\Http\Controllers\AdmissionBatchController::class, 'update'])->name('admission-batches.update');

        Route::resource('courses', CourseController::class)->except(['create', 'edit']);
        Route::get('/courses-add', [CourseController::class, 'ajax_add'])->name('courses.add');
        Route::get('/courses-edit/{id}', [CourseController::class, 'ajax_edit'])->name('courses.edit');
        Route::post('/courses-submit', [CourseController::class, 'submit'])->name('courses.submit');
        Route::put('/courses-update/{id}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses-delete/{id}', [CourseController::class, 'delete'])->name('courses.delete');

        // Sub Courses Routes (Course-like modal pattern)
        Route::resource('sub-courses', App\Http\Controllers\SubCourseController::class)->except(['create', 'edit', 'update', 'store']);
        Route::get('/sub-courses-add', [App\Http\Controllers\SubCourseController::class, 'ajax_add'])->name('sub-courses.add');
        Route::get('/sub-courses-edit/{id}', [App\Http\Controllers\SubCourseController::class, 'ajax_edit'])->name('sub-courses.edit');
        Route::post('/sub-courses-submit', [App\Http\Controllers\SubCourseController::class, 'submit'])->name('sub-courses.submit');
        Route::put('/sub-courses-update/{id}', [App\Http\Controllers\SubCourseController::class, 'updateForm'])->name('sub-courses.updateForm');

        Route::delete('/subjects-delete/{id}', [App\Http\Controllers\SubjectController::class, 'delete'])->name('subjects.delete');
        Route::resource('subjects', App\Http\Controllers\SubjectController::class)->except(['create', 'edit']);
        Route::get('/subjects-add', [App\Http\Controllers\SubjectController::class, 'ajax_add'])->name('subjects.add');
        Route::get('/subjects-edit/{id}', [App\Http\Controllers\SubjectController::class, 'ajax_edit'])->name('subjects.edit');
        Route::post('/subjects-submit', [App\Http\Controllers\SubjectController::class, 'submit'])->name('subjects.submit');
        Route::put('/subjects-update/{id}', [App\Http\Controllers\SubjectController::class, 'update'])->name('subjects.update');

        // Class Times Routes
        Route::resource('class-times', App\Http\Controllers\ClassTimeController::class)->except(['create', 'edit']);
        Route::get('/class-times-add', [App\Http\Controllers\ClassTimeController::class, 'ajax_add'])->name('class-times.add');
        Route::get('/class-times-edit/{id}', [App\Http\Controllers\ClassTimeController::class, 'ajax_edit'])->name('class-times.edit');
        Route::post('/class-times-submit', [App\Http\Controllers\ClassTimeController::class, 'submit'])->name('class-times.submit');
        Route::put('/class-times-update/{id}', [App\Http\Controllers\ClassTimeController::class, 'update'])->name('class-times.update');
        Route::delete('/class-times-delete/{id}', [App\Http\Controllers\ClassTimeController::class, 'delete'])->name('class-times.delete');

        // Offline Places Routes
        Route::resource('offline-places', App\Http\Controllers\OfflinePlaceController::class)->except(['create', 'edit']);
        Route::get('/offline-places-add', [App\Http\Controllers\OfflinePlaceController::class, 'ajax_add'])->name('offline-places.add');
        Route::get('/offline-places-edit/{id}', [App\Http\Controllers\OfflinePlaceController::class, 'ajax_edit'])->name('offline-places.edit');
        Route::post('/offline-places-submit', [App\Http\Controllers\OfflinePlaceController::class, 'submit'])->name('offline-places.submit');
        Route::put('/offline-places-update/{id}', [App\Http\Controllers\OfflinePlaceController::class, 'update'])->name('offline-places.update');
        Route::delete('/offline-places-delete/{id}', [App\Http\Controllers\OfflinePlaceController::class, 'delete'])->name('offline-places.delete');

        Route::resource('teams', TeamController::class)->except(['create', 'edit']);
        Route::get('/teams-add', [TeamController::class, 'ajax_add'])->name('teams.add');
        Route::get('/teams-edit/{id}', [TeamController::class, 'ajax_edit'])->name('teams.edit');
        Route::post('/teams-submit', [TeamController::class, 'submit'])->name('teams.submit');
        Route::put('/teams-update/{id}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/teams-delete/{id}', [TeamController::class, 'delete'])->name('teams.delete');
        Route::get('/teams-members/{id}', [TeamController::class, 'members'])->name('teams.members');
        Route::post('/teams-remove-member', [TeamController::class, 'removeMember'])->name('teams.remove-member');
        Route::post('/teams-add-member', [TeamController::class, 'addMember'])->name('teams.add-member');
        Route::get('/teams/{id}/details', [TeamController::class, 'showDetails'])->name('teams.details');
        Route::post('/teams/{id}/update-details', [TeamController::class, 'updateDetails'])->name('teams.update-details');
        Route::get('/teams/{id}/export-details-pdf', [TeamController::class, 'exportDetailsPdf'])->name('teams.export-details-pdf');
        Route::get('/teams/{id}/terms-and-conditions', [TeamController::class, 'termsAndConditions'])->name('teams.terms-and-conditions');
        Route::post('/teams/{id}/update-terms-and-conditions', [TeamController::class, 'updateTermsAndConditions'])->name('teams.update-terms-and-conditions');

        // B2B Services Routes
        Route::resource('b2b-services', App\Http\Controllers\B2bServiceController::class)->except(['create', 'edit']);
        Route::get('/b2b-services-add', [App\Http\Controllers\B2bServiceController::class, 'ajax_add'])->name('b2b-services.add');
        Route::get('/b2b-services-edit/{id}', [App\Http\Controllers\B2bServiceController::class, 'ajax_edit'])->name('b2b-services.edit');
        Route::post('/b2b-services-submit', [App\Http\Controllers\B2bServiceController::class, 'submit'])->name('b2b-services.submit');
        Route::put('/b2b-services-update/{id}', [App\Http\Controllers\B2bServiceController::class, 'update'])->name('b2b-services.update');
        Route::delete('/b2b-services-delete/{id}', [App\Http\Controllers\B2bServiceController::class, 'delete'])->name('b2b-services.delete');

        // Academic Delivery Structure Routes
        Route::get('/academic-delivery-structures', [App\Http\Controllers\AcademicDeliveryStructureController::class, 'index'])->name('academic-delivery-structures.index');
        Route::get('/academic-delivery-structures-add', [App\Http\Controllers\AcademicDeliveryStructureController::class, 'ajax_add'])->name('academic-delivery-structures.add');
        Route::get('/academic-delivery-structures-edit/{id}', [App\Http\Controllers\AcademicDeliveryStructureController::class, 'ajax_edit'])->name('academic-delivery-structures.edit');
        Route::get('/academic-delivery-structures-view/{id}', [App\Http\Controllers\AcademicDeliveryStructureController::class, 'ajax_view'])->name('academic-delivery-structures.view');
        Route::post('/academic-delivery-structures-submit', [App\Http\Controllers\AcademicDeliveryStructureController::class, 'submit'])->name('academic-delivery-structures.submit');
        Route::put('/academic-delivery-structures-update/{id}', [App\Http\Controllers\AcademicDeliveryStructureController::class, 'update'])->name('academic-delivery-structures.update');
        Route::delete('/academic-delivery-structures-delete/{id}', [App\Http\Controllers\AcademicDeliveryStructureController::class, 'delete'])->name('academic-delivery-structures.delete');


        Route::resource('telecallers', TelecallerController::class)->except(['create', 'edit']);
        Route::get('/telecallers/{id}/edit', [TelecallerController::class, 'ajax_edit'])->name('telecallers.edit');
        Route::get('/telecallers-add', [TelecallerController::class, 'ajax_add'])->name('telecallers.add');
        Route::post('/telecallers-submit', [TelecallerController::class, 'submit'])->name('telecallers.submit');
        Route::put('/telecallers-update/{id}', [TelecallerController::class, 'update'])->name('telecallers.update');
        Route::delete('/telecallers-delete/{id}', [TelecallerController::class, 'delete'])->name('telecallers.delete');
        Route::get('/telecallers-change-password/{id}', [TelecallerController::class, 'changePassword'])->name('telecallers.change-password');
        Route::post('/telecallers-update-password/{id}', [TelecallerController::class, 'updatePassword'])->name('telecallers.update-password');

        Route::resource('marketing', MarketingController::class)->except(['create', 'edit']);
        Route::get('/marketing-add', [MarketingController::class, 'ajax_add'])->name('marketing.add');
        Route::get('/marketing-edit/{id}', [MarketingController::class, 'ajax_edit'])->name('marketing.edit');
        Route::post('/marketing-submit', [MarketingController::class, 'submit'])->name('marketing.submit');
        Route::put('/marketing-update/{id}', [MarketingController::class, 'update'])->name('marketing.update');
        Route::delete('/marketing-delete/{id}', [MarketingController::class, 'delete'])->name('marketing.delete');
        Route::get('/marketing-change-password/{id}', [MarketingController::class, 'changePassword'])->name('marketing.change-password');
        Route::post('/marketing-update-password/{id}', [MarketingController::class, 'updatePassword'])->name('marketing.update-password');
        Route::get('/marketing-d2d-form', [MarketingController::class, 'd2dForm'])->name('marketing.d2d-form');
        Route::post('/marketing-d2d-submit', [MarketingController::class, 'd2dSubmit'])->name('marketing.d2d-submit');
        Route::post('/marketing-check-duplicate-phone', [MarketingController::class, 'checkDuplicatePhone'])->name('marketing.check-duplicate-phone');
        Route::get('/marketing-leads', [MarketingController::class, 'marketingLeads'])->name('marketing.marketing-leads');
        Route::get('/marketing-leads/data', [MarketingController::class, 'getMarketingLeadsData'])->name('marketing.marketing-leads.data');
        Route::get('/marketing-leads/export', [MarketingController::class, 'exportMarketingLeads'])->name('marketing.marketing-leads.export');
        Route::get('/marketing-leads/{id}/view', [MarketingController::class, 'viewMarketingLead'])->name('marketing.marketing-leads.view');
        Route::get('/marketing-leads/{id}/edit', [MarketingController::class, 'editMarketingLead'])->name('marketing.marketing-leads.edit');
        Route::put('/marketing-leads/{id}', [MarketingController::class, 'updateMarketingLead'])->name('marketing.marketing-leads.update');
        Route::get('/marketing-leads/{id}/assign', [MarketingController::class, 'ajaxAssign'])->name('marketing.assign-to-telecaller.ajax');
        Route::post('/marketing-leads/{id}/assign', [MarketingController::class, 'assignToTelecaller'])->name('marketing.assign-to-telecaller');
        Route::get('/marketing-leads/bulk-assign', [MarketingController::class, 'ajaxBulkAssign'])->name('marketing.bulk-assign.ajax');
        Route::post('/marketing-leads/bulk-assign', [MarketingController::class, 'bulkAssign'])->name('marketing.bulk-assign.submit');
        Route::post('/marketing-leads/get-by-filters-assign', [MarketingController::class, 'getMarketingLeadsByFiltersAssign'])->name('marketing.get-by-filters-assign');

        // Teacher routes (role_id = 10)
        Route::resource('teachers', App\Http\Controllers\TeacherController::class)->except(['create', 'edit']);
        Route::get('/teachers-add', [App\Http\Controllers\TeacherController::class, 'ajax_add'])->name('teachers.add');
        Route::get('/teachers-edit/{id}', [App\Http\Controllers\TeacherController::class, 'ajax_edit'])->name('teachers.edit');
        Route::post('/teachers-submit', [App\Http\Controllers\TeacherController::class, 'submit'])->name('teachers.submit');
        Route::put('/teachers-update/{id}', [App\Http\Controllers\TeacherController::class, 'updateForm'])->name('teachers.update-form');

        // Admission Counsellor routes (role_id = 4)
        Route::resource('admission-counsellors', App\Http\Controllers\AdmissionCounsellorController::class)->except(['create', 'edit']);
        Route::get('/admission-counsellors-add', [App\Http\Controllers\AdmissionCounsellorController::class, 'ajax_add'])->name('admission-counsellors.add');
        Route::get('/admission-counsellors-edit/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'ajax_edit'])->name('admission-counsellors.edit');
        Route::post('/admission-counsellors-submit', [App\Http\Controllers\AdmissionCounsellorController::class, 'submit'])->name('admission-counsellors.submit');
        Route::put('/admission-counsellors-update/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'update'])->name('admission-counsellors.update');
        Route::delete('/admission-counsellors-delete/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'delete'])->name('admission-counsellors.delete');
        Route::get('/admission-counsellors-change-password/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'changePassword'])->name('admission-counsellors.change-password');
        Route::post('/admission-counsellors-update-password/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'updatePassword'])->name('admission-counsellors.update-password');

        // General Manager routes (role_id = 11)
        Route::resource('general-managers', App\Http\Controllers\GeneralManagerController::class)->except(['create', 'edit']);
        Route::get('/general-managers-add', [App\Http\Controllers\GeneralManagerController::class, 'ajax_add'])->name('general-managers.add');
        Route::get('/general-managers-edit/{id}', [App\Http\Controllers\GeneralManagerController::class, 'ajax_edit'])->name('general-managers.edit');
        Route::post('/general-managers-submit', [App\Http\Controllers\GeneralManagerController::class, 'submit'])->name('general-managers.submit');
        Route::put('/general-managers-update/{id}', [App\Http\Controllers\GeneralManagerController::class, 'update'])->name('general-managers.update');
        Route::delete('/general-managers-delete/{id}', [App\Http\Controllers\GeneralManagerController::class, 'delete'])->name('general-managers.delete');
        Route::get('/general-managers-change-password/{id}', [App\Http\Controllers\GeneralManagerController::class, 'changePassword'])->name('general-managers.change-password');
        Route::post('/general-managers-update-password/{id}', [App\Http\Controllers\GeneralManagerController::class, 'updatePassword'])->name('general-managers.update-password');

        // Auditor routes (role_id = 12)
        Route::resource('auditors', App\Http\Controllers\AuditorController::class)->except(['create', 'edit']);
        Route::get('/auditors-add', [App\Http\Controllers\AuditorController::class, 'ajax_add'])->name('auditors.add');
        Route::get('/auditors-edit/{id}', [App\Http\Controllers\AuditorController::class, 'ajax_edit'])->name('auditors.edit');
        Route::post('/auditors-submit', [App\Http\Controllers\AuditorController::class, 'submit'])->name('auditors.submit');
        Route::put('/auditors-update/{id}', [App\Http\Controllers\AuditorController::class, 'update'])->name('auditors.update');
        Route::delete('/auditors-delete/{id}', [App\Http\Controllers\AuditorController::class, 'delete'])->name('auditors.delete');
        Route::get('/auditors-change-password/{id}', [App\Http\Controllers\AuditorController::class, 'changePassword'])->name('auditors.change-password');
        Route::post('/auditors-update-password/{id}', [App\Http\Controllers\AuditorController::class, 'updatePassword'])->name('auditors.update-password');

        // Placement Manager routes (role_id = 15)
        Route::resource('placement-officers', App\Http\Controllers\PlacementOfficerController::class)->except(['create', 'edit']);
        Route::get('/placement-officers-add', [App\Http\Controllers\PlacementOfficerController::class, 'ajax_add'])->name('placement-officers.add');
        Route::get('/placement-officers-edit/{id}', [App\Http\Controllers\PlacementOfficerController::class, 'ajax_edit'])->name('placement-officers.edit');
        Route::post('/placement-officers-submit', [App\Http\Controllers\PlacementOfficerController::class, 'submit'])->name('placement-officers.submit');
        Route::put('/placement-officers-update/{id}', [App\Http\Controllers\PlacementOfficerController::class, 'update'])->name('placement-officers.update');
        Route::delete('/placement-officers-delete/{id}', [App\Http\Controllers\PlacementOfficerController::class, 'delete'])->name('placement-officers.delete');
        Route::get('/placement-officers-change-password/{id}', [App\Http\Controllers\PlacementOfficerController::class, 'changePassword'])->name('placement-officers.change-password');
        Route::post('/placement-officers-update-password/{id}', [App\Http\Controllers\PlacementOfficerController::class, 'updatePassword'])->name('placement-officers.update-password');

        // Academic Assistant routes (role_id = 5)
        Route::resource('academic-assistants', App\Http\Controllers\AcademicAssistantController::class)->except(['create', 'edit']);
        Route::get('/academic-assistants-add', [App\Http\Controllers\AcademicAssistantController::class, 'ajax_add'])->name('academic-assistants.add');
        Route::get('/academic-assistants-edit/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'ajax_edit'])->name('academic-assistants.edit');
        Route::post('/academic-assistants-submit', [App\Http\Controllers\AcademicAssistantController::class, 'submit'])->name('academic-assistants.submit');
        Route::put('/academic-assistants-update/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'update'])->name('academic-assistants.update');
        Route::delete('/academic-assistants-delete/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'delete'])->name('academic-assistants.delete');
        Route::get('/academic-assistants-change-password/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'changePassword'])->name('academic-assistants.change-password');
        Route::post('/academic-assistants-update-password/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'updatePassword'])->name('academic-assistants.update-password');

        // Finance routes (role_id = 6)
        Route::resource('finance', App\Http\Controllers\FinanceController::class)->except(['create', 'edit']);
        Route::get('/finance-add', [App\Http\Controllers\FinanceController::class, 'ajax_add'])->name('finance.add');
        Route::get('/finance-edit/{id}', [App\Http\Controllers\FinanceController::class, 'ajax_edit'])->name('finance.edit');
        Route::post('/finance-submit', [App\Http\Controllers\FinanceController::class, 'submit'])->name('finance.submit');
        Route::put('/finance-update/{id}', [App\Http\Controllers\FinanceController::class, 'update'])->name('finance.update');
        Route::delete('/finance-delete/{id}', [App\Http\Controllers\FinanceController::class, 'delete'])->name('finance.delete');
        Route::get('/finance-change-password/{id}', [App\Http\Controllers\FinanceController::class, 'changePassword'])->name('finance.change-password');
        Route::post('/finance-update-password/{id}', [App\Http\Controllers\FinanceController::class, 'updatePassword'])->name('finance.update-password');

        // HOD routes (role_id = 14)
        Route::resource('hod', App\Http\Controllers\HODController::class)->except(['create', 'edit']);
        Route::get('/hod-add', [App\Http\Controllers\HODController::class, 'ajax_add'])->name('hod.add');
        Route::get('/hod-edit/{id}', [App\Http\Controllers\HODController::class, 'ajax_edit'])->name('hod.edit');
        Route::post('/hod-submit', [App\Http\Controllers\HODController::class, 'submit'])->name('hod.submit');
        Route::put('/hod-update/{id}', [App\Http\Controllers\HODController::class, 'update'])->name('hod.update');
        Route::delete('/hod-delete/{id}', [App\Http\Controllers\HODController::class, 'delete'])->name('hod.delete');
        Route::get('/hod-change-password/{id}', [App\Http\Controllers\HODController::class, 'changePassword'])->name('hod.change-password');
        Route::post('/hod-update-password/{id}', [App\Http\Controllers\HODController::class, 'updatePassword'])->name('hod.update-password');

        // Support Team routes (role_id = 8)
        Route::resource('support-team', App\Http\Controllers\SupportTeamController::class)->except(['create', 'edit']);
        Route::get('/support-team-add', [App\Http\Controllers\SupportTeamController::class, 'ajax_add'])->name('support-team.add');
        Route::get('/support-team-edit/{id}', [App\Http\Controllers\SupportTeamController::class, 'ajax_edit'])->name('support-team.edit');
        Route::post('/support-team-submit', [App\Http\Controllers\SupportTeamController::class, 'submit'])->name('support-team.submit');
        Route::put('/support-team-update/{id}', [App\Http\Controllers\SupportTeamController::class, 'update'])->name('support-team.update');
        Route::delete('/support-team-delete/{id}', [App\Http\Controllers\SupportTeamController::class, 'delete'])->name('support-team.delete');
        Route::get('/support-team-change-password/{id}', [App\Http\Controllers\SupportTeamController::class, 'changePassword'])->name('support-team.change-password');
        Route::post('/support-team-update-password/{id}', [App\Http\Controllers\SupportTeamController::class, 'updatePassword'])->name('support-team.update-password');

        // Mentor routes (role_id = 9)
        Route::resource('mentor', App\Http\Controllers\MentorController::class)->except(['create', 'edit']);
        Route::get('/mentor-add', [App\Http\Controllers\MentorController::class, 'ajax_add'])->name('mentor.add');
        Route::get('/mentor-edit/{id}', [App\Http\Controllers\MentorController::class, 'ajax_edit'])->name('mentor.edit');
        Route::post('/mentor-submit', [App\Http\Controllers\MentorController::class, 'submit'])->name('mentor.submit');
        Route::put('/mentor-update/{id}', [App\Http\Controllers\MentorController::class, 'update'])->name('mentor.update');
        Route::delete('/mentor-delete/{id}', [App\Http\Controllers\MentorController::class, 'delete'])->name('mentor.delete');
        Route::get('/mentor-change-password/{id}', [App\Http\Controllers\MentorController::class, 'changePassword'])->name('mentor.change-password');
        Route::post('/mentor-update-password/{id}', [App\Http\Controllers\MentorController::class, 'updatePassword'])->name('mentor.update-password');

        // Faculty routes (role_id = 16)
        Route::resource('faculty', App\Http\Controllers\FacultyController::class)->except(['create', 'edit']);
        Route::get('/faculty-add', [App\Http\Controllers\FacultyController::class, 'ajax_add'])->name('faculty.add');
        Route::get('/faculty-edit/{id}', [App\Http\Controllers\FacultyController::class, 'ajax_edit'])->name('faculty.edit');
        Route::post('/faculty-submit', [App\Http\Controllers\FacultyController::class, 'submit'])->name('faculty.submit');
        Route::put('/faculty-update/{id}', [App\Http\Controllers\FacultyController::class, 'update'])->name('faculty.update');
        Route::delete('/faculty-delete/{id}', [App\Http\Controllers\FacultyController::class, 'delete'])->name('faculty.delete');
        Route::get('/faculty-change-password/{id}', [App\Http\Controllers\FacultyController::class, 'changePassword'])->name('faculty.change-password');
        Route::post('/faculty-update-password/{id}', [App\Http\Controllers\FacultyController::class, 'updatePassword'])->name('faculty.update-password');

        // Post-sales routes (role_id = 7)
        Route::resource('post-sales', App\Http\Controllers\PostSalesController::class)->except(['create', 'edit']);
        Route::get('/post-sales-add', [App\Http\Controllers\PostSalesController::class, 'ajax_add'])->name('post-sales.add');
        Route::get('/post-sales-edit/{id}', [App\Http\Controllers\PostSalesController::class, 'ajax_edit'])->name('post-sales.edit');
        Route::post('/post-sales-submit', [App\Http\Controllers\PostSalesController::class, 'submit'])->name('post-sales.submit');
        Route::put('/post-sales-update/{id}', [App\Http\Controllers\PostSalesController::class, 'update'])->name('post-sales.update');
        Route::delete('/post-sales-delete/{id}', [App\Http\Controllers\PostSalesController::class, 'delete'])->name('post-sales.delete');
        Route::get('/post-sales-change-password/{id}', [App\Http\Controllers\PostSalesController::class, 'changePassword'])->name('post-sales.change-password');
        Route::post('/post-sales-update-password/{id}', [App\Http\Controllers\PostSalesController::class, 'updatePassword'])->name('post-sales.update-password');

        Route::resource('user-roles', UserRoleController::class);
        Route::resource('settings', SettingsController::class);

        // Website Settings routes (must be after resource routes to avoid conflicts)
        Route::get('/website-settings', [App\Http\Controllers\SettingController::class, 'index'])->name('website.settings');
        Route::post('/settings/update-logo', [App\Http\Controllers\SettingController::class, 'updateLogo'])->name('website.settings.update-logo');
        Route::post('/settings/update-favicon', [App\Http\Controllers\SettingController::class, 'updateFavicon'])->name('website.settings.update-favicon');
        Route::post('/settings/update-site-settings', [App\Http\Controllers\SettingController::class, 'updateSiteSettings'])->name('website.settings.update-site-settings');
        Route::post('/settings/update-bg-image', [App\Http\Controllers\SettingController::class, 'updateBackgroundImage'])->name('website.settings.update-bg-image');
        Route::post('/settings/remove-bg-image', [App\Http\Controllers\SettingController::class, 'removeBackgroundImage'])->name('website.settings.remove-bg-image');

        // Call App Settings
        Route::get('/call-app-settings', [App\Http\Controllers\CallAppSettingController::class, 'index'])->name('call-app.settings');
        Route::post('/call-app-settings', [App\Http\Controllers\CallAppSettingController::class, 'update'])->name('call-app.settings.update');
        Route::post('/call-app-settings/remove-apk', [App\Http\Controllers\CallAppSettingController::class, 'removeApk'])->name('call-app.settings.remove-apk');

        // CRM App Settings
        Route::get('/crm-app-settings', [App\Http\Controllers\CrmAppSettingController::class, 'index'])->name('crm-app.settings');
        Route::post('/crm-app-settings', [App\Http\Controllers\CrmAppSettingController::class, 'update'])->name('crm-app.settings.update');
        Route::post('/crm-app-settings/remove-apk', [App\Http\Controllers\CrmAppSettingController::class, 'removeApk'])->name('crm-app.settings.remove-apk');

        // Reports routes
        Route::get('/reports/leads', [App\Http\Controllers\LeadReportController::class, 'index'])->name('reports.leads');
        Route::get('/reports/lead-status', [App\Http\Controllers\LeadReportController::class, 'leadStatusReport'])->name('reports.lead-status');
        Route::get('/reports/lead-source', [App\Http\Controllers\LeadReportController::class, 'leadSourceReport'])->name('reports.lead-source');
        Route::get('/reports/team', [App\Http\Controllers\LeadReportController::class, 'teamReport'])->name('reports.team');
        Route::get('/reports/telecaller', [App\Http\Controllers\LeadReportController::class, 'telecallerReport'])->name('reports.telecaller');
        Route::get('/reports/b2b', [App\Http\Controllers\LeadReportController::class, 'b2bReport'])->name('reports.b2b');

        // Voxbay Call Logs Report routes
        Route::get('/reports/voxbay-call-logs', [App\Http\Controllers\VoxbayReportController::class, 'index'])->name('reports.voxbay-call-logs');
        Route::get('/reports/voxbay-call-logs/export/excel', [App\Http\Controllers\VoxbayReportController::class, 'exportExcel'])->name('reports.voxbay-call-logs.export.excel');
        Route::get('/reports/voxbay-call-logs/export/pdf', [App\Http\Controllers\VoxbayReportController::class, 'exportPdf'])->name('reports.voxbay-call-logs.export.pdf');

        // Course Reports routes
        Route::get('/reports/course-summary', [App\Http\Controllers\CourseReportController::class, 'index'])->name('reports.course-summary');
        Route::get('/reports/course/{courseId}/leads', [App\Http\Controllers\CourseReportController::class, 'courseLeads'])->name('reports.course-leads');
        Route::get('/reports/course/{courseId}/converted-leads', [App\Http\Controllers\CourseReportController::class, 'courseConvertedLeads'])->name('reports.course-converted-leads');
        Route::get('/reports/course-summary/export/excel', [App\Http\Controllers\CourseReportController::class, 'exportCourseSummaryExcel'])->name('reports.course-summary.excel');
        Route::get('/reports/course-summary/export/pdf', [App\Http\Controllers\CourseReportController::class, 'exportCourseSummaryPdf'])->name('reports.course-summary.pdf');

        // Post Sales Reports routes
        Route::get('/reports/post-sales-month-ways', [App\Http\Controllers\PostSalesReportController::class, 'postSalesMonthWaysReport'])->name('reports.post-sales-month-ways');
        Route::get('/reports/post-sales-month-ways/export/pdf', [App\Http\Controllers\PostSalesReportController::class, 'exportPostSalesMonthWaysPdf'])->name('reports.post-sales-month-ways.export.pdf');
        Route::get('/reports/total-monthly', [App\Http\Controllers\PostSalesReportController::class, 'totalMonthlyReport'])->name('reports.total-monthly');
        Route::get('/reports/total-monthly/export/pdf', [App\Http\Controllers\PostSalesReportController::class, 'exportTotalMonthlyPdf'])->name('reports.total-monthly.export.pdf');
        Route::get('/reports/bde-collected-amount-course-ways', [App\Http\Controllers\PostSalesReportController::class, 'bdeCollectedAmountCourseWaysReport'])->name('reports.bde-collected-amount-course-ways');
        Route::get('/reports/bde-collected-amount-course-ways/export/pdf', [App\Http\Controllers\PostSalesReportController::class, 'exportBdeCollectedAmountCourseWaysPdf'])->name('reports.bde-collected-amount-course-ways.export.pdf');
        
        // Finance Reports routes
        Route::get('/reports/telecallers-sales', [App\Http\Controllers\PostSalesReportController::class, 'telecallersSalesReport'])->name('reports.telecallers-sales');
        Route::get('/reports/telecallers-sales/export/pdf', [App\Http\Controllers\PostSalesReportController::class, 'exportTelecallersSalesPdf'])->name('reports.telecallers-sales.export.pdf');
        Route::get('/reports/thanzeels-eschool-sales', [App\Http\Controllers\PostSalesReportController::class, 'thanzeelsEschoolSalesReport'])->name('reports.thanzeels-eschool-sales');
        Route::get('/reports/thanzeels-eschool-sales/export/pdf', [App\Http\Controllers\PostSalesReportController::class, 'exportThanzeelsEschoolSalesPdf'])->name('reports.thanzeels-eschool-sales.export.pdf');
        Route::get('/reports/telecallers-sales/converted-leads', [App\Http\Controllers\PostSalesReportController::class, 'telecallersSalesConvertedLeads'])->name('reports.telecallers-sales.converted-leads');
        Route::get('/reports/thanzeels-eschool-sales/converted-leads', [App\Http\Controllers\PostSalesReportController::class, 'thanzeelsEschoolConvertedLeads'])->name('reports.thanzeels-eschool-sales.converted-leads');
        Route::get('/reports/course-wise-sales', [App\Http\Controllers\PostSalesReportController::class, 'courseWiseSalesReport'])->name('reports.course-wise-sales');
        Route::get('/reports/course-wise-sales/export/pdf', [App\Http\Controllers\PostSalesReportController::class, 'exportCourseWiseSalesPdf'])->name('reports.course-wise-sales.export.pdf');

        // Export routes
        Route::get('/reports/lead-status/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportLeadStatusExcel'])->name('reports.lead-status.excel');
        Route::get('/reports/lead-status/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportLeadStatusPdf'])->name('reports.lead-status.pdf');
        Route::get('/reports/lead-source/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportLeadSourceExcel'])->name('reports.lead-source.excel');
        Route::get('/reports/lead-source/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportLeadSourcePdf'])->name('reports.lead-source.pdf');
        Route::get('/reports/team/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportTeamExcel'])->name('reports.team.excel');
        Route::get('/reports/team/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportTeamPdf'])->name('reports.team.pdf');
        Route::get('/reports/telecaller/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportTelecallerExcel'])->name('reports.telecaller.excel');
        Route::get('/reports/telecaller/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportTelecallerPdf'])->name('reports.telecaller.pdf');
        Route::get('/reports/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportMainReportsExcel'])->name('reports.main.excel');
        Route::get('/reports/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportMainReportsPdf'])->name('reports.main.pdf');

        // Support Ajax Converted Leads (Moved Here)
        Route::get('/support-ajax-converted-leads', [App\Http\Controllers\SupportAjaxController::class, 'index'])->name('support-ajax-converted-leads.index');
        Route::get('/support-ajax-converted-leads/data', [App\Http\Controllers\SupportAjaxController::class, 'getData'])->name('support-ajax-converted-leads.data');
        Route::get('/support-ajax-converted-leads/{id}', [App\Http\Controllers\SupportConvertedLeadController::class, 'showAjax'])->name('support-ajax-converted-leads.details');

        // New Super Admin Reports Routes
        Route::middleware(['super.admin'])->group(function () {
            // Lead Source Efficiency Report
            Route::get('/reports/lead-efficiency', [App\Http\Controllers\LeadEfficiencyReportController::class, 'index'])->name('reports.lead-efficiency');
            Route::get('/reports/lead-efficiency/export/excel', [App\Http\Controllers\LeadEfficiencyReportController::class, 'exportExcel'])->name('reports.lead-efficiency.export.excel');
            Route::get('/reports/lead-efficiency/export/pdf', [App\Http\Controllers\LeadEfficiencyReportController::class, 'exportPdf'])->name('reports.lead-efficiency.export.pdf');

            // Lead Stage Movement Report
            Route::get('/reports/lead-stage-movement', [App\Http\Controllers\LeadStageReportController::class, 'index'])->name('reports.lead-stage-movement');
            Route::get('/reports/lead-stage-movement/export/excel', [App\Http\Controllers\LeadStageReportController::class, 'exportExcel'])->name('reports.lead-stage-movement.export.excel');
            Route::get('/reports/lead-stage-movement/export/pdf', [App\Http\Controllers\LeadStageReportController::class, 'exportPdf'])->name('reports.lead-stage-movement.export.pdf');

            // Lead Aging Report
            Route::get('/reports/lead-aging', [App\Http\Controllers\LeadAgingReportController::class, 'index'])->name('reports.lead-aging');
            Route::get('/reports/lead-aging/export/excel', [App\Http\Controllers\LeadAgingReportController::class, 'exportExcel'])->name('reports.lead-aging.export.excel');
            Route::get('/reports/lead-aging/export/pdf', [App\Http\Controllers\LeadAgingReportController::class, 'exportPdf'])->name('reports.lead-aging.export.pdf');
            Route::get('/reports/lead-detail/{leadId}', [App\Http\Controllers\LeadAgingReportController::class, 'leadDetail'])->name('reports.lead-detail');

            // Team-Wise Detailed Report
            Route::get('/reports/team-wise', [App\Http\Controllers\TeamWiseReportController::class, 'index'])->name('reports.team-wise');
            Route::get('/reports/team-wise/detail', [App\Http\Controllers\TeamWiseReportController::class, 'teamDetail'])->name('reports.team-wise.detail');
            Route::get('/reports/team-wise/export/excel', [App\Http\Controllers\TeamWiseReportController::class, 'export'])->name('reports.team-wise.export');
            Route::get('/reports/team-wise/export/pdf', [App\Http\Controllers\TeamWiseReportController::class, 'exportPdf'])->name('reports.team-wise.export-pdf');
        });

        // Admin Management routes
        Route::get('/admins', [App\Http\Controllers\AdminController::class, 'index'])->name('admins.index');
        Route::get('/admins-add', [App\Http\Controllers\AdminController::class, 'ajax_add'])->name('admins.add');
        Route::get('/admins-edit/{id}', [App\Http\Controllers\AdminController::class, 'ajax_edit'])->name('admins.edit');
        Route::post('/admins-submit', [App\Http\Controllers\AdminController::class, 'submit'])->name('admins.submit');
        Route::put('/admins-update/{id}', [App\Http\Controllers\AdminController::class, 'update'])->name('admins.update');
        Route::delete('/admins-delete/{id}', [App\Http\Controllers\AdminController::class, 'delete'])->name('admins.delete');
        Route::delete('/admins-destroy/{id}', [App\Http\Controllers\AdminController::class, 'destroy'])->name('admins.destroy');
        Route::get('/admins-change-password/{id}', [App\Http\Controllers\AdminController::class, 'changePassword'])->name('admins.change-password');
        Route::post('/admins-update-password/{id}', [App\Http\Controllers\AdminController::class, 'updatePassword'])->name('admins.update-password');

        // Bulk Operations Routes
        Route::get('/leads/bulk-reassign', [App\Http\Controllers\LeadController::class, 'ajaxBulkReassign'])->name('leads.bulk-reassign');
        Route::post('/leads/bulk-reassign', [App\Http\Controllers\LeadController::class, 'bulkReassign'])->name('leads.bulk-reassign.submit');
        Route::get('/leads/pullback', [App\Http\Controllers\LeadController::class, 'ajaxPullbackLeads'])->name('leads.pullback');
        Route::post('/leads/pullback', [App\Http\Controllers\LeadController::class, 'pullbackLeads'])->name('leads.pullback.submit');
        Route::get('/leads/pullbacked', [App\Http\Controllers\LeadController::class, 'pullbackedLeads'])->name('leads.pullbacked');
        Route::get('/leads/pullbacked/data', [App\Http\Controllers\LeadController::class, 'pullbackedLeadsData'])->name('leads.pullbacked.data');
        Route::get('/leads/pullbacked/assign', [App\Http\Controllers\LeadController::class, 'ajaxAssignPullbackedLeads'])->name('leads.pullbacked.assign');
        Route::post('/leads/pullbacked/assign', [App\Http\Controllers\LeadController::class, 'assignPullbackedLeads'])->name('leads.pullbacked.assign.submit');
        Route::get('/leads/bulk-delete', [App\Http\Controllers\LeadController::class, 'ajaxBulkDelete'])->name('leads.bulk-delete');
        Route::post('/leads/bulk-delete', [App\Http\Controllers\LeadController::class, 'bulkDelete'])->name('leads.bulk-delete.submit');
        Route::get('/leads/bulk-convert', [App\Http\Controllers\LeadController::class, 'ajaxBulkConvert'])->name('leads.bulk-convert');
        Route::post('/leads/bulk-convert', [App\Http\Controllers\LeadController::class, 'bulkConvert'])->name('leads.bulk-convert.submit');
        Route::get('/leads/followup', [App\Http\Controllers\LeadController::class, 'followupLeadsModal'])->name('leads.followup');

        // AJAX routes for bulk operations
        Route::post('/leads/get-leads-by-source', [App\Http\Controllers\LeadController::class, 'getLeadsBySource'])->name('leads.get-by-source');
        Route::post('/leads/get-leads-by-source-reassign', [App\Http\Controllers\LeadController::class, 'getLeadsBySourceReassign'])->name('leads.get-by-source-reassign');
        Route::post('/leads/get-pullback-leads', [App\Http\Controllers\LeadController::class, 'getPullbackLeads'])->name('leads.get-pullback-leads');
        Route::post('/leads/get-pullbacked-assign-leads', [App\Http\Controllers\LeadController::class, 'getAssignablePullbackedLeads'])->name('leads.get-pullbacked-assign-leads');

        // Converted Leads Routes
        Route::delete('/converted-leads/{id}', [App\Http\Controllers\ConvertedLeadController::class, 'destroy'])->name('converted-leads.destroy');
        Route::get('/converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'index'])->name('converted-leads.index');
        Route::get('/converted-leads/data', [App\Http\Controllers\ConvertedLeadController::class, 'getConvertedLeadsData'])->name('converted-leads.data');
        Route::get('/converted-leads/export', [App\Http\Controllers\ConvertedLeadController::class, 'export'])->name('converted-leads.export');
        Route::get('/converted-leads/view/{id}', [App\Http\Controllers\ConvertedLeadController::class, 'show'])->name('converted-leads.show');
        Route::get('/converted-leads/{id}/id-card-pdf', [App\Http\Controllers\ConvertedLeadController::class, 'generateIdCardPdf'])->name('converted-leads.id-card-pdf');
        Route::get('/converted-leads/{id}/details-pdf', [App\Http\Controllers\ConvertedLeadController::class, 'generateDetailsPdf'])->name('converted-leads.details-pdf');
        Route::post('/converted-leads/{id}/id-card-generate', [App\Http\Controllers\ConvertedLeadController::class, 'generateAndStoreIdCard'])->name('converted-leads.id-card-generate');
        Route::get('/converted-leads/{id}/id-card', [App\Http\Controllers\ConvertedLeadController::class, 'viewStoredIdCard'])->name('converted-leads.id-card-view');
        Route::post('/converted-leads/{id}/toggle-academic-verify', [App\Http\Controllers\ConvertedLeadController::class, 'toggleAcademicVerification'])->name('converted-leads.toggle-academic-verify');
        Route::get('/converted-leads/{id}/change-course', [App\Http\Controllers\ConvertedLeadController::class, 'showChangeCourseModal'])->name('converted-leads.change-course-modal');
        Route::post('/converted-leads/{id}/change-course', [App\Http\Controllers\ConvertedLeadController::class, 'changeCourse'])->name('converted-leads.change-course');
        Route::get('/converted-leads/{id}/course-pricing', [App\Http\Controllers\ConvertedLeadController::class, 'coursePricing'])->name('converted-leads.course-pricing');
        Route::get('/converted-leads/{id}/cancel-flag', [App\Http\Controllers\ConvertedLeadController::class, 'cancelFlag'])->name('converted-leads.cancel-flag');
        Route::post('/converted-leads/{id}/cancel-flag', [App\Http\Controllers\ConvertedLeadController::class, 'cancelFlagSubmit'])->name('converted-leads.cancel-flag-submit');
        Route::get('/converted-leads/{id}/move-to-placement', [App\Http\Controllers\ConvertedLeadController::class, 'moveToPlacementModal'])->name('converted-leads.move-to-placement');
        Route::post('/converted-leads/{id}/move-to-placement', [App\Http\Controllers\ConvertedLeadController::class, 'moveToPlacementSubmit'])->name('converted-leads.move-to-placement.submit');
        Route::get('/converted-leads/{id}/verify-resume-modal', [App\Http\Controllers\ConvertedLeadController::class, 'verifyResumeModal'])->name('converted-leads.verify-resume-modal');
        Route::post('/converted-leads/{id}/verify-resume', [App\Http\Controllers\ConvertedLeadController::class, 'verifyResume'])->name('converted-leads.verify-resume');
        Route::post('/converted-leads/{id}/unverify-resume', [App\Http\Controllers\ConvertedLeadController::class, 'unverifyResume'])->name('converted-leads.unverify-resume');

        // Placement list (converted students with is_placement_passed = 1)
        Route::get('/placement-list', [App\Http\Controllers\ConvertedLeadController::class, 'placementList'])->name('placement-list.index');
        Route::get('/placement-list/data', [App\Http\Controllers\ConvertedLeadController::class, 'placementListData'])->name('placement-list.data');
        Route::get('/placement-list/{id}', [App\Http\Controllers\ConvertedLeadController::class, 'placementDetails'])->name('placement-list.show');
        Route::get('/placement-list/{id}/pdf', [App\Http\Controllers\ConvertedLeadController::class, 'placementDetailsPdf'])->name('placement-list.pdf');
        Route::patch('/placement-list/{id}/specialization', [App\Http\Controllers\ConvertedLeadController::class, 'updatePlacementSpecialization'])->name('placement-list.update-specialization');
        Route::patch('/placement-list/{id}/remarks', [App\Http\Controllers\ConvertedLeadController::class, 'updatePlacementRemarks'])->name('placement-list.update-remarks');
        Route::post('/placement-list/{id}/mock-test-details', [App\Http\Controllers\ConvertedLeadController::class, 'storeMockTestDetails'])->name('placement-list.mock-test-details.store');
        Route::post('/placement-list/{id}/interviews', [App\Http\Controllers\ConvertedLeadController::class, 'storeScheduleInterview'])->name('placement-list.interviews.store');
        Route::patch('/placement-list/{id}/interviews/{interviewId}/status', [App\Http\Controllers\ConvertedLeadController::class, 'updateInterviewStatus'])->name('placement-list.interviews.status');

        // NIOS Converted Leads Routes
        Route::get('/nios-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'niosIndex'])->name('nios-converted-leads.index');
        Route::get('/nios-converted-leads/data', [App\Http\Controllers\ConvertedLeadController::class, 'getNiosConvertedLeadsData'])->name('nios-converted-leads.data');

        // BOSSE Converted Leads Routes
        Route::get('/bosse-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'bosseIndex'])->name('bosse-converted-leads.index');
        Route::get('/bosse-converted-leads/data', [App\Http\Controllers\ConvertedLeadController::class, 'getBosseConvertedLeadsData'])->name('bosse-converted-leads.data');

        // UG/PG Converted Leads Routes
        Route::get('/ugpg-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'ugpgIndex'])->name('ugpg-converted-leads.index');
        Route::get('/ugpg-converted-leads/data', [App\Http\Controllers\ConvertedLeadController::class, 'getUgpgConvertedLeadsData'])->name('ugpg-converted-leads.data');

        // EduMaster Converted Leads Routes
        Route::get('/edumaster-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'edumasterIndex'])->name('edumaster-converted-leads.index');
        Route::get('/edumaster-converted-leads/data', [App\Http\Controllers\ConvertedLeadController::class, 'getEdumasterConvertedLeadsData'])->name('edumaster-converted-leads.data');

        // Hotel Management Converted Leads Routes
        Route::get('/hotel-management-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'hotelManagementIndex'])->name('hotel-management-converted-leads.index');
        Route::get('/hotel-management-converted-leads/data', [App\Http\Controllers\ConvertedLeadController::class, 'getHotelManagementConvertedLeadsData'])->name('hotel-management-converted-leads.data');

        // GMVSS Converted Leads Routes
        Route::get('/gmvss-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'gmvssIndex'])->name('gmvss-converted-leads.index');
        Route::get('/gmvss-converted-leads/data', [App\Http\Controllers\ConvertedLeadController::class, 'getGmvssConvertedLeadsData'])->name('gmvss-converted-leads.data');
        Route::get('/gmvss-mentor-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'gmvssMentorIndex'])->name('gmvss-mentor-converted-leads.index');
        Route::get('/ai-python-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'aiPythonIndex'])->name('ai-python-converted-leads.index');

        // Post-Sales Converted Students
        Route::get('/post-sales-converted-students', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'index'])->name('post-sales.converted-leads.index');
        Route::get('/post-sales-converted-students/data', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'getPostSalesConvertedStudentsData'])->name('post-sales.converted-leads.data');
        Route::get('/post-sales-postponed-batches', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'postponedBatches'])->name('post-sales.postponed-batches');
        Route::get('/post-sales-converted-students/bulk-assign', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'bulkAssign'])->name('post-sales.converted-leads.bulk-assign');
        Route::get('/post-sales-converted-students/bulk-assign/data', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'getBulkAssignData'])->name('post-sales.converted-leads.bulk-assign.data');
        Route::post('/post-sales-converted-students/bulk-assign', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'bulkAssignSubmit'])->name('post-sales.converted-leads.bulk-assign.submit');
        Route::get('/post-sales-converted-students/{id}', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'show'])->name('post-sales.converted-leads.show');
        Route::get('/post-sales-converted-students/{id}/postponed-batch', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'postponedBatch'])->name('post-sales.converted-leads.postponed-batch');
        Route::post('/post-sales-converted-students/{id}/postponed-batch', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'postponedBatchSubmit'])->name('post-sales.converted-leads.postponed-batch.submit');
        Route::get('/post-sales-converted-students/{id}/status-update', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'statusUpdate'])->name('post-sales.converted-leads.status-update');
        Route::post('/post-sales-converted-students/{id}/status-update', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'statusUpdateSubmit'])->name('post-sales.converted-leads.status-update-submit');
        Route::get('/post-sales-converted-students/{id}/cancel-flag', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'cancelFlag'])->name('post-sales.converted-leads.cancel-flag');
        Route::post('/post-sales-converted-students/{id}/cancel-flag', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'cancelFlagSubmit'])->name('post-sales.converted-leads.cancel-flag-submit');
        Route::get('/post-sales-converted-students/{id}/assign', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'assign'])->name('post-sales.converted-leads.assign');
        Route::post('/post-sales-converted-students/{id}/assign', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'assignSubmit'])->name('post-sales.converted-leads.assign-submit');
        Route::get('/post-sales-converted-students/{id}/details-pdf', [App\Http\Controllers\PostSalesConvertedLeadController::class, 'generateDetailsPdf'])->name('post-sales.converted-leads.details-pdf');
        Route::get('/digital-marketing-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'digitalMarketingIndex'])->name('digital-marketing-converted-leads.index');
        Route::get('/digital-marketing-converted-leads/data', [App\Http\Controllers\ConvertedLeadController::class, 'getDigitalMarketingConvertedLeadsData'])->name('digital-marketing-converted-leads.data');
        Route::get('/diploma-in-data-science-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'aiAutomationIndex'])->name('diploma-in-data-science-converted-leads.index');
        Route::get('/web-development-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'webDevIndex'])->name('web-development-converted-leads.index');
        Route::get('/vibe-coding-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'vibeCodingIndex'])->name('vibe-coding-converted-leads.index');
        Route::get('/graphic-designing-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'graphicDesigningIndex'])->name('graphic-designing-converted-leads.index');
        Route::get('/machine-learning-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'machineLearningIndex'])->name('machine-learning-converted-leads.index');
        Route::get('/flutter-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'flutterIndex'])->name('flutter-converted-leads.index');
        Route::get('/rpa-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'rpaIndex'])->name('rpa-converted-leads.index');
        Route::get('/eduthanzeel-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'eduthanzeelIndex'])->name('eduthanzeel-converted-leads.index');
        Route::get('/e-school-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'eschoolIndex'])->name('e-school-converted-leads.index');
        Route::get('/junior-vlogger-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'juniorVloggerIndex'])->name('junior-vlogger-converted-leads.index');
        Route::get('/converted-leads/{id}/update-register-number-modal', [App\Http\Controllers\ConvertedLeadController::class, 'showUpdateRegisterNumberModal'])->name('converted-leads.update-register-number-modal');
        Route::post('/converted-leads/{id}/update-register-number', [App\Http\Controllers\ConvertedLeadController::class, 'updateRegisterNumber'])->name('converted-leads.update-register-number');
        Route::put('/converted-leads/{id}/update-documents', [App\Http\Controllers\ConvertedLeadController::class, 'updateDocuments'])->name('converted-leads.update-documents');
        Route::post('/converted-leads/{id}/inline-update', [App\Http\Controllers\ConvertedLeadController::class, 'inlineUpdate'])->name('converted-leads.inline-update');
        Route::post('/converted-leads/batch-update', [App\Http\Controllers\ConvertedLeadController::class, 'batchUpdate'])->name('converted-leads.batch-update');

        // BOSSE Mentor Converted Leads Routes
        Route::get('/mentor-bosse-converted-leads', [App\Http\Controllers\MentorConvertedLeadController::class, 'index'])->name('mentor-bosse-converted-leads.index');
        Route::post('/mentor-bosse-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\MentorConvertedLeadController::class, 'updateMentorDetails'])->name('mentor-bosse-converted-leads.update-mentor-details');

        // UG/PG Mentor Converted Leads Routes
        Route::get('/mentor-ugpg-converted-leads', [App\Http\Controllers\UGPGMentorConvertedLeadController::class, 'index'])->name('mentor-ugpg-converted-leads.index');
        Route::post('/mentor-ugpg-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\UGPGMentorConvertedLeadController::class, 'updateMentorDetails'])->name('mentor-ugpg-converted-leads.update-mentor-details');

        // EduMaster Mentor Converted Leads Routes
        Route::get('/mentor-edumaster-converted-leads', [App\Http\Controllers\EduMasterMentorConvertedLeadController::class, 'index'])->name('mentor-edumaster-converted-leads.index');
        Route::post('/mentor-edumaster-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\EduMasterMentorConvertedLeadController::class, 'updateMentorDetails'])->name('mentor-edumaster-converted-leads.update-mentor-details');

        // NIOS Mentor Converted Leads Routes
        Route::get('/mentor-nios-converted-leads', [App\Http\Controllers\NiosMentorConvertedLeadController::class, 'index'])->name('mentor-nios-converted-leads.index');
        Route::post('/mentor-nios-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\NiosMentorConvertedLeadController::class, 'updateMentorDetails'])->name('mentor-nios-converted-leads.update-mentor-details');

        // E-School Mentor Converted Leads Routes
        Route::get('/mentor-eschool-converted-leads', [App\Http\Controllers\ESchoolEduthanzeelMentorController::class, 'eschoolIndex'])->name('mentor-eschool-converted-leads.index');
        Route::post('/mentor-eschool-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\ESchoolEduthanzeelMentorController::class, 'updateMentorDetails'])->name('mentor-eschool-converted-leads.update-mentor-details');

        // Eduthanzeel Mentor Converted Leads Routes
        Route::get('/mentor-eduthanzeel-converted-leads', [App\Http\Controllers\ESchoolEduthanzeelMentorController::class, 'eduthanzeelIndex'])->name('mentor-eduthanzeel-converted-leads.index');
        Route::post('/mentor-eduthanzeel-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\ESchoolEduthanzeelMentorController::class, 'updateMentorDetails'])->name('mentor-eduthanzeel-converted-leads.update-mentor-details');

        // Data Science Mentor Converted Leads Routes
        Route::get('/data-science-mentor-converted-leads', [App\Http\Controllers\DataScienceMentorController::class, 'index'])->name('data-science-mentor-converted-leads.index');
        Route::post('/data-science-mentor-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\DataScienceMentorController::class, 'updateMentorDetails'])->name('data-science-mentor-converted-leads.update-mentor-details');

        // Machine Learning Mentor Converted Leads Routes
        Route::get('/machine-learning-mentor-converted-leads', [App\Http\Controllers\MachineLearningMentorController::class, 'index'])->name('machine-learning-mentor-converted-leads.index');
        Route::post('/machine-learning-mentor-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\MachineLearningMentorController::class, 'updateMentorDetails'])->name('machine-learning-mentor-converted-leads.update-mentor-details');

        // Digital Marketing Mentor Converted Leads Routes
        Route::get('/digital-marketing-mentor-converted-leads', [App\Http\Controllers\DigitalMarketingMentorController::class, 'index'])->name('digital-marketing-mentor-converted-leads.index');
        Route::post('/digital-marketing-mentor-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\DigitalMarketingMentorController::class, 'updateMentorDetails'])->name('digital-marketing-mentor-converted-leads.update-mentor-details');

        // Graphic Designing Mentor Converted Leads Routes
        Route::get('/graphic-designing-mentor-converted-leads', [App\Http\Controllers\GraphicDesigningMentorController::class, 'index'])->name('graphic-designing-mentor-converted-leads.index');
        Route::post('/graphic-designing-mentor-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\GraphicDesigningMentorController::class, 'updateMentorDetails'])->name('graphic-designing-mentor-converted-leads.update-mentor-details');

        // Junior Vlogger Mentor Converted Leads Routes
        Route::get('/junior-vlogger-mentor-converted-leads', [App\Http\Controllers\JuniorVloggerMentorController::class, 'index'])->name('junior-vlogger-mentor-converted-leads.index');
        Route::post('/junior-vlogger-mentor-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\JuniorVloggerMentorController::class, 'updateMentorDetails'])->name('junior-vlogger-mentor-converted-leads.update-mentor-details');

        // Additional Mentor Converted Leads Routes
        Route::get('/medical-coding-mentor-converted-leads', [App\Http\Controllers\AdditionalMentorCourseController::class, 'medicalCodingIndex'])->name('medical-coding-mentor-converted-leads.index');
        Route::get('/python-mentor-converted-leads', [App\Http\Controllers\AdditionalMentorCourseController::class, 'pythonIndex'])->name('python-mentor-converted-leads.index');
        Route::get('/flutter-mentor-converted-leads', [App\Http\Controllers\AdditionalMentorCourseController::class, 'flutterIndex'])->name('flutter-mentor-converted-leads.index');
        Route::get('/rpa-mentor-converted-leads', [App\Http\Controllers\AdditionalMentorCourseController::class, 'rpaIndex'])->name('rpa-mentor-converted-leads.index');

        // BOSSE Faculty Converted Leads Routes
        Route::get('/faculty-bosse-converted-leads', [App\Http\Controllers\FacultyConvertedLeadController::class, 'index'])->name('faculty-bosse-converted-leads.index');
        Route::post('/faculty-bosse-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\FacultyConvertedLeadController::class, 'updateMentorDetails'])->name('faculty-bosse-converted-leads.update-mentor-details');

        // UG/PG Faculty Converted Leads Routes
        Route::get('/faculty-ugpg-converted-leads', [App\Http\Controllers\UGPGFacultyConvertedLeadController::class, 'index'])->name('faculty-ugpg-converted-leads.index');
        Route::post('/faculty-ugpg-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\UGPGFacultyConvertedLeadController::class, 'updateMentorDetails'])->name('faculty-ugpg-converted-leads.update-mentor-details');

        // EduMaster Faculty Converted Leads Routes
        Route::get('/faculty-edumaster-converted-leads', [App\Http\Controllers\EduMasterFacultyConvertedLeadController::class, 'index'])->name('faculty-edumaster-converted-leads.index');
        Route::post('/faculty-edumaster-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\EduMasterFacultyConvertedLeadController::class, 'updateMentorDetails'])->name('faculty-edumaster-converted-leads.update-mentor-details');

        // NIOS Faculty Converted Leads Routes
        Route::get('/faculty-nios-converted-leads', [App\Http\Controllers\NiosFacultyConvertedLeadController::class, 'index'])->name('faculty-nios-converted-leads.index');
        Route::post('/faculty-nios-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\NiosFacultyConvertedLeadController::class, 'updateMentorDetails'])->name('faculty-nios-converted-leads.update-mentor-details');

        // E-School Faculty Converted Leads Routes
        Route::get('/faculty-eschool-converted-leads', [App\Http\Controllers\ESchoolEduthanzeelFacultyController::class, 'eschoolIndex'])->name('faculty-eschool-converted-leads.index');
        Route::post('/faculty-eschool-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\ESchoolEduthanzeelFacultyController::class, 'updateMentorDetails'])->name('faculty-eschool-converted-leads.update-mentor-details');

        // Eduthanzeel Faculty Converted Leads Routes
        Route::get('/faculty-eduthanzeel-converted-leads', [App\Http\Controllers\ESchoolEduthanzeelFacultyController::class, 'eduthanzeelIndex'])->name('faculty-eduthanzeel-converted-leads.index');
        Route::post('/faculty-eduthanzeel-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\ESchoolEduthanzeelFacultyController::class, 'updateMentorDetails'])->name('faculty-eduthanzeel-converted-leads.update-mentor-details');

        // Data Science Faculty Converted Leads Routes
        Route::get('/data-science-faculty-converted-leads', [App\Http\Controllers\DataScienceFacultyController::class, 'index'])->name('data-science-faculty-converted-leads.index');
        Route::post('/data-science-faculty-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\DataScienceFacultyController::class, 'updateMentorDetails'])->name('data-science-faculty-converted-leads.update-mentor-details');

        // Machine Learning Faculty Converted Leads Routes
        Route::get('/machine-learning-faculty-converted-leads', [App\Http\Controllers\MachineLearningFacultyController::class, 'index'])->name('machine-learning-faculty-converted-leads.index');
        Route::post('/machine-learning-faculty-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\MachineLearningFacultyController::class, 'updateMentorDetails'])->name('machine-learning-faculty-converted-leads.update-mentor-details');

        // Digital Marketing Faculty Converted Leads Routes
        Route::get('/digital-marketing-faculty-converted-leads', [App\Http\Controllers\DigitalMarketingFacultyController::class, 'index'])->name('digital-marketing-faculty-converted-leads.index');
        Route::post('/digital-marketing-faculty-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\DigitalMarketingFacultyController::class, 'updateMentorDetails'])->name('digital-marketing-faculty-converted-leads.update-mentor-details');

        // Graphic Designing Faculty Converted Leads Routes
        Route::get('/graphic-designing-faculty-converted-leads', [App\Http\Controllers\GraphicDesigningFacultyController::class, 'index'])->name('graphic-designing-faculty-converted-leads.index');
        Route::post('/graphic-designing-faculty-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\GraphicDesigningFacultyController::class, 'updateMentorDetails'])->name('graphic-designing-faculty-converted-leads.update-mentor-details');

        // Junior Vlogger Faculty Converted Leads Routes
        Route::get('/junior-vlogger-faculty-converted-leads', [App\Http\Controllers\JuniorVloggerFacultyController::class, 'index'])->name('junior-vlogger-faculty-converted-leads.index');
        Route::post('/junior-vlogger-faculty-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\JuniorVloggerFacultyController::class, 'updateMentorDetails'])->name('junior-vlogger-faculty-converted-leads.update-mentor-details');

        // GMVSS Faculty Converted Leads Routes
        Route::get('/gmvss-faculty-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'gmvssFacultyIndex'])->name('gmvss-faculty-converted-leads.index');

        // Additional Faculty Converted Leads Routes
        Route::get('/medical-coding-faculty-converted-leads', [App\Http\Controllers\AdditionalFacultyCourseController::class, 'medicalCodingIndex'])->name('medical-coding-faculty-converted-leads.index');
        Route::get('/python-faculty-converted-leads', [App\Http\Controllers\AdditionalFacultyCourseController::class, 'pythonIndex'])->name('python-faculty-converted-leads.index');
        Route::get('/flutter-faculty-converted-leads', [App\Http\Controllers\AdditionalFacultyCourseController::class, 'flutterIndex'])->name('flutter-faculty-converted-leads.index');
        Route::get('/rpa-faculty-converted-leads', [App\Http\Controllers\AdditionalFacultyCourseController::class, 'rpaIndex'])->name('rpa-faculty-converted-leads.index');

        // Support Converted Lead Details Route (Unified)
        Route::get('/support-converted-leads/{id}/details', [App\Http\Controllers\SupportConvertedLeadController::class, 'show'])->name('support-converted-leads.details');
        Route::post('/support-converted-leads/{id}/feedback', [App\Http\Controllers\SupportConvertedLeadController::class, 'submitFeedback'])->name('support-converted-leads.feedback');
        Route::post('/support-converted-leads/{id}/send-whatsapp', [App\Http\Controllers\SupportConvertedLeadController::class, 'sendSupportWhatsApp'])->name('support-converted-leads.send-whatsapp');
        Route::get('/support-converted-leads/{id}/send-course-mail', [App\Http\Controllers\SupportConvertedLeadController::class, 'showSendCourseMailForm'])->name('support-converted-leads.send-course-mail');
        Route::post('/support-converted-leads/{id}/send-course-mail', [App\Http\Controllers\SupportConvertedLeadController::class, 'sendSupportCourseMail'])->name('support-converted-leads.send-course-mail.submit');

        // Back-compat routes (optional): keep if already linked somewhere
        Route::get('/support-bosse-converted-leads/{id}/details', [App\Http\Controllers\SupportConvertedLeadController::class, 'show'])->name('support-bosse-converted-leads.details');
        Route::get('/support-nios-converted-leads/{id}/details', [App\Http\Controllers\SupportConvertedLeadController::class, 'show'])->name('support-nios-converted-leads.details');

        // BOSSE Support Converted Leads Routes
        Route::get('/support-bosse-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'index'])->name('support-bosse-converted-leads.index');
        Route::post('/support-bosse-converted-leads/{id}/send-whatsapp', [App\Http\Controllers\SupportConvertedLeadController::class, 'sendSupportWhatsApp'])->name('support-bosse-converted-leads.send-whatsapp');
        Route::get('/support-bosse-converted-leads/{id}/send-course-mail', [App\Http\Controllers\SupportConvertedLeadController::class, 'showSendCourseMailForm'])->name('support-bosse-converted-leads.send-course-mail');
        Route::post('/support-bosse-converted-leads/{id}/send-course-mail', [App\Http\Controllers\SupportConvertedLeadController::class, 'sendSupportCourseMail'])->name('support-bosse-converted-leads.send-course-mail.submit');
        Route::post('/support-bosse-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-bosse-converted-leads.update-support-details');

        // NIOS Support Converted Leads Routes
        Route::get('/support-nios-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'niosIndex'])->name('support-nios-converted-leads.index');
        Route::post('/support-nios-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-nios-converted-leads.update-support-details');
        Route::post('/support-converted-leads/{id}/toggle-support-verify', [App\Http\Controllers\SupportConvertedLeadController::class, 'toggleSupportVerification'])->name('support-converted-leads.toggle-support-verify');

        // UG/PG Support Converted Leads Routes
        Route::get('/support-ugpg-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'ugpgIndex'])->name('support-ugpg-converted-leads.index');
        Route::post('/support-ugpg-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-ugpg-converted-leads.update-support-details');
        Route::get('/support-edumaster-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'edumasterIndex'])->name('support-edumaster-converted-leads.index');
        Route::post('/support-edumaster-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-edumaster-converted-leads.update-support-details');

        // Hotel Management Support Converted Leads Routes
        Route::get('/support-hotel-management-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'hotelManagementIndex'])->name('support-hotel-management-converted-leads.index');
        Route::post('/support-hotel-management-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-hotel-management-converted-leads.update-support-details');

        // GMVSS Support Converted Leads Routes
        Route::get('/support-gmvss-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'gmvssIndex'])->name('support-gmvss-converted-leads.index');
        Route::post('/support-gmvss-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-gmvss-converted-leads.update-support-details');

        // AI with Python Support Converted Leads Routes
        Route::get('/support-ai-python-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'aiPythonIndex'])->name('support-ai-python-converted-leads.index');
        Route::post('/support-ai-python-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-ai-python-converted-leads.update-support-details');

        // Digital Marketing Support Converted Leads Routes
        Route::get('/support-digital-marketing-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'digitalMarketingIndex'])->name('support-digital-marketing-converted-leads.index');
        Route::post('/support-digital-marketing-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-digital-marketing-converted-leads.update-support-details');

        // AI Automation Support Converted Leads Routes
        Route::get('/support-diploma-in-data-science-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'aiAutomationIndex'])->name('support-diploma-in-data-science-converted-leads.index');
        Route::post('/support-diploma-in-data-science-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-diploma-in-data-science-converted-leads.update-support-details');

        // Web Development Support Converted Leads Routes
        Route::get('/support-web-development-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'webDevelopmentIndex'])->name('support-web-development-converted-leads.index');
        Route::post('/support-web-development-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-web-development-converted-leads.update-support-details');

        // Vibe Coding Support Converted Leads Routes
        Route::get('/support-vibe-coding-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'vibeCodingIndex'])->name('support-vibe-coding-converted-leads.index');
        Route::post('/support-vibe-coding-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-vibe-coding-converted-leads.update-support-details');

        // Graphic Designing Support Converted Leads Routes
        Route::get('/support-graphic-designing-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'graphicDesigningIndex'])->name('support-graphic-designing-converted-leads.index');
        Route::post('/support-graphic-designing-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-graphic-designing-converted-leads.update-support-details');

        // Diploma in Machine Learning Support Converted Leads Routes
        Route::get('/support-machine-learning-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'machineLearningIndex'])->name('support-machine-learning-converted-leads.index');
        Route::post('/support-machine-learning-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-machine-learning-converted-leads.update-support-details');

        // Flutter Support Converted Leads Routes
        Route::get('/support-flutter-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'flutterIndex'])->name('support-flutter-converted-leads.index');
        Route::post('/support-flutter-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-flutter-converted-leads.update-support-details');

        // Eduthanzeel Support Converted Leads Routes
        Route::get('/support-eduthanzeel-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'eduthanzeelIndex'])->name('support-eduthanzeel-converted-leads.index');
        Route::post('/support-eduthanzeel-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-eduthanzeel-converted-leads.update-support-details');

        // E-School Support Converted Leads Routes
        Route::get('/support-e-school-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'eSchoolIndex'])->name('support-e-school-converted-leads.index');
        Route::post('/support-e-school-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-e-school-converted-leads.update-support-details');

        Route::get('/support-junior-vlogger-converted-leads', [App\Http\Controllers\SupportConvertedLeadController::class, 'juniorVloggerIndex'])->name('support-junior-vlogger-converted-leads.index');
        Route::post('/support-junior-vlogger-converted-leads/{id}/update-support-details', [App\Http\Controllers\SupportConvertedLeadController::class, 'updateSupportDetails'])->name('support-junior-vlogger-converted-leads.update-support-details');

        // Invoice Routes
        Route::get('/invoices/student/{studentId}', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{id}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/create/{studentId}', [App\Http\Controllers\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices/store/{studentId}', [App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{id}/edit-amount', [App\Http\Controllers\InvoiceController::class, 'editAmount'])->name('invoices.edit-amount');
        Route::post('/invoices/{id}/update-amount', [App\Http\Controllers\InvoiceController::class, 'updateAmount'])->name('invoices.update-amount');
        Route::get('/invoices/{id}/edit-discount', [App\Http\Controllers\InvoiceController::class, 'editDiscount'])->name('invoices.edit-discount');
        Route::post('/invoices/{id}/update-discount', [App\Http\Controllers\InvoiceController::class, 'updateDiscount'])->name('invoices.update-discount');

        // Payment Routes
        Route::get('/payments/export-pdf', [App\Http\Controllers\PaymentController::class, 'exportPdf'])->name('payments.export-pdf');
        Route::get('/payments', [App\Http\Controllers\PaymentController::class, 'listAll'])->name('payments.list');
        Route::get('/payments/invoice/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/invoice/{invoice}/payment-links', [App\Http\Controllers\PaymentController::class, 'storePaymentLink'])->name('payments.links.store');
        Route::post('/payments/invoice/{invoice}/payment-links/{paymentLink}/refresh', [App\Http\Controllers\PaymentController::class, 'refreshPaymentLink'])->name('payments.links.refresh');
        Route::delete('/payments/invoice/{invoice}/payment-links/{paymentLink}', [App\Http\Controllers\PaymentController::class, 'deletePaymentLink'])->name('payments.links.delete');
        Route::get('/payments/create/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments/store/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/proofs/{proofId}/view', [App\Http\Controllers\PaymentController::class, 'viewProofFile'])->name('payments.proofs.view');
        Route::get('/payments/proofs/{proofId}/download', [App\Http\Controllers\PaymentController::class, 'downloadProofFile'])->name('payments.proofs.download');
        Route::get('/payments/{id}', [App\Http\Controllers\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{id}/approve', [App\Http\Controllers\PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{id}/reject', [App\Http\Controllers\PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('/payments/{id}/view', [App\Http\Controllers\PaymentController::class, 'viewFile'])->name('payments.view');
        Route::get('/payments/{id}/download', [App\Http\Controllers\PaymentController::class, 'downloadFile'])->name('payments.download');
        Route::get('/payments/{id}/tax-invoice', [App\Http\Controllers\PaymentController::class, 'taxInvoice'])->name('payments.tax-invoice');
        Route::get('/payments/{id}/tax-invoice-pdf', [App\Http\Controllers\PaymentController::class, 'taxInvoicePdf'])->name('payments.tax-invoice-pdf');
        Route::get('/payments/{id}/payment-receipt', [App\Http\Controllers\PaymentController::class, 'paymentReceipt'])->name('payments.payment-receipt');
        Route::get('/payments/{id}/payment-receipt-pdf', [App\Http\Controllers\PaymentController::class, 'paymentReceiptPdf'])->name('payments.payment-receipt-pdf');

        // Call Logs Routes
        Route::get('/call-logs', [VoxbayCallLogController::class, 'index'])->name('call-logs.index');
        Route::get('/call-logs/{callLog}', [VoxbayCallLogController::class, 'show'])->name('call-logs.show');
        Route::delete('/call-logs/{callLog}', [VoxbayCallLogController::class, 'destroy'])->name('call-logs.destroy');

        // Notifications Routes (Admin only)
        Route::resource('notifications', NotificationController::class);
        Route::get('/notifications/{notification}/show', [NotificationController::class, 'show'])->name('notifications.show');

        // Telecaller Tracking Routes (Super Admin only)
        Route::prefix('telecaller-tracking')->name('telecaller-tracking.')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\TelecallerReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/reports', [App\Http\Controllers\TelecallerReportController::class, 'reports'])->name('reports');
            Route::get('/reports/{userId}', [App\Http\Controllers\TelecallerReportController::class, 'telecallerReport'])->name('telecaller-report');
            Route::get('/session-details/{sessionId}', [App\Http\Controllers\TelecallerReportController::class, 'sessionDetails'])->name('session-details');
            Route::get('/reports/export/excel', [App\Http\Controllers\TelecallerReportController::class, 'exportExcel'])->name('export.excel');
            Route::get('/reports/export/pdf', [App\Http\Controllers\TelecallerReportController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/realtime-data', [App\Http\Controllers\TelecallerReportController::class, 'getRealtimeData'])->name('realtime-data');
        });

        // Telecaller Task Management Routes (Super Admin only)
        Route::prefix('telecaller-tasks')->name('telecaller-tasks.')->group(function () {
            Route::get('/', [App\Http\Controllers\TelecallerTaskController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\TelecallerTaskController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\TelecallerTaskController::class, 'store'])->name('store');
            Route::get('/overdue', [App\Http\Controllers\TelecallerTaskController::class, 'overdue'])->name('overdue');
            Route::get('/due-today', [App\Http\Controllers\TelecallerTaskController::class, 'dueToday'])->name('due-today');
            Route::get('/statistics', [App\Http\Controllers\TelecallerTaskController::class, 'statistics'])->name('statistics');
            Route::get('/{task}', [App\Http\Controllers\TelecallerTaskController::class, 'show'])->name('show');
            Route::get('/{task}/edit', [App\Http\Controllers\TelecallerTaskController::class, 'edit'])->name('edit');
            Route::put('/{task}', [App\Http\Controllers\TelecallerTaskController::class, 'update'])->name('update');
            Route::post('/{task}/complete', [App\Http\Controllers\TelecallerTaskController::class, 'complete'])->name('complete');
            Route::delete('/{task}', [App\Http\Controllers\TelecallerTaskController::class, 'destroy'])->name('destroy');
        });

        // Meta Leads Admin Routes (Protected)
        Route::prefix('meta-leads')->name('meta-leads.')->group(function () {
            // Main dashboard
            Route::get('/', [MetaLeadController::class, 'index'])->name('index');

            // Individual lead operations
            Route::get('/lead/{id}', [MetaLeadController::class, 'show'])->name('show');
            Route::delete('/lead/{id}', [MetaLeadController::class, 'destroy'])->name('destroy');
        });
    });

    // Notification routes for all users
    Route::get('/notifications', [NotificationController::class, 'viewAll'])->name('notifications.view-all');
    Route::get('/api/notifications', [NotificationController::class, 'getUserNotifications'])->name('notifications.api');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

// Debug route for Meta leads testing
Route::get('/debug-meta-test', function () {
    try {
        $facebookService = new \App\Services\FacebookApiService();
        $result = $facebookService->fetchLeads();

        return response()->json([
            'status' => 'success',
            'environment_check' => [
                'FB_APP_ID' => config('services.facebook.app_id') ? 'SET' : 'NOT SET',
                'FB_APP_SECRET' => config('services.facebook.app_secret') ? 'SET' : 'NOT SET',
                'FB_ACCESS_TOKEN' => config('services.facebook.access_token') ? 'SET' : 'NOT SET',
                'FB_LEAD_FORM_ID' => config('services.facebook.lead_form_id') ? 'SET' : 'NOT SET'
            ],
            'facebook_result' => $result
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Debug routes for idle time calculation
Route::get('/debug-idle-calc', function () {
    $userId = 2;
    $startDate = '2025-09-17';
    $endDate = '2025-09-17';

    // Get idle times for user 2 on 2025-09-17
    $idleTimes = \App\Models\TelecallerIdleTime::where('user_id', $userId)
        ->whereBetween('idle_start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->get();

    $totalIdleSeconds = $idleTimes->sum('idle_duration_seconds');

    return response()->json([
        'user_id' => $userId,
        'date_range' => [$startDate . ' 00:00:00', $endDate . ' 23:59:59'],
        'idle_times_count' => $idleTimes->count(),
        'idle_times_data' => $idleTimes->pluck('idle_duration_seconds')->toArray(),
        'total_idle_seconds' => $totalIdleSeconds,
        'formatted_time' => gmdate('H:i:s', $totalIdleSeconds)
    ]);
});

Route::get('/debug-telecaller-stats', function () {
    $userId = 2;
    $startDate = '2025-09-17';
    $endDate = '2025-09-17';

    // Simulate the getTelecallerStats method
    $sessions = \App\Models\TelecallerSession::where('user_id', $userId)
        ->whereBetween('login_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->with('idleTimes')
        ->get();

    // If no sessions found in date range, get all sessions for this user (fallback)
    if ($sessions->isEmpty()) {
        $sessions = \App\Models\TelecallerSession::where('user_id', $userId)
            ->with('idleTimes')
            ->get();
    }

    $idleTimes = \App\Models\TelecallerIdleTime::where('user_id', $userId)
        ->whereBetween('idle_start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->get();

    // If no idle times found in date range, get all idle times for this user (fallback)
    if ($idleTimes->isEmpty()) {
        $idleTimes = \App\Models\TelecallerIdleTime::where('user_id', $userId)->get();
    }

    $totalIdleSeconds = $idleTimes->sum('idle_duration_seconds');

    return response()->json([
        'user_id' => $userId,
        'sessions_count' => $sessions->count(),
        'idle_times_count' => $idleTimes->count(),
        'idle_times_data' => $idleTimes->pluck('idle_duration_seconds')->toArray(),
        'total_idle_seconds' => $totalIdleSeconds,
        'formatted_time' => gmdate('H:i:s', $totalIdleSeconds),
        'sessions_data' => $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'login_time' => $session->login_time,
                'idle_times_count' => $session->idleTimes->count(),
                'idle_times_sum' => $session->idleTimes->sum('idle_duration_seconds')
            ];
        })
    ]);
});

// Student Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/student/verification/toggle/{studentId}', [App\Http\Controllers\StudentVerificationController::class, 'toggleVerifyStudent'])->name('student.verification.toggle');
    Route::get('/student/verification/status/{studentId}', [App\Http\Controllers\StudentVerificationController::class, 'getVerificationStatus'])->name('student.verification.status');
    Route::post('/student/verification/bulk', [App\Http\Controllers\StudentVerificationController::class, 'bulkVerify'])->name('student.verification.bulk');
});
