<?php

namespace App\Http\Controllers;

use App\Models\AreaTrabajo;
use App\Models\Criterio;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use Carbon\Carbon; ;
use App\Models\Oferta;
use Illuminate\Support\Facades\DB;
use App\Models\Postulacion;
use App\Models\Postulante;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\Log; 

class EmpresaGestoraController extends Controller
{
 
    
    public function getPostulantes(Request $request)
{
    $startDate = $request->query('startDate') ? Carbon::parse($request->query('startDate'))->startOfDay() : null;
    $endDate = $request->query('endDate') ? Carbon::parse($request->query('endDate'))->endOfDay() : null;
    $genero = $request->query('genero');
    $estadoCivil = $request->query('estadoCivil');
    $provincia = $request->query('provincia');
    $canton = $request->query('canton');

    $query = DB::table('users')
        ->join('postulante', 'users.id', '=', 'postulante.id_usuario')
        ->join('ubicacion', 'postulante.id_ubicacion', '=', 'ubicacion.id')
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.created_at',
            'postulante.id_postulante',
            'postulante.vigencia',
            'postulante.genero',
            'postulante.estado_civil',
            DB::raw("CONCAT(ubicacion.provincia, ', ', ubicacion.canton) as ubicacion")
        );

    if ($startDate) {
        $query->where('users.created_at', '>=', $startDate);
    }

    if ($endDate) {
        $query->where('users.created_at', '<=', $endDate);
    }

    if ($genero) {
        $query->where('postulante.genero', '=', $genero);
    }

    if ($estadoCivil) {
        $query->where('postulante.estado_civil', '=', $estadoCivil);
    }

    if ($provincia) {
        $query->where('ubicacion.provincia', '=', $provincia);
    }

    if ($canton) {
        $query->where('ubicacion.canton', '=', $canton);
    }

    $users = $query->get();

    $result = $users->map(function ($user) {
        $postulaciones = DB::table('postulacion')
            ->join('oferta', 'postulacion.id_oferta', '=', 'oferta.id_oferta')
            ->where('postulacion.id_postulante', '=', $user->id_postulante)
            ->select('oferta.cargo')
            ->get();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d'),
            'num_postulaciones' => $postulaciones->count(),
            'detalles_postulaciones' => $postulaciones,
            'vigencia' => $user->vigencia ? 'Activo' : 'Inactivo',
            'genero' => $user->genero,
            'estado_civil' => $user->estado_civil,
            'ubicacion' => $user->ubicacion,
        ];
    });

    return response()->json($result);
}


    


