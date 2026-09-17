<?php

namespace App\Services;

use App\Models\LmsCourse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class LmsCourseService
{
    /**
     * Fetch courses from the LMS API and upsert new/updated rows into lms_courses.
     * Existing mappings are left untouched.
     *
     * @return array{fetched: int, created: int, updated: int}
     */
    public function refreshCourses(): array
    {
        $configuredUrl = (string) config('services.lms.courses_url');
        $apiKey = (string) config('services.lms.api_key');

        if ($configuredUrl === '' || $apiKey === '') {
            throw new RuntimeException(
                'LMS courses API is not configured. Set LMS_COURSES_API_URL and CRM_API_KEY in .env.'
            );
        }

        $url = $this->resolveCoursesUrl($configuredUrl);

        $response = Http::withHeaders([
            'X-CRM-API-KEY' => $apiKey,
            'Accept' => 'application/json',
        ])
            ->timeout(30)
            ->get($url);

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            Log::warning('LMS courses API request failed', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $body,
            ]);

            throw new RuntimeException(
                'Failed to fetch LMS courses (HTTP '.$response->status().').'
            );
        }

        if (($body['status'] ?? null) !== 'success') {
            throw new RuntimeException(
                'LMS courses API returned an unexpected response.'
            );
        }

        $courses = $body['courses'] ?? [];
        if (! is_array($courses)) {
            throw new RuntimeException('LMS courses API returned invalid course data.');
        }

        $created = 0;
        $updated = 0;

        foreach ($courses as $course) {
            $lmsId = (int) ($course['id'] ?? 0);
            $title = trim((string) ($course['name'] ?? ''));

            if ($lmsId <= 0 || $title === '') {
                continue;
            }

            $existing = LmsCourse::find($lmsId);

            if (! $existing) {
                LmsCourse::create([
                    'id' => $lmsId,
                    'title' => $title,
                ]);
                $created++;
                continue;
            }

            if ($existing->title !== $title) {
                $existing->update(['title' => $title]);
                $updated++;
            }
        }

        return [
            'fetched' => count($courses),
            'created' => $created,
            'updated' => $updated,
        ];
    }

    /**
     * Fetch streams for a mapped LMS course id.
     *
     * @return array<int, array{id: int, name: string, course_id: int|null, course_name: string|null}>
     */
    public function getStreams(int $lmsCourseId): array
    {
        $apiKey = (string) config('services.lms.api_key');
        $configuredStreamsUrl = (string) config('services.lms.streams_url');
        $configuredCoursesUrl = (string) config('services.lms.courses_url');

        if ($apiKey === '') {
            throw new RuntimeException(
                'LMS API is not configured. Set CRM_API_KEY in .env.'
            );
        }

        $url = $this->resolveStreamsUrl(
            $configuredStreamsUrl !== '' ? $configuredStreamsUrl : $configuredCoursesUrl
        );

        if ($url === '') {
            throw new RuntimeException(
                'LMS streams API is not configured. Set LMS_STREAMS_API_URL or LMS_COURSES_API_URL in .env.'
            );
        }

        $response = Http::withHeaders([
            'X-CRM-API-KEY' => $apiKey,
            'Accept' => 'application/json',
        ])
            ->timeout(30)
            ->get($url, ['course_id' => $lmsCourseId]);

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            Log::warning('LMS streams API request failed', [
                'url' => $url,
                'course_id' => $lmsCourseId,
                'status' => $response->status(),
                'body' => $body,
            ]);

            throw new RuntimeException(
                'Failed to fetch LMS streams (HTTP '.$response->status().').'
            );
        }

        if (($body['status'] ?? null) !== 'success') {
            throw new RuntimeException(
                'LMS streams API returned an unexpected response.'
            );
        }

        $streams = $body['streams'] ?? [];
        if (! is_array($streams)) {
            throw new RuntimeException('LMS streams API returned invalid stream data.');
        }

        $normalized = [];
        foreach ($streams as $stream) {
            $id = (int) ($stream['id'] ?? 0);
            $name = trim((string) ($stream['name'] ?? ''));
            if ($id <= 0 || $name === '') {
                continue;
            }

            $normalized[] = [
                'id' => $id,
                'name' => $name,
                'course_id' => isset($stream['course_id']) ? (int) $stream['course_id'] : null,
                'course_name' => isset($stream['course_name']) ? (string) $stream['course_name'] : null,
            ];
        }

        return $normalized;
    }

    /**
     * Create / share a lead on the LMS API.
     *
     * @param  array{
     *     name: string,
     *     phone_no: string,
     *     email?: string|null,
     *     age?: int|null,
     *     course_id: int,
     *     stream_id: int,
     *     notes?: string|null
     * }  $payload
     * @return array<string, mixed>
     */
    public function createLead(array $payload): array
    {
        $apiKey = (string) config('services.lms.api_key');
        $configuredLeadsUrl = (string) config('services.lms.leads_url');
        $configuredCoursesUrl = (string) config('services.lms.courses_url');

        if ($apiKey === '') {
            throw new RuntimeException(
                'LMS API is not configured. Set CRM_API_KEY in .env.'
            );
        }

        $url = $this->resolveLeadsUrl(
            $configuredLeadsUrl !== '' ? $configuredLeadsUrl : $configuredCoursesUrl
        );

        if ($url === '') {
            throw new RuntimeException(
                'LMS leads API is not configured. Set LMS_LEADS_API_URL or LMS_COURSES_API_URL in .env.'
            );
        }

        $bodyPayload = [
            'name' => (string) $payload['name'],
            'phone_no' => (string) $payload['phone_no'],
            'course_id' => (int) $payload['course_id'],
            'stream_id' => (int) $payload['stream_id'],
        ];

        if (! empty($payload['email'])) {
            $bodyPayload['email'] = (string) $payload['email'];
        }

        if (isset($payload['age']) && $payload['age'] !== null && $payload['age'] !== '') {
            $bodyPayload['age'] = (int) $payload['age'];
        }

        if (isset($payload['notes']) && trim((string) $payload['notes']) !== '') {
            $bodyPayload['notes'] = trim((string) $payload['notes']);
        }

        $response = Http::withHeaders([
            'X-CRM-API-KEY' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->timeout(30)
            ->post($url, $bodyPayload);

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            Log::warning('LMS leads API request failed', [
                'url' => $url,
                'status' => $response->status(),
                'payload' => $bodyPayload,
                'body' => $body,
            ]);

            $message = is_array($body)
                ? (string) ($body['message'] ?? $body['error'] ?? '')
                : '';

            throw new RuntimeException(
                $message !== ''
                    ? $message
                    : 'Failed to send lead to LMS (HTTP '.$response->status().').'
            );
        }

        return is_array($body) ? $body : [];
    }

    /**
     * Accept either a full courses URL or an LMS base URL.
     *
     * Examples:
     * - http://127.0.0.1:8000/
     * - http://127.0.0.1:8000/api/v1
     * - http://127.0.0.1:8000/api/v1/crm/courses/
     */
    private function resolveCoursesUrl(string $configured): string
    {
        $url = rtrim(trim($configured), '/');

        if ($url === '') {
            return $url;
        }

        if (str_contains($url, '/crm/courses')) {
            return $url.'/';
        }

        if (str_ends_with($url, '/api/v1')) {
            return $url.'/crm/courses/';
        }

        return $url.'/api/v1/crm/courses/';
    }

    /**
     * Accept either a full streams URL or an LMS base URL.
     *
     * Examples:
     * - http://127.0.0.1:8000/
     * - http://127.0.0.1:8000/api/v1
     * - http://127.0.0.1:8000/api/v1/crm/streams/
     */
    private function resolveStreamsUrl(string $configured): string
    {
        $url = rtrim(trim($configured), '/');

        if ($url === '') {
            return $url;
        }

        if (str_contains($url, '/crm/streams')) {
            return $url.'/';
        }

        if (str_contains($url, '/crm/courses')) {
            return preg_replace('#/crm/courses/?$#', '/crm/streams/', $url) ?? ($url.'/../streams/');
        }

        if (str_ends_with($url, '/api/v1')) {
            return $url.'/crm/streams/';
        }

        return $url.'/api/v1/crm/streams/';
    }

    /**
     * Accept either a full leads URL or an LMS base URL.
     *
     * Examples:
     * - http://127.0.0.1:8000/
     * - http://127.0.0.1:8000/api/v1
     * - http://127.0.0.1:8000/api/v1/crm/leads/
     */
    private function resolveLeadsUrl(string $configured): string
    {
        $url = rtrim(trim($configured), '/');

        if ($url === '') {
            return $url;
        }

        if (str_contains($url, '/crm/leads')) {
            return $url.'/';
        }

        if (str_contains($url, '/crm/courses')) {
            return preg_replace('#/crm/courses/?$#', '/crm/leads/', $url) ?? ($url.'/../leads/');
        }

        if (str_contains($url, '/crm/streams')) {
            return preg_replace('#/crm/streams/?$#', '/crm/leads/', $url) ?? ($url.'/../leads/');
        }

        if (str_ends_with($url, '/api/v1')) {
            return $url.'/crm/leads/';
        }

        return $url.'/api/v1/crm/leads/';
    }
}
