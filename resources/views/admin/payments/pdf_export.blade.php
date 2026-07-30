<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1e3c72;
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 10px;
        }
        .filters {
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #e9ecef;
        }
        .filters h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #1e3c72;
        }
        .filters ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .filters li {
            display: inline-block;
            margin-right: 15px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background-color: #1e3c72;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on {{ date('d M Y, h:i A') }}</p>
    </div>

    @if(!empty(array_filter($filters)))
    <div class="filters">
        <h3>Applied Filters:</h3>
        <ul>
            @if(!empty($filters['from_date'])) <li><strong>From:</strong> {{ $filters['from_date'] }}</li> @endif
            @if(!empty($filters['to_date'])) <li><strong>To:</strong> {{ $filters['to_date'] }}</li> @endif
            @if(!empty($filters['payment_date_from'])) <li><strong>Payment From:</strong> {{ $filters['payment_date_from'] }}</li> @endif
            @if(!empty($filters['payment_date_to'])) <li><strong>Payment To:</strong> {{ $filters['payment_date_to'] }}</li> @endif
            @if(!empty($filters['search'])) <li><strong>Search:</strong> {{ $filters['search'] }}</li> @endif
        </ul>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 10%;">
                    @if($status == 'approved') Approved Date
                    @elseif($status == 'rejected') Rejected Date
                    @else Requested Date @endif
                </th>
                <th style="width: 8%;">Payment Date</th>
                <th style="width: 10%;">Invoice #</th>
                <th style="width: 12%;">Student</th>
                <th style="width: 12%;">Course / Batch</th>
                <th style="width: 8%;" class="text-end">Amount</th>
                <th style="width: 8%;">Fee Head</th>
                <th style="width: 8%;">Type</th>
                <th style="width: 10%;">Transaction ID</th>
                <th style="width: 11%;">Created By</th>
                @if($status == 'approved')
                <th style="width: 11%;">Approved By</th>
                @endif
                @if($status == 'rejected')
                <th style="width: 11%;">Rejected By</th>
                <th style="width: 10%;">Remarks</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $index => $payment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @if($status == 'approved')
                        {{ optional($payment->approved_date)->format('M d, Y') ?? '-' }}
                    @elseif($status == 'rejected')
                        {{ optional($payment->rejected_date)->format('M d, Y') ?? '-' }}
                    @else
                        {{ $payment->created_at->format('M d, Y') }}
                    @endif
                </td>
                <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '-' }}</td>
                <td>{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                <td>
                    {{ $payment->invoice && $payment->invoice->student ? ($payment->invoice->student->name ?? $payment->invoice->student->lead->title ?? 'N/A') : 'N/A' }}
                    <br>
                    <small style="color: #666;">
                        @php
                            $student = $payment->invoice->student ?? null;
                        @endphp
                        {{ $student ? \App\Helpers\PhoneNumberHelper::display($student->code ?? null, $student->phone ?? null) : 'N/A' }}
                    </small>
                </td>
                <td>
                    @if($payment->invoice && $payment->invoice->invoice_type === 'course')
                        {{ $payment->invoice->course->title ?? 'N/A' }}
                        @if($payment->invoice->batch)
                            <br><small style="color: #666;">{{ $payment->invoice->batch->title }}</small>
                        @endif
                    @elseif($payment->invoice && ($payment->invoice->invoice_type === 'batch_change' || $payment->invoice->invoice_type === 'batch_postpond'))
                        {{ $payment->invoice->batch->title ?? 'N/A' }}
                        <br><small style="color: #666;">{{ $payment->invoice->batch->course->title ?? 'N/A' }}</small>
                    @elseif($payment->invoice && $payment->invoice->invoice_type === 'e-service')
                        {{ $payment->invoice->service_name ?? 'N/A' }}
                    @elseif($payment->invoice && $payment->invoice->invoice_type === 'fine')
                        {{ $payment->invoice->service_name ?? 'N/A' }} (Fine)
                    @elseif($payment->invoice && $payment->invoice->invoice_type === 'supply')
                        Supply
                    @else
                        N/A
                    @endif
                </td>
                <td class="text-end">₹{{ number_format(round($payment->amount_paid)) }}</td>
                <td>{{ $payment->fee_head ?? '-' }}</td>
                <td>{{ $payment->payment_type }}</td>
                <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                <td>{{ optional($payment->createdBy)->name ?: (optional($payment->collectedBy)->name ?: (optional($payment->approvedBy)->name ?: (optional($payment->rejectedBy)->name ?: 'N/A')) ) }}</td>
                @if($status == 'approved')
                <td>{{ optional($payment->approvedBy)->name ?: (optional($payment->createdBy)->name ?: 'N/A') }}</td>
                @endif
                @if($status == 'rejected')
                <td>{{ optional($payment->rejectedBy)->name ?: 'N/A' }}</td>
                <td>{{ $payment->rejection_remarks ?? '-' }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $status == 'rejected' ? 13 : ($status == 'approved' ? 12 : 11) }}" class="text-center">
                    No records found for the selected period.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was automatically generated by Skill Park CRM. All amounts in INR (₹).</p>
    </div>
</body>
</html>
