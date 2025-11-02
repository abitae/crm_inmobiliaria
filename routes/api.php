<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de autenticación (públicas)
Route::prefix('auth')->group(function () {
    // Login solo para dateros - Rate limit más restrictivo para prevenir ataques
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('api.auth.login');
    
    // Rutas protegidas con JWT y middleware datero
    Route::middleware(['auth:api', 'datero'])->group(function () {
        // Obtener usuario autenticado
        Route::get('/me', [AuthController::class, 'me'])
            ->name('api.auth.me');
        
        // Cerrar sesión
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('api.auth.logout');
        
        // Refrescar token
        Route::post('/refresh', [AuthController::class, 'refresh'])
            ->name('api.auth.refresh');
    });
});

// Rutas de clientes (protegidas con JWT y middleware datero)
Route::middleware(['auth:api', 'datero', 'throttle:60,1'])->prefix('clients')->group(function () {
    // Obtener opciones para formularios (menos restrictivo, puede cachearse)
    Route::get('/options', [ClientController::class, 'options'])
        ->middleware('throttle:120,1')
        ->name('api.clients.options');
    
    // Listar clientes del datero
    Route::get('/', [ClientController::class, 'index'])
        ->name('api.clients.index');
    
    // Crear nuevo cliente
    Route::post('/', [ClientController::class, 'store'])
        ->name('api.clients.store');
    
    // Ver un cliente específico
    Route::get('/{id}', [ClientController::class, 'show'])
        ->name('api.clients.show');
    
    // Actualizar un cliente
    Route::match(['put', 'patch'], '/{id}', [ClientController::class, 'update'])
        ->name('api.clients.update');
});

