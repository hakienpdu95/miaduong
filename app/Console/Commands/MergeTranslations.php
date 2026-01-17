<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MergeTranslations extends Command
{
    protected $signature = 'translations:merge';
    protected $description = 'Merge all JSON translation files into a single file per locale';

    public function handle()
    {
        $locales = ['en', 'vi']; // Danh sách các locale
        $langPath = lang_path();

        foreach ($locales as $locale) {
            $localePath = "{$langPath}/{$locale}";
            if (!File::isDirectory($localePath)) {
                $this->error("Directory {$localePath} not found.");
                continue;
            }

            $translations = [];
            $filesProcessed = 0;

            // Lấy tất cả file JSON trừ file {locale}.json
            foreach (File::files($localePath) as $file) {
                if ($file->getExtension() === 'json' && $file->getFilename() !== "{$locale}.json") {
                    $this->info("Processing file: {$file->getPathname()}");
                    $content = json_decode($file->getContents(), true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($content)) {
                        $translations = array_merge($translations, $content);
                        $filesProcessed++;
                        $this->info("Successfully merged: {$file->getFilename()}");
                    } else {
                        $errorMsg = json_last_error_msg();
                        $this->error("Invalid JSON in {$file->getPathname()}: {$errorMsg}");
                        Log::warning("Invalid JSON in {$file->getPathname()}: {$errorMsg}");
                    }
                }
            }

            if ($filesProcessed === 0 || empty($translations)) {
                $this->warn("No valid translations found for locale {$locale}.");
                continue;
            }

            // Lưu vào file lang/{locale}.json
            $outputFile = "{$langPath}/{$locale}.json";
            try {
                File::put($outputFile, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $this->info("Merged {$filesProcessed} files for {$locale} into {$outputFile}.");
            } catch (\Exception $e) {
                $this->error("Failed to write to {$outputFile}: {$e->getMessage()}");
                Log::error("Failed to write to {$outputFile}: {$e->getMessage()}");
            }
        }

        return 0;
    }
}