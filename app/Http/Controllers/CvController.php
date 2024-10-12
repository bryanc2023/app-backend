<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use ZipArchive;
class CvController extends Controller
{
    public function showCV($userId)
    {
       // Obtén el postulante por el id_usuario
    $postulante = Postulante::where('id_usuario', $userId)->first();

    if (!$postulante || !$postulante->cv) {
        return response()->json(['message' => 'CV no encontrado.'], 404);
    }

    $pathToFile = storage_path('app/public/' . $postulante->cv);

    if (!file_exists($pathToFile)) {
        return response()->json(['message' => 'Archivo no encontrado.'], 404);
    }

    return response($pathToFile)
        ->header('Content-Type',  'application/pdf')
        ->header('Content-Disposition', 'inline; filename="' . basename($pathToFile) . '"')
        ->header('Access-Control-Allow-Origin', '*');
    }


 
    public function downloadCV($userId)
    {
        // Obtener el postulante por el id_usuario
        $postulante = Postulante::where('id_usuario', $userId)->first();
    
        if (!$postulante || !$postulante->cv) {
            return response()->json(['message' => 'CV no encontrado.'], 404);
        }
    
        // Obtener la ruta del archivo
        $pathToFile = storage_path('app/public/' . $postulante->cv);
    
        if (!file_exists($pathToFile)) {
            return response()->json(['message' => 'Archivo no encontrado.'], 404);
        }
    
        // Crear la respuesta de descarga
        $response = response()->download($pathToFile, basename($postulante->cv));
    
        // Agregar encabezados para CORS
        $response->headers->set('Access-Control-Allow-Origin', '*'); // Permitir todas las orígenes
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS'); // Métodos permitidos
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization'); // Encabezados permitidos
    
        return $response;
    }
    public function downloadCVs(Request $request)
{
    $postulantes = $request->input('postulantes'); // Array de postulantes con sus URLs
    $zipFileName = 'CVs.zip';
    $zipPath = storage_path($zipFileName);

    $zip = new ZipArchive();

    // Crear el archivo ZIP
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return response()->json(['message' => 'No se pudo crear el archivo ZIP.'], 500);
    }

    foreach ($postulantes as $postulante) {
        if (!empty($postulante['cv'])) {
            // Extraer el nombre del archivo de la URL
            $fileName = basename($postulante['cv']);
            $filePath = public_path('storage/cv/' . $fileName); // Ruta del archivo en el servidor

            if (file_exists($filePath)) {
                // Agregar el archivo al ZIP
                if (!$zip->addFile($filePath, $fileName)) {
                    return response()->json(['message' => "Error al agregar el archivo {$fileName}."], 500);
                }
            } else {
                return response()->json(['message' => "El archivo {$fileName} no existe."], 404);
            }
        }
    }

    $zip->close();

    // Descargar el ZIP
    return response()->download($zipPath)->deleteFileAfterSend(true);
}

    
    
    
public function show($filename)
{
    $pathToFile = public_path('storage/cv' . $filename); // Asegúrate de que la ruta sea correcta

    if (!file_exists($pathToFile)) {
        return response()->json(['message' => 'File not found.'], 404);
    }

    $fileContents = file_get_contents($pathToFile);

    return response($fileContents)
        ->header('Content-Type', mime_content_type($pathToFile))
        ->header('Content-Disposition', 'inline; filename="' . basename($pathToFile) . '"')
        ->header('Access-Control-Allow-Origin', '*') // Permitir todas las orígenes
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS') // Permitir métodos
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization'); // Permitir encabezados específicos
}
    

    

    
}
