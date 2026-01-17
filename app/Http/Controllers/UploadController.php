<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function uploadTemp(Request $request)
    {
        \Log::info('Upload Temp Request:', [
            'files' => $request->allFiles(),
            'input' => $request->all(),
        ]);

        $files = $request->file('image-upload');
        if (!$files) {
            return response()->json(['error' => 'No file uploaded or incorrect field name'], 400);
        }

        $file = is_array($files) ? $files[0] : $files;

        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file uploaded'], 400);
        }

        try {
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp', $filename);
            return response()->json(['folder' => $filename]);
        } catch (\Exception $e) {
            \Log::error('Upload Error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to store file'], 500);
        }
    }

    public function deleteTemp(Request $request)
    {
        // Debug request để kiểm tra dữ liệu gửi lên
        \Log::info('Delete Temp Request:', [
            'body' => $request->getContent(),
            'input' => $request->all(),
        ]);

        // FilePond gửi serverId (folder) trực tiếp trong body
        $filename = $request->getContent();

        if (!$filename || !Storage::exists('temp/' . $filename)) {
            \Log::warning('File not found or invalid filename:', ['filename' => $filename]);
            return response()->json(['error' => 'File not found'], 404);
        }

        try {
            Storage::delete('temp/' . $filename);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Delete Error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete file'], 500);
        }
    }
}