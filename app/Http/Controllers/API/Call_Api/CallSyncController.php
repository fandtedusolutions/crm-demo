<?php

namespace App\Http\Controllers\API\Call_Api;

use App\Http\Controllers\Controller;
use App\Models\CallAppLog;
use App\Models\CallAppRecording;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CallSyncController extends Controller
{
    /**
     * Bulk sync call details from the Call Tracker app.
     */
    public function syncCalls(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_id' => 'required|string|max:100',
                'app_version' => 'nullable|string|max:20',
                'calls' => 'required|array|min:1',
                'calls.*.device_call_id' => 'required|string|max:120',
                'calls.*.phone_number' => 'required|string|max:30',
                'calls.*.contact_name' => 'nullable|string|max:150',
                'calls.*.call_type' => 'required|in:incoming,outgoing,missed,rejected,unknown',
                'calls.*.duration_seconds' => 'required|integer|min:0',
                'calls.*.started_at_ms' => 'required|integer',
                'calls.*.started_at' => 'nullable|date',
                'calls.*.has_recording' => 'nullable|boolean',
                'calls.*.recording_duration_seconds' => 'nullable|integer|min:0',
                'calls.*.recording_file_name' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $telecallerId = $request->user()->id;
        $results = [];
        $synced = 0;
        $skipped = 0;

        foreach ($validated['calls'] as $item) {
            $startedAt = isset($item['started_at'])
                ? Carbon::parse($item['started_at'])
                : Carbon::createFromTimestampMs($item['started_at_ms']);

            $call = CallAppLog::updateOrCreate(
                [
                    'telecaller_id' => $telecallerId,
                    'device_call_id' => $item['device_call_id'],
                ],
                [
                    'device_id' => $validated['device_id'],
                    'phone_number' => $item['phone_number'],
                    'contact_name' => $item['contact_name'] ?? null,
                    'call_type' => $item['call_type'],
                    'duration_seconds' => $item['duration_seconds'],
                    'started_at_ms' => $item['started_at_ms'],
                    'started_at' => $startedAt,
                    'has_recording' => (bool) ($item['has_recording'] ?? false),
                    'recording_duration_seconds' => $item['recording_duration_seconds'] ?? null,
                    'recording_file_name' => $item['recording_file_name'] ?? null,
                    'app_version' => $validated['app_version'] ?? null,
                ]
            );

            $call->wasRecentlyCreated ? $synced++ : $skipped++;

            $results[] = [
                'device_call_id' => $call->device_call_id,
                'server_call_id' => $call->id,
                'recording_upload_required' => $call->has_recording && !$call->recording_uploaded,
                'recording_already_uploaded' => (bool) $call->recording_uploaded,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Calls synced successfully',
            'data' => [
                'synced' => $synced,
                'skipped' => $skipped,
                'calls' => $results,
            ],
        ]);
    }

    /**
     * Upload a call recording audio file linked to a synced call.
     */
    public function uploadRecording(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_call_id' => 'nullable|string|max:120',
            'server_call_id' => 'nullable|integer',
            'recording' => 'required|file|max:25600',
            'duration_seconds' => 'nullable|integer|min:0',
            'recorded_at_ms' => 'nullable|integer',
            'file_size_bytes' => 'nullable|integer|min:0',
            'original_file_name' => 'nullable|string|max:255',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->filled('device_call_id') && !$request->filled('server_call_id')) {
                $validator->errors()->add('device_call_id', 'device_call_id or server_call_id is required');
            }

            $file = $request->file('recording');
            if ($file) {
                $allowedMimes = [
                    'audio/amr',
                    'audio/mpeg',
                    'audio/mp4',
                    'audio/x-m4a',
                    'audio/wav',
                    'audio/x-wav',
                    'audio/3gpp',
                    'audio/3gp',
                    'application/octet-stream',
                ];
                $mime = $file->getMimeType();
                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = ['amr', 'm4a', 'mp3', 'wav', '3gp'];

                if (!in_array($mime, $allowedMimes, true) && !in_array($extension, $allowedExtensions, true)) {
                    $validator->errors()->add('recording', 'Invalid audio file type. Allowed: amr, m4a, mp3, wav, 3gp');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $telecallerId = $request->user()->id;

        $callQuery = CallAppLog::where('telecaller_id', $telecallerId);

        if (!empty($validated['server_call_id'])) {
            $callQuery->where('id', $validated['server_call_id']);
        } else {
            $callQuery->where('device_call_id', $validated['device_call_id']);
        }

        $call = $callQuery->first();

        if (!$call) {
            return response()->json([
                'success' => false,
                'message' => 'Call record not found',
            ], 404);
        }

        $file = $request->file('recording');
        $existingRecording = CallAppRecording::where('call_app_log_id', $call->id)->first();

        if ($existingRecording && Storage::disk('public')->exists($existingRecording->file_path)) {
            Storage::disk('public')->delete($existingRecording->file_path);
        }

        $path = $file->store("call-recordings/{$telecallerId}/{$call->id}", 'public');

        $recording = CallAppRecording::updateOrCreate(
            ['call_app_log_id' => $call->id],
            [
                'telecaller_id' => $telecallerId,
                'file_path' => $path,
                'file_name' => $validated['original_file_name'] ?? $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'file_size_bytes' => $validated['file_size_bytes'] ?? $file->getSize(),
                'duration_seconds' => $validated['duration_seconds'] ?? 0,
                'recorded_at_ms' => $validated['recorded_at_ms'] ?? null,
            ]
        );

        $call->update([
            'has_recording' => true,
            'recording_uploaded' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recording uploaded successfully',
            'data' => [
                'server_call_id' => $call->id,
                'recording_id' => $recording->id,
                'recording_url' => Storage::disk('public')->url($path),
                'duration_seconds' => $recording->duration_seconds,
                'file_size_bytes' => $recording->file_size_bytes,
            ],
        ]);
    }

    /**
     * Get sync status for the authenticated telecaller.
     */
    public function status(Request $request)
    {
        $sinceMs = $request->query('since_ms');
        $telecallerId = $request->user()->id;

        $query = CallAppLog::where('telecaller_id', $telecallerId);

        if ($sinceMs) {
            $query->where('started_at_ms', '>=', (int) $sinceMs);
        }

        $calls = $query->orderByDesc('started_at_ms')->get();

        $uploadedDeviceCallIds = $calls
            ->filter(fn ($call) => $call->recording_uploaded)
            ->pluck('device_call_id')
            ->values()
            ->all();

        $pendingRecordings = $calls
            ->filter(fn ($call) => $call->has_recording && !$call->recording_uploaded)
            ->pluck('device_call_id')
            ->values()
            ->all();

        $lastSyncedAt = CallAppLog::where('telecaller_id', $telecallerId)
            ->latest('updated_at')
            ->value('updated_at');

        return response()->json([
            'success' => true,
            'data' => [
                'last_synced_at' => $lastSyncedAt?->toIso8601String(),
                'uploaded_device_call_ids' => $uploadedDeviceCallIds,
                'pending_recordings' => $pendingRecordings,
            ],
        ]);
    }
}
