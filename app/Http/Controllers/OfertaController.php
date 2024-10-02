<?php

namespace App\Http\Controllers;

use App\Models\CriterioOferta;
use App\Models\EducacionRequerida;
use App\Models\Empresa;
use App\Models\Oferta;
use App\Models\pregunta;
use App\Models\Ubicacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OfertaController extends Controller
{
    public function registerOferta(Request $request)
    {

        // Validar los datos recibidos
        $validatedData = $request->validate([
            'cargo' => 'required|string|max:255',
            'id_area' => 'required|integer',
            'experiencia' => 'integer',
            'objetivo_cargo' => 'required|string|max:500',
            'sueldo' => 'nullable|numeric',
            'correo_contacto' => 'nullable|email|max:255',
            'numero_contacto' => 'nullable|string|max:20',
            'detalles_adicionales' => 'nullable|string',
            'mostrar_sueldo' => 'required|boolean',
            'mostrar_empresa' => 'required|boolean',
            'solicitar_sueldo' => 'required|boolean',
            'fecha_max_pos' => 'required|date',
            'funciones' => 'required|string',
            'modalidad' => 'required|string',
            'carga_horaria' => 'required|string',
            'titulos' => 'nullable|array',
            'titulos.*.id' => 'integer',
            'titulos.*.titulo' => 'string|max:500',
            'titulos.*.customTitulo' => 'nullable|string|max:600',
            'criterios' => 'nullable|array',
            'criterios.*.id_criterio' => 'integer',
            'criterios.*.criterio' => 'string|max:255',
            'criterios.*.descripcion' => 'string|max:255',
            'criterios.*.valor' => 'string|nullable|max:255',
            'criterios.*.prioridad' => 'integer|between:1,3',
            'usuario' => 'required|integer',
            'preguntas' => 'nullable|array',
            'preguntas.*' => 'string|max:400',
            'comisiones' => 'nullable|numeric',
            'horasExtras' => 'nullable|numeric',
            'viaticos'=> 'nullable|numeric',
            'comentariosComisiones' => 'string|nullable|max:800',
            'comentariosHorasExtras' => 'string|nullable|max:800',
            'comentariosViaticos' => 'string|nullable|max:800',
            'experienciaEnMeses' => 'boolean',
        ]);
        // Buscar el usuario por ID
        $user = Empresa::getIdEmpresaPorIdUsuario($validatedData['usuario']);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Asignar valores predeterminados si no están presentes
        $validatedData['sueldo'] = $validatedData['sueldo'] ?? 0;
        $validatedData['detalles_adicionales'] = $validatedData['detalles_adicionales'] ?? 'Ninguno';


        // Crear una nueva oferta
        $oferta = new Oferta();
        $oferta->cargo = $validatedData['cargo'];
        $oferta->id_area = $validatedData['id_area'];
        $oferta->id_empresa = $user;
        $oferta->experiencia = $validatedData['experiencia'];
        $oferta->objetivo_cargo = $validatedData['objetivo_cargo'];
        $oferta->sueldo = $validatedData['sueldo'];
        $oferta->correo_contacto = $validatedData['correo_contacto'];
        $oferta->numero_contacto = $validatedData['numero_contacto'];
        $oferta->detalles_adicionales = $validatedData['detalles_adicionales'];
        $oferta->n_mostrar_sueldo = $validatedData['mostrar_sueldo'];
        $oferta->n_mostrar_empresa = $validatedData['mostrar_empresa'];
        $oferta->soli_sueldo = $validatedData['solicitar_sueldo'];
        $oferta->fecha_publi = Carbon::now();
        $oferta->carga_horaria = $validatedData['carga_horaria'];
        $oferta->modalidad = $validatedData['modalidad'];
        $oferta->estado = "En espera";
        $oferta->fecha_max_pos = $validatedData['fecha_max_pos'];
        $oferta->funciones = $validatedData['funciones'];
        $oferta->comisiones = $validatedData['comisiones'];
        $oferta->horasExtras = $validatedData['horasExtras'];
        $oferta->viaticos = $validatedData['viaticos'];
        $oferta->comentariosComisiones = $validatedData['comentariosComisiones'];
        $oferta->comentariosHorasExtras = $validatedData['comentariosHorasExtras'];
        $oferta->comentariosViaticos = $validatedData['comentariosViaticos'];
        $oferta->exp_m = $validatedData['experienciaEnMeses'];
        $oferta->save();




        if (!empty($validatedData['titulos'])) {
            foreach ($validatedData['titulos'] as $titulo) {
                EducacionRequerida::create([
                    'id_oferta' => $oferta->id_oferta,
                    'id_titulo' => $titulo['id'],
                    'titulo_per2' => $titulo['customTitulo'],
                ]);
            }
        }
        if (!empty($validatedData['criterios'])) {
            foreach ($validatedData['criterios'] as $criterio) {
                CriterioOferta::create([
                    'id_criterio' => $criterio['id_criterio'],
                    'valor' => $criterio['valor'],
                    'prioridad' => $criterio['prioridad'],
                    'id_oferta' => $oferta->id_oferta,
                ]);
            }
        }

        // Guardar las preguntas si existen
        if (!empty($validatedData['preguntas'])) {
            foreach ($validatedData['preguntas'] as $preguntaTexto) {
                pregunta::create([
                    'id_oferta' => $oferta->id_oferta,
                    'pregunta' => $preguntaTexto,
                ]);
            }
        }


        return response()->json(['message' => 'Oferta creado exitosamente', 'oferta' => $oferta], 201);
    }

    public function updateOferta(Request $request, $id)
    {
        $oferta = Oferta::where('id_oferta', $id)->first();

        if (!$oferta) {
            return response()->json(['error' => 'Oferta no encontrada'], 404);
        }

        // Validar los datos recibidos
        $validatedData = $request->validate([
            'cargo' => 'required|string|max:255',
            'id_area' => 'required|integer',
            'experiencia' => 'integer',
            'objetivo_cargo' => 'required|string|max:500',
            'sueldo' => 'nullable|numeric',
            'correo_contacto' => 'nullable|email|max:255',
            'numero_contacto' => 'nullable|string|max:20',
            'detalles_adicionales' => 'nullable|string',
            'mostrar_sueldo' => 'nullable|boolean',
            'mostrar_empresa' => 'nullable|boolean',
            'solicitar_sueldo' => 'nullable|boolean',
            'fecha_max_pos' => 'required|date',
            'funciones' => 'required|string',
            'modalidad' => 'required|string',
            'carga_horaria' => 'required|string',
            'titulos' => 'nullable|array',
            'titulos.*.id' => 'integer',
            'titulos.*.titulo' => 'string|max:255',
            'titulos.*.customTitulo' => 'nullable|string|max:600',
            'criterios' => 'nullable|array',
            'criterios.*.id_criterio' => 'integer|exists:criterio,id_criterio',
            'criterios.*.valor' => 'string|nullable|max:255',
            'criterios.*.prioridad' => 'integer|between:1,3',
            'preguntas' => 'nullable|array',
           'preguntas.*' => 'string|max:400',
           'comisiones' => 'nullable|numeric',
            'horasExtras' => 'nullable|numeric',
            'viaticos'=> 'nullable|numeric',
            'comentariosComisiones' => 'string|nullable|max:800',
            'comentariosHorasExtras' => 'string|nullable|max:800',
            'comentariosViaticos' => 'string|nullable|max:800',
            'experienciaEnMeses' => 'boolean',
        ]);

        // Actualizar la oferta con los datos validados
        $oferta->update([
            'cargo' => $validatedData['cargo'],
            'id_area' => $validatedData['id_area'],
            'experiencia' => $validatedData['experiencia'],
            'objetivo_cargo' => $validatedData['objetivo_cargo'],
            'sueldo' => $validatedData['sueldo'] ?? 0,
            'correo_contacto' => $validatedData['correo_contacto'],
            'numero_contacto' => $validatedData['numero_contacto'],
            'detalles_adicionales' => $validatedData['detalles_adicionales'] ?? 'Ninguno',
            'n_mostrar_sueldo' => $validatedData['mostrar_sueldo'] ?? 0,
            'n_mostrar_empresa' => $validatedData['mostrar_empresa'] ?? 0,
            'soli_sueldo' => $validatedData['solicitar_sueldo'] ?? 0,
            'fecha_max_pos' => $validatedData['fecha_max_pos'],
            'funciones' => $validatedData['funciones'],
            'modalidad' => $validatedData['modalidad'],
            'carga_horaria' => $validatedData['carga_horaria'],
            'comisiones'=> $validatedData['comisiones'],
            'horasExtras'=> $validatedData['horasExtras'],
            'viaticos'=> $validatedData['viaticos'],
            'comentariosComisiones' => $validatedData['comentariosComisiones'],
            'comentariosHorasExtras' => $validatedData['comentariosHorasExtras'],
            'comentariosViaticos' => $validatedData['comentariosViaticos'],
            'exp_m' =>  $validatedData['experienciaEnMeses'],
        ]);

        // Actualizar las relaciones (titulos y criterios) si se proporcionan
        if ($request->has('titulos')) {
            // Sincronizar los títulos con la tabla `educacion_requerida`
            $oferta->expe()->sync(array_map(function ($titulo) {
                return ['id_titulo' => $titulo['id'],'titulo_per2' => $titulo['customTitulo']];
            }, $request->titulos));
        }

        if ($request->has('criterios')) {
            // Eliminar los criterios existentes
            CriterioOferta::where('id_oferta', $oferta->id_oferta)->delete();

            // Insertar los nuevos criterios
            foreach ($request->criterios as $criterio) {
                CriterioOferta::create([
                    'id_criterio' => $criterio['id_criterio'],
                    'valor' => $criterio['valor'],
                    'prioridad' => $criterio['prioridad'],
                    'id_oferta' => $oferta->id_oferta,
                ]);
            }
        }

  // Actualizar las preguntas
  if ($request->has('preguntas')) {
    $preguntasActuales = Pregunta::where('id_oferta', $oferta->id_oferta)->get();

    $nuevasPreguntas = array_map('trim', $validatedData['preguntas']);

    // Eliminar preguntas que ya no están en la lista nueva
    foreach ($preguntasActuales as $preguntaActual) {
        if (!in_array($preguntaActual->pregunta, $nuevasPreguntas)) {
            $preguntaActual->delete();
        } else {
            // Actualizar las preguntas existentes
            $preguntaActual->update(['pregunta' => $preguntaActual->pregunta]);
            $nuevasPreguntas = array_diff($nuevasPreguntas, [$preguntaActual->pregunta]);
        }
    }

    // Añadir las nuevas preguntas
    foreach ($nuevasPreguntas as $preguntaTexto) {
        Pregunta::create([
            'id_oferta' => $oferta->id_oferta,
            'pregunta' => $preguntaTexto,
        ]);
    }
} else {
    // Si no se envían preguntas, eliminarlas todas
    Pregunta::where('id_oferta', $oferta->id_oferta)->delete();
}


        return response()->json(['message' => 'Oferta actualizada exitosamente']);
    }

    public function deleteOferta($id)
    {
        $oferta = Oferta::with(['criterios', 'expe'])->find($id);

        if (!$oferta) {
            return response()->json(['error' => 'Oferta no encontrada'], 404);
        }

        // Eliminar los criterios y títulos asociados
        $oferta->criterios()->detach();
        $oferta->expe()->detach();

        // Eliminar la oferta
        $oferta->delete();

        return response()->json(['message' => 'Oferta eliminada exitosamente'], 200);
    }



    public function getOfertasByEmpresa($idEmpresa, Request $request)
    {
        $user = Empresa::getIdEmpresaPorIdUsuario($idEmpresa);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $query = Oferta::where('id_empresa', $user)
            ->with(['areas', 'criterios', 'expe','preguntas']);

        if ($request->has('cargo') && !empty($request->input('cargo'))) {
            $cargo = $request->input('cargo');
            $query->where('cargo', $cargo);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin') && !empty($request->input('fecha_inicio')) && !empty($request->input('fecha_fin'))) {
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            $query->whereBetween('fecha_publi', [$fechaInicio, $fechaFin]);
        }

        if ($request->has('estado') && !empty($request->input('estado'))) {
            $estado = $request->input('estado');
            $query->where('estado', $estado);
        }

        if ($request->has('carga_horaria') && !empty($request->input('carga_horaria'))) {
            $cargaHoraria = $request->input('carga_horaria');
            $query->where('carga_horaria', $cargaHoraria);
        }

        if ($request->has('area') && !empty($request->input('area'))) {
            $area = $request->input('area');
            $query->where('id_area', $area);
        }

        $ofertas = $query->get();

        return response()->json(['ofertas' => $ofertas]);
    }


    public function getOfertaById($id)
    {
        $oferta = Oferta::where('id_oferta', $id)
            ->with(['areas', 'criterios', 'expe','preguntas'])
            ->first();

        if (!$oferta) {
            return response()->json(['error' => 'Oferta no encontrada'], 404);
        }

        return response()->json($oferta);
    }

    public function getAllOfertas()
    {
        $ofertas = Oferta::with(['areas', 'criterios', 'empresa.ubicacion', 'expe', 'preguntas'])
            ->where('estado', 'En espera')
            ->get();

        return response()->json(['ofertas' => $ofertas]);
    }

    public function getOfertasInicio()
    {
        $ofertas = Oferta::with(['areas', 'criterios', 'empresa.ubicacion', 'expe', 'preguntas'])
            ->where('estado', 'En espera')
            ->orderBy('fecha_publi', 'desc')  // Ordena por la fecha de creación de forma descendente
            ->take(3)  // Limita a 3 ofertas
            ->get();

        return response()->json(['ofertas' => $ofertas]);
    }

    // En tu controlador de ofertas

public function actualizarEstadoOfertas()
{
    // Obtén la fecha actual
    $fechaActual = now(); // o puedes usar Carbon::now();

    // Busca las ofertas cuya fecha máxima de postulación sea menor que hoy
    $ofertasInactivas = Oferta::where('fecha_max_pos', '<', $fechaActual)
        ->update(['estado' => 'Inactiva']);

    return response()->json(['mensaje' => 'Estado de ofertas actualizadas', 'ofertas_inactivas' => $ofertasInactivas]);
}

}
