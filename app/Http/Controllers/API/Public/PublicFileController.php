<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Streams a public-disk file for CRM sync.
 * /storage/... 404s when the public storage link is missing or the filename contains spaces.
 *
 * GET /api/v1/public/files/{path}
 * Auth: X-CRM-API-KEY
 */
class PublicFileController extends Controller
{
    public function __invoke(Request $request, string $path): Response|JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Provide a valid X-CRM-API-KEY header.',
            ], 401);
        }

        $path = $this->safeDiskPath($path);
        if ($path === null || ! Storage::disk('public')->exists($path)) {
            return response()->json([
                'status' => false,
                'message' => 'File not found.',
            ], 404);
        }

        return Storage::disk('public')->response($path);
    }

    private function isAuthorized(Request $request): bool
    {
        $configuredKey = (string) config('services.lms.api_key');
        if ($configuredKey === '') {
            return false;
        }

        $provided = (string) (
            $request->header('X-CRM-API-KEY')
            ?? $request->header('X-Api-Key')
            ?? $request->query('api_key')
            ?? ''
        );

        return hash_equals($configuredKey, $provided);
    }

    private function safeDiskPath(string $path): ?string
    {
        $path = ltrim(str_replace('\\', '/', rawurldecode($path)), '/');

        foreach (['storage/', 'public/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }

        $path = ltrim($path, '/');
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }
}
