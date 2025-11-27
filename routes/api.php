<?php

use App\Http\Controllers\Api\Cazador\AuthController as CazadorAuthController;
use App\Http\Controllers\Api\Cazador\ClientController as CazadorClientController;
use App\Http\Controllers\Api\Cazador\ProjectController as CazadorProjectController;
use App\Http\Controllers\Api\Cazador\ReservationController as CazadorReservationController;
use App\Http\Controllers\Api\Datero\AuthController as DateroAuthController;
use App\Http\Controllers\Api\Datero\ClientController as DateroClientController;
use App\Http\Controllers\Api\Datero\CommissionController as DateroCommissionController;
use App\Http\Controllers\Api\Datero\ProfileController as DateroProfileController;
use App\Http\Controllers\Api\DocumentController;
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

// ==================== APLICACIÓN DATERO ====================
Route::prefix('datero')->group(function () {
    // Rutas de autenticación (públicas)
    Route::prefix('auth')->group(function () {
        // Login para dateros - Rate limit más restrictivo
        Route::post('/login', [DateroAuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('api.datero.auth.login');

        // Rutas protegidas con JWT y middleware datero
        Route::middleware(['auth:api', 'datero'])->group(function () {
            Route::get('/me', [DateroAuthController::class, 'me'])
                ->name('api.datero.auth.me');

            Route::post('/logout', [DateroAuthController::class, 'logout'])
                ->name('api.datero.auth.logout');

            Route::post('/refresh', [DateroAuthController::class, 'refresh'])
                ->name('api.datero.auth.refresh');

            Route::post('/change-password', [DateroAuthController::class, 'changePassword'])
                ->name('api.datero.auth.change-password');
        });
    });

    // Rutas de clientes (protegidas con JWT y middleware datero)
    Route::middleware(['auth:api', 'datero', 'throttle:60,1'])->prefix('clients')->group(function () {
        Route::get('/options', [DateroClientController::class, 'options'])
            ->middleware('throttle:120,1')
            ->name('api.datero.clients.options');

        Route::get('/', [DateroClientController::class, 'index'])
            ->name('api.datero.clients.index');

        Route::post('/', [DateroClientController::class, 'store'])
            ->name('api.datero.clients.store');

        Route::get('/{id}', [DateroClientController::class, 'show'])
            ->name('api.datero.clients.show');

        Route::match(['put', 'patch'], '/{id}', [DateroClientController::class, 'update'])
            ->name('api.datero.clients.update');
    });

    // Rutas de comisiones (protegidas con JWT y middleware datero)
    Route::middleware(['auth:api', 'datero', 'throttle:60,1'])->prefix('commissions')->group(function () {
        Route::get('/', [DateroCommissionController::class, 'index'])
            ->name('api.datero.commissions.index');

        Route::get('/stats', [DateroCommissionController::class, 'stats'])
            ->name('api.datero.commissions.stats');

        Route::get('/{id}', [DateroCommissionController::class, 'show'])
            ->name('api.datero.commissions.show');
    });

    // Rutas de perfil (protegidas con JWT y middleware datero)
    Route::middleware(['auth:api', 'datero', 'throttle:60,1'])->prefix('profile')->group(function () {
        Route::get('/', [DateroProfileController::class, 'show'])
            ->name('api.datero.profile.show');

        Route::put('/', [DateroProfileController::class, 'update'])
            ->name('api.datero.profile.update');

        Route::patch('/', [DateroProfileController::class, 'update'])
            ->name('api.datero.profile.update');

        Route::post('/change-password', [DateroProfileController::class, 'changePassword'])
            ->name('api.datero.profile.change-password');
    });

    // Rutas de búsqueda de documentos (protegidas con JWT y middleware datero)
    Route::middleware(['auth:api', 'datero', 'throttle:30,1'])->prefix('documents')->group(function () {
        Route::post('/search', [DocumentController::class, 'search'])
            ->name('api.datero.documents.search');
    });
});

// ==================== APLICACIÓN CAZADOR ====================
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
