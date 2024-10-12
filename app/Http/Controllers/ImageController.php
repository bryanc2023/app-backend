<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function show($filename)
    {
        $pathToFile = public_path('storage/images/postulantes' . $filename);

    if (!file_exists($pathToFile)) {
        return response()->json(['message' => 'Image not found.'], 404);
    }

    $fileContents = file_get_contents($pathToFile);

    return response($fileContents)
        ->header('Content-Type', mime_content_type($pathToFile))
        ->header('Content-Disposition', 'inline; filename="' . basename($pathToFile) . '"')
        ->header('Access-Control-Allow-Origin', '*');
    }
}
