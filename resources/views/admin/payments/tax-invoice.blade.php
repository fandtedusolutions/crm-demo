@php
    use App\Models\Invoice;

    $isEduThanzeel = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 6);
    $isESchool = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 5);
    $isEduMaster = $payment->invoice->invoice_type === 'course' && ((int) ($payment->invoice->course_id ?? 0) === 23);

    $paymentLineTaxTotal = (float) ($payment->tax_invoice_total ?? 0);
    $invoiceNetTotal = (float) ($payment->invoice->net_amount ?? 0);
    $taxInvoiceTotal = isset($payment->tax_invoice_total) ? (float) $payment->tax_invoice_total : $invoiceNetTotal;
    $taxableAmount = isset($payment->tax_invoice_taxable) ? (float) $payment->tax_invoice_taxable : ($taxInvoiceTotal > 0 ? ($taxInvoiceTotal / 1.18) : 0.0);
    $gstAmount = isset($payment->tax_invoice_gst) ? (float) $payment->tax_invoice_gst : ($taxableAmount * 0.18);
    $feeHeadBalance = max($paymentLineTaxTotal - (float) ($payment->amount_paid ?? 0), 0);
    $courseCurrentBalance = (float) ($payment->invoice->pending_amount ?? 0);

    $edumasterRows = [];
    $sumFeeLineNet = 0.0;
    $sumFeeTaxable = 0.0;
    $sumFeeGst = 0.0;
    $hasMobileRow = false;
    $mobileListPrice = 0.0;
    $mobileLineNet = 0.0;
    $lineCount = 1;
    $gstTaxableForSplit = $taxableAmount;

    if ($isEduMaster) {
        $inv = $payment->invoice;
        $inv->loadMissing('course', 'student');
        $totalGross = (float) ($inv->total_amount ?? 0);
        $ratio = $totalGross > 0 ? ($invoiceNetTotal / $totalGross) : 0;
        foreach (['fee_pg_amount' => 'PG', 'fee_ug_amount' => 'UG', 'fee_plustwo_amount' => 'Plus Two', 'fee_sslc_amount' => 'SSLC'] as $col => $label) {
            $g = (float) ($inv->{$col} ?? 0);
            if ($g <= 0.00001) {
                continue;
            }
            $lineNet = round($g * $ratio, 2);
            $taxable = $lineNet > 0 ? round($lineNet / 1.18, 2) : 0.0;
            $gst = round($lineNet - $taxable, 2);
            $edumasterRows[] = ['label' => $label, 'line_net' => $lineNet, 'taxable' => $taxable, 'gst' => $gst];
            $sumFeeLineNet += $lineNet;
            $sumFeeTaxable += $taxable;
            $sumFeeGst += $gst;
        }
        $hasMobileRow = $inv->hasNeedMobileAddon();
        $mobileListPrice = $hasMobileRow ? Invoice::NEED_MOBILE_ADDON_GROSS : 0.0;
        $mobileLineNet = $hasMobileRow ? $inv->mobileNetAmount() : 0.0;
        $lineCount = count($edumasterRows) + ($hasMobileRow ? 1 : 0);
        $gstTaxableForSplit = $sumFeeTaxable;
        $taxInvoiceTotal = $invoiceNetTotal;
        $taxableAmount = $sumFeeTaxable + ($hasMobileRow ? $mobileLineNet : 0.0);
        $gstAmount = $sumFeeGst;
    }

    $invMobile = $payment->invoice;
    $isMobileFeeHeadOnly = false;
    $showNeedMobileSplit = !$isEduMaster
        && $invMobile->invoice_type === 'course'
        && $invMobile->student
        && $invMobile->student->need_mobile
        && $invMobile->mobileNetAmount() > 0
        && $taxInvoiceTotal > 0.00001;

    if ($isEduMaster) {
        $courseLineTotal = 0.0;
        $mobileLineTotal = 0.0;
        $taxableCourse = 0.0;
        $gstCourse = 0.0;
        $taxableMobile = 0.0;
        $gstMobile = 0.0;
    } elseif ($showNeedMobileSplit) {
        $lineSplit = $invMobile->splitLineAmountForMobile($taxInvoiceTotal);
        $courseLineTotal = $lineSplit['course'];
        $mobileLineTotal = $lineSplit['mobile'];
        $taxableCourse = $courseLineTotal > 0 ? round($courseLineTotal / 1.18, 2) : 0.0;
        $gstCourse = round($courseLineTotal - $taxableCourse, 2);
        $taxableMobile = $mobileLineTotal > 0 ? round($mobileLineTotal / 1.18, 2) : 0.0;
        $gstMobile = round($mobileLineTotal - $taxableMobile, 2);
        $taxableAmount = round($taxableCourse + $taxableMobile, 2);
        $gstAmount = round($gstCourse + $gstMobile, 2);
    } else {
        $courseLineTotal = $taxInvoiceTotal;
        $mobileLineTotal = 0.0;
        $taxableCourse = $taxableAmount;
        $gstCourse = $gstAmount;
        $taxableMobile = 0.0;
        $gstMobile = 0.0;
    }
