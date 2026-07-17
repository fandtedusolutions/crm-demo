<?php

namespace App\Http\Controllers\API\NatX_Api;

use App\Http\Controllers\Controller;
use App\Models\NatXWorkStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WorkStatusController extends Controller
{
    /**
     * GET /work-status?date={yyyy-MM-dd}
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $date = $request->query('date');

            $entries = NatXWorkStatus::query()
                ->where('user_id', $user->id)
                ->whereDate('work_date', $date)
                ->orderByRaw("FIELD(slot, 'morning', 'afternoon', 'evening')")
                ->get()
                ->map(fn (NatXWorkStatus $entry) => $entry->toApiEntry())
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'message' => 'Work status fetched',
                'data' => [
                    'date' => $date,
                    'entries' => $entries,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('NatX work status fetch failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'date' => $request->query('date'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ], 500);
        }
    }

    /**
     * POST /work-status
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'slot' => ['required', 'string', Rule::in(NatXWorkStatus::SLOTS)],
            'date' => ['required', 'date_format:Y-m-d'],
            'updated_at' => ['required', 'string'],
            'updated_at_ms' => ['required', 'integer', 'min:1'],
            'device_id' => ['required', 'string', 'max:64'],
        ], [
            'slot.in' => 'Invalid slot value',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $completedAt = $this->parseUpdatedAt($request->input('updated_at'));
            if ($completedAt === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid updated_at format',
                ], 422);
            }

            $updatedAtMs = NatXWorkStatus::normalizeEpochMilliseconds((int) $request->input('updated_at_ms'));
            if ($updatedAtMs === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid updated_at_ms value',
                ], 422);
            }

            $date = $request->input('date');
            $slot = $request->input('slot');

            NatXWorkStatus::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'work_date' => $date,
                    'slot' => $slot,
                ],
                [
                    'updated_at_ms' => $updatedAtMs,
                    'completed_at' => $completedAt,
                    'device_id' => $request->input('device_id'),
                ]
            );

            $entries = NatXWorkStatus::query()
                ->where('user_id', $user->id)
                ->whereDate('work_date', $date)
                ->orderByRaw("FIELD(slot, 'morning', 'afternoon', 'evening')")
                ->get()
                ->map(fn (NatXWorkStatus $entry) => $entry->toApiEntry())
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'message' => 'Work status updated',
                'data' => [
                    'date' => $date,
                    'entries' => $entries,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('NatX work status update failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'payload' => $request->only(['slot', 'date', 'updated_at', 'updated_at_ms', 'device_id']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ], 500);
        }
    }

    private function parseUpdatedAt(string $value): ?Carbon
    {
        try {
            return Carbon::parse($value)->timezone(config('app.timezone', 'Asia/Kolkata'));
        } catch (\Throwable) {
            return null;
        }
    }
}
