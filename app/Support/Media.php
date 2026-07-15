<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Media
{
    /**
     * The bucket has no public-read access (Railway's Bucket dashboard offers no
     * such toggle), so every URL must be a signed, time-limited request instead
     * of a permanent public one. Regenerated fresh on every request that renders
     * it, so the expiry only matters if a single page is left open past it.
     */
    public static function url(?string $path): ?string
    {
        return $path ? Storage::disk('s3')->temporaryUrl($path, now()->addDay()) : null;
    }

    public static function put(UploadedFile $file, string $directory, string $filename): string
    {
        Storage::disk('s3')->putFileAs($directory, $file, $filename);

        return $filename;
    }

    public static function delete(string $directory, ?string $filename): void
    {
        if ($filename) {
            Storage::disk('s3')->delete("{$directory}/{$filename}");
        }
    }
}
