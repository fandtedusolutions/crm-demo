<?php

namespace App\Http\Controllers;

use App\Helpers\DateRangeHelper;
use App\Helpers\PermissionHelper;
use App\Models\NatXAppLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NatXAnalyticsController extends Controller
{
    private function denyUnlessAllowed(): void
    {
        if (!PermissionHelper::can_access_natx_analytics()) {
            abort(403, 'Access denied.');
        }
    }

    private function buildQueryParams(array $filters, array $extra = []): array
    {
        return array_filter(array_merge(
            DateRangeHelper::queryParams($filters),
            array_filter([
                'user_id' => $filters['user_id'] ?? null,
                'call_type' => $filters['call_type'] ?? null,
                'search' => $filters['search'] ?? null,
                'metric' => $filters['metric'] ?? null,
            ], fn ($value) => $value !== null && $value !== ''),
            $extra
        ), fn ($value) => $value !== null && $value !== '');
    }

    private function getFilterParams(Request $request, ?string $defaultDatePreset = null): array
    {
        $dateRange = $request->query('date_range');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($dateRange === null || $dateRange === '') {
            $dateRange = $defaultDatePreset ?? DateRangeHelper::natxDefaultPreset();
        }

        if (!$dateRange && ($startDate || $endDate)) {
            $dateRange = DateRangeHelper::PRESET_CUSTOM;
        }

        $dates = DateRangeHelper::resolve($dateRange, $startDate, $endDate);

        return array_merge($dates, [
            'call_type' => $this->queryFilterValue($request, 'call_type'),
            'search' => $this->queryFilterValue($request, 'search'),
            'metric' => $this->queryFilterValue($request, 'metric'),
        ]);
    }

    /**
     * Read filter values from the URL query string only.
     * AuthMiddleware merges logged-in user_id into the request input bag.
     */
    private function queryFilterValue(Request $request, string $key): mixed
    {
        return $request->query->has($key) ? $request->query($key) : null;
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['date_range']) && $filters['date_range'] !== DateRangeHelper::PRESET_ALL) {
            $query->forReportPeriod($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['call_type'])) {
            $query->where('call_type', $filters['call_type']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('contact_name', 'like', '%' . $search . '%')
                    ->orWhere('device_call_id', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    private function orderCallsByLatest($query)
    {
        return $query->orderByDesc(DB::raw('IF(started_at_ms < 1000000000000, started_at_ms * 1000, started_at_ms)'));
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

    private function getUsers(array $filters = [])
    {
        $userFilters = $filters;
        unset($userFilters['user_id'], $userFilters['metric']);

        $query = NatXAppLog::query();
        $this->applyFilters($query, $userFilters);

        $activeIds = (clone $query)->distinct()->pluck('user_id');

        return User::query()
            ->whereIn('id', $activeIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);
    }

    private function getConnectedContacts($query, array $queryParams, int $perPage = 25)
    {
        return (clone $query)
            ->select([
                DB::raw("REGEXP_REPLACE(phone_number, '[^0-9]', '') as normalized_phone"),
                DB::raw('MAX(phone_number) as phone_number'),
                DB::raw('MAX(contact_name) as contact_name'),
                DB::raw('COUNT(*) as call_count'),
                DB::raw('MAX(IF(started_at_ms < 1000000000000, started_at_ms * 1000, started_at_ms)) as last_started_at_ms'),
                DB::raw('SUM(duration_seconds) as total_duration_seconds'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(user_id ORDER BY IF(started_at_ms < 1000000000000, started_at_ms * 1000, started_at_ms) DESC), ",", 1) as last_user_id'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(id ORDER BY IF(started_at_ms < 1000000000000, started_at_ms * 1000, started_at_ms) DESC), ",", 1) as last_call_id'),
                DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(IF(recording_uploaded = 1, id, NULL) ORDER BY IF(started_at_ms < 1000000000000, started_at_ms * 1000, started_at_ms) DESC SEPARATOR ','), ',', 1) as recording_call_id"),
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

        $calls = NatXAppLog::with('recording')
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

        $detailQuery = NatXAppLog::query();
        $this->applyFilters($detailQuery, $filters);

        if ($metric === 'connected') {
            $contacts = $this->attachRecordingCallsToContacts(
                $this->getConnectedContacts($detailQuery, $queryParams)
            );
            $userIds = $contacts->getCollection()
                ->pluck('last_user_id')
                ->filter()
                ->unique();
            $userNames = User::whereIn('id', $userIds)->pluck('name', 'id');

            return [
                'type' => 'contacts',
                'label' => $this->getMetricLabel($metric),
                'records' => $contacts,
                'user_names' => $userNames,
            ];
        }

        $this->applyMetricFilter($detailQuery, $metric);

        return [
            'type' => 'calls',
            'label' => $this->getMetricLabel($metric),
            'records' => $this->orderCallsByLatest(
                $detailQuery->with(['user', 'recording'])
            )
                ->paginate(25)
                ->appends($queryParams),
        ];
    }

    public function index(Request $request)
    {
        $this->denyUnlessAllowed();

        $filters = $this->getFilterParams($request);
        unset($filters['user_id'], $filters['metric']);

        $queryParams = $this->buildQueryParams($filters);

        $baseQuery = NatXAppLog::query()->with(['user', 'recording']);
        $this->applyFilters($baseQuery, $filters);

        $stats = $this->computeCallStats($baseQuery);

        $calls = $this->orderCallsByLatest($baseQuery)
            ->paginate(25)
            ->appends($queryParams);

        return view('admin.natx-analytics.index', compact(
            'calls',
            'filters',
            'stats',
            'queryParams'
        ));
    }

    public function report(Request $request)
    {
        $this->denyUnlessAllowed();

        $filters = $this->getFilterParams($request);

        $queryParams = $this->buildQueryParams($filters);
        $users = $this->getUsers($filters);

        $summaryFilters = $filters;
        if (!$request->query->has('user_id')) {
            unset($summaryFilters['user_id']);
        }

        $query = NatXAppLog::query();
        $this->applyFilters($query, $summaryFilters);

        $rows = (clone $query)
            ->select([
                'user_id',
                DB::raw('COUNT(*) as total_calls'),
                DB::raw("COUNT(DISTINCT REGEXP_REPLACE(phone_number, '[^0-9]', '')) as connected_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'incoming' THEN 1 ELSE 0 END) as incoming_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'outgoing' THEN 1 ELSE 0 END) as outgoing_calls"),
                DB::raw('SUM(CASE WHEN ' . NatXAppLog::notPickedSqlCondition() . ' THEN 1 ELSE 0 END) as not_picked_calls'),
                DB::raw("SUM(CASE WHEN call_type = 'missed' THEN 1 ELSE 0 END) as missed_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'rejected' THEN 1 ELSE 0 END) as rejected_calls"),
                DB::raw('SUM(duration_seconds) as total_duration_seconds'),
                DB::raw('SUM(CASE WHEN has_recording = 1 THEN 1 ELSE 0 END) as with_recording'),
                DB::raw('SUM(CASE WHEN recording_uploaded = 1 THEN 1 ELSE 0 END) as recordings_uploaded'),
            ])
            ->groupBy('user_id')
            ->orderByDesc('total_calls')
            ->get()
            ->map(function ($row) {
                $row->attended_calls = NatXAppLog::attendedCallCount(
                    (int) $row->incoming_calls,
                    (int) $row->outgoing_calls
                );

                return $row;
            });

        $userMap = User::whereIn('id', $rows->pluck('user_id'))
            ->get(['id', 'name', 'email', 'phone'])
            ->keyBy('id');

        $grandTotals = [
            'total_calls' => $rows->sum('total_calls'),
            'connected_calls' => $this->countConnectedCalls($query),
            'incoming_calls' => $incomingTotal = (int) $rows->sum('incoming_calls'),
            'outgoing_calls' => $outgoingTotal = (int) $rows->sum('outgoing_calls'),
            'attended_calls' => NatXAppLog::attendedCallCount($incomingTotal, $outgoingTotal),
            'not_picked_calls' => $rows->sum('not_picked_calls'),
            'missed_calls' => $rows->sum('missed_calls'),
            'rejected_calls' => $rows->sum('rejected_calls'),
            'total_duration_seconds' => (int) $rows->sum('total_duration_seconds'),
            'with_recording' => $rows->sum('with_recording'),
            'recordings_uploaded' => $rows->sum('recordings_uploaded'),
        ];

        $detail = $this->getReportDetail($filters, $queryParams);
        $activeUser = $request->query->has('user_id') && !empty($filters['user_id'])
            ? $userMap->get((int) $filters['user_id']) ?? User::find($filters['user_id'])
            : null;

        return view('admin.natx-analytics.report', compact(
            'rows',
            'userMap',
            'users',
            'filters',
            'grandTotals',
            'detail',
            'activeUser',
            'queryParams'
        ));
    }

    public function userReport(Request $request, int $user)
    {
        $this->denyUnlessAllowed();

        User::findOrFail($user);

        $filters = $this->getFilterParams($request);
        unset($filters['user_id'], $filters['metric']);
        $filters['user_id'] = $user;
        $filters['metric'] = 'total';

        return redirect()->route('admin.natx-analytics.report', $this->buildQueryParams($filters));
    }

    public function show(NatXAppLog $call)
    {
        $this->denyUnlessAllowed();

        $call->load(['user', 'recording']);

        return view('admin.natx-analytics.show', compact('call'));
    }

    public function streamRecording(NatXAppLog $call)
    {
        $this->denyUnlessAllowed();

        $recording = $call->recording;
        $storedPath = $recording?->storedStoragePath();

        if (!$recording || !$storedPath || !Storage::disk('public')->exists($storedPath)) {
            abort(404, 'Recording not found.');
        }

        $fileName = $recording->file_name ?: basename($storedPath);

        return response()->file(
            Storage::disk('public')->path($storedPath),
            [
                'Content-Type' => $recording->playbackMimeType(),
                'Content-Disposition' => 'inline; filename="' . str_replace('"', '', $fileName) . '"',
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'private, max-age=3600',
            ]
        );
    }

    public function downloadRecording(NatXAppLog $call)
    {
        $this->denyUnlessAllowed();

        $recording = $call->recording;
        $storedPath = $recording?->storedStoragePath();

        if (!$storedPath) {
            abort(404, 'Recording not found.');
        }

        return response()->download(
            Storage::disk('public')->path($storedPath),
            $recording->file_name ?: basename($storedPath)
        );
    }
}
