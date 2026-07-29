<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $pageTitle ?? 'Converted Leads Export' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .header { margin-bottom: 10px; }
        .title { font-size: 14px; font-weight: bold; }
        .meta { font-size: 10px; color: #374151; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #d1d5db; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #1f4e78; color: #ffffff; text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $pageTitle ?? 'Converted Leads Export' }}</div>
        <div class="meta">Date Range: {{ $dateFrom ?? 'all' }} to {{ $dateTo ?? 'all' }}</div>
        <div class="meta">Generated At: {{ $generatedAt ?? now()->format('Y-m-d H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Academic</th>
                <th>Support</th>
                <th>Converted Date</th>
                <th>Register Number</th>
                <th>Name</th>
                <th>BDE Name</th>
                <th>Phone</th>
                <th>DOB</th>
                <th>WhatsApp</th>
                <th>Parent Phone</th>
                <th>Course</th>
                <th>Batch</th>
                <th>Admission Batch</th>
                <th>Status</th>
                <th>Cancelled By</th>
                <th>REG. FEE</th>
                <th>Mail</th>
                <th>Academic Document Approved</th>
                <th>Academic Verified At</th>
                <th>Support Verified At</th>
                <th>Lead Created By</th>
                <th>Pending Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($convertedLeads as $index => $convertedLead)
                @php
                    $pendingPayment = 0;
                    if ($convertedLead->invoices) {
                        foreach ($convertedLead->invoices as $invoice) {
                            $totalPaid = $invoice->payments ? $invoice->payments->sum('amount') : 0;
                            $pendingPayment += ($invoice->total_amount - $totalPaid);
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $convertedLead->is_academic_verified ? 'Yes' : 'No' }}</td>
                    <td>{{ $convertedLead->is_support_verified ? 'Yes' : 'No' }}</td>
                    <td>{{ $convertedLead->created_at ? $convertedLead->created_at->format('d-m-Y') : '-' }}</td>
                    <td>{{ $convertedLead->register_number ?? '-' }}</td>
                    <td>{{ $convertedLead->name ?? '-' }}</td>
                    <td>{{ $convertedLead->lead && $convertedLead->lead->telecaller ? $convertedLead->lead->telecaller->name : '-' }}</td>
                    <td>{{ $convertedLead->code && $convertedLead->phone ? ($convertedLead->code . $convertedLead->phone) : ($convertedLead->phone ?? '-') }}</td>
                    <td>{{ $convertedLead->dob ? \Carbon\Carbon::parse($convertedLead->dob)->format('d-m-Y') : '-' }}</td>
                    <td>{{ ($convertedLead->leadDetail && $convertedLead->leadDetail->whatsapp_number) ? ($convertedLead->leadDetail->whatsapp_code . $convertedLead->leadDetail->whatsapp_number) : '-' }}</td>
                    <td>{{ ($convertedLead->leadDetail && $convertedLead->leadDetail->parents_number) ? ($convertedLead->leadDetail->parents_code . $convertedLead->leadDetail->parents_number) : '-' }}</td>
                    <td>{{ $convertedLead->course ? $convertedLead->course->title : '-' }}</td>
                    <td>{{ $convertedLead->batch ? $convertedLead->batch->title : '-' }}</td>
                    <td>{{ $convertedLead->admissionBatch ? $convertedLead->admissionBatch->title : '-' }}</td>
                    <td>{{ $convertedLead->status ?? '-' }}</td>
                    <td>{{ $convertedLead->cancelledBy ? $convertedLead->cancelledBy->name : '-' }}</td>
                    <td>{{ $convertedLead->studentDetails ? ($convertedLead->studentDetails->reg_fee ?? '-') : '-' }}</td>
                    <td>{{ $convertedLead->email ?? '-' }}</td>
                    <td>{{ $convertedLead->leadDetail && $convertedLead->leadDetail->reviewed_at ? \Carbon\Carbon::parse($convertedLead->leadDetail->reviewed_at)->format('d-m-Y h:i A') : '-' }}</td>
                    <td>{{ $convertedLead->academic_verified_at ? \Carbon\Carbon::parse($convertedLead->academic_verified_at)->format('d-m-Y h:i A') : '-' }}</td>
                    <td>{{ $convertedLead->support_verified_at ? \Carbon\Carbon::parse($convertedLead->support_verified_at)->format('d-m-Y h:i A') : '-' }}</td>
                    <td>{{ $convertedLead->lead && $convertedLead->lead->createdBy ? $convertedLead->lead->createdBy->name : '-' }}</td>
                    <td class="text-right">{{ number_format($pendingPayment, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="23" class="text-center">No converted leads found for the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
