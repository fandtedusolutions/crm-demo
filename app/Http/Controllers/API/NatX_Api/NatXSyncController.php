<?php

namespace App\Http\Controllers\API\NatX_Api;

use App\Http\Controllers\Controller;
use App\Models\NatXAppLog;
use App\Models\NatXAppRecording;
use App\Services\CallRecording\CallRecordingPlaybackPreparer;
use App\Services\CallRecording\RecordingFileTypeDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NatXSyncController extends Controller
{
    public function __construct(
        private readonly RecordingFileTypeDetector $recordingFileTypeDetector,
    ) {
    }

    /**
     * Bulk sync call details from the NatX app.
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
            $timestamps = $this->resolveCallTimestamps($item);

            $remarks = $item['remarks'] ?? null;
            if ($remarks === null && $item['call_type'] === 'not_picked') {
                $remarks = 'Not Picked';
            } elseif ($remarks === null && $item['call_type'] === 'outgoing' && (int) $item['duration_seconds'] === 0) {
                $remarks = 'Not Picked';
            }

            $call = NatXAppLog::updateOrCreate(
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
                    'started_at_ms' => $timestamps['started_at_ms'],
                    'started_at' => $timestamps['started_at'],
                    'end_at_ms' => $timestamps['end_at_ms'],
                    'ended_at' => $timestamps['ended_at'],
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
                'started_at_ms' => $call->started_at_ms,
                'started_at' => $call->display_started_at?->toIso8601String(),
                'end_at_ms' => $call->end_at_ms,
                'ended_at' => $call->display_ended_at?->toIso8601String(),
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
     * Upload a NatX call recording when it is not already stored on the server.
     */
    public function uploadRecording(Request $request)
    {
        $this->prepareRecordingUploadRequest($request);

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

        $file = $this->resolveRecordingUploadFile($request);
        if (!$file || !$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'recording' => ['No valid recording file was received. Send multipart/form-data with a file field named "recording".'],
                ],
            ], 422);
        }

        if ($file->getSize() <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'recording' => ['The recording file is empty.'],
                ],
            ], 422);
        }

        $maxUploadKilobytes = 25600;
        $fileSizeKilobytes = (int) ceil($file->getSize() / 1024);
        if ($fileSizeKilobytes > $maxUploadKilobytes) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'recording' => ["Recording is too large ({$fileSizeKilobytes} KB). Maximum allowed size is {$maxUploadKilobytes} KB."],
                ],
            ], 422);
        }

        $validated = $request->validate([
            'duration_seconds' => 'nullable|integer|min:0',
            'recorded_at_ms' => 'nullable|integer',
            'file_size_bytes' => 'nullable|integer|min:0',
            'original_file_name' => 'nullable|string|max:255',
        ]);

        $originalFileName = $validated['original_file_name']
            ?? $request->input('original_file_name')
            ?? $call->recording_file_name;

        if (!$this->isAllowedRecordingUpload($file, $originalFileName)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'recording' => ['Invalid audio file type. Allowed: amr, m4a, mp3, wav, 3gp, aac.'],
                ],
            ], 422);
        }

        $extension = $this->resolveRecordingExtension($file, $originalFileName) ?? 'bin';
        $path = $file->storeAs(
            "natx-recordings/{$telecallerId}/{$call->id}",
            'recording.' . $extension,
            'public'
        );

        if (!$path || !Storage::disk('public')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store recording file on server',
            ], 500);
        }

        $recording = NatXAppRecording::updateOrCreate(
            ['natx_app_log_id' => $call->id],
            [
                'telecaller_id' => $telecallerId,
                'file_path' => $path,
                'file_name' => $originalFileName ?: $file->getClientOriginalName(),
                'mime_type' => $this->resolveRecordingMimeType($file, $originalFileName),
                'file_size_bytes' => $validated['file_size_bytes'] ?? $file->getSize(),
                'duration_seconds' => $validated['duration_seconds'] ?? 0,
                'recorded_at_ms' => $validated['recorded_at_ms'] ?? null,
            ]
        );

        app(CallRecordingPlaybackPreparer::class)->prepare($path);

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

        $calls = NatXAppLog::where('telecaller_id', $telecallerId)
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

            $recording = $call->recording;
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
     * Get sync status for the authenticated NatX user.
     */
    public function status(Request $request)
    {
        $sinceMs = $request->query('since_ms');
        $telecallerId = $request->user()->id;

        $query = NatXAppLog::where('telecaller_id', $telecallerId);

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

        $lastSyncedAt = NatXAppLog::where('telecaller_id', $telecallerId)
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

    private function prepareRecordingUploadRequest(Request $request): void
    {
        $normalized = [];

        foreach (['duration_seconds', 'recorded_at_ms', 'file_size_bytes', 'server_call_id'] as $field) {
            $value = $request->input($field);
            if ($value === null || $value === '') {
                continue;
            }

            if (is_numeric($value)) {
                $normalized[$field] = (int) $value;
            }
        }

        if ($normalized !== []) {
            $request->merge($normalized);
        }
    }

    private function resolveRecordingUploadFile(Request $request)
    {
        foreach (['recording', 'file', 'audio'] as $field) {
            $file = $request->file($field);
            if ($file) {
                return $file;
            }
        }

        return null;
    }

    private function resolveCallTimestamps(array $item): array
    {
        $startedAtMs = (int) $item['started_at_ms'];
        $startedAt = NatXAppLog::dateTimeFromMilliseconds($startedAtMs);

        $endAtMs = isset($item['end_at_ms']) ? (int) $item['end_at_ms'] : null;
        if (($endAtMs === null || $endAtMs <= 0) && !empty($item['duration_seconds'])) {
            $endAtMs = $startedAtMs + ((int) $item['duration_seconds'] * 1000);
        }

        $endedAt = $endAtMs ? NatXAppLog::dateTimeFromMilliseconds($endAtMs) : null;

        return [
            'started_at_ms' => $startedAtMs,
            'started_at' => $startedAt,
            'end_at_ms' => $endAtMs,
            'ended_at' => $endedAt,
        ];
    }

    private function findCallForRecordingUpload(Request $request, int $telecallerId): ?NatXAppLog
    {
        $query = NatXAppLog::where('telecaller_id', $telecallerId);

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
    private function findExistingServerRecording(NatXAppLog $call): ?NatXAppRecording
    {
        $recording = NatXAppRecording::where('natx_app_log_id', $call->id)->first();

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

    private function recordingUploadResponse(NatXAppLog $call, NatXAppRecording $recording, bool $skipped)
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

        if (in_array($mime, $this->allowedRecordingMimes(), true) || str_starts_with($mime, 'audio/')) {
            return true;
        }

        return $this->detectRecordingTypeFromUploadedFile($file) !== null;
    }

    private function resolveRecordingExtension($file, ?string $originalFileName = null): ?string
    {
        foreach ([
            $file->getClientOriginalExtension(),
            pathinfo((string) $file->getClientOriginalName(), PATHINFO_EXTENSION),
            pathinfo((string) $originalFileName, PATHINFO_EXTENSION),
        ] as $candidate) {
            $extension = strtolower(trim((string) $candidate));
            if ($extension !== '' && in_array($extension, $this->allowedRecordingExtensions(), true)) {
                return $extension;
            }
        }

        $mimeExtension = $this->recordingFileTypeDetector->extensionFromMime(
            strtolower((string) $file->getMimeType())
        );
        if ($mimeExtension && in_array($mimeExtension, $this->allowedRecordingExtensions(), true)) {
            return $mimeExtension;
        }

        $detected = $this->detectRecordingTypeFromUploadedFile($file);

        return $detected['extension'] ?? null;
    }

    private function resolveRecordingMimeType($file, ?string $originalFileName = null): string
    {
        $mime = strtolower((string) ($file->getMimeType() ?? ''));
        if (in_array($mime, ['application/octet-stream', 'audio/octet-stream', 'binary/octet-stream'], true)) {
            $mime = '';
        }

        if ($mime !== '') {
            return $mime;
        }

        $extension = $this->resolveRecordingExtension($file, $originalFileName);
        if ($extension) {
            return $this->recordingFileTypeDetector->mimeFromExtension($extension)
                ?? 'application/octet-stream';
        }

        $detected = $this->detectRecordingTypeFromUploadedFile($file);

        return $detected['mime'] ?? 'application/octet-stream';
    }

    /**
     * @return array{extension: string, mime: string}|null
     */
    private function detectRecordingTypeFromUploadedFile($file): ?array
    {
        $path = $file->getPathname();

        return is_string($path) && $path !== ''
            ? $this->recordingFileTypeDetector->detectFromPath($path)
            : null;
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
            'audio/octet-stream',
            'video/mp4',
            'application/octet-stream',
        ];
    }
}
