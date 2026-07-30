@php
    $isEduThanzeel = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 6);
    $isESchool = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 5);
    $isEduMaster = $payment->invoice->invoice_type === 'course' && ((int) ($payment->invoice->course_id ?? 0) === 23);
    $feeHeadDisplay = match ($payment->fee_head ?? null) {
        'PLUS_TWO' => 'Plus Two',
        'MOBILE' => 'Needed Mobile',
        default => $payment->fee_head ?? null,
    };
    $invReceipt = $payment->invoice;
    $showReceiptMobileSplit = $invReceipt->invoice_type === 'course'
        && $invReceipt->student
        && $invReceipt->student->need_mobile
        && $invReceipt->mobileNetAmount() > 0;
@endphp

@extends('layouts.mantis')

@section('title', 'Payment Receipt - Payment #' . $payment->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Payment Receipt - {{ $payment->invoice->invoice_number }}</h4>
                        <div>
                            <a href="{{ route('admin.payments.payment-receipt-pdf', $payment->id) }}" class="btn btn-danger me-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> View PDF
                            </a>
                            <a href="{{ route('admin.payments.index', $payment->invoice_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Payments
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body invoice-container" style="border-top: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; border-bottom: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; padding: 30px;">
                    <!-- Invoice Header -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <!-- Company Logo and Info -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $isEduThanzeel ? asset('storage/eduthanzeel.png') : ($isESchool ? asset('storage/eschool.png') : asset('storage/logo.png')) }}" alt="Company Logo" class="company-logo">
                            </div>
                            
                            <!-- Company Address -->
                        </div>
                        
                        <div class="col-6 text-end">
                            <!-- Company Name (Right Side) -->
                            <div class="mb-3">
                                <h3 class="mb-0 company-name">{{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</h3>
                            </div>
                            
                            <!-- Company Address (Right Side) -->
                            <div class="company-details mb-3">
                                <p class="mb-1">PALATHINGAL, ULLANAM P.O,</p>
                                <p class="mb-1">676303</p>
                                <p class="mb-1">REG.OFFICE 2/421A, PANTHARANGADI PO </p>
                                <p class="mb-1">676306</p>
                                <p class="mb-1">Phone no.: 6282055715 Email: nisaskillpark@gmail.com</p>
                                <p class="mb-0">GSTIN: 32AAECF7209B1Z7, State: 32-Kerala</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Receipt Title -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h3 class="mb-0" style="color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; font-weight: bold; border-top: 2px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; border-bottom: 2px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; padding: 10px 0;">Payment Receipt</h3>
                        </div>
                    </div>

                    <!-- Bill To and Payment Details Section -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <h6 class="mb-2" style="color: #000; font-weight: bold;font-size: 12px !important;"><strong>Bill To:</strong></h6>
                            <p class="mb-1" style="font-size: 12px !important;"><strong>{{ $payment->invoice->student->name }}</strong></p>
                            <p class="mb-0" style="font-size: 12px !important;">Contact No.: {{ $payment->invoice->student->phone }}</p>
                            @if($payment->invoice->invoice_type === 'course' && $payment->invoice->student->need_mobile)
                                <p class="mb-0 mt-2" style="font-size: 12px !important;">
                                    <strong>Needed Mobile:</strong> ₹{{ number_format(\App\Models\Invoice::NEED_MOBILE_ADDON_GROSS, 2) }}
                                    @if($payment->invoice->student->asset_id)
                                        <br><strong>Asset ID:</strong> {{ $payment->invoice->student->asset_id }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        
                        <div class="col-6 text-end">
                            <h6 class="mb-2" style="color: #000; font-weight: bold;font-size: 12px !important;"><strong>Payment Details:</strong></h6>
                            <p class="mb-1" style="font-size: 12px !important;">Invoice No.: {{ $payment->invoice->invoice_number }}</p>
                            <p class="mb-1" style="font-size: 12px !important;">Payment Date: {{ $payment->created_at->format('d-m-Y') }}</p>
                            <p class="mb-1" style="font-size: 12px !important;">Payment ID: #{{ $payment->id }}</p>
                            <p class="mb-0" style="font-size: 12px !important;">
                                @if($payment->invoice->invoice_type === 'course')
                                    @if($isEduMaster && $feeHeadDisplay)
                                        {{ $feeHeadDisplay }}
                                    @elseif($payment->invoice->course_id == 9 && $payment->invoice->student->leadDetail)
                                        @php
                                            $studentDetail = $payment->invoice->student->leadDetail;
                                            $university = $studentDetail->university;
                                            $courseType = $studentDetail->course_type;
                                        @endphp
                                        @if($university && $courseType)
                                            Type: {{ $university->title }} - {{ $courseType }}
                                        @else
                                            Type: Course - {{ $payment->invoice->course->title ?? 'N/A' }}
                                        @endif
                                    @elseif($payment->invoice->course_id == 16 && $payment->invoice->student->leadDetail)
                                        @php
                                            $studentDetail = $payment->invoice->student->leadDetail;
                                            $class = $studentDetail->class;
                                        @endphp
                                        Type: Course - {{ $payment->invoice->course->title ?? 'N/A' }}
                                        @if($class)
                                            <span class="badge bg-primary ms-1">{{ strtoupper($class) }}</span>
                                        @endif
                                    @else
                                        Type: Course - {{ $payment->invoice->course->title ?? 'N/A' }}
                                    @endif
                                @elseif($payment->invoice->invoice_type === 'e-service')
                                    Type: E-Service - {{ $payment->invoice->service_name ?? 'N/A' }}
                                @elseif($payment->invoice->invoice_type === 'batch_change')
                                    Type: Batch Change - {{ $payment->invoice->batch->title ?? 'N/A' }} ({{ $payment->invoice->batch->course->title ?? 'N/A' }})
                                @elseif($payment->invoice->invoice_type === 'batch_postpond')
                                    Type: Batch Postponed - {{ $payment->invoice->batch->title ?? 'N/A' }} ({{ $payment->invoice->batch->course->title ?? 'N/A' }})
                                @elseif($payment->invoice->invoice_type === 'fine')
                                    Type: Fine - {{ $payment->invoice->service_name ?? 'N/A' }}
                                @elseif($payment->invoice->invoice_type === 'supply')
                                    Type: Supply
                                @else
                                    Type: N/A
                                @endif
                            </p>
                        </div>
                    </div>


                    <!-- Payment Summary -->
                    <div class="row mb-4">

                        <!-- Payment Amount in Words -->
                        <div class="col-6">
                            <div class="section-content">
                                <h6 class="section-header mb-2" style="font-size: 12px !important;"><strong>Payment Amount In Words</strong></h6>
                                <p class="mb-0" style="font-size: 12px !important;">{{ $payment->amount_in_words }} Rupees only</p>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="section-content">
                                <h6 class="section-header mb-3" style="font-size: 12px !important;"><strong>Payment Summary</strong></h6>
                                <div class="row" style="font-size: 12px !important;">
                                    <div class="col-6">
                                        @if($showReceiptMobileSplit)
                                            <p class="mb-1" style="font-size: 12px !important; border-bottom: 1px solid #ddd; padding-bottom: 5px;"><strong>Amount:</strong></p>
                                            <p class="mb-1" style="font-size: 12px !important; border-bottom: 1px solid #ddd; padding-bottom: 5px;"><strong>Needed Mobile:</strong></p>
                                        @endif
                                        <p class="mb-1" style="font-size: 12px !important; border-bottom: 1px solid #ddd; padding-bottom: 5px;"><strong>Total Amount:</strong></p>
                                        <p class="mb-1" style="font-size: 12px !important;"><strong>Received:</strong></p>
                                        <p class="mb-0" style="font-size: 12px !important;border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 5px 0;"><strong>Current Balance:</strong></p>
                                    </div>
                                    <div class="col-6 text-end">
                                        @if($showReceiptMobileSplit)
                                            <p class="mb-1" style="font-size: 12px !important; border-bottom: 1px solid #ddd; padding-bottom: 5px;">₹{{ number_format($invReceipt->courseNetExcludingMobile(), 2) }}</p>
                                            <p class="mb-1" style="font-size: 12px !important; border-bottom: 1px solid #ddd; padding-bottom: 5px;">₹{{ number_format($invReceipt->mobileNetAmount(), 2) }}</p>
                                        @endif
                                        <p class="mb-1" style="font-size: 12px !important; border-bottom: 1px solid #ddd; padding-bottom: 5px;">₹{{ number_format($payment->invoice->net_amount, 2) }}</p>
                                        <p class="mb-1" style="font-size: 12px !important;">₹{{ number_format($payment->amount_paid, 2) }}</p>
                                        <p class="mb-0" style="font-size: 12px !important;border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 5px 0;">₹{{ number_format($payment->invoice->pending_amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Signature Section -->
                    <div class="row">
                        <div class="col-6"></div>
                        <div class="col-6 text-center">
                            <div class="border-top pt-3">
                                <p class="mb-1"><strong>For: {{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</strong></p>
                                <div class="mt-3" style="height: 60px; display: flex; align-items: center; justify-content: center;">
                                    @if(file_exists(storage_path('app/public/accounts-sign.png')))
                                        <img src="{{ asset('storage/accounts-sign.png') }}" alt="Signature" style="max-height: 60px; max-width: 150px; object-fit: contain;">
                                    @else
                                        <div style="height: 60px; width: 150px; border-bottom: 1px solid #333;"></div>
                                    @endif
                                </div>
                                <p class="mb-0"><strong>Authorized Signatory</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('admin.payments.payment-receipt-pdf', $payment->id) }}" class="btn btn-primary me-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> View PDF
                            </a>
                            <a href="{{ route('admin.payments.index', $payment->invoice_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Payments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles to match the exact design */
.invoice-container {
    font-family: Arial, sans-serif;
    background-color: white;
    padding: 30px;
    border-top: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
    border-bottom: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
}

.company-logo {
    height: 150px !important;
    width: auto;
    /* object-fit: contain; */
    padding: 10px !important;
    margin-top: 35px !important;
    margin-right: 15px !important;
}

.company-name {
    color: #000;
    font-weight: bold;
    font-size: 1.1rem;
}

.company-tagline {
    color: #666;
    font-size: 0.9rem;
}

.company-details > p {
    /* color: #666; */
    font-size: 12px;
}

.invoice-title {
    color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
    font-weight: bold;
    text-align: center;
}

.section-header {
    background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
    color: white;
    padding: 8px;
    margin: -12px -12px 12px -12px;
    border-radius: 5px 5px 0 0;
    font-weight: bold;
}

.section-content {
    background-color: #f8f9fa;
    padding: 12px;
    border-radius: 5px;
}

.table-header {
    background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
    color: white !important;
}
.table-header > tr > .table-cell {
    color: white !important;
    font-size: 12px !important;
}

.table-cell {
    padding: 10px;
    border: 1px solid #ddd;
    font-size: 12px !important;
}

.total-row {
    background-color: #f8f9fa;
    font-weight: bold;
}

@media print {
    .btn, .card-header, .card-footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        font-size: 12px;
    }
    .invoice-container {
        padding: 0;
    }
}
</style>
@endsection

