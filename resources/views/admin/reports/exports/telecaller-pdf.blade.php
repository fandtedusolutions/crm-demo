<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportType }} - {{ $fromDate }} to {{ $toDate }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 22px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f2f2f2; font-weight: bold; }
        .text-end { text-align: right; }
        .summary-grid { width: 100%; margin-bottom: 20px; }
        .summary-grid td { width: 25%; text-align: center; background: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
@php $callTotals = $reportSummary['call_grand_totals'] ?? []; @endphp
    <div class="header">
        <h1>{{ $reportType }}</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</p>
        <p>Generated on: {{ $generatedAt }}</p>
    </div>

    <table class="summary-grid">
        <tr>
            <td>Total Leads<br>{{ number_format($reportSummary['total_leads'] ?? 0) }}</td>
            <td>Active Leads<br>{{ number_format($reportSummary['active_leads'] ?? 0) }}</td>
            <td>Converted Leads<br>{{ number_format($reportSummary['converted_leads'] ?? 0) }}</td>
            <td>Total Calls<br>{{ number_format($callTotals['total_calls'] ?? 0) }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Telecaller</th>
                <th>Team</th>
                <th class="text-end">Total Leads</th>
                <th class="text-end">Active</th>
                <th class="text-end">Converted</th>
                <th class="text-end">Total Calls</th>
                <th class="text-end">Connected</th>
                <th class="text-end">Attended</th>
                <th class="text-end">Incoming</th>
                <th class="text-end">Outgoing</th>
                <th class="text-end">Not Picked</th>
                <th class="text-end">Missed</th>
                <th class="text-end">Rejected</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports['telecaller'] as $index => $telecaller)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $telecaller->name }}</td>
                    <td>{{ $telecaller->team_name ?? 'No Team' }}</td>
                    <td class="text-end">{{ number_format($telecaller->total_leads) }}</td>
                    <td class="text-end">{{ number_format($telecaller->active_leads) }}</td>
                    <td class="text-end">{{ number_format($telecaller->converted_leads) }}</td>
                    <td class="text-end">{{ number_format($telecaller->total_calls) }}</td>
                    <td class="text-end">{{ number_format($telecaller->connected_calls) }}</td>
                    <td class="text-end">{{ number_format($telecaller->attended_calls) }}</td>
                    <td class="text-end">{{ number_format($telecaller->incoming_calls) }}</td>
                    <td class="text-end">{{ number_format($telecaller->outgoing_calls) }}</td>
                    <td class="text-end">{{ number_format($telecaller->not_picked_calls) }}</td>
                    <td class="text-end">{{ number_format($telecaller->missed_calls) }}</td>
                    <td class="text-end">{{ number_format($telecaller->rejected_calls) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the CRM System</p>
    </div>
</body>
</html>
