<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Media
{
    /**
     * Resolves a branding asset (logo, home banner, claim icon) stored as a
     * bare filename under a Setting key into its bucket URL.
     */
    public static function brandingUrl(string $settingKey): ?string
    {
        $filename = Setting::get($settingKey);

        return $filename ? self::url('branding/'.$filename) : null;
    }

    /**
     * The bucket has no public-read access (Railway's Bucket dashboard offers no
     * such toggle), so every URL must be a signed, time-limited request instead
     * of a permanent public one. A fresh signature every request would give the
     * same file a different URL on every page load, which defeats the browser's
     * HTTP cache entirely (the URL is the cache key) and forces a full re-fetch
     * of the image bytes every time it's rendered. Caching the signed URL itself
     * for a while shorter than its own validity keeps it stable so the browser
     * can actually reuse what it already downloaded.
     */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Cache::remember(
            "media-url:{$path}",
            now()->addHours(20),
            fn () => Storage::disk('s3')->temporaryUrl($path, now()->addDay()),
        );
    }

    public static function put(UploadedFile $file, string $directory, string $filename): string
    {
        Storage::disk('s3')->putFileAs($directory, $file, $filename);
        Cache::forget("media-url:{$directory}/{$filename}");

        return $filename;
    }

    public static function delete(string $directory, ?string $filename): void
    {
        if ($filename) {
            Storage::disk('s3')->delete("{$directory}/{$filename}");
            Cache::forget("media-url:{$directory}/{$filename}");
        }
    }
}
