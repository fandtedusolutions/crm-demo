<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PublicStorageHelper
{
    /**
     * Whether public/storage is symlinked to storage/app/public.
     */
    public static function publicStorageIsLinked(): bool
    {
        $path = public_path('storage');

        if (is_link($path)) {
            return true;
        }

        if (PHP_OS_FAMILY === 'Windows' && file_exists($path)) {
            $target = @readlink($path);

            return $target !== false && $target !== '';
        }

        return false;
    }

    /**
     * Store an uploaded file on the public disk and mirror it when needed.
     */
    public static function storeFile(UploadedFile $file, string $filename): string
    {
        static::deleteFile($filename);

        Storage::disk('public')->putFileAs('', $file, $filename);
        static::mirrorToWebPublic($filename);

        return 'storage/' . $filename;
    }

    /**
     * Delete a file from both the public disk and public/storage copy.
     */
    public static function deleteFile(string $filename): void
    {
        if (Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->delete($filename);
        }

        $webPath = public_path('storage/' . str_replace('\\', '/', $filename));
        if (is_file($webPath)) {
            @unlink($webPath);
        }
    }

    /**
     * Copy from storage/app/public to public/storage when not symlinked.
     */
    protected static function mirrorToWebPublic(string $filename): void
    {
        if (static::publicStorageIsLinked()) {
            return;
        }

        $source = Storage::disk('public')->path($filename);
        if (!is_file($source)) {
            return;
        }

        $destination = public_path('storage/' . str_replace('\\', '/', $filename));
        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);
    }

    /**
     * Build a public asset URL with cache-busting for uploaded storage files.
     */
    public static function publicUrl(string $path): string
    {
        if (!str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        $relative = substr($path, strlen('storage/'));
        $webPath = public_path('storage/' . $relative);

        if (!is_file($webPath)) {
            $webPath = Storage::disk('public')->path($relative);
        }

        $url = asset($path);

        if (is_file($webPath)) {
            $url .= '?v=' . filemtime($webPath);
        }

        return $url;
    }
}
