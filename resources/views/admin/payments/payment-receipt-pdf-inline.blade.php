@php
    $isEduThanzeel = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 6);
    $isESchool = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 5);
    $isEduMaster = $payment->invoice->invoice_type === 'course' && ((int) ($payment->invoice->course_id ?? 0) === 23);
    $feeHeadDisplay = match ($payment->fee_head ?? null) {
        'PLUS_TWO' => 'Plus Two',
        'MOBILE' => 'Needed Mobile',
        default => $payment->fee_head ?? null,
    };
    $invReceiptPdf = $payment->invoice;
    $showReceiptMobileSplit = $invReceiptPdf->invoice_type === 'course'
        && $invReceiptPdf->student
        && $invReceiptPdf->student->need_mobile
        && $invReceiptPdf->mobileNetAmount() > 0;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->invoice->invoice_number }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path("fonts/DejaVuSans.ttf") }}') format('truetype');
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        .rupee {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            /* font-weight: bold; */
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px; line-height: 1.3; color: #000; background: white; margin: 0; padding: 5px;">
    <div style="width: 100%; max-width: 900px; margin: 0 auto; background: white;">
        
        <!-- Header Section -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td style="vertical-align: top; padding-right: 15px;">
                                @if($isEduThanzeel && file_exists(public_path('storage/eduthanzeel.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/eduthanzeel.png'))) }}" alt="Company Logo" style="height: 90px; width: auto; margin-top: 25px !important;">
                                @elseif($isESchool && file_exists(public_path('storage/eschool.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/eschool.png'))) }}" alt="Company Logo" style="height: 90px; width: auto; margin-top: 25px !important;">
                                @elseif(file_exists(public_path('storage/logo.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/logo.png'))) }}" alt="Company Logo" style="height: 90px; width: auto; margin-top: 25px !important;">
                                @else
                                    <div style="height: 80px; width: 80px; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #666; margin-top: 25px !important;">{{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right; font-size: 10px; line-height: 1.4;">
                        <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">{{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</div>
                    <p style="margin: 2px 0;">PALATHINGAL, ULLANAM P.O,</p>
                    <p style="margin: 2px 0;">676303</p>
                    <p style="margin: 2px 0;">REG.OFFICE 2/421A, PANTHARANGADI PO</p>
                    <p style="margin: 2px 0;">676306</p>
                    <p style="margin: 2px 0;">Phone no.: 6282055715 Email: nisaskillpark@gmail.com</p>
                    <p style="margin: 2px 0;">GSTIN: 32AAECF7209B1Z7, State: 32-Kerala</p>
                </td>
            </tr>
        </table>

        <!-- Payment Receipt Title -->
                        <div style="text-align: center; color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; font-weight: bold; font-size: 18px; border-top: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; border-bottom: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; padding: 10px 0;">Payment Receipt</div>

        <!-- Bill To and Payment Details -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 48%; vertical-align: top; padding-right: 10px;">
                    <h6 style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #000;">Bill To</h6>
                    <p style="margin: 3px 0; font-size: 11px;">Name: {{ $payment->invoice->student->name }}</p>
                    <p style="margin: 3px 0; font-size: 11px;">Contact No.: {{ $payment->invoice->student->phone }}</p>
                    @if($payment->invoice->invoice_type === 'course' && $payment->invoice->student->need_mobile)
                        <p style="margin: 6px 0 0 0; font-size: 11px;">
                            <strong>Needed Mobile:</strong> <span class="rupee">₹</span> {{ number_format(\App\Models\Invoice::NEED_MOBILE_ADDON_GROSS, 2) }}
                            @if($payment->invoice->student->asset_id)
                                <br><strong>Asset ID:</strong> {{ $payment->invoice->student->asset_id }}
                            @endif
                        </p>
                    @endif
                </td>
                <td style="width: 48%; vertical-align: top; padding-left: 10px; text-align: right;">
                    <h6 style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #000;">Payment Details</h6>
                    <p style="margin: 3px 0; font-size: 11px;">Invoice No.: {{ $payment->invoice->invoice_number }}</p>
                    <p style="margin: 3px 0; font-size: 11px;">Payment Date: {{ $payment->created_at->format('d-m-Y') }}</p>
                    <p style="margin: 3px 0; font-size: 11px;">Payment ID: #{{ $payment->id }}</p>
                    <p style="margin: 3px 0; font-size: 11px;">
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
                </td>
            </tr>
        </table>

        <!-- Main Content Area - Table Layout for PDF -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <!-- Left Column -->
                <td style="width: 48%; vertical-align: top; padding-right: 5px;">

                    <!-- Payment Amount In Words -->
                    <div style="padding: 12px; margin-bottom: 15px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 10px 12px; margin: -12px -12px 12px -12px; font-weight: bold; font-size: 12px;">Payment Amount In Words</h6>
                        <p style="margin: 4px 0; font-size: 10px;">{{ $payment->amount_in_words }} Rupees only</p>
                    </div>

                </td>

                <!-- Right Column -->
                <td style="width: 48%; vertical-align: top; padding-left: 5px;">
                    
                    <!-- Payment Summary Section -->
                    <div style="padding: 12px; margin-bottom: 15px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 10px 12px; margin: -12px -12px 12px -12px; font-weight: bold; font-size: 12px;">Payment Summary</h6>
                        <table style="width: 100%; border-collapse: collapse;">
                            @if($showReceiptMobileSplit)
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px; border-bottom: 1px solid #ddd; padding-bottom: 4px;">Amount</td>
                                <td style="padding: 4px 0; font-size: 10px; border-bottom: 1px solid #ddd; padding-bottom: 4px; text-align: right;"><span class="rupee">₹</span> {{ number_format($invReceiptPdf->courseNetExcludingMobile(), 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px; border-bottom: 1px solid #ddd; padding-bottom: 4px;">Needed Mobile</td>
                                <td style="padding: 4px 0; font-size: 10px; border-bottom: 1px solid #ddd; padding-bottom: 4px; text-align: right;"><span class="rupee">₹</span> {{ number_format($invReceiptPdf->mobileNetAmount(), 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px; border-bottom: 1px solid #ddd; padding-bottom: 4px;">Total Amount</td>
                                <td style="padding: 4px 0; font-size: 10px; border-bottom: 1px solid #ddd; padding-bottom: 4px; text-align: right;"><span class="rupee">₹</span> {{ number_format($payment->invoice->net_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px;">Previous Balance</td>
                                <td style="padding: 4px 0; font-size: 10px; text-align: right;"><span class="rupee">₹</span> {{ number_format($payment->invoice->net_amount - ($payment->previous_balance ?? 0), 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px;"><strong>Received</strong></td>
                                <td style="padding: 4px 0; font-size: 10px; text-align: right;"><strong><span class="rupee">₹</span> {{ number_format($payment->amount_paid, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px; border-top: 1px solid #ddd; padding: 4px 0;">Balance</td>
                                <td style="padding: 4px 0; font-size: 10px; text-align: right; border-top: 1px solid #ddd; padding: 4px 0;"><span class="rupee">₹</span> {{ number_format(($payment->invoice->net_amount - ($payment->previous_balance ?? 0)) - $payment->amount_paid, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; font-size: 10px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 4px 0;">Current Balance</td>
                                <td style="padding: 4px 0; font-size: 10px; text-align: right; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 4px 0;"><span class="rupee">₹</span> {{ number_format($payment->invoice->pending_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Signature Section -->
                    <div style="text-align: center; margin-top: 60px;">
                        <p style="font-size: 10px; margin: 3px 0;">For: {{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</p>
                        <div style="height: 60px; margin: 10px 0; display: flex; align-items: center; justify-content: center;">
                            @if(file_exists(storage_path('app/public/accounts-sign.png')))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/accounts-sign.png'))) }}" alt="Signature" style="max-height: 60px; max-width: 150px; object-fit: contain;">
                            @else
                                <div style="height: 60px; width: 150px; border-bottom: 1px solid #333;"></div>
                            @endif
                        </div>
                        <p style="font-size: 10px; margin: 3px 0;">Authorized Signatory</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

