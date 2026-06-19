<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\CallAppLog;
use App\Models\User;
use Carbon\Carbon;
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

    private function getFilterParams(Request $request): array
    {
        return [
            'start_date' => $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d')),
            'end_date' => $request->get('end_date', Carbon::now()->format('Y-m-d')),
            'telecaller_id' => $request->get('telecaller_id'),
            'call_type' => $request->get('call_type'),
            'search' => $request->get('search'),
            'metric' => $request->get('metric'),
        ];
    }

    private function applyFilters($query, array $filters)
    {
        $query->whereBetween('started_at', [
            $filters['start_date'] . ' 00:00:00',
            $filters['end_date'] . ' 23:59:59',
        ]);

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
            'not_picked' => 'Not Picked Calls',
            'missed' => 'Missed Calls',
            'rejected' => 'Rejected Calls',
            'total' => 'All Calls',
            default => 'Call Details',
        };
    }

    private function getConnectedContacts($query, int $perPage = 25)
    {
        return (clone $query)
            ->select([
                DB::raw("REGEXP_REPLACE(phone_number, '[^0-9]', '') as normalized_phone"),
                DB::raw('MAX(phone_number) as phone_number'),
                DB::raw('MAX(contact_name) as contact_name'),
                DB::raw('COUNT(*) as call_count'),
                DB::raw('MAX(started_at) as last_called_at'),
                DB::raw('SUM(duration_seconds) as total_duration_seconds'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(telecaller_id ORDER BY started_at_ms DESC), ",", 1) as last_telecaller_id'),
            ])
            ->groupBy(DB::raw("REGEXP_REPLACE(phone_number, '[^0-9]', '')"))
            ->orderByDesc('last_called_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    private function getReportDetail(Request $request, array $filters): ?array
    {
        $metric = $filters['metric'] ?? null;
        if (empty($metric)) {
            return null;
        }

        $detailQuery = CallAppLog::query();
        $this->applyFilters($detailQuery, $filters);

        if ($metric === 'connected') {
            $contacts = $this->getConnectedContacts($detailQuery);
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
                ->withQueryString(),
        ];
    }

    private function countConnectedCalls($query): int
    {
        return (int) (clone $query)
            ->select(DB::raw("COUNT(DISTINCT REGEXP_REPLACE(phone_number, '[^0-9]', '')) as connected_count"))
            ->value('connected_count');
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
        $telecallers = $this->getTelecallers();

        $baseQuery = CallAppLog::query()->with(['telecaller', 'recording']);
        $this->applyFilters($baseQuery, $filters);

        $statsQuery = clone $baseQuery;
        $stats = [
            'total_calls' => (clone $statsQuery)->count(),
            'connected_calls' => $this->countConnectedCalls($statsQuery),
            'total_duration_seconds' => (int) (clone $statsQuery)->sum('duration_seconds'),
            'with_recording' => (clone $statsQuery)->where('has_recording', true)->count(),
            'recordings_uploaded' => (clone $statsQuery)->where('recording_uploaded', true)->count(),
        ];

        $calls = $baseQuery
            ->orderByDesc('started_at_ms')
            ->paginate(25)
            ->withQueryString();

        return view('admin.call-analytics.index', compact(
            'calls',
            'telecallers',
            'filters',
            'stats'
        ));
    }

    public function report(Request $request)
    {
        $this->denyUnlessAllowed();

        $filters = $this->getFilterParams($request);
        $telecallers = $this->getTelecallers();

        $query = CallAppLog::query();
        $this->applyFilters($query, $filters);

        $rows = (clone $query)
            ->select([
                'telecaller_id',
                DB::raw('COUNT(*) as total_calls'),
                DB::raw("COUNT(DISTINCT REGEXP_REPLACE(phone_number, '[^0-9]', '')) as connected_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'incoming' THEN 1 ELSE 0 END) as incoming_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'outgoing' THEN 1 ELSE 0 END) as outgoing_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'not_picked' OR remarks = 'Not Picked' THEN 1 ELSE 0 END) as not_picked_calls"),
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
            'incoming_calls' => $rows->sum('incoming_calls'),
            'outgoing_calls' => $rows->sum('outgoing_calls'),
            'not_picked_calls' => $rows->sum('not_picked_calls'),
            'missed_calls' => $rows->sum('missed_calls'),
            'rejected_calls' => $rows->sum('rejected_calls'),
            'total_duration_seconds' => (int) $rows->sum('total_duration_seconds'),
            'with_recording' => $rows->sum('with_recording'),
            'recordings_uploaded' => $rows->sum('recordings_uploaded'),
        ];

        $detail = $this->getReportDetail($request, $filters);
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
            'activeTelecaller'
        ));
    }

    public function show(CallAppLog $call)
    {
        $this->denyUnlessAllowed();

        $call->load(['telecaller', 'recording']);

        return view('admin.call-analytics.show', compact('call'));
    }

    public function downloadRecording(CallAppLog $call)
    {
        $this->denyUnlessAllowed();

        $recording = $call->recording;

        if (!$recording || !Storage::disk('public')->exists($recording->file_path)) {
            abort(404, 'Recording not found.');
        }

        return Storage::disk('public')->download(
            $recording->file_path,
            $recording->file_name ?: basename($recording->file_path)
        );
    }
}
