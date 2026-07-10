<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\LeadsController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\MarketingLeadsController;
use App\Http\Controllers\API\FollowupLeadsController;
use App\Http\Controllers\API\RegistrationLeadsController;
use App\Http\Controllers\API\ConvertedLeadsController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\Call_Api\AuthController as CallApiAuthController;
use App\Http\Controllers\API\Call_Api\ProfileController as CallApiProfileController;
use App\Http\Controllers\API\Call_Api\CallSyncController as CallApiSyncController;
use App\Http\Controllers\API\Call_Api\AppVersionController as CallApiAppVersionController;
use App\Http\Controllers\API\NatX_Api\AuthController as NatXApiAuthController;
use App\Http\Controllers\API\NatX_Api\ProfileController as NatXApiProfileController;
use App\Http\Controllers\API\NatX_Api\NatXSyncController as NatXApiSyncController;
use App\Http\Controllers\API\NatX_Api\AppVersionController as NatXApiAppVersionController;
use App\Http\Controllers\API\NatX_Api\MentorStudentsController as NatXApiMentorStudentsController;
use App\Http\Controllers\API\AppVersionController as CrmAppVersionController;

//Call App API Routes
Route::prefix('v1/call')->group(function () {
    Route::get('app/version', [CallApiAppVersionController::class, 'check']);
    Route::post('app/version', [CallApiAppVersionController::class, 'check']);

    Route::post('auth/login', [CallApiAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [CallApiAuthController::class, 'logout']);
        Route::get('profile', [CallApiProfileController::class, 'index']);

        Route::post('sync/calls', [CallApiSyncController::class, 'syncCalls']);
        Route::post('sync/recordings', [CallApiSyncController::class, 'uploadRecording']);
        Route::post('sync/recordings/status', [CallApiSyncController::class, 'recordingStatus']);
        Route::get('sync/status', [CallApiSyncController::class, 'status']);
    });
});

//NatX API Routes
Route::prefix('v1/natx')->group(function () {
    Route::get('app/version', [NatXApiAppVersionController::class, 'check']);
    Route::post('app/version', [NatXApiAppVersionController::class, 'check']);

    Route::post('auth/login', [NatXApiAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [NatXApiAuthController::class, 'logout']);
        Route::get('profile', [NatXApiProfileController::class, 'index']);

        Route::get('mentor/students', [NatXApiMentorStudentsController::class, 'students']);
        Route::get('mentor/courses', [NatXApiMentorStudentsController::class, 'courses']);
        Route::get('mentor/batches', [NatXApiMentorStudentsController::class, 'batches']);

        Route::post('sync/calls', [NatXApiSyncController::class, 'syncCalls']);
        Route::post('sync/recordings', [NatXApiSyncController::class, 'uploadRecording']);
        Route::post('sync/recordings/status', [NatXApiSyncController::class, 'recordingStatus']);
        Route::get('sync/status', [NatXApiSyncController::class, 'status']);
    });
});

// CRM API Routes
Route::prefix('v1')->group(function () {
    Route::get('app/version', [CrmAppVersionController::class, 'check']);
    Route::post('app/version', [CrmAppVersionController::class, 'check']);

    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('home', [HomeController::class, 'index']);
        Route::get('marketing-home', [HomeController::class, 'marketingHome']);
        Route::get('leads', [LeadsController::class, 'index']);
        Route::get('leads/filters', [LeadsController::class, 'filters']);
        Route::get('leads/call', [LeadsController::class, 'callLead']);
        Route::get('leads/{lead}/status-update', [LeadsController::class, 'statusUpdateData'])->whereNumber('lead');
        Route::post('leads/{lead}/status-update', [LeadsController::class, 'statusUpdate'])->whereNumber('lead');
        Route::get('leads/{lead}/plus-two-follow-up', [LeadsController::class, 'plusTwoFollowUpDetails'])->whereNumber('lead');
        Route::get('leads/{lead}/call-history', [App\Http\Controllers\VoxbayCallLogController::class, 'callHistory'])->whereNumber('lead');
        Route::get('notifications', [NotificationController::class, 'index']);

        // Marketing Leads APIs
        Route::get('marketing-leads', [MarketingLeadsController::class, 'index']);
        Route::post('marketing-leads', [MarketingLeadsController::class, 'store']);
        Route::get('marketing-leads/d2d-form-messages', [MarketingLeadsController::class, 'd2dFormMessages']);
        Route::get('marketing-leads/form-data', [MarketingLeadsController::class, 'formData']);
        Route::get('marketing-leads/filters', [MarketingLeadsController::class, 'filters']);

        // Follow-up Leads APIs
        Route::get('followup-leads', [FollowupLeadsController::class, 'index']);
        Route::get('followup-leads/filters', [FollowupLeadsController::class, 'filters']);

        // Registration Form Submitted Leads APIs
        Route::get('registration-leads', [RegistrationLeadsController::class, 'index']);
        Route::get('registration-leads/filters', [RegistrationLeadsController::class, 'filters']);
        Route::get('registration-leads/{lead}', [RegistrationLeadsController::class, 'show'])->whereNumber('lead');
        Route::get('registration-leads/{lead}/batches', [RegistrationLeadsController::class, 'batchesForLead'])->whereNumber('lead');
        Route::get('registration-leads/{lead}/convert', [RegistrationLeadsController::class, 'convert'])->whereNumber('lead');
        Route::post('registration-leads/{lead}/convert', [RegistrationLeadsController::class, 'convertSubmit'])->whereNumber('lead');
        Route::post('registration-leads/inline-update', [RegistrationLeadsController::class, 'inlineUpdate']);
        Route::post('registration-leads/document-verification', [RegistrationLeadsController::class, 'verifyDocument']);
        Route::post('registration-leads/add-sslc-certificate', [RegistrationLeadsController::class, 'addSSLCCertificates']);
        Route::post('registration-leads/add-document', [RegistrationLeadsController::class, 'addDocument']);

        // Converted Leads APIs
        Route::get('converted-leads', [ConvertedLeadsController::class, 'index']);
        Route::get('converted-leads/filters', [ConvertedLeadsController::class, 'filters']);
        Route::get('converted-leads/{id}', [ConvertedLeadsController::class, 'show'])->whereNumber('id');

        // Invoice APIs
        Route::get('invoices/student/{studentId}', [InvoiceController::class, 'getStudentInvoices'])->whereNumber('studentId');

        // Payment APIs
        Route::get('payments/invoice/{invoiceId}', [PaymentController::class, 'getInvoicePayments'])->whereNumber('invoiceId');
    });

    
});
