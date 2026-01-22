<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload ảnh mới và trả về đường dẫn relative. Nếu có oldPath, xóa ảnh cũ trước.
     *
     * @param UploadedFile $file
     * @param string|null $oldPath
     * @return string|null
     */
    public function upload(UploadedFile $file, ?string $oldPath = null): ?string
    {
        // Xóa ảnh cũ nếu tồn tại và valid
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Generate đường dẫn động dựa trên ngày hiện tại
        $datePath = now()->format('Y/m/d');
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $fullPath = "uploads/{$datePath}/{$filename}";

        // Lưu file vào storage/public
        $file->storeAs("uploads/{$datePath}", $filename, 'public');

        return $fullPath; // Trả về relative path để lưu vào DB
    }
}