<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    public function getTranslation($locale)
    {
        // Whitelist locales
        if (!in_array($locale, ['en', 'vi'])) {
            abort(404, 'Invalid locale');
        }

        $path = lang_path("{$locale}.json");
        if (!File::exists($path)) {
            abort(404, 'Language file not found');
        }

        return response()->file($path, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'public, max-age=86400',
            'ETag' => md5_file($path),
        ]);
    }
}