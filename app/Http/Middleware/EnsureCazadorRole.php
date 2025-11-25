<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCazadorRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        // Verificar que el usuario puede acceder al API de Cazador
        // Permite: Administrador, Lider y Cazador (vendedor)
        // NO permite: Dateros
        if (!$user->canAccessCazadorApi()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Solo usuarios Administrador, Lider o Cazador pueden acceder.'
            ], 403);
        }

        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta está desactivada. Contacta al administrador.'
            ], 403);
        }

        return $next($request);
    }
}

