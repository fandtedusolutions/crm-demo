@php
    use App\Models\Invoice;

    $isEduThanzeel = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 6);
    $isESchool = $payment->invoice->invoice_type === 'course' && ($payment->invoice->course_id == 5);
    $isEduMaster = $payment->invoice->invoice_type === 'course' && ((int) ($payment->invoice->course_id ?? 0) === 23);

    $inv = $payment->invoice;
    $inv->loadMissing('course', 'student');

    $invoiceNetTotal = (float) ($inv->net_amount ?? 0);
    $totalGross = (float) ($inv->total_amount ?? 0);
    $ratio = $totalGross > 0 ? ($invoiceNetTotal / $totalGross) : 0;

    $edumasterRows = [];
    $sumFeeLineNet = 0.0;
    $sumFeeTaxable = 0.0;
    $sumFeeGst = 0.0;

    if ($isEduMaster) {
        $feeHeadDefs = [
            ['col' => 'fee_pg_amount', 'label' => 'PG'],
            ['col' => 'fee_ug_amount', 'label' => 'UG'],
            ['col' => 'fee_plustwo_amount', 'label' => 'Plus Two'],
            ['col' => 'fee_sslc_amount', 'label' => 'SSLC'],
        ];
        foreach ($feeHeadDefs as $def) {
            $g = (float) ($inv->{$def['col']} ?? 0);
            if ($g <= 0.00001) {
                continue;
            }
            $lineNet = round($g * $ratio, 2);
            $taxable = $lineNet > 0 ? round($lineNet / 1.18, 2) : 0.0;
            $gst = round($lineNet - $taxable, 2);
            $edumasterRows[] = [
                'label' => $def['label'],
                'line_net' => $lineNet,
                'taxable' => $taxable,
                'gst' => $gst,
            ];
            $sumFeeLineNet += $lineNet;
            $sumFeeTaxable += $taxable;
            $sumFeeGst += $gst;
        }

        $hasMobileRow = $inv->hasNeedMobileAddon();
        $mobileListPrice = $hasMobileRow ? Invoice::NEED_MOBILE_ADDON_GROSS : 0.0;
        $mobileLineNet = $hasMobileRow ? $inv->mobileNetAmount() : 0.0;

        $lineCount = count($edumasterRows) + ($hasMobileRow ? 1 : 0);
    }

    $paymentLineTaxTotal = (float) ($payment->tax_invoice_total ?? 0);
    $taxInvoiceTotal = isset($payment->tax_invoice_total) ? (float) $payment->tax_invoice_total : $invoiceNetTotal;
    $taxableAmount = isset($payment->tax_invoice_taxable) ? (float) $payment->tax_invoice_taxable : ($taxInvoiceTotal > 0 ? ($taxInvoiceTotal / 1.18) : 0.0);
    $gstAmount = isset($payment->tax_invoice_gst) ? (float) $payment->tax_invoice_gst : ($taxableAmount * 0.18);

    if ($isEduMaster) {
        $taxableAmount = $sumFeeTaxable + ($hasMobileRow ? $mobileLineNet : 0.0);
        $gstAmount = $sumFeeGst;
    }

    $feeHeadBalance = max($paymentLineTaxTotal - (float) ($payment->amount_paid ?? 0), 0);
    $courseCurrentBalance = (float) ($payment->invoice->pending_amount ?? 0);

    $sgstAmount = $isEduMaster ? ($sumFeeGst * 0.5) : ($gstAmount * 0.5);
    $cgstAmount = $isEduMaster ? ($sumFeeGst * 0.5) : ($gstAmount * 0.5);
    $gstTaxableForSplit = $isEduMaster ? $sumFeeTaxable : $taxableAmount;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice - {{ $payment->invoice->invoice_number }}</title>
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
            font-size: 10px;
            line-height: 1.25;
        }
        .rupee {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        @page {
            margin: 8mm 10mm;
        }
        .invoice-wrap {
            page-break-inside: avoid;
        }
        .main-block {
            page-break-inside: avoid;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; font-size: 10px; line-height: 1.25; color: #000; background: white; margin: 0; padding: 4px;">
    <div style="width: 100%; max-width: 900px; margin: 0 auto; background: white;" class="invoice-wrap">

        <!-- Header Section -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td style="vertical-align: top; padding-right: 10px;">
                                @if($isEduThanzeel && file_exists(public_path('storage/eduthanzeel.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/eduthanzeel.png'))) }}" alt="Company Logo" style="height: 70px; width: auto; margin-top: 8px;">
                                @elseif($isESchool && file_exists(public_path('storage/eschool.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/eschool.png'))) }}" alt="Company Logo" style="height: 70px; width: auto; margin-top: 8px;">
                                @elseif(file_exists(public_path('storage/logo.png')))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/logo.png'))) }}" alt="Company Logo" style="height: 70px; width: auto; margin-top: 8px;">
                                @else
                                    <div style="height: 60px; width: 80px; background-color: #f0f0f0; border: 1px solid #ddd; font-size: 10px; color: #666; margin-top: 8px;">{{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right; font-size: 9px; line-height: 1.35;">
                    <div style="font-size: 13px; font-weight: bold; margin-bottom: 4px;">{{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</div>
                    <p style="margin: 1px 0;">PALATHINGAL, ULLANAM P.O,</p>
                    <p style="margin: 1px 0;">676303</p>
                    <p style="margin: 1px 0;">REG.OFFICE 2/421A, PANTHARANGADI PO</p>
                    <p style="margin: 1px 0;">676306</p>
                    <p style="margin: 1px 0;">Phone no.: 6282055715 Email: nisaskillpark@gmail.com</p>
                    <p style="margin: 1px 0;">GSTIN: 32AAECF7209B1Z7, State: 32-Kerala</p>
                </td>
            </tr>
        </table>

        <div style="text-align: center; color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; font-weight: bold; font-size: 16px; border-top: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; border-bottom: 3px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; padding: 8px 0;">Tax Invoice</div>

        <!-- Bill To and Invoice Details -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <td style="width: 48%; vertical-align: top; padding-right: 8px;">
                    <h6 style="font-weight: bold; font-size: 11px; margin: 0 0 6px 0; color: #000;">Bill To</h6>
                    <p style="margin: 2px 0; font-size: 10px;">Name: {{ $payment->invoice->student->name }}</p>
                    <p style="margin: 2px 0; font-size: 10px;">Contact No.: {{ $payment->invoice->student->phone }}</p>
                </td>
                <td style="width: 48%; vertical-align: top; padding-left: 8px; text-align: right;">
                    <h6 style="font-weight: bold; font-size: 11px; margin: 0 0 6px 0; color: #000;">Invoice Details</h6>
                    <p style="margin: 2px 0; font-size: 10px;">Invoice No.: {{ $payment->invoice->invoice_number }}</p>
                    <p style="margin: 2px 0; font-size: 10px;">Date: {{ $payment->invoice->invoice_date->format('d-m-Y') }}</p>
                </td>
            </tr>
        </table>

        <!-- Itemized Table -->
        <div style="margin-bottom: 8px;" class="main-block">
            <table style="width: 100%; border-collapse: collapse; font-size: 9px;">
                <thead style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white;">
                    <tr>
                        <th style="padding: 6px 5px; text-align: left; font-weight: bold; font-size: 9px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">#</th>
                        <th style="padding: 6px 5px; text-align: left; font-weight: bold; font-size: 9px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">Item name</th>
                        <th style="padding: 6px 5px; text-align: left; font-weight: bold; font-size: 9px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">HSN/SAC</th>
                        <th style="padding: 6px 5px; text-align: left; font-weight: bold; font-size: 9px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">Qty</th>
                        <th style="padding: 6px 5px; text-align: left; font-weight: bold; font-size: 9px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">Price/Unit</th>
                        <th style="padding: 6px 5px; text-align: left; font-weight: bold; font-size: 9px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">GST</th>
                        <th style="padding: 6px 5px; text-align: left; font-weight: bold; font-size: 9px; border: 1px solid {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }};">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if($isEduMaster)
                        @php $rowNum = 0; @endphp
                        @foreach($edumasterRows as $row)
                            @php $rowNum++; @endphp
                            <tr>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">{{ $rowNum }}</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px; font-weight: bold;">{{ $row['label'] }}</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">{{ $payment->invoice->course->code ?? 'N/A' }}</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">1</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($row['taxable'], 2) }}</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($row['gst'], 2) }} (18%)</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($row['line_net'], 2) }}</td>
                            </tr>
                        @endforeach
                        @if($hasMobileRow)
                            @php $rowNum++; @endphp
                            <tr>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">{{ $rowNum }}</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px; font-weight: bold;">Needed Mobile</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">{{ $payment->invoice->course->code ?? 'N/A' }}</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">1</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($mobileListPrice, 2) }}</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">—</td>
                                <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($mobileLineNet, 2) }}</td>
                            </tr>
                        @endif
                        <tr style="font-weight: bold;">
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;" colspan="3">Total</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">{{ $lineCount }}</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($sumFeeTaxable + ($hasMobileRow ? $mobileListPrice : 0.0), 2) }}</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($gstAmount, 2) }}</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><strong><span class="rupee">₹</span> {{ number_format($invoiceNetTotal, 2) }}</strong></td>
                        </tr>
                    @else
                        <tr>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">1</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px; font-weight: bold;">
                                @if($payment->invoice->invoice_type === 'course')
                                    @if($payment->invoice->course_id == 9 && $payment->invoice->student->leadDetail)
                                        @php
                                            $studentDetail = $payment->invoice->student->leadDetail;
                                            $university = $studentDetail->university;
                                            $courseType = $studentDetail->course_type;
                                        @endphp
                                        @if($university && $courseType)
                                            {{ $university->title }} - {{ $courseType }}
                                        @else
                                            {{ $payment->invoice->course->title ?? 'N/A' }}
                                        @endif
                                    @else
                                        {{ $payment->invoice->course->title ?? 'N/A' }}
                                    @endif
                                @elseif($payment->invoice->invoice_type === 'e-service')
                                    {{ $payment->invoice->service_name ?? 'N/A' }}
                                @elseif($payment->invoice->invoice_type === 'batch_change')
                                    Batch Change - {{ $payment->invoice->batch->title ?? 'N/A' }} ({{ $payment->invoice->batch->course->title ?? 'N/A' }})
                                @elseif($payment->invoice->invoice_type === 'batch_postpond')
                                    Batch Postponed - {{ $payment->invoice->batch->title ?? 'N/A' }} ({{ $payment->invoice->batch->course->title ?? 'N/A' }})
                                @elseif($payment->invoice->invoice_type === 'fine')
                                    Fine - {{ $payment->invoice->service_name ?? 'N/A' }}
                                @elseif($payment->invoice->invoice_type === 'supply')
                                    Supply
                                @else
                                    N/A
                                @endif
                            </td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">
                                @if($payment->invoice->invoice_type === 'course')
                                    {{ $payment->invoice->course->code ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">1</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($taxableAmount, 2) }}</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($gstAmount, 2) }} (18%)</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($taxInvoiceTotal, 2) }}</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;" colspan="3">Total</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;">1</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($taxableAmount, 2) }}</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($gstAmount, 2) }}</td>
                            <td style="padding: 5px; border: 1px solid #ddd; font-size: 9px;"><strong><span class="rupee">₹</span> {{ number_format($taxInvoiceTotal, 2) }}</strong></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Tax + Amounts + footer -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 0;" class="main-block">
            <tr>
                <td style="width: 48%; vertical-align: top; padding-right: 4px;">

                    <div style="padding: 6px; margin-bottom: 6px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 6px 8px; margin: -6px -6px 6px -6px; font-weight: bold; font-size: 10px;">Tax Details</h6>
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th style="padding: 4px 5px; font-size: 9px; font-weight: bold; text-align: left;">Tax type</th>
                                    <th style="padding: 4px 5px; font-size: 9px; font-weight: bold; text-align: left;">Taxable amount</th>
                                    <th style="padding: 4px 5px; font-size: 9px; font-weight: bold; text-align: left;">Rate</th>
                                    <th style="padding: 4px 5px; font-size: 9px; font-weight: bold; text-align: left;">Tax amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 3px 5px; font-size: 9px;">SGST</td>
                                    <td style="padding: 3px 5px; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($gstTaxableForSplit, 2) }}</td>
                                    <td style="padding: 3px 5px; font-size: 9px;">9%</td>
                                    <td style="padding: 3px 5px; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($sgstAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 5px; font-size: 9px;">CGST</td>
                                    <td style="padding: 3px 5px; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($gstTaxableForSplit, 2) }}</td>
                                    <td style="padding: 3px 5px; font-size: 9px;">9%</td>
                                    <td style="padding: 3px 5px; font-size: 9px;"><span class="rupee">₹</span> {{ number_format($cgstAmount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="padding: 6px; margin-bottom: 6px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 6px 8px; margin: -6px -6px 6px -6px; font-weight: bold; font-size: 10px;">Invoice Amount In Words</h6>
                        <p style="margin: 2px 0; font-size: 9px;">{{ $payment->total_amount_in_words }} Rupees only</p>
                    </div>

                    <div style="padding: 6px; margin-bottom: 6px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 6px 8px; margin: -6px -6px 6px -6px; font-weight: bold; font-size: 10px;">Terms and Conditions</h6>
                        <p style="margin: 2px 0; font-size: 9px;">THIS AMOUNT IS NON REFUNDABLE</p>
                    </div>

                    <div style="padding: 6px; margin-bottom: 0;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 6px 8px; margin: -6px -6px 6px -6px; font-weight: bold; font-size: 10px;">Bank Details</h6>
                        <p style="margin: 2px 0; font-size: 9px;">Name: AXIS BANK, KALLAI ROAD, KOZHIKODE</p>
                        <p style="margin: 2px 0; font-size: 9px;">Account No.: 921020041902527</p>
                        <p style="margin: 2px 0; font-size: 9px;">IFSC code: UTIB0001908</p>
                        <p style="margin: 2px 0; font-size: 9px;">Account holder's name: FUTURE AND TREE</p>
                    </div>
                </td>

                <td style="width: 48%; vertical-align: top; padding-left: 4px;">

                    <div style="padding: 6px; margin-bottom: 6px;">
                        <h6 style="background-color: {{ $isEduThanzeel ? '#991E5B' : ($isESchool ? '#0B67C2' : '#a276f3') }}; color: white; padding: 6px 8px; margin: -6px -6px 6px -6px; font-weight: bold; font-size: 10px;">Amounts</h6>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 3px 0; font-size: 9px; border-bottom: 1px solid #ddd;">Sub Total</td>
                                <td style="padding: 3px 0; font-size: 9px; border-bottom: 1px solid #ddd; text-align: right;"><span class="rupee">₹</span> {{ number_format($taxableAmount, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0; font-size: 9px;"><strong>Total</strong></td>
                                <td style="padding: 3px 0; font-size: 9px; text-align: right;"><strong><span class="rupee">₹</span> {{ number_format($isEduMaster ? $invoiceNetTotal : $taxInvoiceTotal, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0; font-size: 9px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;"><strong>Received</strong></td>
                                <td style="padding: 3px 0; font-size: 9px; text-align: right; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;"><strong><span class="rupee">₹</span> {{ number_format(round((float) $payment->amount_paid), 0) }}</strong></td>
                            </tr>
                            @if($isEduMaster && $payment->fee_head)
                                <tr>
                                    <td style="padding: 3px 0; font-size: 9px; border-top: 1px solid #ddd;">Fee Head Balance</td>
                                    <td style="padding: 3px 0; font-size: 9px; text-align: right; border-top: 1px solid #ddd;"><span class="rupee">₹</span> {{ number_format($feeHeadBalance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; font-size: 9px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">Course Current Balance</td>
                                    <td style="padding: 3px 0; font-size: 9px; text-align: right; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;"><span class="rupee">₹</span> {{ number_format($courseCurrentBalance, 2) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td style="padding: 3px 0; font-size: 9px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">Current Balance</td>
                                    <td style="padding: 3px 0; font-size: 9px; text-align: right; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;"><span class="rupee">₹</span> {{ number_format($courseCurrentBalance, 2) }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <div style="text-align: center; margin-top: 24px;">
                        <p style="font-size: 9px; margin: 2px 0;">For: {{ $isEduThanzeel ? 'EDUTHANZEEL' : ($isESchool ? 'E-school' : 'SKILL PARK') }}</p>
                        <div style="height: 48px; margin: 6px 0;">
                            @if(file_exists(storage_path('app/public/accounts-sign.png')))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/accounts-sign.png'))) }}" alt="Signature" style="max-height: 48px; max-width: 130px;">
                            @else
                                <div style="height: 40px; width: 130px; border-bottom: 1px solid #333; margin: 0 auto;"></div>
                            @endif
                        </div>
                        <p style="font-size: 9px; margin: 2px 0;">Authorized Signatory</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
