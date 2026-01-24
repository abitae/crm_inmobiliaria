<?php

use App\Http\Controllers\Api\Cazador\AuthController as CazadorAuthController;
use App\Http\Controllers\Api\Cazador\ClientController as CazadorClientController;
use App\Http\Controllers\Api\Cazador\ClientActivityController;
use App\Http\Controllers\Api\Cazador\ClientTaskController;
use App\Http\Controllers\Api\Cazador\ProjectController as CazadorProjectController;
use App\Http\Controllers\Api\Cazador\ReservationController as CazadorReservationController;
use App\Http\Controllers\Api\Cazador\DateroController as CazadorDateroController;
use App\Http\Controllers\Api\Cazador\DashboardController as CazadorDashboardController;
use App\Http\Controllers\Api\Cazador\ValidationController as CazadorValidationController;
use App\Http\Controllers\Api\Cazador\SyncController as CazadorSyncController;
use App\Http\Controllers\Api\Cazador\ExportController as CazadorExportController;
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
        Route::post('/validate', [CazadorValidationController::class, 'validateClient'])
            ->name('api.cazador.clients.validate');

        Route::get('/export', [CazadorExportController::class, 'clients'])
            ->name('api.cazador.clients.export');

        Route::get('/options', [CazadorClientController::class, 'options'])
            ->middleware(['throttle:120,1', 'cache.headers:public;max_age=300'])
            ->name('api.cazador.clients.options');

        Route::get('/suggestions', [CazadorClientController::class, 'suggestions'])
            ->middleware('cache.headers:public;max_age=60')
            ->name('api.cazador.clients.suggestions');

        Route::get('/', [CazadorClientController::class, 'index'])
            ->middleware('cache.headers:public;max_age=60')
            ->name('api.cazador.clients.index');

        Route::get('/batch', [CazadorClientController::class, 'batchShow'])
            ->middleware('cache.headers:public;max_age=60')
            ->name('api.cazador.clients.batch.show');

        Route::post('/', [CazadorClientController::class, 'store'])
            ->name('api.cazador.clients.store');

        Route::post('/batch', [CazadorClientController::class, 'batchStore'])
            ->name('api.cazador.clients.batch.store');

        Route::get('/{id}', [CazadorClientController::class, 'show'])
            ->middleware('cache.headers:public;max_age=60')
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
        Route::get('/suggestions', [CazadorProjectController::class, 'suggestions'])
            ->middleware('cache.headers:public;max_age=60')
            ->name('api.cazador.projects.suggestions');

        Route::get('/', [CazadorProjectController::class, 'index'])
            ->middleware('cache.headers:public;max_age=120')
            ->name('api.cazador.projects.index');

        Route::get('/{id}', [CazadorProjectController::class, 'show'])
            ->middleware('cache.headers:public;max_age=120')
            ->name('api.cazador.projects.show');

        Route::get('/{id}/units', [CazadorProjectController::class, 'units'])
            ->middleware('cache.headers:public;max_age=120')
            ->name('api.cazador.projects.units');
    });

    // Rutas de dateros (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:60,1'])->prefix('dateros')->group(function () {
        Route::get('/', [CazadorDateroController::class, 'index'])
            ->middleware('cache.headers:public;max_age=60')
            ->name('api.cazador.dateros.index');

        Route::post('/', [CazadorDateroController::class, 'register'])
            ->name('api.cazador.dateros.store');

        Route::get('/{id}', [CazadorDateroController::class, 'show'])
            ->middleware('cache.headers:public;max_age=60')
            ->name('api.cazador.dateros.show');

        Route::match(['put', 'patch'], '/{id}', [CazadorDateroController::class, 'update'])
            ->name('api.cazador.dateros.update');
    });

    // Rutas de dashboard (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:60,1'])->prefix('dashboard')->group(function () {
        Route::get('/stats', [CazadorDashboardController::class, 'stats'])
            ->middleware('cache.headers:public;max_age=60')
            ->name('api.cazador.dashboard.stats');
    });

    // Rutas de reservas (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:60,1'])->prefix('reservations')->group(function () {
        Route::post('/validate', [CazadorValidationController::class, 'validateReservation'])
            ->name('api.cazador.reservations.validate');

        Route::get('/export', [CazadorExportController::class, 'reservations'])
            ->name('api.cazador.reservations.export');

        Route::get('/', [CazadorReservationController::class, 'index'])
            ->middleware('cache.headers:public;max_age=60')
            ->name('api.cazador.reservations.index');

        Route::post('/', [CazadorReservationController::class, 'store'])
            ->name('api.cazador.reservations.store');

        Route::post('/batch', [CazadorReservationController::class, 'batchStore'])
            ->name('api.cazador.reservations.batch.store');

        Route::get('/{id}', [CazadorReservationController::class, 'show'])
            ->middleware('cache.headers:public;max_age=60')
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
        Route::post('/validate-dni', [CazadorValidationController::class, 'validateDni'])
            ->name('api.cazador.documents.validate-dni');

        Route::post('/search', [DocumentController::class, 'search'])
            ->name('api.cazador.documents.search');
    });

    // Rutas de sincronizacion (protegidas con JWT y middleware cazador)
    Route::middleware(['auth:api', 'cazador', 'throttle:30,1'])->get('/sync', [CazadorSyncController::class, 'sync'])
        ->name('api.cazador.sync');

    // Reportes
    Route::middleware(['auth:api', 'cazador', 'throttle:30,1'])->prefix('reports')->group(function () {
        Route::get('/sales', [CazadorExportController::class, 'salesReport'])
            ->name('api.cazador.reports.sales');
    });
});

