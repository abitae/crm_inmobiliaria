<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Inicio de sesión para usuarios datero desde aplicación externa
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            ], [
                'email.required' => 'El email es obligatorio.',
                'email.email' => 'El email debe ser una dirección válida.',
                'password.required' => 'El PIN es obligatorio.',
                'password.size' => 'El PIN debe tener exactamente 6 dígitos.',
                'password.regex' => 'El PIN debe contener solo números.',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $credentials = $request->only('email', 'password');

            // Intentar autenticar
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->unauthorizedResponse('Credenciales inválidas');
            }

            // Obtener el usuario autenticado
            $user = auth()->user();

            // Verificar que el usuario tiene rol datero
            if (!$user->isDatero()) {
                JWTAuth::invalidate($token);
                return $this->forbiddenResponse('Acceso denegado. Solo usuarios con rol datero pueden acceder.');
            }

            // Verificar que el usuario esté activo
            if (!$user->isActive()) {
                JWTAuth::invalidate($token);
                return $this->forbiddenResponse('Tu cuenta está desactivada. Contacta al administrador.');
            }

            // Retornar respuesta exitosa con token y datos del usuario
            return $this->successResponse([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60, // en segundos
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->getRoleName(),
                ]
            ], 'Inicio de sesión exitoso');

        } catch (JWTException $e) {
            return $this->errorResponse('Error al generar el token', ['error' => 'No se pudo crear el token'], 500);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error en el servidor');
        }
    }

    /**
     * Cerrar sesión (invalidar token)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            
            if ($token) {
                JWTAuth::invalidate($token);
            }

            return $this->successResponse(null, 'Sesión cerrada exitosamente');

        } catch (JWTException $e) {
            return $this->errorResponse('Error al cerrar sesión', ['error' => 'No se pudo invalidar el token'], 500);
        }
    }

    /**
     * Obtener el usuario autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return $this->unauthorizedResponse('Usuario no autenticado');
            }

            return $this->successResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->getRoleName(),
                'is_active' => $user->isActive(),
            ]);

        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al obtener información del usuario');
        }
    }

    /**
     * Refrescar el token JWT
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($token);

            return $this->successResponse([
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Token renovado exitosamente');

        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token inválido o expirado');
        }
    }
}

