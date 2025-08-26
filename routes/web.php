<?php

use App\Livewire\Projects\ProjectView;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Clients\ClientList;
use App\Livewire\Projects\ProjectList;
use App\Livewire\Opportunities\OpportunityList;
use App\Livewire\Tasks\TaskList;
use App\Livewire\Reports\SalesReport;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Dashboard principal
    Route::get('/dashboard', Dashboard::class)->middleware('permission:view_dashboard')->name('dashboard');

    // Gestión de Clientes
    Route::get('/clients', ClientList::class)->middleware('permission:view_clients')->name('clients.index');

    // Gestión de Proyectos
    Route::get('/projects', ProjectList::class)->middleware('permission:view_projects')->name('projects.index');
    // Vista de Proyecto
    Route::get('/projects/{projectId}', ProjectView::class)->middleware('permission:view_projects')->name('projects.project-view');

// Gestión de Oportunidades
    Route::get('/opportunities', OpportunityList::class)->middleware('permission:view_opportunities')->name('opportunities.index');

    // Gestión de Tareas
    Route::get('/tasks', TaskList::class)->middleware('permission:view_tasks')->name('tasks.index');

    // Reportes
    Route::get('/reports/sales', SalesReport::class)->middleware('permission:view_reports')->name('reports.sales');

    // Rutas adicionales del CRM
    Route::prefix('crm')->name('crm.')->group(function () {
        // Reservas
        Route::get('/reservations', function () {
            return view('crm.reservations.index');
        })->middleware('permission:view_reservations')->name('reservations.index');

        // Comisiones
        Route::get('/commissions', function () {
            return view('crm.commissions.index');
        })->middleware('permission:view_commissions')->name('commissions.index');

        // Documentos
        Route::get('/documents', function () {
            return view('crm.documents.index');
        })->middleware('permission:view_documents')->name('documents.index');

        // Actividades
        Route::get('/activities', function () {
            return view('crm.activities.index');
        })->middleware('permission:view_activities')->name('activities.index');

        // Interacciones
        Route::get('/interactions', function () {
            return view('crm.interactions.index');
        })->middleware('permission:view_interactions')->name('interactions.index');
    });
});

require __DIR__ . '/auth.php';
