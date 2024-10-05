<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\configuracion;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configuraciones = Configuracion::all();
        return response()->json($configuraciones);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'dias_max_edicion' => 'required|integer',
            'dias_max_eliminacion' => 'required|integer',
            'valor_prioridad_alta' => 'required|integer',
            'valor_prioridad_media' => 'required|integer',
            'valor_prioridad_baja' => 'required|integer',
            'terminos_condiciones' => 'nullable|string',
            'gratis_ofer'=> 'required|integer',
            'gratis_d'=> 'required|integer',
            'estandar_ofer'=> 'required|integer',
            'estandar_d'=> 'required|integer',
            'premium_ofer'=> 'required|integer',
            'premiun_d'=> 'required|integer',
            'u_ofer'=> 'required|integer',
            'u_d'=> 'required|integer',
            
        ]);

        // Verificar si ya existe alguna configuración en la base de datos
        $existingConfigurations = Configuracion::count();

        // Si no hay configuraciones, establecer vigencia como true, de lo contrario false
        $validatedData['vigencia'] = $existingConfigurations === 0;

        $configuracion = Configuracion::create($validatedData);

        return response()->json(['message' => 'Configuración guardada correctamente', 'configuracion' => $configuracion], 201);
    }

    public function activate(Request $request, $id)
    {
        // Desactivar todas las configuraciones
        Configuracion::where('vigencia', true)->update(['vigencia' => false]);

        // Activar la configuración específica
        $configuracion = Configuracion::findOrFail($id);
        $configuracion->vigencia = true;
        $configuracion->save();

        return response()->json(['message' => 'Configuración activada correctamente', 'configuracion' => $configuracion], 200);
    }

    public function getActiveConfiguration()
    {
        $configuracion = Configuracion::where('vigencia', true)->first();
        if ($configuracion) {
            return response()->json($configuracion, 200);
        } else {
            return response()->json(['message' => 'No active configuration found'], 404);
        }
    }

    public function edit()
    {
        $configuracion = Configuracion::first();
        return view('admin.configuracion.edit', compact('configuracion'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'dias_max_edicion' => 'required|integer',
            'dias_max_eliminacion' => 'required|integer',
            'valor_prioridad_alta' => 'required|integer',
            'valor_prioridad_media' => 'required|integer',
            'valor_prioridad_baja' => 'required|integer',
            'terminos_condiciones' => 'required|string',
        ]);

        $configuracion = Configuracion::first();
        $configuracion->update($request->all());

        return redirect()->route('configuracion.edit')->with('success', 'Configuración actualizada correctamente.');
    }
}
