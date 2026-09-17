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
}
