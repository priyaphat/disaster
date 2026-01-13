<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function servePrivate($fileName)
    {
        $fileName = 'image' . DIRECTORY_SEPARATOR . $fileName;
        if (!Storage::disk('local')->exists($fileName)) {
            abort(404);
        }
        $image = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR . $fileName;
        return response()->file($image);
    }

    public function download($fileName)
    {
        $fileName = 'image' . DIRECTORY_SEPARATOR . $fileName;
        if (!Storage::disk('local')->exists($fileName)) {
            abort(404);
        }
        $image = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR . $fileName;
        return response()->download($image);
    }
}
