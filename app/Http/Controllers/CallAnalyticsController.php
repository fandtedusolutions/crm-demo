<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Helpers\DateRangeHelper;
use App\Models\CallAppLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CallAnalyticsController extends Controller
{
    private function canAccessModule(): bool
    {
        return RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_post_sales()
            || RoleHelper::is_general_manager()
            || RoleHelper::is_senior_manager()
            || RoleHelper::is_admission_counsellor()
            || RoleHelper::is_hod()
            || RoleHelper::is_academic_assistant();
    }

    private function denyUnlessAllowed()
    {
        if (!$this->canAccessModule()) {
            abort(403, 'Access denied.');
        }
    }

    private function buildQueryParams(array $filters, array $extra = []): array
    {
        return array_filter(array_merge(
            DateRangeHelper::queryParams($filters),
            array_filter([
                'telecaller_id' => $filters['telecaller_id'] ?? null,
                'call_type' => $filters['call_type'] ?? null,
                'search' => $filters['search'] ?? null,
                'metric' => $filters['metric'] ?? null,
            ], fn ($value) => $value !== null && $value !== ''),
            $extra
        ), fn ($value) => $value !== null && $value !== '');
    }

    private function buildTelecallerReportQueryParams(array $filters): array
    {
        return array_filter(array_merge(
            DateRangeHelper::queryParams($filters),
            array_filter([
                'call_type' => $filters['call_type'] ?? null,
                'search' => $filters['search'] ?? null,
            ], fn ($value) => $value !== null && $value !== '')
        ), fn ($value) => $value !== null && $value !== '');
    }

    private function computeCallStats($baseQuery): array
    {
        $statsQuery = clone $baseQuery;

        return [
            'total_calls' => (clone $statsQuery)->count(),
            'connected_calls' => $this->countConnectedCalls($statsQuery),
            'attended_calls' => $this->countAttendedCalls($statsQuery),
            'total_duration_seconds' => (int) (clone $statsQuery)->sum('duration_seconds'),
            'with_recording' => (clone $statsQuery)->where('has_recording', true)->count(),
            'recordings_uploaded' => (clone $statsQuery)->where('recording_uploaded', true)->count(),
        ];
    }

    private function getFilterParams(Request $request): array
    {
        $dateRange = $request->get('date_range');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if (!$dateRange && ($startDate || $endDate)) {
            $dateRange = DateRangeHelper::PRESET_CUSTOM;
        }

        $dates = DateRangeHelper::resolve($dateRange, $startDate, $endDate);

        return array_merge($dates, [
            'telecaller_id' => $request->get('telecaller_id'),
            'call_type' => $request->get('call_type'),
            'search' => $request->get('search'),
            'metric' => $request->get('metric'),
        ]);
    }

    private function applyFilters($query, array $filters)
    {
        [$startMs, $endMs] = CallAppLog::millisecondRangeForDates(
            $filters['start_date'],
            $filters['end_date']
        );

        $query->whereBetween('started_at_ms', [$startMs, $endMs]);

        if (!empty($filters['telecaller_id'])) {
            $query->where('telecaller_id', $filters['telecaller_id']);
        }

        if (!empty($filters['call_type'])) {
            $query->where('call_type', $filters['call_type']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('contact_name', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    private function applyMetricFilter($query, ?string $metric)
    {
        if (empty($metric) || $metric === 'connected' || $metric === 'total') {
            return $query;
        }

        switch ($metric) {
            case 'incoming':
                $query->where('call_type', 'incoming');
                break;
            case 'outgoing':
                $query->where('call_type', 'outgoing');
                break;
            case 'attended':
                $query->attended();
                break;
            case 'not_picked':
                $query->where(function ($q) {
                    $q->where('call_type', 'not_picked')
                        ->orWhere('remarks', 'Not Picked');
                });
                break;
            case 'missed':
                $query->where('call_type', 'missed');
                break;
            case 'rejected':
                $query->where('call_type', 'rejected');
                break;
        }

        return $query;
    }

    private function getMetricLabel(?string $metric): string
    {
        return match ($metric) {
            'connected' => 'Connected Contacts',
            'incoming' => 'Incoming Calls',
            'outgoing' => 'Outgoing Calls',
            'attended' => 'Attended Calls',
            'not_picked' => 'Not Picked Calls',
            'missed' => 'Missed Calls',
            'rejected' => 'Rejected Calls',
            'total' => 'All Calls',
            default => 'Call Details',
        };
    }

    private function getConnectedContacts($query, array $queryParams, int $perPage = 25)
    {
        return (clone $query)
            ->select([
                DB::raw("REGEXP_REPLACE(phone_number, '[^0-9]', '') as normalized_phone"),
                DB::raw('MAX(phone_number) as phone_number'),
                DB::raw('MAX(contact_name) as contact_name'),
                DB::raw('COUNT(*) as call_count'),
                DB::raw('MAX(started_at_ms) as last_started_at_ms'),
                DB::raw('SUM(duration_seconds) as total_duration_seconds'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(telecaller_id ORDER BY started_at_ms DESC), ",", 1) as last_telecaller_id'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(id ORDER BY started_at_ms DESC), ",", 1) as last_call_id'),
                DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(IF(recording_uploaded = 1, id, NULL) ORDER BY started_at_ms DESC SEPARATOR ','), ',', 1) as recording_call_id"),
            ])
            ->groupBy(DB::raw("REGEXP_REPLACE(phone_number, '[^0-9]', '')"))
            ->orderByDesc('last_started_at_ms')
            ->paginate($perPage)
            ->appends($queryParams);
    }

    private function attachRecordingCallsToContacts($contacts)
    {
        $callIds = $contacts->getCollection()
            ->flatMap(fn ($contact) => array_filter([
                $contact->recording_call_id ?? null,
                $contact->last_call_id ?? null,
            ]))
            ->unique()
            ->values();

        if ($callIds->isEmpty()) {
            return $contacts;
        }

        $calls = CallAppLog::with('recording')
            ->whereIn('id', $callIds)
            ->get()
            ->keyBy('id');

        $contacts->getCollection()->transform(function ($contact) use ($calls) {
            $recordingCall = !empty($contact->recording_call_id)
                ? $calls->get((int) $contact->recording_call_id)
                : null;
            $lastCall = !empty($contact->last_call_id)
                ? $calls->get((int) $contact->last_call_id)
                : null;

            $contact->recording_call = $recordingCall ?? $lastCall;

            return $contact;
        });

        return $contacts;
    }

    private function getReportDetail(array $filters, array $queryParams): ?array
    {
        $metric = $filters['metric'] ?? null;
        if (empty($metric)) {
            return null;
        }

        $detailQuery = CallAppLog::query();
        $this->applyFilters($detailQuery, $filters);

        if ($metric === 'connected') {
            $contacts = $this->attachRecordingCallsToContacts(
                $this->getConnectedContacts($detailQuery, $queryParams)
            );
            $telecallerIds = $contacts->getCollection()
                ->pluck('last_telecaller_id')
                ->filter()
                ->unique();
            $telecallerNames = User::whereIn('id', $telecallerIds)
                ->pluck('name', 'id');

            return [
                'type' => 'contacts',
                'label' => $this->getMetricLabel($metric),
                'records' => $contacts,
                'telecaller_names' => $telecallerNames,
            ];
        }

        $this->applyMetricFilter($detailQuery, $metric);

        return [
            'type' => 'calls',
            'label' => $this->getMetricLabel($metric),
            'records' => $detailQuery
                ->with(['telecaller', 'recording'])
                ->orderByDesc('started_at_ms')
                ->paginate(25)
                ->appends($queryParams),
        ];
    }

    private function countConnectedCalls($query): int
    {
        return (int) (clone $query)
            ->select(DB::raw("COUNT(DISTINCT REGEXP_REPLACE(phone_number, '[^0-9]', '')) as connected_count"))
            ->value('connected_count');
    }

    private function countAttendedCalls($query): int
    {
        return (int) (clone $query)->attended()->count();
    }

    private function getTelecallers()
    {
        $activeIds = CallAppLog::query()->distinct()->pluck('telecaller_id');

        return User::query()
            ->where(function ($q) use ($activeIds) {
                $q->where('role_id', 3);
                if ($activeIds->isNotEmpty()) {
                    $q->orWhereIn('id', $activeIds);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);
    }

    public function index(Request $request)
    {
        $this->denyUnlessAllowed();

        $filters = $this->getFilterParams($request);
        $queryParams = $this->buildQueryParams($filters);
        $telecallers = $this->getTelecallers();

        $baseQuery = CallAppLog::query()->with(['telecaller', 'recording']);
        $this->applyFilters($baseQuery, $filters);

        $stats = $this->computeCallStats($baseQuery);

        $calls = $baseQuery
            ->orderByDesc('started_at_ms')
            ->paginate(25)
            ->appends($queryParams);

        return view('admin.call-analytics.index', compact(
            'calls',
            'telecallers',
            'filters',
            'stats',
            'queryParams'
        ));
    }

    public function report(Request $request)
    {
        $this->denyUnlessAllowed();

        $filters = $this->getFilterParams($request);

        if (!empty($filters['telecaller_id']) && empty($filters['metric'])) {
            return redirect()->route('admin.call-analytics.report.telecaller', array_merge(
                ['telecaller' => $filters['telecaller_id']],
                $this->buildTelecallerReportQueryParams($filters)
            ));
        }

        $queryParams = $this->buildQueryParams($filters);
        $telecallers = $this->getTelecallers();

        $query = CallAppLog::query();
        $this->applyFilters($query, $filters);

        $rows = (clone $query)
            ->select([
                'telecaller_id',
                DB::raw('COUNT(*) as total_calls'),
                DB::raw("COUNT(DISTINCT REGEXP_REPLACE(phone_number, '[^0-9]', '')) as connected_calls"),
                DB::raw(CallAppLog::attendedCallsAggregateSql()),
                DB::raw("SUM(CASE WHEN call_type = 'incoming' THEN 1 ELSE 0 END) as incoming_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'outgoing' THEN 1 ELSE 0 END) as outgoing_calls"),
                DB::raw('SUM(CASE WHEN ' . CallAppLog::notPickedSqlCondition() . ' THEN 1 ELSE 0 END) as not_picked_calls'),
                DB::raw("SUM(CASE WHEN call_type = 'missed' THEN 1 ELSE 0 END) as missed_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'rejected' THEN 1 ELSE 0 END) as rejected_calls"),
                DB::raw('SUM(duration_seconds) as total_duration_seconds'),
                DB::raw('SUM(CASE WHEN has_recording = 1 THEN 1 ELSE 0 END) as with_recording'),
                DB::raw('SUM(CASE WHEN recording_uploaded = 1 THEN 1 ELSE 0 END) as recordings_uploaded'),
            ])
            ->groupBy('telecaller_id')
            ->orderByDesc('total_calls')
            ->get();

        $telecallerMap = User::whereIn('id', $rows->pluck('telecaller_id'))
            ->get(['id', 'name', 'email', 'phone'])
            ->keyBy('id');

        $grandTotals = [
            'total_calls' => $rows->sum('total_calls'),
            'connected_calls' => $this->countConnectedCalls($query),
            'attended_calls' => $rows->sum('attended_calls'),
            'incoming_calls' => $rows->sum('incoming_calls'),
            'outgoing_calls' => $rows->sum('outgoing_calls'),
            'not_picked_calls' => $rows->sum('not_picked_calls'),
            'missed_calls' => $rows->sum('missed_calls'),
            'rejected_calls' => $rows->sum('rejected_calls'),
            'total_duration_seconds' => (int) $rows->sum('total_duration_seconds'),
            'with_recording' => $rows->sum('with_recording'),
            'recordings_uploaded' => $rows->sum('recordings_uploaded'),
        ];

        $detail = $this->getReportDetail($filters, $queryParams);
        $activeTelecaller = !empty($filters['telecaller_id'])
            ? $telecallerMap->get((int) $filters['telecaller_id']) ?? User::find($filters['telecaller_id'])
            : null;

        return view('admin.call-analytics.report', compact(
            'rows',
            'telecallerMap',
            'telecallers',
            'filters',
            'grandTotals',
            'detail',
            'activeTelecaller',
            'queryParams'
        ));
    }

    public function telecallerReport(Request $request, User $telecaller)
    {
        $this->denyUnlessAllowed();

        $filters = $this->getFilterParams($request);
        $filters['telecaller_id'] = $telecaller->id;

        $filterQueryParams = $this->buildTelecallerReportQueryParams($filters);

        $baseQuery = CallAppLog::query()->with(['telecaller', 'recording']);
        $this->applyFilters($baseQuery, $filters);

        $stats = $this->computeCallStats($baseQuery);

        $calls = (clone $baseQuery)
            ->orderByDesc('started_at_ms')
            ->paginate(25)
            ->appends($filterQueryParams);

        return view('admin.call-analytics.telecaller-report', compact(
            'telecaller',
            'filters',
            'stats',
            'calls',
            'filterQueryParams'
        ));
    }

    public function show(CallAppLog $call)
    {
        $this->denyUnlessAllowed();

        $call->load(['telecaller', 'recording']);

        return view('admin.call-analytics.show', compact('call'));
    }

    public function streamRecording(CallAppLog $call)
    {
        $this->denyUnlessAllowed();

        $recording = $call->recording;

        if (!$recording) {
            abort(404, 'Recording not found.');
        }

        $playbackPath = $recording->playbackStoragePath();
        if (!$playbackPath || !Storage::disk('public')->exists($playbackPath)) {
            abort(404, 'Recording not found.');
        }

        $fileName = $recording->file_name ?: basename($playbackPath);
        $mimeType = str_ends_with(strtolower($playbackPath), '.m4a')
            ? 'audio/mp4'
            : $recording->playbackMimeType();

        return response()->file(
            Storage::disk('public')->path($playbackPath),
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . str_replace('"', '', preg_replace('/\.aac$/i', '.m4a', $fileName)) . '"',
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'private, max-age=3600',
            ]
        );
    }

    public function downloadRecording(CallAppLog $call)
    {
        $this->denyUnlessAllowed();

        $recording = $call->recording;
        $storedPath = $recording?->storedStoragePath();

        if (!$storedPath) {
            abort(404, 'Recording not found.');
        }

        $downloadName = $recording->file_name ?: basename($storedPath);

        return Storage::disk('public')->download($storedPath, $downloadName);
    }
}
