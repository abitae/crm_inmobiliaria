<?php

use App\Http\Controllers\Api\Datero\AuthController as DateroAuthController;
use App\Http\Controllers\Api\Datero\ClientController as DateroClientController;
use App\Http\Controllers\Api\Datero\CityController as DateroCityController;
use App\Http\Controllers\Api\Datero\CommissionController as DateroCommissionController;
use App\Http\Controllers\Api\Datero\ProfileController as DateroProfileController;
use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Datero
|--------------------------------------------------------------------------
|
| Rutas de la API para la aplicación Datero (captadores de leads)
|
*/

Route::prefix('datero')->group(function () {
    // Rutas de autenticación (públicas)
    Route::prefix('auth')->group(function () {
        // Registro de dateros - Rate limit más restrictivo
        Route::post('/register', [DateroAuthController::class, 'register'])
            ->middleware('throttle:3,1')
            ->name('api.datero.auth.register');

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

            Route::post('/change-pin', [DateroAuthController::class, 'changePin'])
                ->name('api.datero.auth.change-pin');
        });
    });

    // Rutas de ciudades (protegidas con JWT y middleware datero)
    Route::middleware(['auth:api', 'datero', 'throttle:60,1'])->prefix('cities')->group(function () {
        Route::get('/', [DateroCityController::class, 'index'])
            ->name('api.datero.cities.index');
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

        Route::post('/validate', [DateroClientController::class, 'validateClient'])
            ->name('api.datero.clients.validate');

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
    });

    // Rutas de búsqueda de documentos (protegidas con JWT y middleware datero)
    Route::middleware(['auth:api', 'datero', 'throttle:30,1'])->prefix('documents')->group(function () {
        Route::post('/search', [DocumentController::class, 'search'])
            ->name('api.datero.documents.search');
    });
});

