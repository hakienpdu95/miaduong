<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ImageService;

class ImageUploadController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function upload(Request $request)
    {
        Log::debug('Upload request data:', ['request' => $request->all(), 'files' => $request->file()]);

        $files = $request->file('single_image') ?: $request->file('multiple_images') ?: [];
        $uploadedFiles = [];

        try {
            if (empty($files)) {
                Log::error('No files received in request');
                return response()->json([
                    'success' => false,
                    'message' => 'No files uploaded',
                ], 422);
            }

            $files = is_array($files) ? $files : [$files];

            // ← THÊM: Lấy user_id (giả sử auth ok)
            $userId = auth()->id();

            foreach ($files as $file) {
                Log::debug('Checking file validity:', [
                    'file' => $file->getClientOriginalName(),
                    'valid' => $file->isValid(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'path' => $file->getPathname()
                ]);

                if ($file->isValid()) {
                    Log::debug('Processing file:', ['file' => $file->getClientOriginalName()]);

                    // Pass context default 'post' (có thể override từ request nếu cần, e.g., $request->input('context'))
                    $context = $request->input('context', 'post');  // ← THÊM: Linh hoạt context từ request

                    $result = $this->imageService->processImage($file, [
                        'context' => $context,
                        'alt' => $file->getClientOriginalName(),
                    ], null, 'thumbnail', $userId);  // ← SỬA: Pass $userId

                    // ← SỬA: Lấy preview từ first size (e.g., 'list'), fallback full_url nếu null/rỗng
                    $previewUrl = null;
                    if (is_array($result['sizes']) && !empty($result['sizes'])) {
                        $previewUrl = reset($result['sizes']);  // First key (e.g., 'list')
                    } else {
                        $previewUrl = $result['full_url'] ?? null;  // Fallback full nếu không có sizes
                    }

                    if (!$previewUrl) {
                        Log::warning('No preview URL available for file', ['file' => $file->getClientOriginalName()]);
                        continue;  // Skip nếu không có URL
                    }

                    $uploadedFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'full_url' => $result['full_url'],
                        'full_url_webp' => $result['full_url'],  // Alias, vì giờ chỉ WebP
                        'preview_url' => $previewUrl,
                        'sizes' => $result['sizes'] ?? [],
                        'alt' => $result['alt'],
                        'width' => $result['width'],
                        'height' => $result['height'],
                    ];

                    Log::debug('File processed:', ['result' => $result]);
                } else {
                    Log::warning('Invalid file:', [
                        'file' => $file->getClientOriginalName(),
                        'error' => $file->getErrorMessage()
                    ]);
                }
            }

            if (empty($uploadedFiles)) {
                Log::error('No valid files processed');
                return response()->json([
                    'success' => false,
                    'message' => 'No valid files processed',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Image upload failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function revert(Request $request)
    {
        Log::debug('Revert request data:', ['request' => $request->getContent()]);

        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || (!isset($data['full_url']) && !isset($data['url'])) || !isset($data['sizes'])) {
                Log::error('Invalid revert request data');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or missing file data',
                ], 422);
            }

            $fullUrl = $data['full_url'] ?? $data['url'];
            $imageData = [
                'full_url' => $fullUrl,
                'sizes' => $data['sizes'],
            ];

            Log::info('Manual revert called by user', ['full_url' => $fullUrl]);

            // Chỉ xóa khi user explicitly yêu cầu (e.g., click remove trong FilePond)
            $this->imageService->deleteImage($imageData);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('File deletion failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function load(Request $request)
    {
        Log::debug('Load image request:', ['url' => $request->query('source')]);

        try {
            $url = $request->query('source');
            if (!$url) {
                Log::error('No image URL provided for load');
                return response()->json([
                    'success' => false,
                    'message' => 'No image URL provided',
                ], 422);
            }

            // Giả định ảnh đã tồn tại trong storage, trả về thông tin cho FilePond
            // ← SỬA: Để tương thích, trả 'sizes' rỗng hoặc parse nếu cần, nhưng giữ đơn giản
            $filename = basename($url);

            return response()->json([
                'success' => true,
                'file' => [
                    'name' => $filename,
                    'full_url' => $url,
                    'preview_url' => $url,
                    'sizes' => [],  // Rỗng cho load, vì không cần sizes ở đây
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Image load failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}