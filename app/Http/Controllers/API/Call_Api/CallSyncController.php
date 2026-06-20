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
                'calls.*.call_type' => 'required|in:incoming,outgoing,missed,rejected,not_picked,unknown',
                'calls.*.duration_seconds' => 'required|integer|min:0',
                'calls.*.remarks' => 'nullable|string|max:255',
                'calls.*.started_at_ms' => 'required|integer',
                'calls.*.started_at' => 'nullable|date',
                'calls.*.end_at_ms' => 'nullable|integer',
                'calls.*.ended_at' => 'nullable|date',
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

            $endAtMs = $item['end_at_ms'] ?? null;
            if ($endAtMs === null && !empty($item['duration_seconds'])) {
                $endAtMs = $item['started_at_ms'] + ((int) $item['duration_seconds'] * 1000);
            }

            $endedAt = isset($item['ended_at'])
                ? Carbon::parse($item['ended_at'])
                : ($endAtMs ? Carbon::createFromTimestampMs($endAtMs) : null);

            $remarks = $item['remarks'] ?? null;
            if ($remarks === null && $item['call_type'] === 'not_picked') {
                $remarks = 'Not Picked';
            } elseif ($remarks === null && $item['call_type'] === 'outgoing' && (int) $item['duration_seconds'] === 0) {
                $remarks = 'Not Picked';
            }

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
                    'remarks' => $remarks,
                    'duration_seconds' => $item['duration_seconds'],
                    'started_at_ms' => $item['started_at_ms'],
                    'started_at' => $startedAt,
                    'end_at_ms' => $endAtMs,
                    'ended_at' => $endedAt,
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
                'status' => $call->wasRecentlyCreated ? 'created' : 'skipped',
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
     * Upload a call recording when it is not already stored on the server.
     */
    public function uploadRecording(Request $request)
    {
        $idValidator = Validator::make($request->all(), [
            'device_call_id' => 'nullable|string|max:120',
            'server_call_id' => 'nullable|integer',
        ]);

        $idValidator->after(function ($validator) use ($request) {
            if (!$request->filled('device_call_id') && !$request->filled('server_call_id')) {
                $validator->errors()->add('device_call_id', 'device_call_id or server_call_id is required');
            }
        });

        if ($idValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $idValidator->errors(),
            ], 422);
        }

        $telecallerId = $request->user()->id;
        $call = $this->findCallForRecordingUpload($request, $telecallerId);

        if (!$call) {
            return response()->json([
                'success' => false,
                'message' => 'Call record not found',
            ], 404);
        }

        $existingRecording = $this->findExistingServerRecording($call);
        if ($existingRecording) {
            return $this->recordingUploadResponse($call, $existingRecording, skipped: true);
        }

        $validator = Validator::make($request->all(), [
            'recording' => 'required|file|max:25600',
            'duration_seconds' => 'nullable|integer|min:0',
            'recorded_at_ms' => 'nullable|integer',
            'file_size_bytes' => 'nullable|integer|min:0',
            'original_file_name' => 'nullable|string|max:255',
        ]);

        $validator->after(function ($validator) use ($request) {
            $file = $request->file('recording');
            if (!$file || $this->isAllowedRecordingUpload($file, $request->input('original_file_name'))) {
                return;
            }

            $validator->errors()->add(
                'recording',
                'Invalid audio file type. Allowed: amr, m4a, mp3, wav, 3gp, aac'
            );
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $file = $request->file('recording');
        $extension = $this->resolveRecordingExtension($file, $validated['original_file_name'] ?? null) ?? 'bin';
        $storedFileName = 'recording.' . $extension;
        $path = $file->storeAs(
            "call-recordings/{$telecallerId}/{$call->id}",
            $storedFileName,
            'public'
        );

        if (!$path || !Storage::disk('public')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store recording file on server',
            ], 500);
        }

        $recording = CallAppRecording::updateOrCreate(
            ['call_app_log_id' => $call->id],
            [
                'telecaller_id' => $telecallerId,
                'file_path' => $path,
                'file_name' => $validated['original_file_name'] ?? $file->getClientOriginalName(),
                'mime_type' => $this->resolveRecordingMimeType(
                    $file,
                    $validated['original_file_name'] ?? null
                ),
                'file_size_bytes' => $validated['file_size_bytes'] ?? $file->getSize(),
                'duration_seconds' => $validated['duration_seconds'] ?? 0,
                'recorded_at_ms' => $validated['recorded_at_ms'] ?? null,
            ]
        );

        $call->update([
            'has_recording' => true,
            'recording_uploaded' => true,
        ]);

        return $this->recordingUploadResponse($call, $recording, skipped: false);
    }

    /**
     * Check recording upload status for a batch of device_call_ids.
     */
    public function recordingStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_call_ids' => 'required|array|min:1|max:100',
                'device_call_ids.*' => 'required|string|max:120',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $telecallerId = $request->user()->id;
        $ids = array_values(array_unique($validated['device_call_ids']));

        $calls = CallAppLog::where('telecaller_id', $telecallerId)
            ->whereIn('device_call_id', $ids)
            ->with('recording')
            ->get()
            ->keyBy('device_call_id');

        $recordings = [];
        $notFound = [];

        foreach ($ids as $deviceCallId) {
            $call = $calls->get($deviceCallId);

            if (!$call) {
                $notFound[] = $deviceCallId;
                continue;
            }

            $storedPath = $recording?->storedStoragePath();
            $uploaded = (bool) $call->recording_uploaded && $storedPath !== null;
            $hasRecording = (bool) $call->has_recording;

            $recordings[] = [
                'device_call_id' => $call->device_call_id,
                'server_call_id' => $call->id,
                'has_recording' => $hasRecording,
                'recording_uploaded' => $uploaded,
                'upload_required' => $hasRecording && !$uploaded,
                'recording_url' => $storedPath ? $recording->file_url : null,
                'recording_id' => $recording?->id,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'recordings' => $recordings,
                'not_found' => $notFound,
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

    private function findCallForRecordingUpload(Request $request, int $telecallerId): ?CallAppLog
    {
        $query = CallAppLog::where('telecaller_id', $telecallerId);

        if ($request->filled('server_call_id')) {
            $query->where('id', $request->input('server_call_id'));
        } else {
            $query->where('device_call_id', $request->input('device_call_id'));
        }

        return $query->first();
    }

    /**
     * Returns the stored recording when the audio file is already on the server.
     */
    private function findExistingServerRecording(CallAppLog $call): ?CallAppRecording
    {
        $recording = CallAppRecording::where('call_app_log_id', $call->id)->first();

        if (!$recording || !$recording->storedStoragePath()) {
            return null;
        }

        if (!$call->recording_uploaded) {
            $call->update([
                'has_recording' => true,
                'recording_uploaded' => true,
            ]);
        }

        return $recording;
    }

    private function recordingUploadResponse(CallAppLog $call, CallAppRecording $recording, bool $skipped)
    {
        return response()->json([
            'success' => true,
            'message' => $skipped
                ? 'Recording already exists on server'
                : 'Recording uploaded successfully',
            'data' => [
                'server_call_id' => $call->id,
                'device_call_id' => $call->device_call_id,
                'recording_id' => $recording->id,
                'recording_url' => $recording->file_url,
                'duration_seconds' => $recording->duration_seconds,
                'file_size_bytes' => $recording->file_size_bytes,
                'recording_uploaded' => true,
                'skipped' => $skipped,
            ],
        ]);
    }

    private function isAllowedRecordingUpload($file, ?string $originalFileName = null): bool
    {
        $extension = $this->resolveRecordingExtension($file, $originalFileName);
        if ($extension && in_array($extension, $this->allowedRecordingExtensions(), true)) {
            return true;
        }

        $mime = strtolower((string) $file->getMimeType());

        return in_array($mime, $this->allowedRecordingMimes(), true)
            || str_starts_with($mime, 'audio/');
    }

    private function resolveRecordingExtension($file, ?string $originalFileName = null): ?string
    {
        foreach ([
            $file->getClientOriginalExtension(),
            pathinfo((string) $file->getClientOriginalName(), PATHINFO_EXTENSION),
            pathinfo((string) $originalFileName, PATHINFO_EXTENSION),
        ] as $candidate) {
            $extension = strtolower(trim((string) $candidate));
            if ($extension !== '') {
                return $extension;
            }
        }

        return null;
    }

    private function resolveRecordingMimeType($file, ?string $originalFileName = null): string
    {
        $mime = strtolower((string) ($file->getMimeType() ?? ''));
        if ($mime !== '' && $mime !== 'application/octet-stream') {
            return $mime;
        }

        return match ($this->resolveRecordingExtension($file, $originalFileName)) {
            'aac' => 'audio/aac',
            'm4a' => 'audio/mp4',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'amr' => 'audio/amr',
            '3gp' => 'audio/3gpp',
            default => $mime !== '' ? $mime : 'application/octet-stream',
        };
    }

    /**
     * @return list<string>
     */
    private function allowedRecordingExtensions(): array
    {
        return ['amr', 'm4a', 'mp3', 'wav', '3gp', 'aac'];
    }

    /**
     * @return list<string>
     */
    private function allowedRecordingMimes(): array
    {
        return [
            'audio/amr',
            'audio/mpeg',
            'audio/mp4',
            'audio/x-m4a',
            'audio/mp4a-latm',
            'audio/aac',
            'audio/x-aac',
            'audio/aacp',
            'audio/wav',
            'audio/x-wav',
            'audio/3gpp',
            'audio/3gp',
            'video/mp4',
            'application/octet-stream',
        ];
    }
}
