<?php

namespace App\Http\Controllers;

use App\Models\Titulo;
use Illuminate\Http\Request;

class TituloController extends Controller
{
      // Método para obtener Titulos Niveles Y Campos
      public function getTitulosNivelesCampos()
      {
          $nivelEducacion = Titulo::distinct()->pluck('nivel_educacion');
          $campoAmplio = TItulo::distinct()->pluck('campo_amplio');
          $titulo = TItulo::select()->pluck('titulo');
          
          return response()->json([
              'nivel' => $nivelEducacion,
              'campo' => $campoAmplio,
              'titulo' => $titulo,
          ]);
      }

       // Método para obtener campos por nivel
      public function getCamposNivel($nivel)
    {
        $campos = Titulo::where('nivel_educacion', $nivel)->distinct()->pluck('campo_amplio');
        
        return response()->json($campos);
    }

    public function getTitulosCamposNivel($nivel,$campo)
    {
        $query = Titulo::where('nivel_educacion', $nivel);

        // Si el campo no es "todos", aplicar el filtro por campo amplio
        if ($campo !== 'todos') {
            $query->where('campo_amplio', $campo);
        }
    
        $titulos = $query->select('id', 'titulo')
                         ->distinct()
                         ->get();
    
        return response()->json($titulos);
    }

   
}
