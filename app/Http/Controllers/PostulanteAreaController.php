<?php

namespace App\Http\Controllers;

use App\Models\PostulanteArea as ModelsPostulanteArea;
use Illuminate\Http\Request;
use Illuminate\Queue\NullQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

namespace App\Http\Controllers;

use App\Models\PostulanteArea; // Asegúrate de que este es el nombre correcto del modelo
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class PostulanteAreaController extends Controller
{
    public function deleteNoti(Request $request, $id_area, $id_postulante)
    {
        try {
            // Convertir los valores a números enteros
            $idArea = intval($id_area);
            $idPostulante = intval($id_postulante);

            // Eliminar el registro basado en la clave compuesta
            $deleted = PostulanteArea::where('id_area', $idArea)
                                     ->where('id_postulante', $idPostulante)
                                     ->delete();

            // Verificar si el registro fue eliminado
            if ($deleted === 0) {
                return response()->json(['message' => 'Notificación no encontrada o ya eliminada'], 404);
            }

            return response()->json(['message' => 'Notificación eliminada exitosamente'], 200);
        } catch (\Exception $e) {
            // Registro de errores
           
            return response()->json(['message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }
}

