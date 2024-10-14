<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Empresa;
use App\Models\EmpresaRed;
use App\Models\SectorEconomico;
use App\Models\Ubicacion;
use App\Models\User;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function registerEmp(Request $request)
    {
        $empresa = new Empresa();
        $empresa->id_ubicacion = $request->ubicacion;
        $empresa->id_usuario = $request->usuario_id;
        if ($request->sector == '0') {
            // Si es un string, crear un nuevo sector en la base de datos
            $nuevoSector = SectorEconomico::create([
                'sector' => 'OTRO',
                'division' => $request->division,
            ]);

            // Asignar el ID del nuevo sector a la empresa
            $empresa->id_sector = $nuevoSector->id;
        } else {
            $empresa->id_sector = $request->sector;
        }

        $empresa->nombre_comercial = $request->companyName;

        // Obtener el número de empleados
        $numberOfEmployees = $request->input('numberOfEmployees');

        // Determinar el tamaño de la empresa
        $empresa->tamanio = $this->calcularTamano($numberOfEmployees);
        $empresa->descripcion = $request->description;
        $empresa->cantidad_empleados = $request->numberOfEmployees;
        $empresa->ruc = $request->ruc;
        $empresa->razon_s = $request->razon_s;
        $empresa->sitio = $request->sitio;
        $empresa->telefono = $request->telefono;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
    
            // Verificar si el archivo es válido
            if ($logo->isValid()) {
                $logoName = time() . '_' . $request->companyName. '.'. $request->usuario_id. '.' . $logo->getClientOriginalExtension(); // Renombrar el archivo de logo
                $logo->storeAs('images/logos', $logoName, 'public'); // Guardar el archivo en la carpeta 'public/images/logos'
                $empresa->logo = $request->url.'images/logos/' . $logoName; // Guardar la ruta en la base de datos
            } else {
                return response()->json(['message' => 'El archivo del logo no es válido.'], 422);
            }
        } else {
            // Si no se sube logo, asignar un logo por defecto
            $empresa->logo = $request->url.'images/logos/default-empresa.jpg';
        }
    
        // Guardar la empresa en la base de datos
        $empresa->save();

        // Guardar los enlaces sociales si existen
        $socialLinksData = json_decode($request->input('socialLinks'), true); // Asegurarse que esté decodificado
    if (!empty($socialLinksData)) {
        foreach ($socialLinksData as $linkData) {
            EmpresaRed::create([
                'nombre_red' => $linkData['platform'],
                'enlace' => $linkData['url'],
                'id_empresa' => $empresa->id_empresa,
            ]);
        }
    }

        if ($empresa->id_usuario) {
            $user = User::find($empresa->id_usuario);
            if ($user) {
                $user->first_login_at = now();
                $user->save();
            }
        }

        return response()->json(['message' => 'Empresa creada exitosamente', 'empresa' => $empresa], 201);
    }

    public function completo(Request $request)
    {
        $empresa = Empresa::find($request->id_empresa);
        if ($empresa && $empresa->id_usuario) {
            $user = User::find($empresa->id_usuario);
            if ($user) {
                $user->first_login_at = now();
                $user->save();
            }
        }
        return response()->json(['message' => 'Empresa actualizada exitosamente', 'empresa' => $empresa], 201);
    }

    public function getEmpresaByIdUser($idUser)
    {
        try {
            $empresa = Empresa::with([
                'red' => function ($query) {
                    $query->select('id_empresa_red', 'nombre_red', 'enlace');
                },
                'sector' => function ($query) {
                    $query->select('id', 'sector', 'division');
                },
                'ubicacion' => function ($query) {
                    $query->select('id', 'provincia', 'canton');
                }
            ])->where('id_usuario', $idUser)->first();

            return response()->json($empresa);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Empresa no encontrada',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateEmpresaByIdUser(Request $request, $idUser)
    {
        try {
            $empresa = Empresa::where('id_usuario', $idUser)->first();

            if (!$empresa) {
                return response()->json([
                    'message' => 'Empresa no encontrada'
                ], 404);
            }

            // Actualizar los campos de la empresa
            $empresa->nombre_comercial = $request->input('nombre_comercial', $empresa->nombre_comercial);
            $empresa->descripcion = $request->input('descripcion', $empresa->descripcion);
            $empresa->cantidad_empleados = $request->input('cantidad_empleados', $empresa->cantidad_empleados);
            $empresa->tamanio = $this->calcularTamano($empresa->cantidad_empleados);

            $empresa->ruc = $request->input('ruc', $empresa->ruc);
            $empresa->razon_s = $request->input('razon_s', $empresa->razon_s);
            $empresa->sitio = $request->input('sitio', $empresa->sitio);
            $empresa->telefono = $request->input('telefono', $empresa->telefono);
            // Asumir que el frontend ya subió el logo a Firebase y solo recibe la URL
            if ($request->has('logo')) {
                $empresa->logo = $request->input('logo');
            }

            // Actualizar la red si está presente en el request
            if ($request->has('red')) {
                foreach ($request->input('red') as $redData) {
                    $red = $empresa->red()->where('id_empresa_red', $redData['id_empresa_red'])->first();
                    if ($red) {
                        $red->nombre_red = $redData['nombre_red'] ?? $red->nombre_red;
                        $red->enlace = $redData['enlace'] ?? $red->enlace;
                        $red->save();
                    }
                }
            }

            // Actualizar el sector si está presente en el request
            if ($request->has('division')) {
                $sector = SectorEconomico::find($request->input('division'));
                if ($sector) {
                    $empresa->id_sector = $sector->id;
                }
                // Guardar los cambios de la empresa
            }
            if ($request->has('customDivision') && !empty($request->input('customDivision'))) {
                // Crear un nuevo sector si se proporciona un customDivision
                $nuevoSector = SectorEconomico::create([
                    'sector' => 'OTRO', // Aquí puedes cambiar 'OTRO' según sea necesario
                    'division' => $request->input('customDivision'),
                ]);
                // Asignar el nuevo sector a la empresa
                $empresa->id_sector = $nuevoSector->id;
                // Guardar los cambios de la empresa
            }



            // Actualizar la ubicación si está presente en el request
            if ($request->has('canton')) {
                $ubicacion = Ubicacion::find($request->input('canton'));
                if ($ubicacion) {
                    $empresa->id_ubicacion = $ubicacion->id;
                }
            }

            $empresa->save();

            return response()->json([
                'message' => 'Empresa actualizada correctamente',
                'empresa' => $empresa
            ]);
        } catch (\Throwable $th) {
            Log::error('Error al actualizar la empresa: ' . $th->getMessage());
            return response()->json([
                'message' => 'Error al actualizar la empresa',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    private function calcularTamano($numberOfEmployees)
    {
        if ($numberOfEmployees >= 1 && $numberOfEmployees <= 9) {
            return 'Microempresa';
        } elseif ($numberOfEmployees >= 10 && $numberOfEmployees <= 49) {
            return 'Pequeña';
        } elseif ($numberOfEmployees >= 50 && $numberOfEmployees <= 199) {
            return 'Mediana';
        } elseif ($numberOfEmployees >= 200) {
            return 'Gran empresa';
        } else {
            return 'No definido';
        }
    }
    public function getEmpresaByName(Request $request)
    {
        try {
            $nombreComercial = $request->input('nombre_comercial');

            // Realizar la búsqueda con el comodín correcto
            $empresa = Empresa::where('nombre_comercial', 'like', $nombreComercial . '%')->get();

            // Verificar si no se encontraron empresas
            if ($empresa->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontró a la empresa',
                ], 404);
            }

            // Devolver las empresas encontradas
            return response()->json($empresa);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al buscar la empresa',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function getEmpresaById($id_empresa)
    {
        try {
            // Obtén los datos de la empresa junto con sus relaciones
            $empresa = Empresa::with(['usuario', 'ubicacion', 'sector', 'ofertas', 'red'])->find($id_empresa);

            if (!$empresa) {
                return response()->json(['message' => 'Empresa no encontrada'], 404);
            }

            return response()->json($empresa);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener la empresa', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateLogo(Request $request, $id_empresa)
    {
        try {
            // Encuentra la empresa por su ID
            $empresa = Empresa::find($id_empresa);

            if (!$empresa) {
                return response()->json(['message' => 'Empresa no encontrada'], 404);
            }

            // Validar la solicitud para asegurarse de que el logo está presente
            $request->validate([
                'logo' => 'required|image', // Asegúrate de que sea una imagen
            ]);


            $previousPhotoPath = $empresa->logo && $empresa->logo !== 'images/logos/default-empresa.jpg'
            ? public_path($empresa->logo)
            : null;

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
    
                // Verifica que el archivo sea una imagen
                if ($image->isValid()) {
                    // Obtener el nombre de la imagen desde el postulante
                    $imageName = basename($previousPhotoPath); // Extraer solo el nombre del archivo
    
                    // Si hay una foto previa, eliminarla
                    if (!$previousPhotoPath || $empresa->logo ===  $request->url .'images/logos/default-empresa.jpg') {
                        // Generar un nombre único para la nueva imagen
                        $imageName = time() . '_' . $request->image_name; // Puede usar `time()` o cualquier otra lógica para crear un nombre único
                    } else {
                        // Si hay una foto previa válida, extraer su nombre
                        $imageName = basename($previousPhotoPath);
                    }
    
                    // Guardar la nueva imagen usando el nombre del postulante
                    $image->storeAs('images/logos', $imageName, 'public');
    
                    // Actualizar la ruta de la imagen en el modelo
                    $empresa->logo = $request->url . 'images/logos/' . $imageName; // Guardar la nueva ruta
                } else {
                    return response()->json(['message' => 'El archivo no es una imagen válida.'], 422);
                }
            }
            $empresa->save();

            return response()->json(['message' => 'Logo actualizado correctamente', 'empresa' => $empresa], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el logo de la empresa: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar el logo', 'error' => $e->getMessage()], 500);
        }
    }

    public function getCantidadDest($usuario)
    {
        // Obtener el id de la empresa por el id del usuario
        $user = Empresa::getIdEmpresaPorIdUsuario($usuario);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Buscar la empresa utilizando el ID obtenido
        $empresa = Empresa::find($user); // Asegúrate de que el campo correcto es 'id_empresa'

        if (!$empresa) {
            return response()->json(['error' => 'Empresa no encontrada'], 404);
        }

        // Obtener el campo deseado de la empresa
        $cantidadDest = $empresa->cantidad_dest; // Reemplaza 'campo' por el nombre del campo que necesitas
        $plan = $empresa->plan; // Campo plan

        return response()->json(['cantidad_dest' => $cantidadDest, 'plan' => $plan], 200);
    }
}