public function getEmpresas(Request $request)
{
    $startDate = $request->query('startDate') ? Carbon::parse($request->query('startDate'))->startOfDay() : null;
    $endDate = $request->query('endDate') ? Carbon::parse($request->query('endDate'))->endOfDay() : null;
    $provincia = $request->query('provincia');
    $canton = $request->query('canton');
    $sector = $request->query('sector');
    $tamanio = $request->query('tamanio');

    $query = Empresa::with(['usuario', 'ubicacion', 'sector', 'ofertas']);

    if ($provincia) {
        $query->whereHas('ubicacion', function($q) use ($provincia) {
            $q->where('provincia', $provincia);
        });
    }

    if ($canton) {
        $query->whereHas('ubicacion', function($q) use ($canton) {
            $q->where('canton', $canton);
        });
    }

    if ($sector) {
        $query->whereHas('sector', function($q) use ($sector) {
            $q->where('sector', $sector);
        });
    }

    if ($tamanio) {
        $query->where('tamanio', $tamanio);
    }

    if ($startDate) {
        $query->whereHas('usuario', function ($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate);
        });
    }

    if ($endDate) {
        $query->whereHas('usuario', function ($q) use ($endDate) {
            $q->where('created_at', '<=', $endDate);
        });
    }

    $empresas = $query->get()->map(function ($empresa) {
        $totalOfertas = $empresa->ofertas->count(); 

        return [
            'id' => $empresa->id_empresa,
            'nombre_comercial' => $empresa->nombre_comercial,  
            'tamanio' => $empresa->tamanio,                    
            'ubicacion' => $empresa->ubicacion->provincia . ', ' . $empresa->ubicacion->canton,  
            'sector' => $empresa->sector ? $empresa->sector->sector : 'N/A', 
            'total_ofertas' => $totalOfertas,    
        ];
    });

    return response()->json($empresas);
}


    




    public function getOfertasPorMes(Request $request)
    {
        $year = $request->query('year', date('Y'));
    
        $ofertasPorMes = Oferta::select(
                DB::raw('YEAR(fecha_publi) as year'),
                DB::raw('MONTH(fecha_publi) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('fecha_publi', $year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    
        return response()->json($ofertasPorMes);
    }

    public function getUsuariosRegistradosPorMes(Request $request)
    {
        // Consulta para obtener el conteo de usuarios agrupados por mes y año
        $year = $request->query('year', date('Y'));

        $usuariosPorMes = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json($usuariosPorMes);
    }

    public function getPostulacionesPorMes(Request $request)
    {
        $year = $request->query('year', date('Y'));
    
        $postulaciones = DB::table('postulacion')
            ->select(
                DB::raw('YEAR(fecha_postulacion) as year'),
                DB::raw('MONTH(fecha_postulacion) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('fecha_postulacion', $year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    
        return response()->json($postulaciones);
    }

    // Controlador para obtener las áreas
public function getAreas()
{
    $areas = AreaTrabajo::all();
    return response()->json($areas);
}

// Controlador para obtener las ubicaciones
public function getUbicaciones()
{
    $ubicaciones = Ubicacion::all();
    return response()->json($ubicaciones);
}

// Controlador para obtener los postulantes por ubicación
public function getPostulantesPorUbicacion(Request $request)
{
   
        $ubicacionId = $request->input('ubicacion');
    
        $postulaciones = Postulacion::join('postulante', 'postulacion.id_postulante', '=', 'postulante.id_postulante')
            ->where('postulante.id_ubicacion', $ubicacionId)
            ->count();
    
        $ofertas = Oferta::join('empresa', 'oferta.id_empresa', '=', 'empresa.id_empresa')
            ->where('empresa.id_ubicacion', $ubicacionId)
            ->count();
    
        return response()->json([
            'postulaciones' => $postulaciones,
            'ofertas' => $ofertas,
        ]);
    
}

// Controlador para obtener los postulantes por área
public function getPostulantesPorArea(Request $request)
{
    $areaId = $request->input('area');

    $postulaciones = Postulacion::join('oferta', 'postulacion.id_oferta', '=', 'oferta.id_oferta')
        ->where('oferta.id_area', $areaId)
        ->count();

    $ofertas = Oferta::where('id_area', $areaId)->count();

    return response()->json([
        'postulaciones' => $postulaciones,
        'ofertas' => $ofertas,
    ]);
}

// Controlador para obtener los postulantes por género
public function getPostulantesPorGenero(Request $request)
{
    $query = Postulante::select(
            DB::raw('SUM(CASE WHEN genero = "Masculino" THEN 1 ELSE 0 END) as masculino'),
            DB::raw('SUM(CASE WHEN genero = "Femenino" THEN 1 ELSE 0 END) as femenino'),
            DB::raw('SUM(CASE WHEN genero NOT IN ("Masculino", "Femenino") THEN 1 ELSE 0 END) as otro')
        );

    if ($request->has('area') && $request->input('area') != '') {
        $query->join('postulacion', 'postulante.id_postulante', '=', 'postulacion.id_postulante')
              ->join('oferta', 'postulacion.id_oferta', '=', 'oferta.id_oferta')
              ->where('oferta.id_area', $request->input('area'));
    }


 
    $data = $query->first();

    return response()->json($data);
}

public function getOfertas(Request $request)
{
    $startDate = $request->query('startDate') ? Carbon::parse($request->query('startDate'))->startOfDay() : null;
    $endDate = $request->query('endDate') ? Carbon::parse($request->query('endDate'))->endOfDay() : null;
    $cargo = $request->query('cargo');
    $experiencia = $request->query('experiencia');
    $cargaHoraria = $request->query('carga_horaria');
    $modalidad = $request->query('modalidad');
    $estado = $request->query('estado');

    // Log de los parámetros recibidos
    Log::info('Filtros recibidos', [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'cargo' => $cargo,
        'experiencia' => $experiencia,
        'carga_horaria' => $cargaHoraria,
        'modalidad' => $modalidad,
        'estado' => $estado,
    ]);

        $query = DB::table('oferta')
        ->join('empresa', 'oferta.id_empresa', '=', 'empresa.id_empresa')
        ->leftJoin('postulacion', 'oferta.id_oferta', '=', 'postulacion.id_oferta')
        ->select(
            'oferta.id_oferta',
            'oferta.cargo',
            'oferta.sueldo',
            'oferta.objetivo_cargo',
            'empresa.nombre_comercial',
            'oferta.experiencia',
            'oferta.funciones',
            'oferta.carga_horaria',
            'oferta.modalidad',
            'oferta.estado',
            DB::raw('COUNT(postulacion.id_oferta) as num_postulaciones')  // Contamos sobre id_oferta
        )
        ->groupBy(
            'oferta.id_oferta',
            'oferta.cargo',
            'oferta.sueldo',
            'oferta.objetivo_cargo',
            'empresa.nombre_comercial',
            'oferta.experiencia',
            'oferta.funciones',
            'oferta.carga_horaria',
            'oferta.modalidad',
            'oferta.estado'
        );

    if ($startDate) {
        $query->where('oferta.fecha_publi', '>=', $startDate);
    }

    if ($endDate) {
        $query->where('oferta.fecha_publi', '<=', $endDate);
    }

    if ($cargo) {
        $query->where('oferta.cargo', 'like', "%$cargo%");
    }

    if ($experiencia !== null) {
        $query->where('oferta.experiencia', '=', $experiencia);
    }

    if ($cargaHoraria) {
        $query->where('oferta.carga_horaria', '=', $cargaHoraria);
    }

    if ($modalidad) {
        $query->where('oferta.modalidad', '=', $modalidad);
    }

    if ($estado) {
        $query->where('oferta.estado', '=', $estado);
    }

    $ofertas = $query->get();

    // Log de la consulta generada
    Log::info('Consulta generada', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);

    return response()->json($ofertas);
    }

    public function update(Request $request, $id)
    {
        $criterio = Criterio::findOrFail($id);
        $criterio->criterio = $request->input('criterio');
        $criterio->descripcion = $request->input('descripcion');
        $criterio->save();
    
        return response()->json(['message' => 'Criterio actualizado correctamente'], 200);
    }

    public function toggleVigencia(Request $request, $id)
{
    $criterio = Criterio::findOrFail($id);
    $criterio->vigencia = $request->input('vigencia');
    $criterio->save();

    return response()->json(['message' => 'Vigencia del criterio actualizada correctamente'], 200);
}

public function updateRole(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'usuario_id' => 'required|integer|exists:users,id',
        ]);

        try {
            // Buscar el usuario por su ID
            $user = User::find($request->input('usuario_id'));

            // Actualizar el role_id del usuario a 0
            $user->role_id = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'El role_id del usuario ha sido actualizado a 0.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el role_id del usuario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getEmpresaByRoleId()
{
    
        // Obtén la empresa donde role_id sea 4
        $empresa = User::where('role_id', 4)->first();

        if (!$empresa) {
            return response()->json(['error' => 'Empresa no encontrada'], 404);
        }

        // Devuelve solo el id de la empresa
    return response()->json(['id' => $empresa->id], 200);
    
}

public function getPGestora()
{
        // Obtén todos los usuarios donde role_id sea 5
        $usuarios = User::where('role_id', 5)->get();

        // Verifica si hay usuarios
        if ($usuarios->isEmpty()) {
            return response()->json(['error' => 'No se encontraron usuarios con este rol'], 404);
        }

        // Devuelve los usuarios en formato JSON
        return response()->json($usuarios, 200);
    }
    
}