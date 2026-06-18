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

        $rows = $query
            ->select([
                'telecaller_id',
                DB::raw('COUNT(*) as total_calls'),
                DB::raw("SUM(CASE WHEN call_type = 'incoming' THEN 1 ELSE 0 END) as incoming_calls"),
                DB::raw("SUM(CASE WHEN call_type = 'outgoing' THEN 1 ELSE 0 END) as outgoing_calls"),
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
            'incoming_calls' => $rows->sum('incoming_calls'),
            'outgoing_calls' => $rows->sum('outgoing_calls'),
            'missed_calls' => $rows->sum('missed_calls'),
            'rejected_calls' => $rows->sum('rejected_calls'),
            'total_duration_seconds' => (int) $rows->sum('total_duration_seconds'),
            'with_recording' => $rows->sum('with_recording'),
            'recordings_uploaded' => $rows->sum('recordings_uploaded'),
        ];

        return view('admin.call-analytics.report', compact(
            'rows',
            'telecallerMap',
            'telecallers',
            'filters',
            'grandTotals'
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