@endphp

@extends('layouts.mantis')

@section('title', 'Tax Invoice - Payment #' . $payment->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Tax Invoice - {{ $payment->invoice->invoice_number }}</h4>
                        <div>
                            <!-- <button type="button" class="btn btn-success me-2" onclick="printInvoice()">
                                <i class="fas fa-print"></i> Print Invoice
                            </button> -->
                            <a href="{{ route('admin.payments.tax-invoice-pdf', $payment->id) }}" class="btn btn-danger me-2" target="_blank">
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
                    
                    <!-- Tax Invoice Title -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h3 class="mb-0" style="color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; font-weight: bold; border-top: 2px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; border-bottom: 2px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; padding: 10px 0;">Tax Invoice</h3>
                        </div>
                    </div>

                    <!-- Bill To and Invoice Details Section -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <h6 class="mb-2" style="color: #000; font-weight: bold;font-size: 12px !important;"><strong>Bill To:</strong></h6>
                            <p class="mb-1" style="font-size: 12px !important;"><strong>{{ $payment->invoice->student->name }}</strong></p>
                            <p class="mb-0" style="font-size: 12px !important;">Contact No.: {{ $payment->invoice->student->phone }}</p>
                        </div>
                        
                        <div class="col-6 text-end">
                            <h6 class="mb-2" style="color: #000; font-weight: bold;font-size: 12px !important;"><strong>Invoice Details:</strong></h6>
                            <p class="mb-1" style="font-size: 12px !important;">Invoice No.: {{ $payment->invoice->invoice_number }}</p>
                            <p class="mb-0" style="font-size: 12px !important;">Date: {{ $payment->invoice->invoice_date->format('d-m-Y') }}</p>
                            @if(!$isEduMaster && $payment->invoice->invoice_type === 'course' && $payment->invoice->student->need_mobile)
                                <p class="mb-0 mt-1" style="font-size: 12px !important;">
                                    <strong>Needed Mobile:</strong> ₹{{ number_format(\App\Models\Invoice::NEED_MOBILE_ADDON_GROSS, 2) }}
                                    @if($payment->invoice->student->asset_id)
                                        <span class="d-block"><strong>Asset ID:</strong> {{ $payment->invoice->student->asset_id }}</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Details Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered" style="border-collapse: collapse; width: 100%;">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-cell">#</th>
                                    <th class="table-cell">Item Name</th>
                                    <th class="table-cell">HSN/SAC</th>
                                    <th class="table-cell">Quantity</th>
                                    <th class="table-cell">Price/Unit</th>
                                    <th class="table-cell">GST</th>
                                    <th class="table-cell">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($isEduMaster)
                                    @php $emRow = 0; @endphp
                                    @foreach($edumasterRows as $row)
                                        @php $emRow++; @endphp
                                        <tr>
                                            <td class="table-cell">{{ $emRow }}</td>
                                            <td class="table-cell" style="font-weight: bold !important;">{{ $row['label'] }}</td>
                                            <td class="table-cell">{{ $payment->invoice->course->code ?? 'N/A' }}</td>
                                            <td class="table-cell">1</td>
                                            <td class="table-cell">₹{{ number_format($row['taxable'], 2) }}</td>
                                            <td class="table-cell">₹{{ number_format($row['gst'], 2) }} (18%)</td>
                                            <td class="table-cell">₹{{ number_format($row['line_net'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                    @if($hasMobileRow)
                                        @php $emRow++; @endphp
                                        <tr>
                                            <td class="table-cell">{{ $emRow }}</td>
                                            <td class="table-cell" style="font-weight: bold !important;">Needed Mobile</td>
                                            <td class="table-cell">{{ $payment->invoice->course->code ?? 'N/A' }}</td>
                                            <td class="table-cell">1</td>
                                            <td class="table-cell">₹{{ number_format($mobileListPrice, 2) }}</td>
                                            <td class="table-cell">—</td>
                                            <td class="table-cell">₹{{ number_format($mobileLineNet, 2) }}</td>
                                        </tr>
                                    @endif
                                @else
                                <tr>
                                    <td class="table-cell">1</td>
                                    <td class="table-cell" style="font-weight: bold !important;">
                                        @if($isEduMaster && $payment->fee_head)
                                            @if($payment->fee_head === 'MOBILE')
                                                Needed Mobile
                                            @elseif($payment->fee_head === 'PLUS_TWO')
                                                Plus Two
                                            @else
                                                {{ $payment->fee_head }}
                                            @endif
                                        @elseif($payment->invoice->invoice_type === 'course')
                                            @if($payment->invoice->course_id == 9 && $payment->invoice->student->leadDetail)
                                                @php
                                                    $studentDetail = $payment->invoice->student->leadDetail;
                                                    $university = $studentDetail->university;
                                                    $courseType = $studentDetail->course_type;
                                                @endphp
                                                @if($university && $courseType)
                                                    Payment for {{ $university->title }} - {{ $courseType }}
                                                @else
                                                    Payment for {{ $payment->invoice->course->title ?? 'N/A' }}
                                                @endif
                                            @else
                                                Payment for {{ $payment->invoice->course->title ?? 'N/A' }}
                                            @endif
                                        @elseif($payment->invoice->invoice_type === 'e-service')
                                            Payment for {{ $payment->invoice->service_name ?? 'N/A' }}
                                        @elseif($payment->invoice->invoice_type === 'batch_change')
                                            Payment for Batch Change - {{ $payment->invoice->batch->title ?? 'N/A' }} ({{ $payment->invoice->batch->course->title ?? 'N/A' }})
                                        @elseif($payment->invoice->invoice_type === 'batch_postpond')
                                            Payment for Batch Postponed - {{ $payment->invoice->batch->title ?? 'N/A' }} ({{ $payment->invoice->batch->course->title ?? 'N/A' }})
                                        @elseif($payment->invoice->invoice_type === 'fine')
                                            Payment for Fine - {{ $payment->invoice->service_name ?? 'N/A' }}
                                        @elseif($payment->invoice->invoice_type === 'supply')
                                            Payment for Supply
                                        @else
                                            Payment
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        @if($payment->invoice->invoice_type === 'course')
                                            {{ $payment->invoice->course->code ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="table-cell">1</td>
                                    <td class="table-cell">₹{{ number_format($taxableCourse, 2) }}</td>
                                    <td class="table-cell">₹{{ number_format($gstCourse, 2) }} (18%)</td>
                                    <td class="table-cell">₹{{ number_format($courseLineTotal, 2) }}</td>
                                </tr>
                                @if($showNeedMobileSplit)
                                <tr>
                                    <td class="table-cell">2</td>
                                    <td class="table-cell" style="font-weight: bold !important;">Needed Mobile</td>
                                    <td class="table-cell">
                                        @if($payment->invoice->invoice_type === 'course')
                                            {{ $payment->invoice->course->code ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="table-cell">1</td>
                                    <td class="table-cell">₹{{ number_format($taxableMobile, 2) }}</td>
                                    <td class="table-cell">₹{{ number_format($gstMobile, 2) }} (18%)</td>
                                    <td class="table-cell">₹{{ number_format($mobileLineTotal, 2) }}</td>
                                </tr>
                                @endif
                                @endif
                                <tr class="total-row">
                                    <td class="table-cell" colspan="3"><strong>Total</strong></td>
                                    <td class="table-cell">{{ $isEduMaster ? $lineCount : ($showNeedMobileSplit ? 2 : 1) }}</td>
                                    <td class="table-cell">₹{{ number_format($isEduMaster ? ($sumFeeTaxable + ($hasMobileRow ? $mobileListPrice : 0.0)) : $taxableAmount, 2) }}</td>
                                    <td class="table-cell">₹{{ number_format($gstAmount, 2) }}</td>
                                    <td class="table-cell"><strong>₹{{ number_format($isEduMaster ? $invoiceNetTotal : $taxInvoiceTotal, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tax Breakdown and Amounts Summary -->
                    <div class="row mb-4">
                        <!-- Tax Breakdown -->
                        <div class="col-6">
                            <div class="section-content">
                                <h6 class="section-header mb-3" style="font-size: 12px !important;"><strong>Tax Type</strong></h6>
                                <table class="table table-sm table-borderless" style="margin-bottom: 0; font-size: 12px !important;">
                                    <thead>
                                        <tr>
                                            <th style="padding: 5px; border-bottom: 1px solid #ddd; font-size: 12px !important;">Tax Type</th>
                                            <th style="padding: 5px; border-bottom: 1px solid #ddd; font-size: 12px !important;">Taxable Amount</th>
                                            <th style="padding: 5px; border-bottom: 1px solid #ddd; font-size: 12px !important;">Rate</th>
                                            <th style="padding: 5px; border-bottom: 1px solid #ddd; font-size: 12px !important;">Tax Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 5px; font-size: 12px !important;">SGST</td>
                                            <td style="padding: 5px; font-size: 12px !important;">₹{{ number_format($isEduMaster ? $gstTaxableForSplit : $taxableAmount, 2) }}</td>
                                            <td style="padding: 5px; font-size: 12px !important;">9%</td>
                                            <td style="padding: 5px; font-size: 12px !important;">₹{{ number_format($gstAmount * 0.5, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px; font-size: 12px !important;">CGST</td>
                                            <td style="padding: 5px; font-size: 12px !important;">₹{{ number_format($isEduMaster ? $gstTaxableForSplit : $taxableAmount, 2) }}</td>
                                            <td style="padding: 5px; font-size: 12px !important;">9%</td>
                                            <td style="padding: 5px; font-size: 12px !important;">₹{{ number_format($gstAmount * 0.5, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Amounts Summary -->
                        <div class="col-6">
                            <div class="section-content">
                                <h6 class="section-header mb-3" style="font-size: 12px !important;"><strong>Amounts</strong></h6>
                                <div class="row" style="font-size: 12px !important;">
                                    <div class="col-6">
                                        <p class="mb-1" style="font-size: 12px !important; border-bottom: 1px solid #ddd; padding-bottom: 5px;"><strong>Sub Total:</strong></p>
                                        <p class="mb-1" style="font-size: 12px !important;"><strong>Total:</strong></p>
                                        <p class="mb-1" style="font-size: 12px !important;"><strong>Received:</strong></p>
                                        <!-- <p class="mb-1" style="font-size: 12px !important; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 5px 0;"><strong>Balance:</strong></p>
                                        <p class="mb-1" style="font-size: 12px !important;"><strong>Previous Balance:</strong></p> -->
                                        @if($isEduMaster && $payment->fee_head)
                                            <p class="mb-1" style="font-size: 12px !important;"><strong>Fee Head Balance:</strong></p>
                                            <p class="mb-0" style="font-size: 12px !important;border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 5px 0;"><strong>Course Current Balance:</strong></p>
                                        @else
                                            <p class="mb-0" style="font-size: 12px !important;border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 5px 0;"><strong>Current Balance:</strong></p>
                                        @endif
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-1" style="font-size: 12px !important; border-bottom: 1px solid #ddd; padding-bottom: 5px;">₹{{ number_format($taxableAmount, 2) }}</p>
                                        <p class="mb-1" style="font-size: 12px !important;"><strong>₹{{ number_format($isEduMaster ? $invoiceNetTotal : $taxInvoiceTotal, 2) }}</strong></p>
                                        <p class="mb-1" style="font-size: 12px !important;">₹{{ number_format(round((float) $payment->amount_paid), 0) }}</p>
                                        <!-- <p class="mb-1" style="font-size: 12px !important; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 5px 0;">₹{{ number_format($payment->invoice->total_amount - $payment->amount_paid, 2) }}</p> -->
                                        <!-- <p class="mb-1" style="font-size: 12px !important;">₹{{ number_format($payment->previous_balance, 2) }}</p> -->
                                        @if($isEduMaster && $payment->fee_head)
                                            <p class="mb-1" style="font-size: 12px !important;">₹{{ number_format($feeHeadBalance, 2) }}</p>
                                            <p class="mb-0" style="font-size: 12px !important;border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 5px 0;">₹{{ number_format($courseCurrentBalance, 2) }}</p>
                                        @else
                                            <p class="mb-0" style="font-size: 12px !important;border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 5px 0;">₹{{ number_format($courseCurrentBalance, 2) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Amount in Words -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="section-content">
                                <h6 class="section-header mb-2" style="font-size: 12px !important;"><strong>Invoice Amount In Words</strong></h6>
                                <p class="mb-0" style="font-size: 12px !important;">{{ $payment->total_amount_in_words }} Rupees only</p>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="section-content">
                                <h6 class="section-header mb-2" style="font-size: 12px !important;"><strong>Terms and Conditions</strong></h6>
                                <p class="mb-0" style="font-size: 12px !important;">THIS AMOUNT IS NON REFUNDABLE</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="section-content">
                                <h6 class="section-header mb-2" style="font-size: 12px !important;"><strong>Bank Details</strong></h6>
                                <p class="mb-1" style="font-size: 12px !important;"><strong>Name:</strong> AXIS BANK, KALLAI ROAD, KOZHIKODE</p>
                                <p class="mb-1" style="font-size: 12px !important;"><strong>Account No.:</strong> 921020041902527</p>
                                <p class="mb-1" style="font-size: 12px !important;"><strong>IFSC Code:</strong> UTIB0001908</p>
                                <p class="mb-0" style="font-size: 12px !important;"><strong>Account Holder's Name:</strong> FUTURE AND TREE</p>
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
                            <a href="{{ route('admin.payments.tax-invoice-pdf', $payment->id) }}" class="btn btn-primary me-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> View PDF
                            </a>
                            <!-- <button onclick="window.print()" class="btn btn-primary me-2">
                                <i class="fas fa-print me-1"></i> Print Invoice
                            </button> -->
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

<script>
function printInvoice() {
    // Get the invoice container content
    const invoiceContent = document.querySelector('.invoice-container').innerHTML;
    
    // Create a new window for printing
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    // Write the content to the new window
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Tax Invoice - {{ $payment->invoice->invoice_number }}</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.3;
                    color: #000;
                    background: white;
                    margin: 0;
                    padding: 15px;
                }
                
                .invoice-container {
                    width: 100%;
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    border-top: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
                    border-bottom: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
                    padding: 20px;
                }
                
                /* Header Section */
                .header-section {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 20px;
                }
                
                .logo-section {
                    display: flex;
                    align-items: flex-start;
                }
                
                .company-logo {
                    height: 80px;
                    width: auto;
                    margin-right: 15px;
                }
                
                .logo-text {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }
                
                .company-name {
                    font-size: 18px;
                    font-weight: bold;
                    color: #000;
                    margin-bottom: 3px;
                }
                
                .company-tagline {
                    font-size: 11px;
                    color: #666;
                }
                
                .company-details {
                    text-align: right;
                    font-size: 10px;
                    line-height: 1.4;
                }
                
                .company-details .company-name {
                    font-size: 14px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                
                /* Tax Invoice Title */
                .invoice-title {
                    text-align: center;
                    color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
                    font-weight: bold;
                    font-size: 18px;
                    margin: 20px 0;
                    border-top: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
                    border-bottom: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
                    padding: 10px 0;
                }
                
                /* Bill To and Invoice Details */
                .details-section {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 20px;
                }
                
                .bill-to, .invoice-details {
                    width: 48%;
                }
                
                .bill-to h6, .invoice-details h6 {
                    font-weight: bold;
                    font-size: 12px;
                    margin-bottom: 8px;
                    color: #000;
                }
                
                .bill-to p, .invoice-details p {
                    margin: 3px 0;
                    font-size: 11px;
                }
                
                /* Tables */
                .table-container {
                    margin-bottom: 20px;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 10px;
                }
                
                .table-header {
                    background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
                    color: white;
                }
                
                .table-header th {
                    padding: 10px 8px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 10px;
                    border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
                }
                
                .table-cell {
                    padding: 8px;
                    border: 1px solid #ddd;
                    font-size: 10px;
                }
                
                .total-row {
                    background-color: #f8f9fa;
                    font-weight: bold;
                }
                
                /* Section Headers */
                .section-header {
                    background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};
                    color: white;
                    padding: 10px 12px;
                    margin: 0 -12px 12px -12px;
                    font-weight: bold;
                    font-size: 12px;
                }
                
                .section-content {
                    background-color: #f8f9fa;
                    padding: 12px;
                    margin-bottom: 15px;
                }
                
                /* Tax Type Table */
                .tax-type-table {
                    margin-bottom: 10px;
                }
                
                .tax-type-table th {
                    padding: 6px 8px;
                    font-size: 10px;
                    font-weight: bold;
                    background: white;
                    color: #000;
                    border: none;
                    text-align: left;
                }
                
                .tax-type-table td {
                    padding: 6px 8px;
                    font-size: 10px;
                    border: none;
                }
                
                /* Amounts Section */
                .amounts-section {
                    display: flex;
                    justify-content: space-between;
                }
                
                .amounts-labels, .amounts-values {
                    width: 48%;
                }
                
                .amounts-labels p, .amounts-values p {
                    margin: 4px 0;
                    font-size: 10px;
                }
                
                .amounts-values {
                    text-align: right;
                }
                
                .border-bottom {
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 4px;
                }
                
                .border-top-bottom {
                    border-top: 1px solid #ddd;
                    border-bottom: 1px solid #ddd;
                    padding: 4px 0;
                }
                
                /* Other Sections */
                .other-sections p {
                    margin: 4px 0;
                    font-size: 10px;
                }
                
                /* Signature Section */
                .signature-section {
                    text-align: right;
                    margin-top: 25px;
                }
                
                .signature-line {
                    border-bottom: 1px solid #333;
                    width: 180px;
                    margin: 15px 0 8px auto;
                }
                
                .signature-section p {
                    font-size: 10px;
                    margin: 3px 0;
                }
                
                /* Print optimizations */
                @media print {
                    body {
                        margin: 0;
                        padding: 10px;
                    }
                    
                    .invoice-container {
                        padding: 15px;
                        max-width: 100%;
                    }
                    
                    @page {
                        size: A4;
                        margin: 0.5in;
                    }
                }
            </style>
        </head>
        <body>
            <div class="invoice-container">
                ${invoiceContent}
            </div>
        </body>
        </html>
    `);
    
    // Close the document and trigger print
    printWindow.document.close();
    
    // Wait for content to load, then print
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
    };
}
</script>

