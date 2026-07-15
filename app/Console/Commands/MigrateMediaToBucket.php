<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('media:migrate-to-bucket')]
#[Description('Upload existing local branding/package images to the S3-compatible bucket (one-off, idempotent)')]
class MigrateMediaToBucket extends Command
{
    public function handle(): void
    {
        $branding = array_filter([
            Setting::get('logo'),
            Setting::get('home_banner'),
            Setting::get('claim_image'),
        ]);

        foreach ($branding as $filename) {
            $this->copyToDisk(base_path("images/{$filename}"), "branding/{$filename}");
        }

        $packagesDir = base_path('images/packages');
        if (is_dir($packagesDir)) {
            foreach (scandir($packagesDir) as $filename) {
                if ($filename === '.' || $filename === '..') {
                    continue;
                }
                $this->copyToDisk("{$packagesDir}/{$filename}", "packages/{$filename}");
            }
        }

        $this->info('Media migration complete.');
    }

    private function copyToDisk(string $localPath, string $remotePath): void
    {
        if (!is_file($localPath)) {
            $this->warn("Skipping missing file: {$localPath}");
            return;
        }

        if (Storage::disk('s3')->exists($remotePath)) {
            $this->line("Already in bucket, skipping: {$remotePath}");
            return;
        }

        Storage::disk('s3')->put($remotePath, file_get_contents($localPath));
        $this->info("Uploaded: {$remotePath}");
    }
}
