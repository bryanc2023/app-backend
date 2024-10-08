<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Notifications\VerifyEmailCustom;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {


        $role = Role::where('name', 'postulante')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $role ? $role->id : null,
        ]);


        $user->sendEmailVerificationNotification();
        $token = JWTAuth::fromUser($user);



        return response()->json(compact('user', 'token'), 201);
    }

    public function registerEmpresa(RegisterRequest $request)
    {


        $role = Role::where('name', 'empresa_oferente')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $role ? $role->id : null,
        ]);
        $user->sendEmailVerificationNotification();
        $token = JWTAuth::fromUser($user);



        return response()->json(compact('user', 'token'), 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Intentar autenticar al usuario con las credenciales proporcionadas
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        // Obtener al usuario autenticado
        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Verificar si el usuario está activo
        if (!$user->is_active) {
            return response()->json(['error' => 'Tu cuenta ha sido desactivada. Contacta al administrador.'], 403);
        }

        // Verificar si el email ha sido verificado
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Por favor, verifica tu correo electrónico primero'], 403);
        }

        // Inicializar la respuesta con los datos del usuario
        $response = [
            'user' => $user,
            'token' => $token,
            'role' => $user->role ? $user->role->name : 'Sin rol asignado',
        ];

        // Si el rol del usuario es 5, obtener también el usuario con ID 4
        if ($user->role && $user->role->id === 5) {
            $user4 = User::find(4);
            if ($user4) {
                $response['user4'] = $user4; // Añadir el usuario con ID 4 a la respuesta
            } else {
                // Si el usuario 4 no existe, puedes decidir qué hacer aquí, 
                // por ejemplo, no añadir nada o añadir un mensaje indicando que no se encontró.
                $response['user4'] = null; // o eliminar esta línea si no deseas incluirlo
            }
        }

        return response()->json($response, 200);
    }



    public function loginEmpresa(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales invalidas'], 401);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Por favor, verifica tu correo electrónico primero'], 403);
        }

        return response()->json(compact('user', 'token'), 201);
    }

    public function verify(Request $request)
    {
        $userId = $request->route('id');
        $user = User::findOrFail($userId);

        // Verificar si el hash proporcionado es válido
        $providedHash = $request->route('hash');
        $expectedHash = sha1($user->getEmailForVerification());
        if (!hash_equals((string) $providedHash, $expectedHash)) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }

        // Verificar si el correo electrónico ya ha sido verificado
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 401);
        }

        // Marcar el correo electrónico como verificado
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            return response()->json(['message' => 'Email successfully verified'], 200);
        }

        // En caso de error, devolver una respuesta adecuada
        return response()->json(['message' => 'Failed to verify email'], 500);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email resent'], 200);
    }

    /**
     * Verifica si el usuario ha completado su registro.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRegistrationStatus()
    {
        $user = Auth::user();

        // Verificar si el usuario ha verificado su email y si ha completado el primer login (registro completo)
        $isProfileComplete = $user->email_verified_at !== null && $user->first_login_at !== null;

        return response()->json([
            'profileCompleted' => $isProfileComplete,
        ]);
    }
}
