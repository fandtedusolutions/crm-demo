<?php

namespace App\Support;

use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Models\CallAppLog;
use App\Models\Lead;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

class TelecallerPerformanceReportBuilder
{
    public static function build(string $fromDate, string $toDate, ?int $teamId = null, ?int $telecallerId = null): array
    {
        $callStats = self::callStatsForPeriod($fromDate, $toDate, $teamId, $telecallerId);
        $telecallers = self::mergeTelecallersWithCallActivity(
            self::resolveTelecallers($teamId, $telecallerId),
            $callStats,
            $teamId,
            $telecallerId
        );
        $leadStats = self::leadStatsByTelecaller($fromDate, $toDate, $teamId, $telecallerId);

        $rows = $telecallers->map(function ($telecaller) use ($leadStats, $callStats) {
            $leads = $leadStats->get($telecaller->id);
            $calls = $callStats->get($telecaller->id);

            $totalLeads = (int) ($leads->total_leads ?? 0);
            $activeLeads = (int) ($leads->active_leads ?? 0);
            $convertedLeads = (int) ($leads->converted_leads ?? 0);

            return (object) [
                'id' => $telecaller->id,
                'name' => $telecaller->name,
                'phone' => $telecaller->phone,
                'team_name' => $telecaller->team_name,
                'count' => $totalLeads,
                'total_leads' => $totalLeads,
                'active_leads' => $activeLeads,
                'converted_leads' => $convertedLeads,
                'conversion_rate' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0.0,
                'total_calls' => (int) ($calls->total_calls ?? 0),
                'connected_calls' => (int) ($calls->connected_calls ?? 0),
                'incoming_calls' => (int) ($calls->incoming_calls ?? 0),
                'outgoing_calls' => (int) ($calls->outgoing_calls ?? 0),
                'not_picked_calls' => (int) ($calls->not_picked_calls ?? 0),
                'missed_calls' => (int) ($calls->missed_calls ?? 0),
                'rejected_calls' => (int) ($calls->rejected_calls ?? 0),
                'total_duration_seconds' => (int) ($calls->total_duration_seconds ?? 0),
                'with_recording' => (int) ($calls->with_recording ?? 0),
                'recordings_uploaded' => (int) ($calls->recordings_uploaded ?? 0),
            ];
        })->filter(function ($row) use ($telecallerId) {
            if ($telecallerId && (int) $row->id === (int) $telecallerId) {
                return true;
            }

            return $row->total_leads > 0 || $row->total_calls > 0;
        })->sortByDesc('total_leads')->values();

        return [
            'rows' => $rows,
            'summary' => self::buildSummary($rows, $fromDate, $toDate, $teamId, $telecallerId),
        ];
    }

    private static function resolveTelecallers(?int $teamId, ?int $telecallerId = null): Collection
    {
        if ($telecallerId) {
            $telecaller = User::where('id', $telecallerId)
                ->where('role_id', 3)
                ->select('id', 'name', 'phone', 'team_id')
                ->first();

            if (!$telecaller || !self::canIncludeTelecaller($telecaller, $teamId)) {
                return collect();
            }

            $teamNames = Team::whereIn('id', collect([$telecaller->team_id])->filter())
                ->pluck('name', 'id');
            $telecaller->team_name = $teamNames->get($telecaller->team_id);

            return collect([$telecaller]);
        }

        $currentUser = AuthHelper::getCurrentUser();

        if ($currentUser && RoleHelper::is_team_lead()) {
            $userTeamId = $currentUser->team_id;
            if ($userTeamId) {
                $telecallers = User::where('role_id', 3)
                    ->where('team_id', $userTeamId)
                    ->select('id', 'name', 'phone', 'team_id')
                    ->get();
            } else {
                $telecallers = collect([$currentUser]);
            }
        } elseif ($currentUser && AuthHelper::isTelecaller()) {
            $telecallers = collect([$currentUser]);
        } else {
            $query = User::where('role_id', 3)->select('id', 'name', 'phone', 'team_id');
            if ($teamId) {
                $query->where('team_id', $teamId);
            }
            $telecallers = $query->get();
        }

        $teamNames = Team::whereIn('id', $telecallers->pluck('team_id')->filter()->unique())
            ->pluck('name', 'id');

        return $telecallers->map(function ($telecaller) use ($teamNames) {
            $telecaller->team_name = $teamNames->get($telecaller->team_id);

            return $telecaller;
        });
    }

    /**
     * Ensure telecallers with call_app_logs activity appear even if not in the initial user list.
     */
    private static function mergeTelecallersWithCallActivity(
        Collection $telecallers,
        Collection $callStats,
        ?int $teamId,
        ?int $telecallerId = null
    ): Collection {
        if ($telecallerId) {
            return $telecallers;
        }

        $missingIds = $callStats->keys()
            ->diff($telecallers->pluck('id'))
            ->filter()
            ->values()
            ->all();

        if ($missingIds === []) {
            return $telecallers;
        }

        $extraQuery = User::whereIn('id', $missingIds)->select('id', 'name', 'phone', 'team_id');
        if ($teamId) {
            $extraQuery->where('team_id', $teamId);
        }

        $extra = $extraQuery->get();
        if ($extra->isEmpty()) {
            return $telecallers;
        }

        $teamNames = Team::whereIn('id', $extra->pluck('team_id')->filter()->unique())
            ->pluck('name', 'id');

        $extra = $extra->map(function ($telecaller) use ($teamNames) {
            $telecaller->team_name = $teamNames->get($telecaller->team_id);

            return $telecaller;
        });

        return $telecallers->merge($extra)->unique('id')->values();
    }

