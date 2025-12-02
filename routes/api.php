<?php

use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

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

// Cargar rutas de Cazador
require __DIR__ . '/api/cazador.php';

// Cargar rutas de Datero
require __DIR__ . '/api/datero.php';

// ==================== RUTAS PÚBLICAS (Proyectos publicados) ====================
Route::middleware(['throttle:120,1'])->prefix('projects')->group(function () {
    // Listar proyectos publicados
    Route::get('/', [ProjectController::class, 'index'])
        ->name('api.projects.index');
    
    // Obtener unidades de un proyecto publicado
    Route::get('/{id}/units', [ProjectController::class, 'units'])
        ->name('api.projects.units');
    
    // Ver un proyecto publicado específico
    Route::get('/{id}', [ProjectController::class, 'show'])
        ->name('api.projects.show');
});
