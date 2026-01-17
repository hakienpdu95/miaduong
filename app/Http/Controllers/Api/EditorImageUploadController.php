<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\EditorImageService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EditorImageUploadController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('throttle:60,1', only: ['upload', 'revert']),
        ];
    }

    protected $imageService;

    public function __construct(EditorImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function upload(Request $request)
    {
        $file = $request->file('single_image');
        if (!$file || !$file->isValid()) {
            return response()->json(['success' => false, 'message' => 'No valid file uploaded'], 422);
        }

        try {
            $options = $request->only(['watermark', 'quality']);
            $result = $this->imageService->processImage($file, $options);
            return response()->json(['success' => true, 'files' => [$result]], 200);
        } catch (\Exception $e) {
            Log::error('Editor image upload failed:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function revert(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || !isset($data['full_url'])) {
                return response()->json(['success' => false, 'message' => 'Invalid or missing full_url'], 422);
            }

            $this->imageService->deleteImage($data['full_url']);
            return response()->json(['success' => true, 'message' => 'File deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Editor image deletion failed:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}