    private static function leadStatsByTelecaller(string $fromDate, string $toDate, ?int $teamId, ?int $telecallerId = null): Collection
    {
        $query = Lead::query()
            ->select('telecaller_id')
            ->selectRaw('COUNT(*) as total_leads')
            ->selectRaw('SUM(CASE WHEN COALESCE(is_converted, 0) = 0 THEN 1 ELSE 0 END) as active_leads')
            ->selectRaw('SUM(CASE WHEN is_converted = 1 THEN 1 ELSE 0 END) as converted_leads')
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->whereNotNull('telecaller_id');

        self::applyRoleBasedLeadFilter($query);

        if ($telecallerId) {
            $query->where('telecaller_id', $telecallerId);
        }

        if ($teamId) {
            $query->whereHas('telecaller', function ($q) use ($teamId) {
                $q->where('team_id', $teamId);
            });
        }

        return $query->groupBy('telecaller_id')->get()->keyBy('telecaller_id');
    }

    private static function baseCallLogQuery(string $fromDate, string $toDate, ?int $teamId, ?int $telecallerId = null)
    {
        $query = CallAppLog::query()->forReportPeriod($fromDate, $toDate);

        self::applyRoleBasedCallFilter($query);

        if ($telecallerId) {
            $query->where('telecaller_id', $telecallerId);
        }

        if ($teamId) {
            $query->whereHas('telecaller', function ($q) use ($teamId) {
                $q->where('team_id', $teamId);
            });
        }

        return $query;
    }

    private static function callStatsForPeriod(string $fromDate, string $toDate, ?int $teamId, ?int $telecallerId = null): Collection
    {
        return self::baseCallLogQuery($fromDate, $toDate, $teamId, $telecallerId)
            ->select(CallAppLog::telecallerAggregateColumns())
            ->groupBy('telecaller_id')
            ->get()
            ->keyBy('telecaller_id');
    }

    private static function callStatsByTelecaller(string $fromDate, string $toDate, array $telecallerIds): Collection
    {
        return CallAppLog::aggregateByTelecaller($fromDate, $toDate, $telecallerIds);
    }

    private static function buildSummary(Collection $rows, string $fromDate, string $toDate, ?int $teamId, ?int $telecallerId = null): array
    {
        $callGrandTotals = self::callGrandTotals($fromDate, $toDate, $teamId, $telecallerId);

        $totalLeads = (int) $rows->sum('total_leads');
        $convertedLeads = (int) $rows->sum('converted_leads');

        return [
            'total_leads' => $totalLeads,
            'active_leads' => (int) $rows->sum('active_leads'),
            'converted_leads' => $convertedLeads,
            'conversion_rate' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0.0,
            'telecallers_with_leads' => $rows->where('total_leads', '>', 0)->count(),
            'telecallers_with_calls' => $rows->where('total_calls', '>', 0)->count(),
            'call_grand_totals' => $callGrandTotals,
        ];
    }

    private static function callGrandTotals(string $fromDate, string $toDate, ?int $teamId, ?int $telecallerId = null): array
    {
        $query = self::baseCallLogQuery($fromDate, $toDate, $teamId, $telecallerId);

        $aggregates = (clone $query)
            ->selectRaw('COUNT(*) as total_calls')
            ->selectRaw("SUM(CASE WHEN call_type = 'incoming' THEN 1 ELSE 0 END) as incoming_calls")
            ->selectRaw("SUM(CASE WHEN call_type = 'outgoing' THEN 1 ELSE 0 END) as outgoing_calls")
            ->selectRaw("SUM(CASE WHEN call_type = 'not_picked' OR remarks = 'Not Picked' THEN 1 ELSE 0 END) as not_picked_calls")
            ->selectRaw("SUM(CASE WHEN call_type = 'missed' THEN 1 ELSE 0 END) as missed_calls")
            ->selectRaw("SUM(CASE WHEN call_type = 'rejected' THEN 1 ELSE 0 END) as rejected_calls")
            ->selectRaw('SUM(duration_seconds) as total_duration_seconds')
            ->selectRaw('SUM(CASE WHEN has_recording = 1 THEN 1 ELSE 0 END) as with_recording')
            ->selectRaw('SUM(CASE WHEN recording_uploaded = 1 THEN 1 ELSE 0 END) as recordings_uploaded')
            ->first();

        return [
            'total_calls' => (int) ($aggregates->total_calls ?? 0),
            'connected_calls' => CallAppLog::countDistinctConnectedContacts($query),
            'incoming_calls' => (int) ($aggregates->incoming_calls ?? 0),
            'outgoing_calls' => (int) ($aggregates->outgoing_calls ?? 0),
            'not_picked_calls' => (int) ($aggregates->not_picked_calls ?? 0),
            'missed_calls' => (int) ($aggregates->missed_calls ?? 0),
            'rejected_calls' => (int) ($aggregates->rejected_calls ?? 0),
            'total_duration_seconds' => (int) ($aggregates->total_duration_seconds ?? 0),
            'with_recording' => (int) ($aggregates->with_recording ?? 0),
            'recordings_uploaded' => (int) ($aggregates->recordings_uploaded ?? 0),
        ];
    }

