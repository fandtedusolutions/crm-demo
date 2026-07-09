<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\NatXAppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NatXAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        if (!PermissionHelper::can_access_natx_analytics()) {
            abort(403, 'Access denied.');
        }

        $query = NatXAppLog::query();

        if ($request->filled('call_type')) {
            $query->where('call_type', $request->call_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('contact_name', 'like', '%' . $search . '%');
            });
        }

        $baseQuery = clone $query;
        $stats = [
            'total_calls' => (clone $baseQuery)->count(),
            'unique_users' => (clone $baseQuery)->distinct('user_id')->count('user_id'),
            'total_duration_seconds' => (int) (clone $baseQuery)->sum('duration_seconds'),
            'with_recording' => (clone $baseQuery)->where('has_recording', true)->count(),
            'recordings_uploaded' => (clone $baseQuery)->where('recording_uploaded', true)->count(),
        ];

        $callTypeCounts = (clone $baseQuery)
            ->select('call_type', DB::raw('COUNT(*) as total'))
            ->groupBy('call_type')
            ->pluck('total', 'call_type');

        $calls = $query
            ->with('user:id,name,email')
            ->orderByDesc('started_at_ms')
            ->paginate(25)
            ->appends($request->query());

        return view('admin.natx-analytics.index', compact('calls', 'stats', 'callTypeCounts'));
    }
}
