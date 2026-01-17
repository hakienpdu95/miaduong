<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckMissingTranslations extends Command
{
    protected $signature = 'lang:check-missing';
    protected $description = 'Check for missing translations in JSON files';

    public function handle()
    {
        $locales = ['en', 'vi'];
        $baseFile = lang_path('en.json');

        // Kiểm tra file base
        if (!File::exists($baseFile)) {
            $this->error("Base file {$baseFile} does not exist.");
            // Tạo file en.json mặc định nếu không tồn tại
            $this->createDefaultBaseFile($baseFile);
            $base = json_decode(File::get($baseFile), true);
        } else {
            $base = json_decode(File::get($baseFile), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Invalid JSON in {$baseFile}: " . json_last_error_msg());
                return 1;
            }
        }

        $missingKeys = [];

        foreach ($locales as $locale) {
            $file = lang_path("{$locale}.json");
            if (!File::exists($file)) {
                $this->warn("File {$file} does not exist.");
                continue;
            }

            $translations = json_decode(File::get($file), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->warn("Invalid JSON in {$file}: " . json_last_error_msg());
                continue;
            }

            $missing = array_diff_key($base, $translations);

            if ($missing) {
                $missingKeys[$locale] = array_keys($missing);
                $this->table(
                    ['Key', 'English Value'],
                    array_map(fn($key) => [$key, $base[$key]], array_keys($missing))
                );
            }
        }

        if (empty($missingKeys)) {
            $this->info('No missing translations found.');
        } else {
            $this->askToSync($missingKeys, $base);
        }

        return 0;
    }

    protected function createDefaultBaseFile($baseFile)
    {
        $defaultTranslations = [
            'welcome' => 'Welcome to the system',
            'add_success' => 'Added :title successfully',
            'submit' => 'Submit',
        ];

        File::put($baseFile, json_encode($defaultTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("Created default {$baseFile}.");
    }

    protected function askToSync($missingKeys, $base)
    {
        if ($this->confirm('Do you want to sync missing keys?')) {
            foreach ($missingKeys as $locale => $keys) {
                $file = lang_path("{$locale}.json");
                $translations = json_decode(File::get($file), true);

                foreach ($keys as $key) {
                    $translations[$key] = $locale === 'en' ? $base[$key] : '';
                }

                File::put($file, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $this->info("Synced missing keys for {$locale}.");
            }
        }
    }
}