    public static function emptyCallTotals(): array
    {
        return CallAppLog::emptyReportTotals();
    }

    public static function callAnalyticsForTelecaller(int $telecallerId, string $fromDate, string $toDate): array
    {
        $stats = self::callStatsByTelecaller($fromDate, $toDate, [$telecallerId])->get($telecallerId);

        return [
            'total_calls' => (int) ($stats->total_calls ?? 0),
            'connected_calls' => (int) ($stats->connected_calls ?? 0),
            'incoming_calls' => (int) ($stats->incoming_calls ?? 0),
            'outgoing_calls' => (int) ($stats->outgoing_calls ?? 0),
            'not_picked_calls' => (int) ($stats->not_picked_calls ?? 0),
            'missed_calls' => (int) ($stats->missed_calls ?? 0),
            'rejected_calls' => (int) ($stats->rejected_calls ?? 0),
            'total_duration_seconds' => (int) ($stats->total_duration_seconds ?? 0),
            'with_recording' => (int) ($stats->with_recording ?? 0),
            'recordings_uploaded' => (int) ($stats->recordings_uploaded ?? 0),
            'talk_time' => CallAppLog::formatDuration((int) ($stats->total_duration_seconds ?? 0)),
        ];
    }

    public static function recentCallsForTelecaller(int $telecallerId, string $fromDate, string $toDate, int $limit = 50): array
    {
        return CallAppLog::query()
            ->forReportPeriod($fromDate, $toDate)
            ->where('telecaller_id', $telecallerId)
            ->orderByDesc('started_at_ms')
            ->limit($limit)
            ->get()
            ->map(function (CallAppLog $call) {
                $startedAt = $call->display_started_at;

                return [
                    'phone_number' => $call->phone_number,
                    'contact_name' => $call->contact_name ?: '—',
                    'call_type' => $call->call_type ? ucfirst(str_replace('_', ' ', $call->call_type)) : '—',
                    'call_type_raw' => $call->call_type,
                    'duration' => CallAppLog::formatDuration((int) $call->duration_seconds),
                    'started_at' => $startedAt ? $startedAt->format('d-m-Y h:i A') : '—',
                    'remarks' => $call->remarks ?: '—',
                    'has_recording' => (bool) $call->has_recording,
                    'recording_uploaded' => (bool) $call->recording_uploaded,
                ];
            })
            ->values()
            ->all();
    }

    private static function applyRoleBasedLeadFilter($query): void
    {
        $currentUser = AuthHelper::getCurrentUser();

        if (!$currentUser) {
            return;
        }

        if ($currentUser->is_team_lead == 1) {
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId();
                $query->whereIn('telecaller_id', $teamMemberIds);
            } else {
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            }

            return;
        }

        if (AuthHelper::isTelecaller()) {
            $query->where('telecaller_id', AuthHelper::getCurrentUserId());
        }
    }

    private static function applyRoleBasedCallFilter($query): void
    {
        $currentUser = AuthHelper::getCurrentUser();

        if (!$currentUser) {
            return;
        }

        if ($currentUser->is_team_lead == 1) {
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId();
                $query->whereIn('telecaller_id', $teamMemberIds);
            } else {
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            }

            return;
        }

        if (AuthHelper::isTelecaller()) {
            $query->where('telecaller_id', AuthHelper::getCurrentUserId());
        }
    }

    private static function canIncludeTelecaller(User $telecaller, ?int $teamId): bool
    {
        if ($teamId && (int) $telecaller->team_id !== (int) $teamId) {
            return false;
        }

        $currentUser = AuthHelper::getCurrentUser();
        if (!$currentUser) {
            return true;
        }

        if ($currentUser->is_team_lead == 1) {
            $userTeamId = $currentUser->team_id;
            if (!$userTeamId) {
                return (int) $telecaller->id === (int) AuthHelper::getCurrentUserId();
            }

            $teamMemberIds = AuthHelper::getTeamMemberIds($userTeamId);
            $teamMemberIds[] = AuthHelper::getCurrentUserId();

            return in_array((int) $telecaller->id, array_map('intval', $teamMemberIds), true);
        }

        if (AuthHelper::isTelecaller()) {
            return (int) $telecaller->id === (int) AuthHelper::getCurrentUserId();
        }

        return true;
    }
}
