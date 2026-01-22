<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditorImageService
{
    /**
     * Xử lý upload ảnh cho editor, lưu vào uploads/Y/m/d/random.ext
     * Trả về array data cho response (full_url, editor_image_id, etc.)
     *
     * @param UploadedFile $file
     * @param array $options (optional: watermark, quality)
     * @return array
     */
    public function processImage(UploadedFile $file, array $options = []): array
    {
        $datePath = now()->format('Y/m/d');
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $relativePath = "uploads/{$datePath}/{$filename}";

        // Lưu file
        $file->storeAs("uploads/{$datePath}", $filename, 'public');

        // Optional: Xử lý watermark/quality nếu có (dùng Intervention Image nếu install)
        // Ví dụ: if ($options['watermark']) { ... }

        // Generate data cho response
        $fullUrl = Storage::url($relativePath);
        $editorImageId = Str::random(10); // Hoặc save vào DB để track real ID nếu cần

        return [
            'full_url' => $fullUrl,
            'editor_image_id' => $editorImageId,
            'name' => $file->getClientOriginalName(),
            'alt' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'width' => getimagesize($file->getPathname())[0] ?? 0,
            'height' => getimagesize($file->getPathname())[1] ?? 0,
        ];
    }

    /**
     * Xóa ảnh dựa trên full_url (parse để lấy relative path)
     *
     * @param string $fullUrl
     * @return void
     */
    public function deleteImage(string $fullUrl): void
    {
        // Parse relative path từ full_url (bỏ /storage/)
        $relativePath = str_replace('/storage/', '', parse_url($fullUrl, PHP_URL_PATH));

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}