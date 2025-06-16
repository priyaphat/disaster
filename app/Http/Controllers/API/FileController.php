<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function fileStorageServe($file_path)
    {
        // dd(config('filesystems.disks.local.root'));
        
        if (!Storage::disk('public')->exists($file_path)) {
            abort(404);
        }
        $local_path = storage_path('app/public/') . $file_path;
        return response()->file($local_path);
    }
}
