<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Notificaciones;

class NotificacionesController extends Controller
{
    public function construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            // Obtener al usuario autenticado
            $user = auth()->user();
            
            // Verificar si el rol del usuario autenticado es "p_empresa_g"
            if ($user->role->name === 'p_empresa_g') {
                // Buscar el primer usuario con rol 4 (empresa)
                $empresaUser = User::where('role_id', 4)->first();
                
                if ($empresaUser) {
                    // Obtener las notificaciones no leídas del usuario con rol 4
                    $notificaciones = $empresaUser->unreadNotifications;
                } else {
                    return response()->json(['message' => 'No hay empresa con rol 4'], 404);
                }
            } else {
                // Si no es "p_empresa_g", obtener las notificaciones del usuario autenticado
                $notificaciones = $user->unreadNotifications;
            }
    
            // Verificar si hay notificaciones
            if ($notificaciones->isEmpty()) {
                return response()->json(['message' => 'No hay notificaciones'], 404);
            }
    
            // Retornar las notificaciones
            return response()->json($notificaciones);

        } catch (\Throwable $th) {
            return response()->json(
                [
                    'message' => 'Error al obtener las notificaciones',
                    'error' => $th->getMessage()
                ],
                
                500
            );
        }
 
    }

    public function marcarLeida($id)
    {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        return response()->json(['message' => 'Notificación marcada como leída']);
    }

    public function marcarTodasLeidas()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
    }
    public function __construct()
    {
        $this->middleware('auth');
    }

    
}
