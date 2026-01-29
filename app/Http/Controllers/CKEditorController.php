<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        // 🔒 SECURITY: Validasi file wajib gambar
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072', // Max 3MB
        ]);

        if ($request->hasFile('upload')) {
            try {
                $path = \App\Helpers\ImageHelper::uploadAndOptimize($request->file('upload'), 'ckeditor', 1000);
                $url = asset('storage/' . $path);

                return response()->json([
                    'uploaded' => 1,
                    'fileName' => basename($path),
                    'url' => $url,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'uploaded' => 0,
                    'error' => [
                        'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
                    ]
                ]);
            }
        }
    }
}
