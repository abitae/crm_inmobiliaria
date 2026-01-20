<?php

use App\Http\Controllers\Api\Cazador\AuthController as CazadorAuthController;
use App\Http\Controllers\Api\Cazador\ClientController as CazadorClientController;
use App\Http\Controllers\Api\Cazador\ClientActivityController;
use App\Http\Controllers\Api\Cazador\ClientTaskController;
use App\Http\Controllers\Api\Cazador\ProjectController as CazadorProjectController;
use App\Http\Controllers\Api\Cazador\ReservationController as CazadorReservationController;
use App\Http\Controllers\Api\Cazador\DateroController as CazadorDateroController;
use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Cazador
|--------------------------------------------------------------------------
|
| Rutas de la API para la aplicación Cazador (vendedores/asesores)
|
*/

Route::prefix('cazador')->group(function () {
    // Rutas de autenticación (públicas)
    Route::prefix('auth')->group(function () {
        // Login para cazadores - Rate limit más restrictivo
        Route::post('/login', [CazadorAuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('api.cazador.auth.login');

        // Rutas protegidas con JWT y middleware cazador
        Route::middleware(['auth:api', 'cazador'])->group(function () {
            Route::get('/me', [CazadorAuthController::class, 'me'])
                ->name('api.cazador.auth.me');

            Route::post('/logout', [CazadorAuthController::class, 'logout'])
                ->name('api.cazador.auth.logout');

            Route::post('/refresh', [CazadorAuthController::class, 'refresh'])
                ->name('api.cazador.auth.refresh');

            Route::post('/change-password', [CazadorAuthController::class, 'changePassword'])
                ->name('api.cazador.auth.change-password');
        });
    });

    // Rutas de clientes (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:60,1'])->prefix('clients')->group(function () {
        Route::get('/options', [CazadorClientController::class, 'options'])
            ->middleware('throttle:120,1')
            ->name('api.cazador.clients.options');

        Route::get('/', [CazadorClientController::class, 'index'])
            ->name('api.cazador.clients.index');

        Route::post('/', [CazadorClientController::class, 'store'])
            ->name('api.cazador.clients.store');

        Route::get('/{id}', [CazadorClientController::class, 'show'])
            ->name('api.cazador.clients.show');

        Route::match(['put', 'patch'], '/{id}', [CazadorClientController::class, 'update'])
            ->name('api.cazador.clients.update');

        Route::post('/{client}/activities', [ClientActivityController::class, 'store'])
            ->name('api.cazador.clients.activities.store');

        Route::post('/{client}/tasks', [ClientTaskController::class, 'store'])
            ->name('api.cazador.clients.tasks.store');
    });

    // Rutas de proyectos (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:60,1'])->prefix('projects')->group(function () {
        Route::get('/', [CazadorProjectController::class, 'index'])
            ->name('api.cazador.projects.index');

        Route::get('/{id}', [CazadorProjectController::class, 'show'])
            ->name('api.cazador.projects.show');

        Route::get('/{id}/units', [CazadorProjectController::class, 'units'])
            ->name('api.cazador.projects.units');
    });

    // Rutas de dateros (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:60,1'])->prefix('dateros')->group(function () {
        Route::get('/', [CazadorDateroController::class, 'index'])
            ->name('api.cazador.dateros.index');

        Route::post('/', [CazadorDateroController::class, 'register'])
            ->name('api.cazador.dateros.store');

        Route::get('/{id}', [CazadorDateroController::class, 'show'])
            ->name('api.cazador.dateros.show');

        Route::match(['put', 'patch'], '/{id}', [CazadorDateroController::class, 'update'])
            ->name('api.cazador.dateros.update');
    });

    // Rutas de reservas (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:60,1'])->prefix('reservations')->group(function () {
        Route::get('/', [CazadorReservationController::class, 'index'])
            ->name('api.cazador.reservations.index');

        Route::post('/', [CazadorReservationController::class, 'store'])
            ->name('api.cazador.reservations.store');

        Route::get('/{id}', [CazadorReservationController::class, 'show'])
            ->name('api.cazador.reservations.show');

        Route::match(['put', 'patch'], '/{id}', [CazadorReservationController::class, 'update'])
            ->name('api.cazador.reservations.update');

        Route::post('/{id}/confirm', [CazadorReservationController::class, 'confirm'])
            ->name('api.cazador.reservations.confirm');

        Route::post('/{id}/cancel', [CazadorReservationController::class, 'cancel'])
            ->name('api.cazador.reservations.cancel');

        Route::post('/{id}/convert-to-sale', [CazadorReservationController::class, 'convertToSale'])
            ->name('api.cazador.reservations.convert-to-sale');
    });

    // Rutas de búsqueda de documentos (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:30,1'])->prefix('documents')->group(function () {
        Route::post('/search', [DocumentController::class, 'search'])
            ->name('api.cazador.documents.search');
    });
});

